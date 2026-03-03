<?

/**
 * @var array $arParams
 * @var array $templateData
 * @var string $templateFolder
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Context;

global $APPLICATION;
//	lazy load and big data json answers
$request = Context::getCurrent()->getRequest();

if ($request->isAjaxRequest() && ($request->get('action') === 'showMore' || $request->get('action') === 'deferredLoad'))
{
    $content = ob_get_contents();
    ob_end_clean();

    [, $itemsContainer] = explode('<!-- items-container -->', $content);
    $paginationContainer = '';
    if ($templateData['USE_PAGINATION_CONTAINER'])
    {
        [, $paginationContainer] = explode('<!-- pagination-container -->', $content);
    }
    [, $epilogue] = explode('<!-- component-end -->', $content);

    if (isset($arParams['AJAX_MODE']) && $arParams['AJAX_MODE'] === 'Y')
    {
        // $component->prepareLinks($paginationContainer);
    }

    $result = [
        'items' => $itemsContainer,
        'pagination' => $paginationContainer,
        'epilogue' => $epilogue,
        'deferredLoad' => $request->get('action') === 'deferredLoad',
        'navParams' => $templateData['NAV_PARAMS']
    ];

    $result['JS'] = Asset::getInstance()->getJs();
    $APPLICATION->RestartBuffer();
    echo Json::encode($result);
    CMain::FinalActions();
}