<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Мои заказы");
?>
<? $APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order", 
	".default", 
	array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ALLOW_INNER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"DETAIL_HIDE_USER_INFO" => array(
			0 => "0",
		),
		"DISALLOW_CANCEL" => "N",
		"HISTORIC_STATUSES" => array(
			0 => "F",
		),
		"NAV_TEMPLATE" => "arrows_catalog",
		"ONLY_INNER_FULL" => "N",
		"ORDERS_PER_PAGE" => "2",
		"ORDER_DEFAULT_SORT" => "DATE_INSERT",
		"PATH_TO_BASKET" => "/personal/basket/",
		"PATH_TO_CATALOG" => "/produktsiya/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PROP_1" => "",
		"REFRESH_PRICES" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array(
			0 => "0",
		),
		"SAVE_IN_SESSION" => "Y",
		"SEF_FOLDER" => "/personal/moi-zakazy/",
		"SEF_MODE" => "Y",
		"SET_TITLE" => "Y",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"COMPONENT_TEMPLATE" => ".default",
		"PROP_2" => array(
		),
		"STATUS_COLOR_P" => "yellow",
		"SEF_URL_TEMPLATES" => array(
			"list" => "",
			"detail" => "detail/#ID#/",
			"cancel" => "cancel/#ID#/",
		)
	),
	false
);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>