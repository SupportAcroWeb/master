<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\StoreTable;

Loader::includeModule('catalog');

Loc::loadMessages(__FILE__);

$ID = intval($_REQUEST["ID"]);
$bCopy = ($action == "copy");

$APPLICATION->SetTitle($ID > 0 ? "Редактирование склада" : "Добавление нового склада");

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && check_bitrix_sessid())
{
    $arFields = array(
        "TITLE" => $_POST["TITLE"],
        "ACTIVE" => $_POST["ACTIVE"] === "Y" ? "Y" : "N",
        "ADDRESS" => $_POST["ADDRESS"],
        "DESCRIPTION" => $_POST["DESCRIPTION"],
        "PHONE" => $_POST["PHONE"],
        "SCHEDULE" => $_POST["SCHEDULE"],
        "EMAIL" => $_POST["EMAIL"],
        "SORT" => $_POST["SORT"],
        "IMAGE_ID" => $_POST["IMAGE_ID"],
        "GPS_N" => $_POST["GPS_N"],
        "GPS_S" => $_POST["GPS_S"],
        "ISSUING_CENTER" => $_POST["ISSUING_CENTER"] === "Y" ? "Y" : "N",
        "SHIPPING_CENTER" => $_POST["SHIPPING_CENTER"] === "Y" ? "Y" : "N",
        "SITE_ID" => $_POST["SITE_ID"],
        "CODE" => $_POST["CODE"],
        "XML_ID" => $_POST["XML_ID"],
    );

    if ($ID > 0)
    {
        $result = StoreTable::update($ID, $arFields);
    }
    else
    {
        $result = StoreTable::add($arFields);
        if ($result->isSuccess())
        {
            $ID = $result->getId();
        }
    }

    if ($result->isSuccess())
    {
        LocalRedirect("/bitrix/admin/acroweb_helper_edit_stores.php?lang=".LANGUAGE_ID);
    }
    else
    {
        $errors = $result->getErrorMessages();
    }
}

if ($_REQUEST["action"] == "delete" && $ID > 0 && check_bitrix_sessid())
{
    $result = StoreTable::delete($ID);
    if ($result->isSuccess())
    {
        LocalRedirect("/bitrix/admin/acroweb_helper_edit_stores.php?lang=".LANGUAGE_ID);
    }
    else
    {
        $errors = $result->getErrorMessages();
    }
}

if ($ID > 0 && empty($errors))
{
    $arStore = StoreTable::getById($ID)->fetch();
}
else
{
    $arStore = array(
        "TITLE" => "",
        "ACTIVE" => "Y",
        "ADDRESS" => "",
        "DESCRIPTION" => "",
        "PHONE" => "",
        "SCHEDULE" => "",
        "EMAIL" => "",
        "SORT" => 100,
        "IMAGE_ID" => "",
        "GPS_N" => "",
        "GPS_S" => "",
        "ISSUING_CENTER" => "Y",
        "SHIPPING_CENTER" => "Y",
        "SITE_ID" => "",
        "CODE" => "",
        "XML_ID" => "",
    );
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
    array(
        "TEXT" => "Список складов",
        "LINK" => "acroweb_helper_edit_stores.php?lang=".LANGUAGE_ID,
        "ICON" => "btn_list",
    )
);

