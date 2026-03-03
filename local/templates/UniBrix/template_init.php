<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;
use Bitrix\Main\Localization\Loc;
use Acroweb\Mage\Helpers\TemplateHelper;
use Acroweb\Mage\Settings\TemplateSettings;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

Loc::loadLanguageFile(__FILE__);

if (!Loader::includeModule('acroweb.mage')) {
    ShowError(Loc::getMessage('ACROWEB_NO_INSTALL_MODULE'));
    return;
}

define('SITE_TEMPLATE_PATH', __DIR__);
define('SITE_DIR', SITE_DIR ?? '/');

$bAuthForm = TemplateHelper::getParam('authForm', false) === true;
TemplateHelper::clearParams();

$asset = Asset::getInstance();

// CSS files
$cssFiles = [
    '/css/styles.css'
];

foreach ($cssFiles as $cssFile) {
    $asset->addCss(SITE_TEMPLATE_PATH . $cssFile);
}

// JS files
$jsFiles = [
    '/js/scripts.js'
];

//$asset->addString('<script src="https://api-maps.yandex.ru/2.1/?apikey=501154ad-c814-48f4-b844-e67e642e55ea&lang=ru_RU"></script>', true, AssetLocation::AFTER_CSS);

foreach ($jsFiles as $jsFile) {
    $asset->addJs(SITE_TEMPLATE_PATH . $jsFile);
}

// Determine layout
$layout = 'default';
$currentPage = Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory();

if (defined('ERROR_404') && ERROR_404 === 'Y')
{
    $layout = '404';
    TemplateHelper::setParam('bodyClass', 'page404');
}
elseif ($currentPage === SITE_DIR)
{
    $layout = 'index';
    TemplateHelper::setParam('bodyClass', 'mp');
}

TemplateHelper::setParam('layout', $layout);

$arJsMessages = [
    // Add your JS messages here
];

TemplateHelper::includeJSMessages($arJsMessages);