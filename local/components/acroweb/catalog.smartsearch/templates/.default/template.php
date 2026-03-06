<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CatalogSmartSearchComponent $component */
?>

<form class="searchbox" id="<?= $arResult['COMPONENT_ID'] ?>" data-component-id="<?= $arResult['COMPONENT_ID'] ?>" action="/produktsiya/" method="get">
    <input
        class="searchbox__input"
        data-action-input="showSearchDropdown"
        type="text"
        name="q"
        placeholder="Что вы хотите найти?"
        autocomplete="off"
    >
    <svg class="searchbox__icon" width="26" height="26" aria-hidden="true">
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#search1"></use>
    </svg>
    <button class="searchbox__clear" type="button" style="display: none;">
        <svg aria-hidden="true" width="14" height="14">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
        </svg>
        <span class="v-h">Очистить</span>
    </button>
    <button class="searchbox__btn" type="submit">
        <span class="v-h">Искать</span>
    </button>
    <div class="header-search-dd" style="display: none;"></div>
</form>

<script>
    BX.ready(function() {
        new BX.Acroweb.CatalogSmartSearch({
            componentId: '<?= $arResult['COMPONENT_ID'] ?>',
            componentName: 'acroweb:catalog.smartsearch',
            signedParameters: '<?= $this->getComponent()->getSignedParameters() ?>',
            minQueryLength: 3,
            debounceDelay: 300,
            itemsLimit: <?= (int)$arParams['ITEMS_LIMIT'] ?>
        });
    });
</script>

