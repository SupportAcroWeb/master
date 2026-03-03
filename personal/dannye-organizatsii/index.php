<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Данные организации");
?>

<?$APPLICATION->IncludeComponent(
    "acroweb:organization.list",
    "",
    [],
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>