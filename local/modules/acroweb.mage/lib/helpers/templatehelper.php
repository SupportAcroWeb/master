<?php

namespace Acroweb\Mage\Helpers;

use Bitrix\Main\IO;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\Uri;
use Throwable;
use Bitrix\Main\Context;
use CMain;
use Bitrix\Main\Application;
use Acroweb\Mage\Settings\TemplateSettings;

/**
 * Class TemplateHelper
 *
 * Helper class for managing template-related functionality.
 */
class TemplateHelper
{
    /**
     * @var array
     */
    protected static array $params = [];

    /**
     * Get a parameter value.
     *
     * @param string $paramName
     * @param mixed $default
     * @return mixed
     */
    public static function getParam(string $paramName, $default = null)
    {
        return $paramName !== '' && array_key_exists($paramName, self::$params) ? self::$params[$paramName] : $default;
    }

    /**
     * Get the path to partials.
     *
     * @return string|null
     */
    public static function getPartialsPath(): ?string
    {
        return IO\Path::convertSiteRelativeToAbsolute(SITE_TEMPLATE_PATH . '/partials/');
    }

    /**
     * Set a parameter value.
     *
     * @param string $paramName
     * @param mixed $value
     * @return void
     */
    public static function setParam(string $paramName, $value): void
    {
        self::$params[$paramName] = $value;
    }

    /**
     * Show the title.
     *
     * @param string $strClass
     * @return void
     */
    public static function showTitle(string $strClass = ''): void
    {
        global $APPLICATION;

        $APPLICATION->AddBufferContent(function () use ($APPLICATION, $strClass) {
            if ($APPLICATION->GetPageProperty('SHOW_MAIN_TITLE', 'N') !== 'Y') {
                return '';
            }

            return "<h2 " . (strlen($strClass) ? ' class="' . $strClass . '"' : '') . ">" . $APPLICATION->GetTitle(
                    false,
                    false
                ) . "</h2>";
        });
    }

    /**
     * Show div class.
     *
     * @param string $class
     * @return void
     */
    public static function showDivClass(string $class = ''): void
    {
        global $APPLICATION;
        $APPLICATION->AddBufferContent(function () use ($class) {
            if (empty($class)) {
                return '';
            }
            $strClass = self::getParam($class, '');
            return !empty($strClass) ? ' ' . $strClass : '';
        });
    }

    /**
     * Set multiple parameters.
     *
     * @param array $arParams
     * @return void
     */
    public static function setParams(array $arParams): void
    {
        if (!empty($arParams)) {
            self::$params = array_merge(self::$params, $arParams);
        }
    }

    /**
     * Get all parameters.
     *
     * @return array
     */
    public static function getParams(): array
    {
        return self::$params;
    }

    /**
     * Clear all parameters.
     *
     * @return void
     */
    public static function clearParams(): void
    {
        self::$params = [];
    }

    /**
     * Include JS messages.
     *
     * @param array $arMessCodes
     * @param bool $add2Asset
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function includeJSMessages(array $arMessCodes = [], bool $add2Asset = true): string
    {
        if (!empty($arMessCodes)) {
            $arMessages = [];

            foreach ($arMessCodes as $key => $messCode) {
                if (!($mess = Loc::getMessage($messCode))) {
                    continue;
                }

                if (is_numeric($key)) {
                    $arMessages[$messCode] = $mess;
                } else {
                    $arMessages[$key] = $mess;
                }
            }

            if (!empty($arMessages)) {
                $strJs = '<script>BX.message(' . Json::encode($arMessages) . ')</script>';

                if ($add2Asset) {
                    Asset::getInstance()->addString($strJs);
                } else {
                    return $strJs;
                }
            }
        }

        return '';
    }

    /**
     * Process the end of buffer content.
     *
     * @param string $content
     * @return void
     */
    public static function onEndBufferContent(string &$content): void
    {
        global $APPLICATION;

        if (preg_match_all('#(<!--move-from-content-(.+)-->)(.*)\1#is', $content, $matches)) {
            foreach ($matches[2] as $index => $tag) {
                $APPLICATION->AddViewContent($tag, $matches[3][$index]);
                $content = str_replace($matches[0][$index], '', $content);
            }
        }

        if (preg_match_all('#<!--move-content-(.+)-->#Uis', $content, $matches)) {
            foreach ($matches[1] as $index => $tag) {
                $content = str_replace($matches[0][$index], $APPLICATION->GetViewContent($tag), $content);
            }
        }

        if (preg_match_all('#<!--move-page-(title)-->#Uis', $content, $matches)) {
            $strTitle = $APPLICATION->GetTitle(false, false);

            foreach ($matches[1] as $index => $tag) {
                $content = str_replace($matches[0][$index], $strTitle, $content);
            }
        }
    }

