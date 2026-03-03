<?php
/**
 * Ajax-шаблон для кнопки "Показать ещё"
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

if ($arResult['HAS_MORE']): ?>
    <div class="header-search-dd__show-more">
        <button class="btn btn-default btn-buy btn-sm" data-role="show-more">
            Показать ещё
        </button>
    </div>
<?php endif; ?>

