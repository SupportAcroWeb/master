<?php

namespace Acroweb\Mage\Settings;

use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\Engine\CurrentUser;

Loc::loadMessages(__FILE__);

/**
 * Class TemplateSettings
 *
 * @package Acroweb\Mage\Settings
 */
class TemplateSettings
{
    private static ?self $instance = null;
    private string $moduleId = 'acroweb.mage';
    private array $settings = [];
    private int $cacheTtl = 86400;
    private string $cacheKey = 'template_settings';
    private string $cacheDir = '/acroweb.mage/settings/';
    private $userSettings = null;
    private $isUserSettingsEnabled = null;

    /**
     * Private constructor to prevent direct instantiation.
     * Use getInstance() instead.
     */
    private function __construct()
    {
        $this->loadSettings();
    }

    /**
     * Get the singleton instance of TemplateSettings.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load settings from cache or database.
     */
    private function loadSettings(): void
    {
        $cache = Cache::createInstance();

        if ($cache->initCache($this->cacheTtl, $this->cacheKey, $this->cacheDir)) {
            $this->settings = $cache->getVars() ?? [];
        } elseif ($cache->startDataCache()) {
            $this->settings = Option::getForModule($this->moduleId);

            if (empty($this->settings)) {
                $this->setDefaultSettings();
            }

            $cache->endDataCache($this->settings);
        }
    }

    /**
     * Set default settings for the module.
     */
    private function setDefaultSettings(): void
    {
        $defaults = SettingsConfig::getDefaultSettings();
        foreach ($defaults as $key => $value) {
            if (!isset($this->settings[$key])) {
                Option::set($this->moduleId, $key, $value);
                $this->settings[$key] = $value;
            }
        }

        $this->clearCache();
    }

    /**
     * Get all module settings.
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        if ($this->isUserSettingsEnabled()) {
            $userSettings = $this->getUserSettings();
            return array_merge($this->settings, $userSettings);
        }

        return $this->settings;
    }

    /**
     * Получить значение настройки по ключу.
     * Учитывает, что Bitrix может хранить ключи в нижнем регистре,
     * хотя в конфиге и коде они используются в camelCase.
     *
     * @param string $key Ключ настройки
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    public function getSetting(string $key, $default = null): mixed
    {
        if ($this->isUserSettingsEnabled()) {
            $userSettings = $this->getUserSettings();
            if (array_key_exists($key, $userSettings)) {
                return $userSettings[$key];
            }

            $lowerKey = strtolower($key);
            if (array_key_exists($lowerKey, $userSettings)) {
                return $userSettings[$lowerKey];
            }
        }

        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }

        $lowerKey = strtolower($key);
        if (array_key_exists($lowerKey, $this->settings)) {
            return $this->settings[$lowerKey];
        }

        return SettingsConfig::getDefaultValue($key) ?? $default;
    }

    public function setUserSetting(string $key, $value): void
    {
        if ($this->isUserSettingsEnabled() && SettingsConfig::isValidOption($key, $value)) {
            if ($this->userSettings === null) {
                $this->userSettings = $this->getUserSettings();
            }
            $this->userSettings[$key] = $value;
        }
    }

    public function saveUserSettings(): void
    {
        if ($this->isUserSettingsEnabled() && $this->userSettings !== null) {
            $application = Application::getInstance();
            $context = $application->getContext();
            $response = $context->getResponse();

            $cookie = new Cookie('user_settings', json_encode($this->userSettings), time() + 30 * 24 * 3600);
            $cookie->setDomain($context->getServer()->getHttpHost());
            $cookie->setHttpOnly(false);
            $response->addCookie($cookie);
        }
    }

    /**
     * @return bool
     */
    private function isAdmin(): bool
    {
        return CurrentUser::get()->isAdmin();
    }

    /**
     * @return bool
     */
    private function isUserSettingsEnabled(): bool
    {
        if ($this->isUserSettingsEnabled === null) {
            $settingEnabled = ($this->settings['enable_user_settings'] ?? 'N') === 'Y';
            $this->isUserSettingsEnabled = $settingEnabled && !$this->isAdmin();
        }
        return $this->isUserSettingsEnabled;
    }

    /**
     * Check if the current user can save settings.
     *
     * @return bool
     */
    public function canSaveSettings(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->isUserSettingsEnabled();
    }

    private function getUserSettings(): array
    {
        if ($this->userSettings === null) {
            $request = Application::getInstance()->getContext()->getRequest();
            $cookieData = $request->getCookie('user_settings');
            $this->userSettings = $cookieData ? json_decode($cookieData, true) : [];
        }
        return $this->userSettings;
    }

    public function saveSettings(array $settings): void
    {
        if ($this->isAdmin()) {
            $this->updateSettings($settings);
        } elseif ($this->isUserSettingsEnabled()) {
            foreach ($settings as $key => $value) {
                $this->setUserSetting($key, $value);
            }
            $this->saveUserSettings();
            $this->clearOtherCache();
        } else {
            throw new \Exception(Loc::getMessage('ACROWEB_CORE_SETTINGS_SAVE_NOT_ALLOWED'));
        }
    }

