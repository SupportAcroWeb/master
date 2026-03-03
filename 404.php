<?

include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

global $APPLICATION;
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found");
?>
    <div class="container">
        <div class="page404">
            <svg class="page404__404" width="599" height="140" viewBox="0 0 599 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M136.329 1.59544V82.7635H155.064V110.285H136.329V140H103.243V110.285H0V63.8177L77.1336 1.59544H136.329ZM103.243 82.7635V29.1168H95.4703L33.0857 78.3761V82.7635H103.243Z" fill="#0C0B0B"></path>
                <path d="M202.765 89.3447C202.765 107.493 208.545 112.479 228.676 112.479H362.215C383.342 112.479 388.125 107.892 388.125 89.1453V52.2507C388.125 32.1083 381.349 27.5214 361.617 27.5214H229.274C208.545 27.5214 202.765 32.7065 202.765 52.4501V89.3447ZM421.211 92.735C421.211 127.236 409.85 140 371.184 140H220.703C183.631 140 169.68 126.239 169.68 92.735V49.2593C169.68 11.3675 183.033 0 220.703 0H371.184C405.465 0 421.211 10.3704 421.211 49.2593V92.735Z" fill="#0C0B0B"></path>
                <path d="M580.265 1.59544V82.7635H599V110.285H580.265V140H547.179V110.285H443.936V63.8177L521.069 1.59544H580.265ZM547.179 82.7635V29.1168H539.406L477.021 78.3761V82.7635H547.179Z" fill="#0C0B0B"></path>
                <path d="M323 66.5C323 60.701 323 56 333.5 56H354.5C365 56 365 60.701 365 66.5V73.5C365 79.299 365 84 354.5 84H333.5C323 84 323 79.299 323 73.5V66.5Z" fill="#EC1314"></path>
            </svg>
            <h1 class="page404__title">Страница не найдена...</h1>
            <div class="page404__text">
                Может быть, эта страница переместилась, была удалена, находится
                на карантине или никогда не существовала, попробуйте проверить адрес, обновить
                страницу или же вернитесь назад.
            </div>
            <div class="page404__btn">
                <a class="btn btn_primary btn_arr" href="/">
                    <span>на главную</span>
                    <svg width="14" height="14" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>

<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>