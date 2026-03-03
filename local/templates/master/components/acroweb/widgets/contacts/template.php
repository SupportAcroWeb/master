<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
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
$socialVk = trim((string)($settings->getSettingValue('socialVk') ?? ''));
$socialTelegram = trim((string)($settings->getSettingValue('socialTelegram') ?? ''));

$shopAdrDisplay = is_array($shopAdr) ? ($shopAdr[0] ?? '') : (string)$shopAdr;
?>
<div class="contact-cart">
    <div id="map1" class="contact-cart__map" style="position:absolute;top:0;left:0;right:0;width:100%;height:520px;"></div>
    <div class="container">
        <div class="contacts-card">
            <?php if (!empty($shopOfName)): ?>
            <p class="contacts-card__name"><?= htmlspecialcharsbx($shopOfName) ?></p>
            <?php endif; ?>
            <?php if (!empty($shopAdrDisplay)): ?>
            <p class="contacts-card__title">Адрес</p>
            <div class="contacts-card__location">
                <svg aria-hidden="true" width="15" height="18">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#placemark1"></use>
                </svg>
                <span><?= nl2br(htmlspecialcharsbx($shopAdrDisplay)) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($phoneList) || !empty($emailList)): ?>
            <p class="contacts-card__title">Контактные данные</p>
            <div class="contacts-card__contacts">
                <?php if (!empty($phoneList)): ?>
                    <?php foreach ($phoneList as $phone): ?>
                        <?php $phoneClean = preg_replace('/\D+/', '', $phone); ?>
                <a class="contact-item" href="tel:<?= $phoneClean ?>">
                    <svg aria-hidden="true" width="18" height="18">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#phone1"></use>
                    </svg>
                    <span><?= htmlspecialcharsbx($phone) ?></span>
                </a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if (!empty($emailList)): ?>
                    <?php foreach ($emailList as $email): ?>
                        <?php $emailClean = preg_replace('/[^\w\d@.-]/', '', $email); ?>
                <a class="contact-item" href="mailto:<?= $emailClean ?>">
                    <svg aria-hidden="true" width="18" height="14">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mail1"></use>
                    </svg>
                    <span><?= htmlspecialcharsbx($email) ?></span>
                </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ($socialVk !== '' || $socialTelegram !== ''): ?>
            <div class="contacts-card__socials">
                <?php if ($socialVk !== ''): ?>
                <a class="social-item" href="<?= htmlspecialcharsbx($socialVk) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="v-h">Вконтакте</span>
                    <svg aria-hidden="true" width="22" height="12">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#vk1"></use>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($socialTelegram !== ''): ?>
                <a class="social-item" href="<?= htmlspecialcharsbx($socialTelegram) ?>" target="_blank" rel="noopener noreferrer">
                    <span class="v-h">Телеграм</span>
                    <svg aria-hidden="true" width="18" height="15">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#telegram1"></use>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($shopLocationLatitude) && !empty($shopLocationLongitude)): ?>
<script src="https://api-maps.yandex.ru/2.1/?apikey=82b0e496-d8c8-4106-beb4-a1f7c5dc377b&lang=ru_RU"></script>
<script>
(function() {
    const map1 = document.getElementById('map1');
    if (!map1) return;
    if (typeof ymaps === 'undefined') {
        console.warn('Yandex Maps API не загружено для карты #map1');
        return;
    }
    ymaps.ready(function() {
        try {
            const baseCoords = [<?= (float)$shopLocationLatitude ?>, <?= (float)$shopLocationLongitude ?>];
            const isMobile = window.innerWidth <= 992;
            const centerCoords = isMobile
                ? [baseCoords[0] + 0.006, baseCoords[1]]
                : [baseCoords[0], baseCoords[1] - 0.015];
            const myMap = new ymaps.Map('map1', {
                center: centerCoords,
                zoom: 15,
                controls: ['zoomControl'],
                behaviors: ['drag']
            });
            const balloonContent = <?= json_encode(
                '<div class="map-balloon">' .
                (!empty($shopOfName) ? '<strong>' . htmlspecialcharsbx($shopOfName) . '</strong><br>' : '') .
                (!empty($shopAdrDisplay) ? nl2br(htmlspecialcharsbx($shopAdrDisplay)) : '') .
                (empty($shopOfName) && empty($shopAdrDisplay) ? 'Точка на карте' : '') .
                '</div>',
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
            ) ?>;
            const placemark = new ymaps.Placemark(baseCoords, {
                balloonContent: balloonContent
            }, {
                iconLayout: 'default#image',
                iconImageHref: '<?= SITE_TEMPLATE_PATH ?>/img/location2.svg',
                iconImageSize: [60, 60],
                iconImageOffset: [-30, -30]
            });
            myMap.geoObjects.add(placemark);
        } catch (e) {
            console.error('Ошибка инициализации карты #map1:', e);
        }
    });
})();
</script>
<?php endif; ?>