    /**
     * Get the value of a setting, considering custom options and option values.
     *
     * @param string $key The setting key
     * @param mixed $default Default value if the setting is not found
     * @return mixed
     */
    public function getSettingValue(string $key, $default = null): mixed
    {
        $value = $this->getSetting($key, $default);

        // Универсальная обработка множественных опций
        if (SettingsConfig::isMultipleOption($key)) {
            $items = preg_split('/[\r\n]+/', (string)$value, -1, PREG_SPLIT_NO_EMPTY);
            return array_map('trim', $items);
        }

        if ($value === 'custom') {
            $customKey = $key . '_custom';
            return $this->getSetting($customKey, $default);
        }

        $options = SettingsConfig::getAllOptions();
        if (isset($options[$key]) && is_array($options[$key])) {
            return $options[$key][$value] ?? $value;
        }

        return $value;
    }

    /**
     * Get the current header type.
     *
     * @return string
     */
    public function getHeaderType(): string
    {
        $isMainPage = $this->isMainPage();
        $settingKey = $isMainPage ? 'header_type_main' : 'header_type_inner';
        return $this->getSetting($settingKey, 'default');
    }

    /**
     * Get the current footer type.
     *
     * @return string
     */
    public function getFooterType(): string
    {
        return $this->getSetting('footer_type', 'default');
    }

    /**
     * Update settings with new values.
     *
     * @param array $newSettings
     * @throws ArgumentOutOfRangeException
     */
    public function updateSettings(array $newSettings): void
    {
        foreach ($newSettings as $key => $value) {
            if (SettingsConfig::isValidOption($key, $value)) {
                Option::set($this->moduleId, $key, $value);
            } else {
                throw new ArgumentOutOfRangeException(
                    $key,
                    Loc::getMessage('ACROWEB_CORE_INVALID_OPTION_VALUE')
                );
            }
        }

        $this->refreshSettings();
    }

    /**
     * Clear the settings cache.
     */
    private function clearCache(): void
    {
        $cache = Cache::createInstance();
        $cache->clean($this->cacheKey, $this->cacheDir);
    }

    /**
     * Clear module settings.
     *
     * @param array|null $keys Array of specific keys to clear. If null, all settings will be cleared.
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public function clearSettings(?array $keys = null): void
    {
        if ($keys === null) {
            $options = Option::getForModule($this->moduleId);
            foreach ($options as $key => $value) {
                Option::delete($this->moduleId, ['name' => $key]);
            }
            $this->settings = [];
        } else {
            foreach ($keys as $key) {
                Option::delete($this->moduleId, ['name' => $key]);
                unset($this->settings[$key]);
            }
        }

        $this->refreshSettings();
    }

    /**
     * Reset settings to default values.
     */
    public function resetToDefaultSettings(): void
    {
        $this->clearSettings();
        $this->setDefaultSettings();
        $this->refreshSettings();
    }

    /**
     * Get available options for a specific setting.
     *
     * @param string $key
     * @return array
     */
    public function getAvailableOptions(string $key): array
    {
        return SettingsConfig::getOptionValues($key);
    }

    /**
     * Render a file based on type and folder.
     *
     * @param string $type
     * @param string $folder
     * @return string
     */
    private function renderFile(string $type, string $folder): string
    {
        $file = Application::getDocumentRoot() . SITE_TEMPLATE_PATH . "/includes/{$folder}/{$type}.php";

        if (file_exists($file)) {
            ob_start();
            include $file;
            return ob_get_clean();
        }

        return '';
    }

    /**
     * Render the header.
     *
     * @return string
     */
    public function renderHeader(): string
    {
        $isMainPage = $this->isMainPage();
        $headerType = $this->getHeaderType();
        $folder = $isMainPage ? 'headers_main' : 'headers_inner';
        return $this->renderFile("header_{$headerType}", $folder);
    }

    /**
     * Render the footer.
     *
     * @return string
     */
    public function renderFooter(): string
    {
        return $this->renderFile('footer_' . $this->getFooterType(), 'footers');
    }

    public function isMainPage(): bool
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $curPage = $request->getRequestedPageDirectory();
        return $curPage === SITE_DIR;
    }

    /**
     * Check if the current page should display an image header.
     *
     * @return bool
     */
    public function hasImageHeader(): bool
    {
        $isMainPage = $this->isMainPage();
        $headerType = $this->getHeaderType();

        return !$isMainPage && $headerType === 'type3';
    }

    /**
     * Render the banner.
     *
     * @return string
     */
    public function renderBanner(): string
    {
        $bannerType = $this->getSetting('banner_type', 'default');
        return $this->renderFile("banner_{$bannerType}", 'banners');
    }

    /**
     * Render the page info block.
     *
     * @param string $pageType The type of the page
     * @return string
     */
    public function renderPageInfo(string $pageType): string
    {
        return $this->renderFile("{$pageType}", 'page_info');
    }

    /**
     * Refresh settings by clearing the cache and reloading from the database.
     */
    public function refreshSettings(): void
    {
        $this->clearCache();
        $this->loadSettings();

        $this->clearOtherCache();
    }

    /**
     * Clear other cache.
     * @return void
     */
    private function clearOtherCache(): void
    {
        // TODO: не совсем надежно так как кеш может быть большой, что то придумать
        \CBitrixComponent::clearComponentCache('bitrix:news.list');
    }
}