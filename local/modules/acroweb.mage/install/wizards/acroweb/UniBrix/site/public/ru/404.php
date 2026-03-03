<?

include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

global $APPLICATION;
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
?>
    <img class="page404__bg" src="<?= SITE_TEMPLATE_PATH ?>/img/page404.webp" alt="">
    <div class="main-wrapper-outer">
        <main class="main">
            <div class="page404__inner">
                <img class="page404__pic" src="<?= SITE_TEMPLATE_PATH ?>/img/404.svg" alt="404">
                <div class="textblock">
                    <h1>Страница не найдена...</h1>
                    <p>Может быть, эта страница переместилась, была удалена, находится на карантине или никогда не
                        существовала, попробуйте проверить адрес, обновить страницу или же вернитесь назад.</p>
                </div>
                <a href="/" class="btn btn_primary">
                    <span>Перейти на главную</span>
                    <svg class="btn__icon" width="16" height="14" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                    </svg>
                </a>
            </div>
        </main>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>