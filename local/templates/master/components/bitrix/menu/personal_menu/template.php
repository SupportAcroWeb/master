<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
?>

<?php if (!empty($arResult)): ?>
    <nav class="lk-aside__nav">
        <ul class="lk-aside__list">
            <?php foreach ($arResult as $arItem): ?>
                <li class="lk-aside__item">
                    <a
                        href="<?= htmlspecialcharsbx($arItem['LINK']) ?>"
                        class="lk-aside__link<?= $arItem['SELECTED'] ? ' lk-aside__link--active' : '' ?>"
                    >
                        <?= htmlspecialcharsbx($arItem['TEXT']) ?>
                    </a>
                </li>
            <?php endforeach ?>

            <li class="lk-aside__item">
                <a
                    class="btn-text btn-text_black"
                    href="<?= htmlspecialcharsbx($APPLICATION->GetCurPageParam('logout=yes&' . bitrix_sessid_get(), ['login', 'logout', 'sessid'])) ?>"
                >
                    <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 9H11.5M4 12L1 9L4 6M9 4V3C9 2.46957 9.21071 1.96086 9.58579 1.58579C9.96086 1.21071 10.4696 1 11 1H16C16.5304 1 17.0391 1.21071 17.4142 1.58579C17.7893 1.96086 18 2.46957 18 3V15C18 15.5304 17.7893 16.0391 17.4142 16.4142C17.0391 16.7893 16.5304 17 16 17H11C10.4696 17 9.96086 16.7893 9.58579 16.4142C9.21071 16.0391 9 15.5304 9 15V14" stroke="#1E1E1E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <span>Выйти</span>
                </a>
            </li>
        </ul>
    </nav>
<?php endif ?>