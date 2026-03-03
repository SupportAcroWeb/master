<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

global $APPLICATION;

$APPLICATION->SetTitle("Главная страница"); ?>
    <section class="block-intro">
        <div class="container">
            <div class="heading-cols2">
                <div class="heading-cols2__col">
                    <h1 class="title1">Люки скрытой установки</h1>
                </div>
                <div class="heading-cols2__col">
                    <div class="block-intro__text">
                        <p>Сантехническое оборудование от ведущих производителей по самым выгодным условиям и ценам</p>
                    </div>
                    <a class="btn btn_arr btn_primary btn_big" href="#">
                        <span>Каталог продукции</span>
                        <svg width="14" height="14" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                        </svg>
                    </a>
                </div>
            </div>
            <img class="block-intro__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/intro.webp" alt="">
        </div>
    </section>

    <section class="block-advantages">
        <div class="container">
            <div class="grid1">
                <div class="card-advantage">
                    <div class="card-advantage__top">
                        <span class="card-advantage__index">01</span>
                        <span class="card-advantage__name">Цена</span>
                    </div>
                    <div class="card-advantage__text">Лучшие цены в сегменте — гарантируем выгодную стоимость без скрытых платежей.</div>
                </div>
                <div class="card-advantage">
                    <div class="card-advantage__top">
                        <span class="card-advantage__index">02</span>
                        <span class="card-advantage__name">Качество</span>
                    </div>
                    <div class="card-advantage__text">Высокое качество — фундамент безопасности и долгосрочной работы.</div>
                </div>
                <div class="card-advantage">
                    <div class="card-advantage__top">
                        <span class="card-advantage__index">03</span>
                        <span class="card-advantage__name">Скорость доставки</span>
                    </div>
                    <div class="card-advantage__text">Скорость без компромиссов: Ваш заказ приедет точно в оговорённый срок.</div>
                </div>
                <div class="card-advantage">
                    <div class="card-advantage__top">
                        <span class="card-advantage__index">04</span>
                        <span class="card-advantage__name">Уникальный подход</span>
                    </div>
                    <div class="card-advantage__text">Мы создадим люк по Вашим чертежам или разработаем уникальное решение «под ключ»</div>
                </div>
            </div>
        </div>
    </section>

    <section class="block-production">
        <div class="container">
            <div class="heading-cols1">
                <div class="heading-cols1__col">
                    <h2 class="title2">Наша продукция</h2>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__text">Все изделия разрабатываются с учётом условий <span>эксплуатации, нагрузки, герметичности и эстетики.</span></div>
                </div>
            </div>
            <div class="grid1">
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category1.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Ревизионные люки</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки быстрого монтажа</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки в потолок</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки напольные</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки двери</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под плитку</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под покраску</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под гипсокартон</a>
                                </li>
                            </ul>
                            <a class="card-category__link" href="catalog.html">Смотреть</a>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category2.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Сантехнические люки</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки для ванной</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для туалета</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для счетчиков</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки на магнитах</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category card-category_wide">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category3.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Люки индивидуальные на заказ</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки для ванной</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для туалета</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для счетчиков</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки на магнитах</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category4.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Люки выхода на кровлю</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки быстрого монтажа</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки в потолок</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки напольные</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки двери</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под плитку</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под покраску</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под гипсокартон</a>
                                </li>
                            </ul>
                            <a class="card-category__link" href="catalog.html">Смотреть</a>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category5.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Окна люки</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки для ванной</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для туалета</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для счетчиков</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки на магнитах</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category1.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Ревизионные люки</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки быстрого монтажа</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки в потолок</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки напольные</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки двери</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под плитку</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под покраску</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки под гипсокартон</a>
                                </li>
                            </ul>
                            <a class="card-category__link" href="catalog.html">Смотреть</a>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
                <div class="card-category">
                    <img loading="lazy" class="card-category__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/category2.webp" alt="">
                    <div class="card-category__inner">
                        <div class="card-category__name">
                            <a href="catalog.html">Сантехнические люки</a>
                        </div>
                        <div class="card-category__inner1">
                            <ul class="card-category__list">
                                <li>
                                    <a href="catalog.html">Люки для ванной</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для туалета</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки для счетчиков</a>
                                </li>
                                <li>
                                    <a href="catalog.html">Люки на магнитах</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <span class="btn-arrow1">
									<svg width="14" height="14" aria-hidden="true">
										<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
									</svg>
								</span>
                </div>
            </div>
        </div>
    </section>

    <section class="block-products">
        <div class="container">
            <div class="tabs1" data-tab="container">
                <div class="grid2">
                    <div class="tabs1-nav">
                        <button data-action="tab1" data-alias="hit" class="tabs1-nav__btn active" type="button">Хиты</button>
                        <button data-action="tab1" data-alias="new" class="tabs1-nav__btn" type="button">Новинки</button>
                        <button data-action="tab1" data-alias="special" class="tabs1-nav__btn" type="button">Акции</button>
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
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status outofstock">Нет в наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_red">Акция</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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
                                <img loading="lazy" src="<?= SITE_TEMPLATE_PATH ?>/img/product1.webp" alt="Люк металлический на магнитах">
                                <div class="card-product__badges">
                                    <span class="badge1 badge1_black">Новинка</span>
                                    <span class="badge1 badge1_yellow">Хит</span>
                                </div>
                            </div>
                            <div class="card-product__name">
                                <a href="catalog-detail.html">Люк металлический на магнитах "Люк металлический на магнитах"</a>
                            </div>
                            <div class="card-product__status instock">В наличии</div>
                            <div class="card-product__description">Алюминиевый люк со съемной дверцей, прост в монтаже, обладает страховочным тросиком</div>
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

    <section class="block-portfolio">
        <div data-swiper="container" class="container">
            <div class="heading-cols1">
                <div class="heading-cols1__col">
                    <h2 class="title2">Портфолио</h2>
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__text">Мы знаем, как совместить <span>надёжность и эстетику.</span></div>
                </div>
            </div>
            <div class="grid2">
                <div class="swiper-navs">
                    <button class="swiper-nav swiper-nav_prev" type="button">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                        </svg>
                    </button>
                    <button class="swiper-nav swiper-nav_next" type="button">
                        <svg width="16" height="16" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow2"></use>
                        </svg>
                    </button>
                </div>
                <a class="btn-text btn-text_white" href="portfolio.html">
                    <span>Смотреть все</span>
                    <svg class="btn-text__icon" width="14" height="14" aria-hidden="true">
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                    </svg>
                </a>
            </div>
            <div data-swiper="products" class="swiper swiper-products">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="card-portfolio">
                            <img loading="lazy" class="card-portfolio__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/portfolio1.webp" alt="">
                            <span class="card-portfolio__count badge1 badge1_white">25 фото</span>
                            <div class="card-portfolio__name">
                                <a href="portfolio-detail.html">Подробно об особенностях выбора и установки напольных люков невидимок под плитку</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="block-about">
        <div class="container">
            <div class="heading-cols1">
                <div class="heading-cols1__col">
                    <h2 class="title2">О компании</h2>
                    <img loading="lazy" class="block-about__photo" src="<?= SITE_TEMPLATE_PATH ?>/img/about.webp" alt="">
                </div>
                <div class="heading-cols1__col">
                    <div class="heading-cols1__text">Наша компания специализируется на производстве <span>ревизионных люков премиального качества</span> для жилых и общественных помещений.</div>
                    <div class="textblock1">
                        <p>В ассортименте — люки для ванных комнат, кухонь, коридоров и технических помещений. Каждый продукт отличается точной геометрией и безупречной подгонкой элементов. Мы используем только проверенные материалы: высокопрочный металл, износостойкие покрытия и надёжную фурнитуру. Наши люки легко открываются и закрываются, обеспечивая удобный доступ к счётчикам, трубам и электропроводке.</p>
                        <p>Конструкция разработана так, чтобы выдерживать ежедневную эксплуатацию без деформаций и скрипов. Мы предлагаем варианты под плитку, покраску и другие виды отделки — люк становится незаметным элементом интерьера. Вся продукция проходит многоступенчатый контроль качества и соответствует отраслевым стандартам.</p>
                        <p><strong>Мы создаём решения, которые надёжно скрывают инженерные коммуникации, сохраняя эстетику интерьера.</strong></p>
                    </div>
                    <a class="btn btn_arr btn_primary btn_big" href="#">
                        <span>Подробнее</span>
                        <svg width="14" height="14" aria-hidden="true">
                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#arrow1"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>