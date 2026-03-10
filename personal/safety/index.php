<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Безопасность");
?>

<? $APPLICATION->IncludeComponent(
	"bitrix:main.profile",
	"safe",
	array(
		"CHECK_RIGHTS" => "N",
		"SEND_INFO" => "N",
		"SET_TITLE" => "N",
		"USER_PROPERTY_NAME" => "",
		"COMPONENT_TEMPLATE" => "safe"
	),
	false
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>