    /**
     * Handle 404 page.
     *
     * @return void
     */
    public static function page404(): void
    {
        global $APPLICATION;

        if (defined('ERROR_404') && ERROR_404 == 'Y' && !defined('ADMIN_SECTION')) {
            $APPLICATION->RestartBuffer();
            $documentRoot = Application::getDocumentRoot();
            include $documentRoot . SITE_TEMPLATE_PATH . '/header.php';
            require($documentRoot . '/404.php');
            include $documentRoot . SITE_TEMPLATE_PATH . '/footer.php';
            die;
        }
    }

    /**
     * Include layout file.
     *
     * @param string|null $file
     * @return void
     */
    public static function includeLayout(?string $file = null): void
    {
        $templateLayout = self::getParam('layout', 'default');
        if (!$file || $templateLayout == 'empty') {
            return;
        }

        try {
            $layoutFile = IO\Path::convertSiteRelativeToAbsolute(
                IO\Path::normalize(SITE_TEMPLATE_PATH . '/layouts/' . $templateLayout . '/' . $file . '.php')
            );
        } catch (Throwable) {
            return;
        }

        if (file_exists($layoutFile)) {
            include($layoutFile);
        }
    }

    /**
     * Include partial file.
     *
     * @param string $path
     * @return void
     */
    public static function includePartial(string $path): void
    {
        try {
            $file = IO\Path::normalize(self::getPartialsPath() . '/' . $path . '.php');
        } catch (Throwable) {
            return;
        }

        if (file_exists($file)) {
            include($file);
        }
    }

    /**
     * Include a page controller file.
     *
     * This method attempts to include a PHP file from the page controllers directory.
     * If the file doesn't exist or an error occurs during the process, it silently returns.
     *
     * @param string $path The path to the controller file, relative to the page controllers directory
     *
     * @return void
     */
    public static function includePageController(string $path): void
    {
        try {
            $file = IO\Path::normalize(self::getPageControllersPath() . '/' . $path . '.php');
        } catch (Throwable) {
            return;
        }

        if (file_exists($file)) {
            include $file;
        }
    }

    /**
     * Get the absolute path to the page controllers directory.
     *
     * This method returns the absolute path to the directory containing
     * page controller files. It uses the SITE_TEMPLATE_PATH constant
     * to determine the base path.
     *
     * @return string|null The absolute path to the page controllers directory,
     *                     or null if the path couldn't be determined
     */
    public static function getPageControllersPath(): ?string
    {
        return IO\Path::convertSiteRelativeToAbsolute(SITE_TEMPLATE_PATH . '/page_controllers/');
    }

    /**
     * Set technical mode.
     *
     * @param bool $status
     * @param string|array $allowedIPs
     * @return void
     */
    public static function technicalMode(bool $status, string|array $allowedIPs = []): void
    {
        if ($status) {
            if ($allowedIPs && !is_array($allowedIPs)) {
                $allowedIPs = [$allowedIPs];
            }

            self::checkAccess($allowedIPs);
        }
    }

