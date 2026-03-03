<?php

/** @global CMain $APPLICATION */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Acroweb\Mage\Helpers\TemplateHelper;

TemplateHelper::includeLayout('footer');

?>
</main>
<? if (!defined('ERROR_404')) { ?>
<footer class="footer">
    <div class="footer__top">
        <div class="container">
            <div class="footer__grid">
                <div>
                    <?php TemplateHelper::includePartial('logo_footer'); ?>
                </div>
                <div>
                    <p class="footer__caption"><a href="/produktsiya/">Каталог</a></p>
                    <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "bottom",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(
                                ),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "bottom",
                                "USE_EXT" => "N",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                        ); ?>
                    <? $APPLICATION->IncludeComponent(
                        "bitrix:menu",
                        "footer_menu",
                        array(
                            "ALLOW_MULTI_SELECT" => "N",
                            "CHILD_MENU_TYPE" => "catalog",
                            "DELAY" => "N",
                            "MAX_LEVEL" => "1",
                            "MENU_CACHE_GET_VARS" => array(),
                            "MENU_CACHE_TIME" => "3600",
                            "MENU_CACHE_TYPE" => "N",
                            "MENU_CACHE_USE_GROUPS" => "Y",
                            "ROOT_MENU_TYPE" => "catalog",
                            "USE_EXT" => "N",
                            "COMPONENT_TEMPLATE" => "footer_menu"
                        ),
                        false
                    ); ?>
                </div>
                <div>
                    <? $APPLICATION->IncludeComponent(
                            "bitrix:menu",
                            "footer_menu",
                            array(
                                "ALLOW_MULTI_SELECT" => "N",
                                "CHILD_MENU_TYPE" => "podmenu",
                                "DELAY" => "N",
                                "MAX_LEVEL" => "1",
                                "MENU_CACHE_GET_VARS" => array(),
                                "MENU_CACHE_TIME" => "3600",
                                "MENU_CACHE_TYPE" => "N",
                                "MENU_CACHE_USE_GROUPS" => "Y",
                                "ROOT_MENU_TYPE" => "top",
                                "USE_EXT" => "N",
                                "COMPONENT_TEMPLATE" => "footer_menu"
                            ),
                            false
                    ); ?>
                </div>
                <div>
                    <?php TemplateHelper::includePartial('footer_contacts'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="footer__bottom-grid">
                <?
                    $APPLICATION->IncludeComponent(
                        "bitrix:main.include", "",
                        array(
                            "AREA_FILE_SHOW" => "file",
                            "PATH" => "/include/footer/copyright.php",
                        ),
                        false,
                        array('HIDE_ICONS'=>'N')
                    );
                ?>
                <div class="footer__bottom-grid-inner">
                    <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "",
                            array(
                                "AREA_FILE_SHOW" => "file",
                                "PATH" => "/include/footer/policy.php",
                            ),
                            false,
                            array('HIDE_ICONS'=>'N')
                        );
                    ?>
                    <a class="logo" href="https://acroweb.ru/">
                        <img src="<?= SITE_TEMPLATE_PATH ?>/img/logo-acroweb.svg" alt="">
                    </a>
                </div>
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
<?php } ?>
</body>

</html>