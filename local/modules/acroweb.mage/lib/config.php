<?php

namespace Acroweb\Mage;

use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Application;
use Acroweb\Mage\Helpers\IblockHelper;

class Config
{
    private const CACHE_TIME = 86400; // 1 day
    private const CACHE_ID = 'acroweb_mage_config';
    private const CACHE_DIR = '/acroweb.mage/config/';

    private static function getIblockAliases(): array
    {
        return include __DIR__ . '/../config/iblock_aliases.php';
    }

    private static function getFormAliases(): array
    {
        return include __DIR__ . '/../config/form_aliases.php';
    }

    public static function getParams(): array
    {
        $cache = Cache::createInstance();

        if ($cache->initCache(self::CACHE_TIME, self::CACHE_ID, self::CACHE_DIR)) {
            $params = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $params = self::fetchParams();

            if (defined('BX_COMP_MANAGED_CACHE')) {
                $taggedCache = Application::getInstance()->getTaggedCache();
                $taggedCache->startTagCache(self::CACHE_DIR);
                $taggedCache->registerTag('iblock_id_new');
                $taggedCache->registerTag('form_id_new');
                $taggedCache->endTagCache();
            }

            $cache->endDataCache($params);
        }

        return $params;
    }

    private static function fetchParams(): array
    {
        $params = [];

        if (!Loader::includeModule('iblock')) {
            return $params;
        }

        foreach (self::getIblockAliases() as $alias => $iblockCode) {
            $iblock = IblockHelper::resolveIblock($iblockCode . SITE_ID);
            if ($iblock instanceof \Bitrix\Iblock\Iblock) {
                $params['IBLOCKS'][$alias] = [
                    'ID' => $iblock->getId(),
                    'TYPE' => $iblock->getIblockTypeId(),
                ];
            }
        }

        if (Loader::includeModule('form')) {
            foreach (self::getFormAliases() as $alias => $formSid) {
                //$formId = self::getFormIdBySid($formSid);
//                if ($formId) {
//                    $params['FORMS'][$alias] = $formId;
//                }
                $params['FORMS'][$alias] = $formSid . '_' . SITE_ID;
            }
        }

        return $params;
    }

    private static function getFormIdBySid($sid)
    {
        $result = \CForm::GetBySID($sid)->Fetch();
        return $result ? $result['ID'] : null;
    }

    public static function clearCache(): void
    {
        $cache = Cache::createInstance();
        $cache->clean(self::CACHE_ID, self::CACHE_DIR);

        if (defined('BX_COMP_MANAGED_CACHE')) {
            $taggedCache = Application::getInstance()->getTaggedCache();
            $taggedCache->clearByTag('iblock_id_new');
            $taggedCache->clearByTag('form_id_new');
        }
    }

    public static function onAfterIBlockUpdate(&$arFields)
    {
        if (in_array($arFields['CODE'], array_values(self::getIblockAliases()))) {
            self::clearCache();
        }
    }

    public static function onAfterFormUpdate($formId)
    {
        $form = \CForm::GetByID($formId)->Fetch();
        if ($form && in_array($form['SID'], array_values(self::getFormAliases()))) {
            self::clearCache();
        }
    }
}