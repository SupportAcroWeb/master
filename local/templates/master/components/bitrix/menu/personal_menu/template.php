<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):
$previousLevel = 0;
foreach($arResult as $arItem): ?>
    <a href="<?=$arItem["LINK"]?>" class="nav-user-cabinet__title<?= $arItem['SELECTED'] ? ' active' : ''?>"><span><?=$arItem["TEXT"]?></span></a>
<?endforeach?>
<?endif?>