if ($ID > 0)
{
    $aMenu[] = array(
        "TEXT" => "Удалить склад",
        "LINK" => "javascript:if(confirm('Вы уверены, что хотите удалить этот склад?')) window.location='acroweb_helper_edit_store.php?ID=".$ID."&action=delete&".bitrix_sessid_get()."&lang=".LANGUAGE_ID."';",
        "ICON" => "btn_delete",
    );
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if (!empty($errors))
{
    CAdminMessage::ShowMessage(join("<br>", $errors));
}

$aTabs = array(
    array("DIV" => "edit1", "TAB" => "Склад", "ICON" => "catalog", "TITLE" => "Параметры склада"),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&ID=<?=$ID?>" name="store_edit" enctype="multipart/form-data">
<?=bitrix_sessid_post()?>
<?$tabControl->BeginNextTab();?>
    <tr>
        <td width="40%">ID:</td>
        <td width="60%"><?=$ID > 0 ? $ID : 'Будет создан автоматически'?></td>
    </tr>
    <tr>
        <td>Склад по умолчанию:</td>
        <td>
            <input type="checkbox" name="IS_DEFAULT" value="Y" <?=$arStore["IS_DEFAULT"] == "Y" ? "checked" : ""?>>
        </td>
    </tr>
    <tr>
        <td>Активен:</td>
        <td>
            <input type="checkbox" name="ACTIVE" value="Y" <?=$arStore["ACTIVE"] == "Y" ? "checked" : ""?>>
        </td>
    </tr>
    <tr>
        <td>Пункт выдачи:</td>
        <td>
            <input type="checkbox" name="ISSUING_CENTER" value="Y" <?=$arStore["ISSUING_CENTER"] == "Y" ? "checked" : ""?>>
        </td>
    </tr>
    <tr>
        <td>Название:</td>
        <td>
            <input type="text" name="TITLE" value="<?=htmlspecialcharsbx($arStore["TITLE"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>Символьный код:</td>
        <td>
            <input type="text" name="CODE" value="<?=htmlspecialcharsbx($arStore["CODE"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>Адрес:</td>
        <td>
            <textarea name="ADDRESS" cols="30" rows="3"><?=htmlspecialcharsbx($arStore["ADDRESS"])?></textarea>
        </td>
    </tr>
    <tr>
        <td>Описание:</td>
        <td>
            <textarea name="DESCRIPTION" cols="30" rows="3"><?=htmlspecialcharsbx($arStore["DESCRIPTION"])?></textarea>
        </td>
    </tr>
    <tr>
        <td>Телефон:</td>
        <td>
            <input type="text" name="PHONE" value="<?=htmlspecialcharsbx($arStore["PHONE"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>График работы:</td>
        <td>
            <input type="text" name="SCHEDULE" value="<?=htmlspecialcharsbx($arStore["SCHEDULE"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>Email:</td>
        <td>
            <input type="text" name="EMAIL" value="<?=htmlspecialcharsbx($arStore["EMAIL"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>GPS широта:</td>
        <td>
            <input type="text" name="GPS_N" value="<?=htmlspecialcharsbx($arStore["GPS_N"])?>" size="15">
        </td>
    </tr>
    <tr>
        <td>GPS долгота:</td>
        <td>
            <input type="text" name="GPS_S" value="<?=htmlspecialcharsbx($arStore["GPS_S"])?>" size="15">
        </td>
    </tr>
    <tr>
        <td>Внешний код:</td>
        <td>
            <input type="text" name="XML_ID" value="<?=htmlspecialcharsbx($arStore["XML_ID"])?>" size="30">
        </td>
    </tr>
    <tr>
        <td>Сортировка:</td>
        <td>
            <input type="text" name="SORT" value="<?=intval($arStore["SORT"])?>" size="5">
        </td>
    </tr>
    <tr>
        <td>Изображение:</td>
        <td>
            <?
            echo CFileInput::Show("IMAGE_ID", $arStore["IMAGE_ID"], 
                array(
                    "IMAGE" => "Y",
                    "PATH" => "Y",
                    "FILE_SIZE" => "Y",
                    "DIMENSIONS" => "Y",
                    "IMAGE_POPUP" => "Y",
                    "MAX_SIZE" => array(
                        "W" => 200,
                        "H" => 200,
                    ),
                ),
                array(
                    'upload' => true,
                    'medialib' => true,
                    'file_dialog' => true,
                    'cloud' => true,
                    'del' => true,
                    'description' => true,
                )
            );
            ?>
        </td>
    </tr>

<?$tabControl->EndTab();?>

<?$tabControl->Buttons(array(
    "back_url" => "acroweb_helper_edit_stores.php?lang=".LANGUAGE_ID,
));?>

<?$tabControl->End();?>
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>