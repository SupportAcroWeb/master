<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle("Политика конфиденциальности"); ?>

<? $APPLICATION->IncludeComponent(
    "acroweb:widgets",
    "policy",
    array(
        "AGREEMENT_ID" => '2'
    )
); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
