<?php
/**
 * Обработчик AJAX запросов для корзины
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;

// Обработка очистки корзины через AJAX
$request = Application::getInstance()->getContext()->getRequest();

if (
	$request->isAjaxRequest()
	&& $request->isPost()
	&& $request->getPost('action') === 'clearBasket' 
	&& check_bitrix_sessid()
	&& Loader::includeModule('sale')
) {
	$fUserId = Fuser::getId();
	$basket = Basket::loadItemsForFUser($fUserId, SITE_ID);
	
	/** @var \Bitrix\Sale\BasketItem $item */
	foreach ($basket as $item) {
		$item->delete();
	}
	
	$result = $basket->save();
	
	global $APPLICATION;
	$APPLICATION->RestartBuffer();
	
	header('Content-Type: application/json');
	if ($result->isSuccess()) {
		echo json_encode([
			'status' => 'success',
			'data' => ['BASKET_COUNT' => 0]
		]);
	} else {
		echo json_encode([
			'status' => 'error',
			'errors' => $result->getErrorMessages()
		]);
	}
	
	CMain::FinalActions();
}

