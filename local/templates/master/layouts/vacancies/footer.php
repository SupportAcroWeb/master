<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Acroweb\Mage\Helpers\TemplateHelper;

global $APPLICATION; ?>
</div>
<?php
TemplateHelper::includePartial('block_questions'); ?>

<div class="hystmodal" id="modalVacancies" aria-hidden="true">
    <div class="hystmodal__wrap">
        <div class="hystmodal__window" role="dialog" aria-modal="true">
            <div class="hystmodal__inner">
                <h2 class="hystmodal__title">Откликнуться на вакансию</h2>
                <div class="hystmodal__description">Введите данные, что бы откликнуться на вакансию</div>
                <?
                $APPLICATION->IncludeComponent(
                    "acroweb:universal.form",
                    "vacancies",
                    [
                        "FORM_SID" => "acroweb_vacancies_s1",
                        "AJAX" => "Y",
                    ]
                );
                ?>
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
