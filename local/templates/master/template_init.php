<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;
use Acroweb\Mage\Helpers\TemplateHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;

Loc::loadLanguageFile(__FILE__);

if (!Loader::includeModule('acroweb.mage')) {
    ShowError(Loc::getMessage('ACROWEB_NO_INSTALL_MODULE'));
    return;
}

define('SITE_TEMPLATE_PATH', __DIR__);
define('SITE_DIR', SITE_DIR ?? '/');

global $USER;

$asset = Asset::getInstance();

// CSS files
$cssFiles = [
    '/css/swiper.min.css',
    '/css/fancybox.min.css',
    '/css/hystmodal.min.css',
    '/css/styles.css',
];

foreach ($cssFiles as $cssFile) {
    $asset->addCss(SITE_TEMPLATE_PATH . $cssFile);
}

// JS files
$jsFiles = [
    '/js/jquery-4.0.0.min.js',
    '/js/swiper-11.0.5.min.js',
    '/js/imask-7.6.1.min.js',
    '/js/jquery.validate-1.21.0.min.js',
    '/js/tom-select.complete-2.4.3.min.js',
    '/js/fancybox.umd.js',
    '/js/hystmodal.min.js',
    '/js/dragscroll.min.js',
    '/js/scripts.js',
];

//$asset->addString('<script src="https://api-maps.yandex.ru/2.1/?apikey=501154ad-c814-48f4-b844-e67e642e55ea&lang=ru_RU"></script>', true, AssetLocation::AFTER_CSS);

foreach ($jsFiles as $jsFile) {
    $asset->addJs(SITE_TEMPLATE_PATH . $jsFile);
}

// Determine layout
$layout = 'default';
$currentPage = Application::getInstance()->getContext()->getRequest()->getRequestedPageDirectory();
$request = Application::getInstance()->getContext()->getRequest();
$searchQuery = trim((string)$request->get('q'));

if (preg_match('#^/personal/#i', $currentPage) && !$USER->IsAuthorized() && $currentPage != '/personal/basket/'&& $currentPage != '/personal/order/make/')
{
    LocalRedirect('/auth/');
}

if (defined('ERROR_404') && ERROR_404 === 'Y')
{
    $layout = '404';
    TemplateHelper::setParam('mainClass', 'page404-wrapper');
}
elseif ($currentPage === SITE_DIR)
{
    $layout = 'index';
    TemplateHelper::setParam('bodyClass', 'mp');
}
elseif ($currentPage == '/favorites/')
{
    $layout = 'favorites';
}
elseif ($currentPage == '/o-kompanii/')
{
    $layout = 'about';
}
elseif ($currentPage == '/o-kompanii/vakansii/')
{
    $layout = 'vacancies';
}
elseif (preg_match('#^/informatsiya/#i', $currentPage))
{
    TemplateHelper::setParam('mainClass', 'features-wrapper');
}
elseif ($currentPage == '/privacy-policy/')
{
    $layout = 'policy';
}
elseif ($currentPage == '/personal/basket/')
{
    $layout = 'basket';
}
elseif ($currentPage == '/personal/' || $currentPage == '/personal/moi-zakazy/')
{
    $layout = 'personal';
    TemplateHelper::setParam('personalClass', 'container_personal');
    if ($currentPage == '/personal/moi-zakazy/') {
        TemplateHelper::setParam('personalClassTwo', 'orders-wrapper');
    }
}
elseif ($currentPage == '/personal/dannye-organizatsii/')
{
    $layout = 'personal';
    TemplateHelper::setParam('organizationClass', 'block-organization-cabinet');
}
elseif ($currentPage == '/kontakty/')
{
    $layout = 'contacts';
    TemplateHelper::setParam('mainClass', 'contacts-wrapper');
}
elseif ($currentPage == '/personal/order/make/')
{
    $layout = 'order';
}
elseif ($currentPage == '/auth/')
{
    $layout = 'auth';
}
elseif ($currentPage == '/produktsiya/' && $request->get('q') == null && empty($searchQuery))
{
    $layout = 'produktsiya';
}
elseif (preg_match('#^/personal/moi-zakazy/detail/#i', $currentPage))
{
    $layout = 'order_detail';
}
elseif (preg_match('#^/produktsiya/#i', $currentPage))
{
    $layout = 'catalog';
}
elseif (preg_match('#^/portfolio/#i', $currentPage) && $currentPage != '/portfolio/')
{
    $layout = 'portfolio';
}
elseif (preg_match('#^/personal/#i', $currentPage))
{
    $layout = 'personal';
    TemplateHelper::setParam('personalClass', 'container_bordered2');
}
elseif (preg_match('#^/news/#i', $currentPage))
{
    $layout = 'news';
}
elseif (preg_match('#^/informatsiya/blog/#i', $currentPage))
{
    $layout = 'news';
}

TemplateHelper::setParam('layout', $layout);

$arJsMessages = [
    // Add your JS messages here
];

TemplateHelper::includeJSMessages($arJsMessages);