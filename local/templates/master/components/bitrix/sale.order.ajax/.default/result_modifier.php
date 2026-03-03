<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Acroweb\Mage\Organization\Service as OrganizationService;

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$arParams['SERVICES_IMAGES_SCALING'] = (string)($arParams['SERVICES_IMAGES_SCALING'] ?? 'adaptive');

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

// Проверяем наличие организаций у авторизованного пользователя
$currentUser = CurrentUser::get();
$userId = (int)$currentUser->getId();

if ($userId > 0 && Loader::includeModule('acroweb.mage')) {
	try {
		// Получаем список организаций пользователя через сервис
		$userOrganizations = OrganizationService::getListByUser($userId);
		
		if (empty($userOrganizations)) {
			// Показываем ошибку и прекращаем работу компонента
			ShowError('Добавьте организацию в личном кабинете, заказ без организации невозможен.');
			
			// Очищаем результат компонента
			$arResult = [];
			
			// Прекращаем выполнение
			return;
		}
	} catch (\Exception $e) {
		ShowError('Ошибка при проверке организаций. Обратитесь к администратору.');
		
		$arResult = [];
		return;
	}
}
