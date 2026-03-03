<?php

/** @global CMain $APPLICATION */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;
use Acroweb\Mage\Settings\TemplateSettings;

TemplateHelper::includeLayout('footer');

$settings = TemplateSettings::getInstance();
$shopOfName = $settings->getSettingValue('shopOfName');
$shopUrAdr = $settings->getSettingValue('shopUrAdr');
$shopINN = $settings->getSettingValue('shopINN');
$shopKPP = $settings->getSettingValue('shopKPP');
$shopAdr = $settings->getSettingValue('shopAdr');
$shopAdrDisplay = is_array($shopAdr) ? ($shopAdr[0] ?? '') : (string)$shopAdr;
?>
</main>
<footer class="footer">
    <div class="footer__top">
        <div class="container">
            <div class="footer__grid1">
                <div class="footer__col">
                    <?php TemplateHelper::includePartial('logo_footer'); ?>
                    <?php if (!empty($shopOfName) || !empty($shopUrAdr) || !empty($shopINN) || !empty($shopKPP)): ?>
                    <div class="footer__text1">
                        <p class="footer__title">Реквизиты</p>
                        <p>
                            <?php if (!empty($shopOfName)): ?><?= htmlspecialcharsbx($shopOfName) ?><br><?php endif; ?>
                            <?php if (!empty($shopUrAdr)): ?><?= nl2br(htmlspecialcharsbx($shopUrAdr)) ?><br><?php endif; ?>
                            <?php if (!empty($shopINN) || !empty($shopKPP)): ?>
                                ИНН/КПП: <?= htmlspecialcharsbx($shopINN ?? '') ?>/<?= htmlspecialcharsbx($shopKPP ?? '') ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    <div class="footer__text2">
                        <?php
                        $APPLICATION->IncludeComponent(
                            'bitrix:main.include',
                            '',
                            [
                                'AREA_FILE_SHOW' => 'file',
                                'PATH' => '/include/footer/policy.php',
                            ],
                            false,
                            ['HIDE_ICONS' => 'N']
                        );
                        ?>
                    </div>
                </div>
                <div class="footer__col">
                    <p class="footer__title"><a href="/produktsiya/">Продукция</a></p>
                    <?php
                    $APPLICATION->IncludeComponent(
                        'bitrix:menu',
                        'footer_menu',
                        [
                            'ALLOW_MULTI_SELECT' => 'N',
                            'CHILD_MENU_TYPE' => 'catalog',
                            'DELAY' => 'N',
                            'MAX_LEVEL' => '1',
                            'MENU_CACHE_GET_VARS' => [],
                            'MENU_CACHE_TIME' => '3600',
                            'MENU_CACHE_TYPE' => 'N',
                            'MENU_CACHE_USE_GROUPS' => 'Y',
                            'ROOT_MENU_TYPE' => 'bottom',
                            'USE_EXT' => 'N',
                            'COMPONENT_TEMPLATE' => 'footer_menu',
                        ],
                        false
                    );
                    ?>
                </div>
                <div class="footer__col">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'bitrix:menu',
                        'footer_menu_base',
                        [
                            'ALLOW_MULTI_SELECT' => 'N',
                            'CHILD_MENU_TYPE' => 'podmenu',
                            'DELAY' => 'N',
                            'MAX_LEVEL' => '1',
                            'MENU_CACHE_GET_VARS' => [],
                            'MENU_CACHE_TIME' => '3600',
                            'MENU_CACHE_TYPE' => 'N',
                            'MENU_CACHE_USE_GROUPS' => 'Y',
                            'ROOT_MENU_TYPE' => 'top',
                            'USE_EXT' => 'N',
                            'COMPONENT_TEMPLATE' => 'footer_menu',
                        ],
                        false
                    );
                    ?>
                </div>
                <div class="footer__col">
                    <?php TemplateHelper::includePartial('footer_contacts'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="footer__grid2">
                <div class="footer__copy">
                    <?php
                    $APPLICATION->IncludeComponent(
                        'bitrix:main.include',
                        '',
                        [
                            'AREA_FILE_SHOW' => 'file',
                            'PATH' => '/include/footer/copyright.php',
                        ],
                        false,
                        ['HIDE_ICONS' => 'N']
                    );
                    ?>
                </div>
                <a href="https://acroweb.ru/" target="_blank" rel="noopener noreferrer" class="footer__dev">
                    <span>Разработка и поддержка сайта</span>
                    <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/acroweb-light.svg" alt="">
                </a>
            </div>
        </div>
    </div>
</footer>

</div>
<?php TemplateHelper::includePartial('modal_callback'); ?>

<?php
$APPLICATION->IncludeComponent(
    'acroweb:cart.buttonswitcher',
    '',
    [],
    false
);
?>
<div class="hystmodal modal-notification" id="modalNotification" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="hystmodal__inner">
                <div data-title class="hystmodal__title"></div>
                <div data-text class="textblock1"></div>
                <button data-hystclose data-btn class="btn btn_primary"></button>
            </div>
            <button data-hystclose class="hystmodal__close">
                <svg aria-hidden="true" width="20" height="20">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
                </svg>
                <span class="v-h">Закрыть</span>
            </button>
        </div>
    </div>
</div>
</body>

</html>
