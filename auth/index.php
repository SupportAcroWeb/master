<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->IncludeComponent(
        'acroweb:widgets',
        'auth',
        [],
        false
);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';