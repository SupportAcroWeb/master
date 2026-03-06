<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $APPLICATION;

$APPLICATION->SetTitle("Главная страница"); ?>

<section class="block-production">
    <div class="container">
        <div class="heading-cols1">
            <div class="heading-cols1__col">
                <h2 class="title2"><?php $APPLICATION->IncludeFile('/include/home/production_title.php', [], ['MODE' => 'php']); ?></h2>
            </div>
            <div class="heading-cols1__col">
                <div class="heading-cols1__text"><?php $APPLICATION->IncludeFile('/include/home/production_text.php', [], ['MODE' => 'php']); ?></div>
            </div>
        </div>
        <?
        $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "catalog_home",
                array(
                        "ADDITIONAL_COUNT_ELEMENTS_FILTER" => "additionalCountFilter",
                        "ADD_SECTIONS_CHAIN" => "Y",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "36000000",
                        "CACHE_TYPE" => "A",
                        "COUNT_ELEMENTS" => "N",
                        "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                        "FILTER_NAME" => "sectionsMainFilter",
                        "HIDE_SECTIONS_WITH_ZERO_COUNT_ELEMENTS" => "N",
                        "IBLOCK_ID" => "3",
                        "IBLOCK_TYPE" => "acroweb_catalog_s1",
                        "SECTION_CODE" => "",
                        "SECTION_FIELDS" => array("", ""),
                        "SECTION_ID" => $_REQUEST["SECTION_ID"],
                        "SECTION_URL" => "",
                        "SECTION_USER_FIELDS" => array("UF_SHOW_MAIN", "UF_BIG_IMG", ""),
                        "SHOW_PARENT_NAME" => "Y",
                        "TOP_DEPTH" => "3",
                        "VIEW_MODE" => "LINE",
                )
        ); ?>
    </div>
</section>

<?$APPLICATION->IncludeComponent(
        "acroweb:widgets",
        "recommendations",
        array(
                "COMPONENT_TEMPLATE" => "recommendations",
                "IBLOCK_TYPE" => "acroweb_catalog_s1",
                "IBLOCK_ID" => "3",
                "SORT_BY1" => "sort",
                "SORT_ORDER1" => "asc",
                "SORT_BY2" => "id",
                "SORT_ORDER2" => "desc",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "Y"
        ),
        false
);?>


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

<section class="block-about">
    <div class="container">
        <div class="heading-cols1">
            <div class="heading-cols1__col">
                <h2 class="title2"><?php $APPLICATION->IncludeFile('/include/home/about_title.php', [], ['MODE' => 'php']); ?></h2>
                <?php $APPLICATION->IncludeFile('/include/home/about_image.php', [], ['MODE' => 'php']); ?>
            </div>
            <div class="heading-cols1__col">
                <div class="heading-cols1__text"><?php $APPLICATION->IncludeFile('/include/home/about_text.php', [], ['MODE' => 'php']); ?></div>
                <div class="textblock1">
                    <?php $APPLICATION->IncludeFile('/include/home/about_description.php', [], ['MODE' => 'php']); ?>
                </div>
                <?php $APPLICATION->IncludeFile('/include/home/about_button.php', [], ['MODE' => 'php']); ?>
            </div>
        </div>
    </div>
</section>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>