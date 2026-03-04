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

    <section class="block-products">
        <div class="container">
            <div class="tabs1" data-tab="container">
                <div class="grid2">
                    <div class="tabs1-nav">
                        <button data-action="tab1" data-alias="hit" class="tabs1-nav__btn active" type="button">Хиты
                        </button>
                        <button data-action="tab1" data-alias="new" class="tabs1-nav__btn" type="button">Новинки
                        </button>
                        <button data-action="tab1" data-alias="special" class="tabs1-nav__btn" type="button">Акции
                        </button>
                    </div>
                    <a class="btn-text btn-text_primary" href="catalog.html">
                        <span>Смотреть все</span>
                        <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                        </svg>
                    </a>
                </div>
                <div class="tabs1__content active" data-tab="content" data-alias="hit">
                    <div class="grid1">
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status outofstock">Нет в наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tabs1__content" data-tab="content" data-alias="new">
                    <div class="grid1">
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tabs1__content" data-tab="content" data-alias="special">
                    <div class="grid1">
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                        <div class="card-product">
                            <div class="card-product__photo">
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp"
                                     alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на
                                    магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже,
                                обладает страховочным тросиком
                            </div>
                            <div class="card-product__bar">
                                <div class="card-product__price">
                                    <span class="card-product__price1">от 2350 ₽</span>
                                </div>
                                <span class="btn-arrow1">
												<svg width="14" height="14" aria-hidden="true">
													<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
												</svg>
											</span>
                            </div>
                        </div>
                    </div>
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