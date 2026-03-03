<?php

namespace Acroweb\Mage\Settings;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

class SettingsConfig
{
    private $templateSettings;

    public function __construct(TemplateSettings $templateSettings)
    {
        $this->templateSettings = $templateSettings;
    }

    public static function create(): self
    {
        return new self(TemplateSettings::getInstance());
    }

    public static function getBitrixOptions(): array
    {
        return [
            'system' => [
                'TITLE' => Loc::getMessage('ACROWEB_CORE_TAB_SYSTEM'),
                'OPTIONS' => [
                    'siteName' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_NAME'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'siteNameFooter' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_NAME_FOOTER'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'siteTelephone' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_TELEPHONE'),
                        'TYPE' => 'textarea',
                        'DEFAULT' => '',
                        'HINT' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_TELEPHONE_HINT'),
                        'MULTIPLE' => true,
                    ],
                    'siteEmail' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_EMAIL'),
                        'TYPE' => 'textarea',
                        'DEFAULT' => '',
                        'HINT' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_EMAIL_HINT'),
                        'MULTIPLE' => true,
                    ],
                    'siteLogo' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_LOGO'),
                        'TYPE' => 'file',
                        'DEFAULT' => '',
                    ],
                    'siteLogoRetina' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_LOGO_RETINA'),
                        'TYPE' => 'file',
                        'DEFAULT' => '',
                    ],
                    'siteLogoMobile' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_LOGO_MOBILE'),
                        'TYPE' => 'file',
                        'DEFAULT' => '',
                    ],
                    'siteLogoMobileRetina' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SITE_LOGO_MOBILE_RETINA'),
                        'TYPE' => 'file',
                        'DEFAULT' => '',
                    ],
                ],
            ],
            'company' => [
                'TITLE' => Loc::getMessage('ACROWEB_CORE_TAB_COMPANY'),
                'OPTIONS' => [
                    'shopOfName' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_OF_NAME'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopLocationLatitude' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_LOCATION_LATITUDE'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopLocationLongitude' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_LOCATION_LONGITUDE'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopAdr' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_ADR'),
                        'TYPE' => 'textarea',
                        'DEFAULT' => '',
                    ],
                    'shopUrAdr' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_UR_ADR'),
                        'TYPE' => 'textarea',
                        'DEFAULT' => '',
                    ],
                    'shopINN' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_INN'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopKPP' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_KPP'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopNS' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_NS'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopBANK' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_BANK'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopBIK' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_BIK'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                    'shopKS' => [
                        'TITLE' => Loc::getMessage('ACROWEB_CORE_OPTION_SHOP_KS'),
                        'TYPE' => 'text',
                        'DEFAULT' => '',
                    ],
                ],
            ],
        ];
    }

    public static function getAllOptions(): array
    {
        $bitrixOptions = self::getBitrixOptions();
        $result = [];

        foreach ($bitrixOptions as $tabKey => $tabData) {
            foreach ($tabData['OPTIONS'] as $optionKey => $optionData) {
                $result[$optionKey] = $optionData['VALUES'] ?? [$optionData['TYPE']];
            }
        }

        return $result;
    }

    public static function getDefaultSettings(): array
    {
        $bitrixOptions = self::getBitrixOptions();
        $defaults = [];

        foreach ($bitrixOptions as $tabData) {
            foreach ($tabData['OPTIONS'] as $optionKey => $optionData) {
                $defaults[$optionKey] = $optionData['DEFAULT'];
            }
        }

        return $defaults;
    }

    public static function getOptionValues(string $key): array
    {
        $allOptions = self::getAllOptions();
        return $allOptions[$key] ?? [];
    }

    public static function isValidOption(string $key, $value): bool
    {
        $options = self::getOptionValues($key);

        if (empty($options)) {
            return false;
        }

        // Если options - это массив значений (для selectbox)
        if (count($options) > 1) {
            return array_key_exists($value, $options);
        }

        // Для остальных типов
        $type = $options[0];
        switch ($type) {
            case 'checkbox':
                return $value === 'Y' || $value === 'N';
            case 'text':
                return is_string($value) && strlen(trim($value)) <= 255;
            case 'textarea':
                return is_string($value) && strlen(trim($value)) <= 2000;
            case 'colorpicker':
                return preg_match('/^#[0-9A-Fa-f]{6}$/', $value) === 1;
            case 'hidden':
                return is_string($value) && strlen(trim($value)) <= 255;
            case 'file':
                if (!is_string($value)) {
                    return false;
                }
                $realPath = str_replace('SITE_TEMPLATE_PATH', Application::getDocumentRoot() . SITE_TEMPLATE_PATH, $value);
                return file_exists($realPath);
            default:
                return false;
        }
    }

    public static function getDefaultValue(string $key)
    {
        $defaults = self::getDefaultSettings();
        return $defaults[$key] ?? null;
    }

    /**
     * Проверяет, является ли опция множественной
     *
     * @param string $key Ключ опции
     * @return bool
     */
    public static function isMultipleOption(string $key): bool
    {
        $bitrixOptions = self::getBitrixOptions();

        foreach ($bitrixOptions as $tabData) {
            foreach ($tabData['OPTIONS'] as $optionKey => $optionData) {
                if ($optionKey === $key) {
                    return isset($optionData['MULTIPLE']) && $optionData['MULTIPLE'] === true;
                }
            }
        }

        return false;
    }

    public function getCurrentSettings(): array
    {
        $defaultSettings = self::getDefaultSettings();
        $currentSettings = [];

        foreach ($defaultSettings as $key => $defaultValue) {
            $currentSettings[$key] = $this->templateSettings->getSetting($key, $defaultValue);
        }

        return $currentSettings;
    }
}
