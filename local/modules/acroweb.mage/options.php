<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Acroweb\Mage\Settings\SettingsConfig;
use Acroweb\Mage\Settings\TemplateSettings;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


global $APPLICATION;

CJSCore::Init(['acroweb_mage.module_options', 'jquery', 'color_picker']);

$module_id = 'acroweb.mage';

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

Loader::includeModule($module_id);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$settingsConfig = SettingsConfig::create();
$templateName = $settingsConfig->getCurrentSettings()['template_name'];
$pathTemplate = '';
if ($templateName) {
    $localTemplatePath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/' . $templateName;
    $bitrixTemplatePath = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/templates/' . $templateName;

    if (is_dir($localTemplatePath)) {
        $pathTemplate = '/local/templates/' . $templateName;
    } elseif (is_dir($bitrixTemplatePath)) {
        $pathTemplate = '/bitrix/templates/' . $templateName;
    }
}

if ($request->isPost() && check_bitrix_sessid()) {
    $bitrixOptions = $settingsConfig->getBitrixOptions();
    foreach ($bitrixOptions as $tabData) {
        foreach ($tabData['OPTIONS'] as $optionKey => $optionData) {
            if ($optionData['TYPE'] === 'file') {
                $file = $request->getFile($optionKey);
                $deleteFile = $request->getPost($optionKey . '_delete') === 'Y';

                if ($deleteFile) {
                    $currentFilePath = Option::get($module_id, $optionKey, '');
//                    if ($currentFilePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentFilePath)) {
//                        unlink($_SERVER['DOCUMENT_ROOT'] . $currentFilePath);
//                    }

                    if ($pathTemplate && $optionData['DEFAULT']) {
                        $optionData['DEFAULT'] = str_replace(SITE_TEMPLATE_PATH, $pathTemplate, $optionData['DEFAULT']);
                    }

                    Option::set($module_id, $optionKey, $optionData['DEFAULT']);
                } elseif ($file && $file['error'] == UPLOAD_ERR_OK) {
                    $uploadDir = '/upload/acroweb.mage/';
                    $absUploadDir = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
                    if (!is_dir($absUploadDir)) {
                        mkdir($absUploadDir, 0755, true);
                    }
                    $fileName = $file['name'];
                    $filePath = $absUploadDir . $fileName;
                    if (move_uploaded_file($file['tmp_name'], $filePath)) {
                        $relativePath = $uploadDir . $fileName;
                        Option::set($module_id, $optionKey, $relativePath);
                    }
                }
            } else {
                $optionValue = $request->getPost($optionKey);
                if ($optionData['TYPE'] === 'checkbox' && $optionValue === null) {
                    $optionValue = 'N';
                }
                Option::set($module_id, $optionKey, $optionValue);
            }
        }
    }

    // Очистка кеша TemplateSettings
    TemplateSettings::getInstance()->refreshSettings();

    CAdminMessage::ShowNote(Loc::getMessage("ACROWEB_CORE_OPTIONS_SAVED"));
}

$tabControl = new CAdminTabControl("tabControl", [
        [
                "DIV" => "edit1",
                "TAB" => Loc::getMessage("ACROWEB_CORE_OPTIONS_TAB_GENERAL"),
                "TITLE" => Loc::getMessage("ACROWEB_CORE_OPTIONS_TAB_GENERAL_TITLE"),
        ],
]);

