<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка и оплата");
?>

    <div class="features-block grid grid-2">
        <div class="grid__inner">
            <div class="features">
                <div class="features__item">
                    <?php $APPLICATION->IncludeFile('/include/informatsiya/dostavka-i-oplata/delivery.php', [], ['MODE' => 'php']); ?>
                </div>
            </div>
        </div>
        <div class="grid__inner">
            <div class="features">
                <div class="features__item">
                    <?php $APPLICATION->IncludeFile('/include/informatsiya/dostavka-i-oplata/payment.php', [], ['MODE' => 'php']); ?>
                </div>
            </div>
        </div>
    </div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>