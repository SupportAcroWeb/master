<?php
/**
 * Ajax-шаблон для рендера товаров
 * 
 * @var array $arResult
 */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult['ITEMS'])): ?>
    <div class="header-search-dd__empty">Ничего не найдено</div>
<?php else: ?>
    <?php
    // Маппинг кодов лейблов на CSS классы (как в детальной странице товара)
    $arLabelsClass = [
        'NEWPRODUCT' => 'badge1 badge1_black',
        'SALELEADER' => 'badge1 badge1_orange',
        'SPECIALOFFER' => 'badge1 badge1_red',
    ];
    
    foreach ($arResult['ITEMS'] as $item): ?>
        <div class="card-product2 smartsearch">
            <div class="card-product2__col-photo">
                <?php if (!empty($item['LABELS'])): ?>
                    <div class="card-product2__badges">
                        <?php foreach ($item['LABELS'] as $code => $value): ?>
                            <span class="<?= $arLabelsClass[$code] ?? 'badge1 badge1_black' ?>"
                                  title="<?= htmlspecialcharsbx($value) ?>"><?= htmlspecialcharsbx($value) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <img src="<?= htmlspecialcharsbx($item['PICTURE']) ?>" loading="lazy" alt="">
            </div>
            <div class="card-product2__col-data">
                <div class="card-product2__name">
                    <a href="<?= htmlspecialcharsbx($item['URL']) ?>"><?= htmlspecialcharsbx($item['NAME']) ?></a>
                </div>
                <?php if (!empty($item['DESCRIPTION'])): ?>
                    <div class="card-product2__description"><?= htmlspecialcharsbx(mb_substr($item['DESCRIPTION'], 0, 100)) ?></div>
                <?php endif; ?>
                <div class="card-product2__status <?= $item['IN_STOCK'] ? 'status_instock' : 'status_outstock' ?>">
                    <?= $item['IN_STOCK'] ? 'В наличии' : 'Нет в наличии' ?>
                </div>
            </div>
            <?php if (!empty($item['PRICE'])): ?>
                <div class="card-product2__col-price">
                    <?php if (!$item['IN_STOCK'] && !empty($item['ARRIVAL_DATE'])): ?>
                        <div class="card-product2__arrival-date">Дата поступления: <?= htmlspecialcharsbx($item['ARRIVAL_DATE']) ?></div>
                    <?php endif; ?>
                    <div class="card-product2__label1">
                        С НДС (1 <?= htmlspecialcharsbx($item['MEASURE_NAME']) ?>)
                    </div>
                    <div class="card-product2__price1"><?= $item['PRICE'] ?></div>
                    <?php if ($item['HAS_DISCOUNT'] && !empty($item['BASE_PRICE'])): ?>
                        <div class="card-product2__price2"><?= $item['BASE_PRICE'] ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