$tabControl->Begin();
?>

    <form method="post" action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($module_id) ?>&lang=<?= LANGUAGE_ID ?>" enctype="multipart/form-data">
        <?= bitrix_sessid_post() ?>
        <?php $tabControl->BeginNextTab(); ?>

        <tr>
            <td colspan="2">
                <div class="adm-detail-content-item-block">
                    <div class="acroweb-options-tabs">
                        <div class="acroweb-options-tabs-menu">
                            <?php
                            $bitrixOptions = $settingsConfig->getBitrixOptions();
                            $firstTab = true;
                            foreach ($bitrixOptions as $tabKey => $tabData):
                                ?>
                                <div class="acroweb-options-tab-menu-item<?= $firstTab ? ' active' : '' ?>" data-tab="<?= $tabKey ?>">
                                    <?= $tabData['TITLE'] ?>
                                </div>
                                <?php
                                $firstTab = false;
                            endforeach;
                            ?>
                        </div>
                        <div class="acroweb-options-tabs-content">
                            <?php
                            $firstTab = true;
                            foreach ($bitrixOptions as $tabKey => $tabData):
                                ?>
                                <div class="acroweb-options-tab-content<?= $firstTab ? ' active' : '' ?>" data-tab="<?= $tabKey ?>">
                                    <table class="adm-detail-content-table edit-table">
                                        <tbody>
                                        <?php foreach ($tabData['OPTIONS'] as $optionKey => $optionData):
                                            if ($optionData['TYPE'] === 'hidden')
                                                continue;
                                            ?>
                                            <tr>
                                                <td class="adm-detail-content-cell-l">
                                                    <?php if (isset($optionData['HINT'])): ?>
                                                        <span class="adm-info-page-icon"
                                                              onclick="BX.hint(this, '<?= htmlspecialcharsbx($optionData['HINT']) ?>')">
                                                    </span>
                                                    <?php endif; ?>
                                                    <label for="<?= htmlspecialcharsbx($optionKey) ?>"><?= $optionData['TITLE'] ?>:</label>
                                                </td>
                                                <td class="adm-detail-content-cell-r">
                                                    <?php
                                                    $optionValue = Option::get($module_id, $optionKey, $optionData['DEFAULT']);
                                                    switch ($optionData['TYPE']):
                                                        case 'checkbox':
                                                            ?>
                                                            <input type="checkbox" class="adm-designed-checkbox"
                                                                   id="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                   name="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                   value="Y" <?= ($optionValue === 'Y') ? 'checked' : '' ?>>
                                                            <label class="adm-designed-checkbox-label"
                                                                   for="<?= htmlspecialcharsbx($optionKey) ?>"></label>
                                                            <?php
                                                            break;
                                                        case 'selectbox':
                                                            ?>
                                                            <select class="adm-select"
                                                                    id="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                    name="<?= htmlspecialcharsbx($optionKey) ?>">
                                                                <?php foreach ($optionData['VALUES'] as $value => $label): ?>
                                                                    <option value="<?= htmlspecialcharsbx($value) ?>"
                                                                            <?= ($optionValue === $value) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialcharsbx($label) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <?php
                                                            break;
                                                        case 'color_theme':
                                                            ?>
                                                            <div class="adm-color-theme-selector">
                                                                <?php foreach ($optionData['VALUES'] as $value => $color): ?>
                                                                    <label class="adm-color-theme-item">
                                                                        <input type="radio"
                                                                               name="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                               value="<?= htmlspecialcharsbx($value) ?>"
                                                                                <?= ($optionValue === $value) ? 'checked' : '' ?>>
                                                                        <?php if ($value === 'custom'): ?>
                                                                            <span class="adm-color-theme-custom" title="<?= Loc::getMessage("ACROWEB_CORE_CUSTOM_COLOR") ?>">
                                                                        <i class="adm-icon-colorpicker"></i>
                                                                    </span>
                                                                        <?php else: ?>
                                                                            <span class="adm-color-theme-color"
                                                                                  style="background-color: <?= htmlspecialcharsbx($color) ?>;"></span>
                                                                        <?php endif; ?>
                                                                    </label>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <input type="hidden" id="<?= htmlspecialcharsbx($optionKey) ?>_custom"
                                                                   name="<?= htmlspecialcharsbx($optionKey) ?>_custom"
                                                                   value="<?= htmlspecialcharsbx(Option::get($module_id, $optionKey . '_custom', '')) ?>">
                                                            <?php
                                                            break;
                                                        case 'hidden':
                                                            ?>
                                                            <?php
                                                            break;
                                                        case 'file':
                                                            $filePath = Option::get($module_id, $optionKey, '');
                                                            $defaultFilePath = $optionData['DEFAULT'] ?? '';

                                                            $displayPath = $filePath ?: $defaultFilePath;

                                                            // Проверяем, является ли значение числовым (ID файла)
                                                            if (is_numeric($displayPath)) {
                                                                $fileArray = CFile::GetFileArray($displayPath);
                                                                if ($fileArray) {
                                                                    $displayPath = $fileArray['SRC'];
                                                                }
                                                            }

                                                            $isImage = $displayPath && in_array(strtolower(pathinfo($displayPath, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'ico', 'webp', 'svg', 'png', 'gif', 'bmp']);
                                                            ?>
                                                            <div class="adm-input-file-control">
                                                                <input type="file" class="adm-designed-file" id="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                       name="<?= htmlspecialcharsbx($optionKey) ?>" accept="image/*,application/pdf"
                                                                       onchange="handleFileChange(this)">
                                                                <label for="<?= htmlspecialcharsbx($optionKey) ?>" class="adm-designed-file-label" id="label_<?= htmlspecialcharsbx($optionKey) ?>">
                                                                    <?= Loc::getMessage("ACROWEB_CORE_CHOOSE_FILE") ?: 'Выберите файл' ?>
                                                                </label>
                                                            </div>
                                                            <div id="preview_<?= htmlspecialcharsbx($optionKey) ?>" class="adm-current-file-preview">
                                                                <?php if ($isImage): ?>
                                                                    <img src="<?= htmlspecialcharsbx($displayPath) ?>" alt="Current file">
                                                                <?php elseif ($displayPath): ?>
                                                                    <img src="/bitrix/images/fileman/types/file.gif" alt="File icon">
                                                                    <span><?= htmlspecialcharsbx(basename($displayPath)) ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if ($filePath): ?>
                                                            <label>
                                                                <input type="checkbox" name="<?= htmlspecialcharsbx($optionKey) ?>_delete" value="Y">
                                                                <?= Loc::getMessage("ACROWEB_CORE_DELETE_FILE") ?>
                                                            </label>
                                                        <?php endif; ?>
                                                            <?php
                                                            break;
                                                        case 'textarea':
                                                            ?>
                                                            <textarea class="adm-input" rows="5"
                                                                      id="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                      name="<?= htmlspecialcharsbx($optionKey) ?>"><?= htmlspecialcharsbx($optionValue) ?></textarea>
                                                            <?php
                                                            break;
                                                        case 'text':
                                                        default:
                                                            ?>
                                                            <input type="text" class="adm-input"
                                                                   id="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                   name="<?= htmlspecialcharsbx($optionKey) ?>"
                                                                   value="<?= htmlspecialcharsbx($optionValue) ?>">
                                                            <?php
                                                            break;
                                                    endswitch;
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                                $firstTab = false;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <?php $tabControl->Buttons(); ?>
        <input type="submit" name="save" value="<?= Loc::getMessage("MAIN_SAVE") ?>" class="adm-btn-save">
    </form>

<?php $tabControl->End(); ?>

    <script>
        function handleFileChange(input) {
            var file = input.files[0];
            var label = document.getElementById('label_' + input.id);
            var preview = document.getElementById('preview_' + input.id);

            if (file) {
                // Меняем текст и цвет кнопки
                if (label) {
                    label.textContent = 'Выбран: ' + file.name;
                    label.style.backgroundColor = '#28a745';
                }

                // Показываем превью
                if (preview) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var isImage = file.type.startsWith('image/');
                        if (isImage) {
                            preview.innerHTML = '<img src="' + e.target.result + '" alt="Превью" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px; border-radius: 3px;">';
                        } else {
                            preview.innerHTML = '<div><img src="/bitrix/images/fileman/types/file.gif" alt="Файл" style="vertical-align: middle; margin-right: 5px;"><span>' + file.name + '</span></div>';
                        }
                    }
                    reader.readAsDataURL(file);
                }
            } else {
                // Возвращаем исходное состояние
                if (label) {
                    label.textContent = 'Выберите файл';
                    label.style.backgroundColor = '#3bc8f5';
                }
                if (preview) {
                    preview.innerHTML = '';
                }
            }
        }
    </script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
