<?

namespace Acroweb\Mage\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use CIBlockSection;
use Exception;

class IblockSectionHelper
{
    public static function getInheritProperty($iblockId, $sectionId, $prop)
    {
        static $cache = [];

        try {
            $iblockId = (int)$iblockId;
            $sectionId = (int)$sectionId;
            $prop = trim($prop);

            if
            (
                !Loader::includeModule('iblock')
                || $iblockId <= 0 || $sectionId <= 0
                || strlen($prop) <= 0
            ) {
                return false;
            }

            $key = $iblockId . '|' . $sectionId . '|' . $prop;

            if (isset($cache['RESULT'][$key])) {
                return $cache['RESULT'][$key];
            }

            if (!isset($cache['USER_PROPERTIES_FIELD'][$iblockId])) {
                $arUserFields = Application::getUserTypeManager()
                    ->GetUserFields('IBLOCK_' . $iblockId . '_SECTION');

                $cache['USER_PROPERTIES_FIELD'][$iblockId] = is_array($arUserFields) ? $arUserFields : [];
            } else {
                $arUserFields = $cache['USER_PROPERTIES_FIELD'][$iblockId];
            }

            $arLimbSections = [];

            if (!isset($cache[$iblockId]['LIMB'][$sectionId])) {
                $dbCurSec = CIBlockSection::GetList(
                    [],
                    ['IBLOCK_ID' => $iblockId, 'ID' => $sectionId],
                    false,
                    ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'LEFT_MARGIN', 'RIGHT_MARGIN']
                );

                if ($arCurSec = $dbCurSec->Fetch()) {
                    $cache[$iblockId]['LIMB'][$sectionId] = [];

                    $dbLimb = CIBlockSection::GetList(
                        ['left_margin' => 'asc'],
                        [
                            'IBLOCK_ID' => $iblockId,
                            '<=LEFT_BORDER' => $arCurSec['LEFT_MARGIN'],
                            '>=RIGHT_BORDER' => $arCurSec['RIGHT_MARGIN'],
                        ],
                        false,
                        ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'UF_*']
                    );

                    while ($arSec = $dbLimb->Fetch()) {
                        $arLimbSections[$arSec['ID']] = $arSec;
                        $cache[$iblockId]['LIMB'][$arSec['ID']] = &$arLimbSections;
                    }
                }
            } else {
                $arLimbSections = $cache[$iblockId]['LIMB'][$sectionId];
            }

            $value = false;

            if (!empty($arLimbSections)) {
                $arTmp = array_reverse($cache[$iblockId]['LIMB'][$sectionId], true);

                foreach ($arTmp as $section) {
                    if (!isset($section[$prop])) {
                        continue;
                    }

                    $arProp = $arUserFields[$prop] ?? false;
                    $v = $section[$prop];

                    if
                    (
                        ($arProp !== false
                            && (
                                ($arProp['USER_TYPE']['BASE_TYPE'] == 'double' && $v > 0)
                                || ($arProp['USER_TYPE']['BASE_TYPE'] != 'double' && $v != '')
                            )
                        )
                        || ($arProp === false && $v != '')
                    ) {
                        $value = $v;
                        break;
                    }
                }
            }

            $cache['RESULT'][$key] = $value;

            return $value;
        } catch (Exception) {
        }

        return false;
    }
}