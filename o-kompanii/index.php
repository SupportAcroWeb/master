<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("«Люк Мастер.РФ» — российский производитель ревизионных люков, которому доверяют крупные строительные компании и архитектурно-проектные бюро по всей стране.");

use Acroweb\Mage\Helpers\TemplateHelper;
?>
    <section class="company-intro">
        <div class="container">
            <?php $APPLICATION->IncludeFile('/include/o-kompanii/intro_image.php', [], ['MODE' => 'php']); ?>
        </div>
    </section>
    <section class="block-mission">
        <div class="container">
            <div class="heading-cols1">
                <div class="heading-cols1__col">
                    <h2 class="title2"><?php $APPLICATION->IncludeFile('/include/o-kompanii/mission_title.php', [], ['MODE' => 'php']); ?></h2>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__text"><?php $APPLICATION->IncludeFile('/include/o-kompanii/mission_text.php', [], ['MODE' => 'php']); ?></div>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__desk"><?php $APPLICATION->IncludeFile('/include/o-kompanii/mission_desk1.php', [], ['MODE' => 'php']); ?></div>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__desk"><?php $APPLICATION->IncludeFile('/include/o-kompanii/mission_desk2.php', [], ['MODE' => 'php']); ?></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-advantages">
        <div class="container">
            <div class="heading-cols1">
                <div class="heading-cols1__col">
                    <h2 class="title2"><?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_title.php', [], ['MODE' => 'php']); ?></h2>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__text"><?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_text.php', [], ['MODE' => 'php']); ?></div>
                </div>
            </div>
            <div class="advantages-cols1">
                <div class="advantages-cols1__item advantages-cols1__item--1">
                    <?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_image.php', [], ['MODE' => 'php']); ?>
                </div>
                <div class="advantages-cols1__item advantages-cols1__item--2">
                    <?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_item1.php', [], ['MODE' => 'php']); ?>
                </div>
                <div class="advantages-cols1__item advantages-cols1__item--3">
                    <?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_item2.php', [], ['MODE' => 'php']); ?>
                </div>
                <div class="advantages-cols1__item advantages-cols1__item--4">
                    <?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_item3.php', [], ['MODE' => 'php']); ?>
                </div>
                <div class="advantages-cols1__item advantages-cols1__item--5">
                    <?php $APPLICATION->IncludeFile('/include/o-kompanii/advantages_item4.php', [], ['MODE' => 'php']); ?>
                </div>
            </div>
        </div>
    </section>
    <? $APPLICATION->IncludeComponent(
            "acroweb:widgets",
            "block_news",
            array(
                    "COMPONENT_TEMPLATE" => "block_news",
                    "IBLOCK_TYPE" => "acroweb_content_s1",
                    "IBLOCK_ID" => "8",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "SORT",
                    "SORT_ORDER2" => "ASC",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_FILTER" => "N",
                    "CACHE_GROUPS" => "Y",
                    "SHOW_ON_MAIN" => "Y",
                    "NAME_BLOCK" => "Новости"
            ),
            false
    ); ?>
<? TemplateHelper::includePartial('block_questions'); ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>