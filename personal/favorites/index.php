<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");
?>

<?$APPLICATION->IncludeComponent(
    "acroweb:widgets",
    "favorites",
    array(
        "COMPONENT_TEMPLATE" => "favorites",
        "IBLOCK_TYPE" => "acroweb_catalog_s1",
        "IBLOCK_ID" => "3",
        "SORT_BY1" => "sort",
        "SORT_ORDER1" => "asc",
        "SORT_BY2" => "id",
        "SORT_ORDER2" => "desc",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y"
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>