    /**
     * Check access based on IP.
     *
     * @param array $allowedIPs
     * @return void
     */
    public static function checkAccess(array $allowedIPs = []): void
    {
        $request = Context::getCurrent()->getRequest();
        $userIP = $request->getRemoteAddress();

        global $USER, $APPLICATION;
        if (is_object($USER) && method_exists($USER, 'IsAdmin')) {
            $isAdmin = $USER->IsAdmin();
        } else {
            $isAdmin = false;
        }

        $isAllowedIP = in_array($userIP, $allowedIPs);
        if (!$isAdmin && !$isAllowedIP) {
            $technicalWorksFile = Application::getDocumentRoot() . $APPLICATION->GetTemplatePath('technical_works.php');

            if (file_exists($technicalWorksFile)) {
                include $technicalWorksFile;
            } else {
                ShowMessage(Loc::getMessage('TECHNICAL_WORKS_MESSAGE'));
            }
            CMain::FinalActions();
        }
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function startsWith(string $haystack, string $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * Process an array of blocks based on template settings.
     *
     * This function iterates through an array of blocks, checks their types and settings,
     * and includes partials based on the conditions specified in each block.
     *
     * @param array $blocks An array of block configurations. Each block should be an associative array
     *                      containing keys such as 'type', 'filter', 'globalVars', and 'partial'.
     *                      - 'type': String, either 'checkbox' or 'selectbox'.
     *                      - 'filter': Optional. An array with 'name' and 'conditions' keys.
     *                      - 'globalVars': Optional. An associative array of global variables to set.
     *                      - 'partial': String, the name of the partial to include.
     *
     * @return void This function does not return a value, but it may include partials
     *              and set global variables as a side effect.
     */
    public static function processBlocks(array $blocks): void
    {
        $templateSettings = TemplateSettings::getInstance();
        $settings = $templateSettings->getAllSettings();

        foreach ($blocks as $key => $block) {
            $shouldInclude = false;
            $templateValue = '';

            if ($block['type'] === 'checkbox') {
                $shouldInclude = ($settings[$key] === 'Y');
            } elseif ($block['type'] === 'selectbox') {
                $shouldInclude = true;
                $templateValue = $settings[$key];
            }

            if ($shouldInclude) {
                if (isset($block['filter'])) {
                    global ${$block['filter']['name']};
                    ${$block['filter']['name']} = $block['filter']['conditions'];
                }
                if (isset($block['globalVars'])) {
                    foreach ($block['globalVars'] as $varName => $value) {
                        global ${$varName};
                        ${$varName} = $value;
                    }
                }

                if ($block['type'] === 'selectbox') {
                    global $templateValue;
                    $templateValue = $settings[$key];
                }

                self::includePartial($block['partial']);
            }
        }
    }

    /**
     * Добавляет в верхнюю панель Битрикс кнопку очистки структуры магазина.
     *
     * @return void
     */
    public static function addClearStructurePanelButton(): void
    {
        global $APPLICATION, $USER;

        if (!is_object($APPLICATION) || !is_object($USER) || !$USER->IsAdmin()) {
            return;
        }

        $request = Context::getCurrent()->getRequest();
        $uri = new Uri($request->getRequestUri());
        $uri->addParams(['acroweb_clear_structure' => 'Y']);

        $APPLICATION->AddPanelButton([
            'ID' => 'acroweb_shop_clear_structure',
            'TEXT' => 'Обновить кеш структуры магазина',
            'TITLE' => 'Очистить кеш структуры магазина и обновить страницу',
            'MAIN_SORT' => 2000,
            'SORT' => 10,
            'HREF' => $uri->getUri(),
            'ICON' => 'bx-panel-themes-icon',
        ]);
    }

    /**
     * Обрабатывает запрос на очистку структуры магазина с последующей перезагрузкой страницы.
     *
     * @return void
     */
    public static function handleClearStructureRequest(): void
    {
        $request = Context::getCurrent()->getRequest();

        if ($request->get('acroweb_clear_structure') !== 'Y') {
            return;
        }

        global $USER;
        if (!is_object($USER) || !$USER->IsAdmin()) {
            return;
        }

        self::clearSitePartialsCache();

        $uri = new Uri($request->getRequestUri());
        $uri->deleteParams(['acroweb_clear_structure']);

        LocalRedirect($uri->getUri());
    }

    /**
     * Очищает кеш частичных шаблонов для указанного сайта.
     *
     * @param string|null $siteId
     * @return void
     */
    public static function clearSitePartialsCache(?string $siteId = null): void
    {
        $siteId = $siteId ?: (defined('SITE_ID') ? SITE_ID : '');
        if ($siteId === '') {
            return;
        }

        $partialsPath = self::getPartialsPath();
        if ($partialsPath === null) {
            return;
        }

        try {
            $sitePartialsDir = IO\Path::normalize($partialsPath . '/' . $siteId . '/');
        } catch (Throwable) {
            $sitePartialsDir = null;
        }

        if ($sitePartialsDir && IO\Directory::isDirectoryExists($sitePartialsDir)) {
            IO\Directory::deleteDirectory($sitePartialsDir);
        }

        try {
            $langPartialsDir = IO\Path::normalize(
                self::getTemplatePath() . '/lang/' . LANGUAGE_ID . '/partials/' . $siteId . '/'
            );
        } catch (Throwable) {
            $langPartialsDir = null;
        }

        if ($langPartialsDir && IO\Directory::isDirectoryExists($langPartialsDir)) {
            IO\Directory::deleteDirectory($langPartialsDir);
        }
    }

    public static function getTemplatePath(): ?string
    {
        return IO\Path::convertSiteRelativeToAbsolute(SITE_TEMPLATE_PATH);
    }
}