<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>
    <div class="container container_bordered1">
        <div class="block-user-cabinet__content">
            <? $APPLICATION->IncludeComponent(
                "bitrix:main.profile",
                ".default",
                array(
                    "CHECK_RIGHTS" => "N",
                    "SEND_INFO" => "N",
                    "SET_TITLE" => "N",
                    "USER_PROPERTY" => array(
                        0 => "UF_DOP_INFO",
                        1 => "UF_MANAGER_ID",
                    ),
                    "USER_PROPERTY_NAME" => "",
                    "COMPONENT_TEMPLATE" => ".default"
                ),
                false
            ); ?>
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
        </div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>