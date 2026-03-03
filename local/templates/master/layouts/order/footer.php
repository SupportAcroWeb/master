<?php
global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
if (!$request->get('ORDER_ID')) {
?>
    </div>
</div>
<?php
}