<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true);

use Acroweb\Mage\Settings\TemplateSettings;

$settings = TemplateSettings::getInstance();
$shopAdr = $settings->getSettingValue('shopAdr');
$shopOfName = $settings->getSettingValue('shopOfName');
$phoneList = $settings->getSettingValue('siteTelephone');
$emailList = $settings->getSettingValue('siteEmail');
$shopLocationLatitude = $settings->getSettingValue('shopLocationLatitude');
$shopLocationLongitude = $settings->getSettingValue('shopLocationLongitude');
?>

<div class="block-contacts__body">
    <h2 class="title4">
        <?= $shopOfName ?>
    </h2>
    <ul>
        <li>
            <div class="block-contacts__name">Адрес</div>
            <div class="block-contacts__location">
                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/location1.svg" alt="">
                <span><?= $shopAdr ?></span>
            </div>
        </li>
        <? if (!empty($phoneList)): ?>
        <li>
            <div class="block-contacts__name">Телефоны</div>
            <?php foreach ($phoneList as $phone): ?>
                <a href="tel:<?= preg_replace('/\D+/', '', $phone) ?>"><?= $phone ?></a>
            <?php endforeach; ?>
        </li>
        <? endif; ?>
        <? if (!empty($emailList)): ?>
        <li>
            <div class="block-contacts__name">E-mail</div>
            <?php foreach ($emailList as $email): ?>
                <a class="mail" href="mailto:<?= preg_replace('/[^\w\d@.-]/', '', $email) ?>"><?= $email ?></a>
            <?php endforeach; ?>
        </li>
        <? endif; ?>
    </ul>
</div>
<div id="map1" class="block-contacts__map"></div>

<script src="https://api-maps.yandex.ru/2.1/?apikey=82b0e496-d8c8-4106-beb4-a1f7c5dc377b&amp;lang=ru_RU&amp;_v=20240623012845&amp;_v=20250429150847"></script>
<script>

    //Карта
    const map1 = document.querySelector("#map1");
    if (map1) {
        if ("undefined" !== typeof ymaps) ymaps.ready((() => initMainMap()));
        else console.warn("Yandex Maps API не загружено для карты #map1");

        function initMainMap() {
            try {
                const isMobile = window.innerWidth <= 992;
                const baseCoords = [<?= $shopLocationLatitude ?>, <?= $shopLocationLongitude ?>];

                // Смещаем координаты
                let centerCoords;
                if (isMobile) {
                    // Для мобильных - смещаем вниз
                    centerCoords = [baseCoords[0] + 0.006, baseCoords[1]]; // смещение по широте (вниз)
                } else {
                    // Для десктопа - смещаем вправо
                    centerCoords = [baseCoords[0], baseCoords[1] - 0.015]; // смещение по долготе (вправо)
                }

                var myMap1 = new ymaps.Map("map1", {
                    center: centerCoords,
                    zoom: 15,
                    controls: ["zoomControl"],
                    behaviors: ["drag"]
                }, {
                    searchControlProvider: "yandex#search"
                });

                const placemark1 = new ymaps.Placemark([47.250522, 39.765606], {}, {
                    iconLayout: "default#image",
                    iconImageHref: "<?= SITE_TEMPLATE_PATH ?>/img/location2.svg",
                    iconImageSize: [60, 60],
                    iconImageOffset: [-30, -30]
                });

                myMap1.geoObjects.add(placemark1);
            } catch (error) {
                console.error("Ошибка при инициализации карты #map1:", error);
            }
        }
    }
</script>