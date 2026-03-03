<?php

use Bitrix\Main\UserConsent\Agreement;

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

$APPLICATION->SetTitle('Политика конфиденциальности');

$agreementId = 2;
$agreement = new Agreement($agreementId);

if ($agreement->isExist()) {
    echo $agreement->getHtml();
} else {
    echo '<p>Текст политики конфиденциальности временно недоступен.</p>';
}

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';