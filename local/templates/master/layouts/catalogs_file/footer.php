<?php
global $APPLICATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

    </div>
</div>

<div class="block-description-about block1">
    <div class="container">
        <div class="block-description-about__content">
            <h2 class="title4"><? $APPLICATION->IncludeFile(SITE_DIR . 'include/catalogs_file/title.php') ?></h2>
            <? $APPLICATION->IncludeFile(SITE_DIR . 'include/catalogs_file/description.php') ?>
        </div>
    </div>
</div>
