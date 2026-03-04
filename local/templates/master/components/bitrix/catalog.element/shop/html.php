<div class="container">
    <div class="block3">
        <a class="btn-arrow btn-arrow_primary btn-arrow_l" href="catalog-grid.html">
            Вернуться в раздел
            <svg aria-hidden="true" width="16" height="14">
                <use xlink:href="img/sprite.svg#arrow1"></use>
            </svg>
        </a>
    </div>
    <div class="catalog-detail-grid block4">
        <div class="catalog-detail-grid__main catalog-detail-main">
            <div class="catalog-detail-main__top">
                <h1 class="title3">Геймпад Razer Wolverine V2 Chroma (RZ06-04010200-R3M1) белый</h1>
                <a class="catalog-detail-main__logo" href="#">
                    <img src="img/content/razer.jpg" alt="">
                </a>
            </div>
            <div class="catalog-detail-main__bottom">
                <div class="catalog-detail-main__left">
                    <div class="rating rating_3" title="Рейтинг: Нормально">
                        <svg class="rating__star" aria-hidden="true" width="16" height="15">
                            <use xlink:href="img/sprite.svg#star1"></use>
                        </svg>
                        <svg class="rating__star" aria-hidden="true" width="16" height="15">
                            <use xlink:href="img/sprite.svg#star1"></use>
                        </svg>
                        <svg class="rating__star" aria-hidden="true" width="16" height="15">
                            <use xlink:href="img/sprite.svg#star1"></use>
                        </svg>
                        <svg class="rating__star" aria-hidden="true" width="16" height="15">
                            <use xlink:href="img/sprite.svg#star1"></use>
                        </svg>
                        <svg class="rating__star" aria-hidden="true" width="16" height="15">
                            <use xlink:href="img/sprite.svg#star1"></use>
                        </svg>
                        <span class="rating__count">(10 отзывов)</span>
                    </div>
                    <a class="lnk-share" href="#">
                        <svg aria-hidden="true" width="16" height="16">
                            <use xlink:href="img/sprite.svg#share1"></use>
                        </svg>
                        Поделиться
                    </a>
                </div>
                <div class="catalog-detail-main__code">Код товара: 5004 6519</div>
            </div>
        </div>
        <div class="catalog-detail-grid__order">
            <div class="rblock2 catalog-detail-order">
                <div class="catalog-detail-order__prices">
                    <span class="catalog-detail-order__price">10 000 ₽</span>
                    <span class="catalog-detail-order__price-old">14 000 ₽</span>
                </div>
                <p class="catalog-detail-order__status text-status">
                    <svg aria-hidden="true" width="16" height="16">
                        <use xlink:href="img/sprite.svg#status1"></use>
                    </svg>
                    В наличии в <a href="#">3 магазинах</a>
                </p>
                <div class="catalog-detail-order__btns">
                    <button class="btn btn_primary btn_wide" type="button">В корзину</button>
                    <button data-action="popupBuyByOneClick" class="btn btn_black btn_hollow btn_wide" type="button">Купить в 1 клик</button>
                </div>
            </div>
            <div class="rblock2 catalog-detail-order catalog-detail-links">
                <p>
                    <button data-action="popupLowPrice" type="button">
                        <svg aria-hidden="true" width="16" height="16">
                            <use xlink:href="img/sprite.svg#currency1"></use>
                        </svg>
                        Нашли дешевле?
                    </button>
                </p>
                <p>
                    <button data-action="popupShippingCalc" type="button">
                        <svg aria-hidden="true" width="16" height="16">
                            <use xlink:href="img/sprite.svg#shipping1"></use>
                        </svg>
                        Рассчитать доставку
                    </button>
                </p>
                <p>
                    <button data-action="popupWantAsGift" type="button">
                        <svg aria-hidden="true" width="16" height="16">
                            <use xlink:href="img/sprite.svg#gift1"></use>
                        </svg>
                        Хочу в подарок
                    </button>
                </p>
                <template id="popupTpl_buyByOneClick">
                    <swal-title>Купить в 1 клик</swal-title>
                    <swal-html>
                        <form data-validate action="" class="form-grid2">
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Ф.И.О.</label>
                                    <input class="field-input1 form-group__field" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Номер телефона</label>
                                    <input class="field-input1 form-group__field" data-mask="phone" type="tel" name="phone" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">E-mail</label>
                                    <input class="field-input1 form-group__field" type="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label">Комментарий к заказу</label>
                                    <textarea class="field-input1 form-group__field" name="text"></textarea>
                                </div>
                            </div>
                            <div class="form-grid2__row popup__text2">
                                <p>Нажимая на кнопку «Купить в 1 клик», Вы соглашаетесь на <a target="_blank" href="#">обработку персональных данных</a> и с <a target="_blank" href="#">публичной офертой</a></p>
                            </div>
                            <div class="form-grid2__row form-grid2__row_end">
                                <button type="submit" class="swal2-confirm btn btn_primary btn_wide btn_submit">Купить в 1 клик</button>
                            </div>
                        </form>
                    </swal-html>
                </template>
                <template id="popupTpl_lowPrice">
                    <swal-title>Нашли дешевле?</swal-title>
                    <swal-html>
                        <form data-validate action="" class="form-grid2">
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Ф.И.О.</label>
                                    <input class="field-input1 form-group__field" type="text" name="name" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Номер телефона</label>
                                    <input class="field-input1 form-group__field" data-mask="phone" type="tel" name="phone" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label">E-mail</label>
                                    <input class="field-input1 form-group__field" type="email" name="email">
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Ссылка на товар другого магазина</label>
                                    <input class="field-input1 form-group__field" type="text" name="link" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label">Сообщение</label>
                                    <textarea class="field-input1 form-group__field" name="text"></textarea>
                                </div>
                            </div>
                            <div class="form-grid2__row popup__text2">
                                <p>Нажимая на кнопку «Отправить», Вы соглашаетесь на <a target="_blank" href="#">обработку персональных данных</a> и с <a target="_blank" href="#">публичной офертой</a></p>
                            </div>
                            <div class="form-grid2__row form-grid2__row_end">
                                <button type="submit" class="swal2-confirm btn btn_primary btn_wide btn_submit">Отправить</button>
                            </div>
                        </form>
                    </swal-html>
                </template>
                <template id="popupTpl_shippingCalc">
                    <swal-title>Стоимость доставки</swal-title>
                    <swal-html>
                        <div class="blocks-list4 shipping-calc">
                            <div>
													<span class="shipping-location">
														<svg aria-hidden="true" width="15" height="18">
															<use xlink:href="img/sprite.svg#placemark2"></use>
														</svg>
														Москва
													</span>
                            </div>
                            <div class="shipping-calc-bar">
                                <div class="shipping-calc-bar__count">
                                    <span class="shipping-calc-bar__label">Количество:</span>
                                    <div data-stepcounter class="stepcounter">
                                        <button class="stepcounter__btn" data-stepcounter="-" type="button" disabled>
                                            <svg aria-hidden="true">
                                                <use xlink:href="img/sprite.svg#minus1"></use>
                                            </svg>
                                        </button>
                                        <input data-stepcounter-input class="stepcounter__input" type="number" value="1" max="10">
                                        <button class="stepcounter__btn" data-stepcounter="+" type="button">
                                            <svg aria-hidden="true">
                                                <use xlink:href="img/sprite.svg#plus1"></use>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <label class="checkbox-text">
														<span class="checkbox">
															<input class="checkbox__input" type="checkbox">
															<span class="checkbox__visual">
																<svg class="checkbox__mark" aria-hidden="true">
																	<use xlink:href="img/sprite.svg#mark1"></use>
																</svg>
															</span>
														</span>
                                    <span class="checkbox-text__label">Учитывать всю корзину</span>
                                </label>
                            </div>
                            <div class="rblock1 rblock1_content3 shipping-calc-item">
                                <p class="shipping-calc-item__top">
                                    <span class="shipping-calc-item__name">Доставка курьером</span>
                                    <span class="shipping-calc-item__cost">700 ₽</span>
                                </p>
                                <p class="shipping-calc-item__description">От 1 до 7 дней</p>
                            </div>
                            <div class="rblock1 rblock1_content3 shipping-calc-item">
                                <p class="shipping-calc-item__top">
                                    <span class="shipping-calc-item__name">Самовывоз из пункта выдачи</span>
                                    <span class="shipping-calc-item__cost">Бесплатно</span>
                                </p>
                            </div>
                        </div>
                    </swal-html>
                </template>
                <template id="popupTpl_wantAsGift">
                    <swal-title>Намекни другу о подарке</swal-title>
                    <swal-html>
                        <form data-validate action="" class="form-grid2">
                            <div class="form-grid2__row">
                                <div class="gift-proposal">
                                    <div class="gift-proposal__text">Нашли что-то особенное? Намекните другу о подарке!</div>
                                    <div class="product-order">
                                        <a target="_blank" href="#" class="product-order__img" tabindex="-1">
                                            <img src="img/content/catalog-item3.webp" alt="" />
                                        </a>
                                        <div class="product-order__info">
                                            <p class="product-order__title">
                                                <a target="_blank" href="#">Геймпад Razer Wolverine V2 Chroma (RZ06-04010200-R3M1)</a>
                                            </p>
                                            <div class="product-order__bottom">
                                                <p class="product-order__price">
                                                    10 000 ₽
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Ваше имя</label>
                                    <input class="field-input1 form-group__field" type="text" name="name1" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">Имя получателя</label>
                                    <input class="field-input1 form-group__field" type="text" name="name2" required>
                                </div>
                            </div>
                            <div class="form-grid2__row">
                                <div class="form-group">
                                    <label class="form-group__label form-group__label_req">E-mail получателя</label>
                                    <input class="field-input1 form-group__field" type="email" name="email" required>
                                </div>
                            </div>
                            <div class="form-grid2__row popup__text2">
                                <p>Нажимая на кнопку «Отправить», Вы соглашаетесь на <a target="_blank" href="#">обработку персональных данных</a> и с <a target="_blank" href="#">публичной офертой</a></p>
                            </div>
                            <div class="form-grid2__row form-grid2__row_end">
                                <button type="submit" class="swal2-confirm btn btn_primary btn_wide btn_submit">Отправить</button>
                            </div>
                        </form>
                    </swal-html>
                </template>
            </div>
        </div>
        <div class="catalog-detail-grid__photo">
            <div class="catalog-photo">
                <div class="catalog-photo__big catalog-photo-big">
                    <div class="catalog-photo-big__labels">
                        <span class="badge badge_primary">-40%</span>
                        <span class="badge badge_orange">-40%</span>
                    </div>
                    <div data-swiper="photoBig" class="swiper swiper-photo-big">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item1.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item1.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item2.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item2.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item3.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item3.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item4.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item4.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item5.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item5.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item6.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item6.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item7.webp">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item7.webp" alt="">
                                </a>
                            </div>
                            <div class="swiper-slide swiper-photo-big__item">
                                <a data-fancybox="catalogDetail" class="swiper-photo-big__link" href="img/content/catalog-item8.png">
                                    <img class="swiper-photo-big__photo" src="img/content/catalog-item8.png" alt="">
                                </a>
                            </div>
                        </div>
                        <button type="button" data-swiper-nav="next" class="btn-swiper-nav btn-swiper-nav_next">
                            <svg aria-hidden="true" width="16" height="14">
                                <use xlink:href="img/sprite.svg#arrow1"></use>
                            </svg>
                        </button>
                        <button type="button" data-swiper-nav="prev" class="btn-swiper-nav btn-swiper-nav_prev">
                            <svg aria-hidden="true" width="16" height="14">
                                <use xlink:href="img/sprite.svg#arrow1"></use>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="catalog-photo__previews">
                    <div data-swiper="photoPreview" class="swiper swiper-photo-preview">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide swiper-photo-preview__item swiper-photo-preview__item_active">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item1.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item2.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item3.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item4.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item5.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item6.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item7.webp" alt="">
                            </div>
                            <div class="swiper-slide swiper-photo-preview__item">
                                <img class="swiper-photo-preview__photo" src="img/content/catalog-item8.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="catalog-detail-grid__description">
            <div class="blocks-list3">
                <div class="catalog-detail-text-wrapper" data-item-expandable="collapsed">
                    <div class="catalog-detail-text">
                        <p>Попадите в высшую лигу с <a href="#">проводным</a> игровым контроллером, разработанным для консолей Xbox Series X | S. Благодаря расширенным возможностям настройки для большей точности и контроля над игрой, Razer Wolverine V2 позволит вам доминировать в сражениях, не вставая с дивана.</p>
                        <p>Эргономичные L-образные рукоятки Wolverine V2 обеспечивают естественный захват, превосходящий по своим характеристикам своего предшественника, в то время как контурный дизайн и нескользящие резиновые рукоятки позволяют быстро и точно нажимать кнопки для игр в течение всего дня с максимальной производительностью.</p>
                    </div>
                    <button data-item-expandable-handle="show" class="btn-text btn-text_1 btn-text_primary">Подробнее</button>
                    <button data-item-expandable-handle="hide" class="btn-text btn-text_1 btn-text_primary">Свернуть</button>
                </div>
                <div>
                    <h2 class="title4">Характеристики</h2>
                    <div class="speclist1-wrapper" data-item-expandable="collapsed">
                        <div class="speclist1">
                            <span class="speclist1__key">Совместимость</span>
                            <span class="speclist1__value">Xbox 360, Xbox One, PC</span>
                            <span class="speclist1__key">Интерфейс связи с ПК</span>
                            <span class="speclist1__value">USB</span>
                            <span class="speclist1__key">Режим вибрации</span>
                            <span class="speclist1__value">Да</span>
                            <span class="speclist1__key">Длина кабеля</span>
                            <span class="speclist1__value">3 м</span>
                            <span class="speclist1__key">Вес</span>
                            <span class="speclist1__value">270 г</span>
                            <span class="speclist1__key">Цвет</span>
                            <span class="speclist1__value">Белый</span>
                            <span class="speclist1__key">Гарантия</span>
                            <span class="speclist1__value">1 год</span>
                            <span data-item-expandable-item class="speclist1__key">Стики</span>
                            <span data-item-expandable-item class="speclist1__value">2 шт</span>
                            <span data-item-expandable-item class="speclist1__key">Расположение стиков</span>
                            <span data-item-expandable-item class="speclist1__value">ассиметричное</span>
                            <span data-item-expandable-item class="speclist1__key">Тип стиков</span>
                            <span data-item-expandable-item class="speclist1__value">аналоговые</span>
                            <span data-item-expandable-item class="speclist1__key">D-Pad (крестовина)</span>
                            <span data-item-expandable-item class="speclist1__value">есть</span>
                            <span data-item-expandable-item class="speclist1__key">Тип крестовины (D-Pad)</span>
                            <span data-item-expandable-item class="speclist1__value">4-позиционный</span>
                            <span data-item-expandable-item class="speclist1__key">Расположение кнопок XYBA</span>
                            <span data-item-expandable-item class="speclist1__value">Xbox-like</span>
                            <span data-item-expandable-item class="speclist1__key">Бамперы</span>
                            <span data-item-expandable-item class="speclist1__value">есть</span>
                            <span data-item-expandable-item class="speclist1__key">Триггеры</span>
                            <span data-item-expandable-item class="speclist1__value">есть</span>
                            <span data-item-expandable-item class="speclist1__key">Дополнительные лепестки</span>
                            <span data-item-expandable-item class="speclist1__value">нет</span>
                            <span data-item-expandable-item class="speclist1__key">Трекпад </span>
                            <span data-item-expandable-item class="speclist1__value">нет</span>
                            <span data-item-expandable-item class="speclist1__key">Количество кнопок</span>
                            <span data-item-expandable-item class="speclist1__value">20 шт</span>
                        </div>
                        <button data-item-expandable-handle="show" class="btn-text btn-text_1 btn-text_primary">Все характеристики</button>
                        <button data-item-expandable-handle="hide" class="btn-text btn-text_1 btn-text_primary">Свернуть характеристики</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="hr1 block4">
    <div class="tabs1 block1" data-container>
        <ul class="tabs-nav2" role="tablist">
            <li class="tabs-nav2__item" role="presentation">
                <button id="catalogDetail1" data-action="tab1" data-tab-btn="1" class="btn btn_tab1 btn_active" type="button" role="tab" aria-selected="true">
                    <span>Описание</span>
                </button>
            </li>
            <li class="tabs-nav2__item" role="presentation">
                <button id="catalogDetail2" data-action="tab1" data-tab-btn="2" class="btn btn_tab1" type="button" role="tab" aria-selected="false">
                    <span>Характеристики</span>
                </button>
            </li>
        </ul>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail1" class="tabs1__content tabs1__content_active" data-tab-content="1">
            <div class="textblock textblock_small">
                <p>Геймпад Razer Wolverine V2 Chroma (RZ06-04010200-R3M1) в белом корпусе — модель с эргономичным исполнением, совместимая с PC и консолями Xbox 360, Xbox One. Используется проводное соединение при помощи порта USB. Специальные резиновые рукоятки L-образной формы обеспечивают надежный захват и позволяют быстро и точно нажимать кнопки, чтобы оперативно отвечать на действия соперников. За счет особой формы предусмотрен облегченный доступ к триггерам и бамперам. </p>
                <p>Механические кнопки обеспечивают сверхчувствительное срабатывание и мягкое нажатие. Каждое движение усиливается. Срок службы кнопок достигает 3 млн нажатий. Для расширения игровых возможностей геймпад снабжен дополнительными кнопками, среди которых предусмотрены две многофункциональные. Их при необходимости можно переназначить для получения более индивидуального стиля игры. Общее количество кнопок достигает 24. Длина кабеля составляет 3 м. Вес устройства — 270 г.</p>
            </div>
        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail2" class="tabs1__content" data-tab-content="2">
            <div class="speclist2 columns1">
                <p class="speclist2__row">
                    <span class="speclist2__key">Совместимость</span>
                    <span class="speclist2__value">Xbox 360, Xbox One, PC</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Интерфейс связи с ПК</span>
                    <span class="speclist2__value">USB</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Режим вибрации</span>
                    <span class="speclist2__value">Да</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Длина кабеля</span>
                    <span class="speclist2__value">3 м</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Вес</span>
                    <span class="speclist2__value">270 г</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Цвет</span>
                    <span class="speclist2__value">Белый</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Гарантия</span>
                    <span class="speclist2__value">1 год</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Количество клавиш</span>
                    <span class="speclist2__value">24 шт</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Серия</span>
                    <span class="speclist2__value">Wolverine</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Удачное решение</span>
                    <span class="speclist2__value">два сменных триггера</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Хорошо придумано</span>
                    <span class="speclist2__value">подсветка</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Встроенный мини-джойстик</span>
                    <span class="speclist2__value">2</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Страна</span>
                    <span class="speclist2__value">Китай</span>
                </p>
                <p class="speclist2__row">
                    <span class="speclist2__key">Материал корпуса</span>
                    <span class="speclist2__value">пластик</span>
                </p>
            </div>
        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail3" class="tabs1__content" data-tab-content="3">
            <h2 class="title2">Отзывы</h2>
            <div class="rating-big block2">
                <div class="rating rating_4" title="Рейтинг: Хорошо">
                    <svg class="rating__star" aria-hidden="true" width="28" height="27">
                        <use xlink:href="img/sprite.svg#star2"></use>
                    </svg>
                    <svg class="rating__star" aria-hidden="true" width="28" height="27">
                        <use xlink:href="img/sprite.svg#star2"></use>
                    </svg>
                    <svg class="rating__star" aria-hidden="true" width="28" height="27">
                        <use xlink:href="img/sprite.svg#star2"></use>
                    </svg>
                    <svg class="rating__star" aria-hidden="true" width="28" height="27">
                        <use xlink:href="img/sprite.svg#star2"></use>
                    </svg>
                    <svg class="rating__star" aria-hidden="true" width="28" height="27">
                        <use xlink:href="img/sprite.svg#star2"></use>
                    </svg>
                </div>
                <span class="rating-big__value">4.2</span>
                <span class="rating-big__label">(на основе 10 отзывов)</span>
            </div>
            <div class="grid1">
                <div id="skipLinkTarget" class="grid1__col-wide">
                    <div class="blocks-list1">
                        <article class="review-card rblock1 review-card_1">
                            <div class="review-card__top">
                                <div class="userinfo">
                                    <div class="userinfo__avatar">И</div>
                                    <div class="userinfo__data">
                                        <p class="userinfo__name">Инкогнито 5512</p>
                                        <p class="userinfo__date">10 февраля 2023</p>
                                        <div class="rating" title="Рейтинг: Без оценки">
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="review-card__preheader">
                                Отзыв:
                            </p>
                            <div class="review-card__bottom">
                                <div class="review-card__text">
                                    Рыбатекст используется дизайнерами, проектировщиками и фронтендерами, когда нужно быстро заполнить
                                    макеты или прототипы
                                    содержимым. Это тестовый контент, который не должен нести никакого смысла...
                                </div>
                                <a href="#" class="review-card__link">
                                    Читать полностью
                                </a>
                            </div>
                        </article>
                        <article class="review-card rblock1 review-card_1">
                            <div class="review-card__top">
                                <div class="userinfo">
                                    <div class="userinfo__avatar userinfo__avatar_photo">
                                        <img aria-hidden="true" src="img/content/avatar.jpg" alt="" />
                                    </div>
                                    <div class="userinfo__data">
                                        <p class="userinfo__name" title="Очень длинное имя пользователя">Очень длинное имя пользователя
                                        </p>
                                        <p class="userinfo__date">10 февраля 2023</p>
                                        <div class="rating rating_5" title="Рейтинг: Отлично">
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="review-card__preheader">
                                Отзыв:
                            </p>
                            <div class="review-card__bottom">
                                <div class="review-card__text">
                                    Рыбатекст используется дизайнерами, проектировщиками и фронтендерами, когда нужно быстро заполнить
                                    макеты или прототипы
                                    содержимым. Это тестовый контент, который не должен нести никакого смысла...
                                </div>
                                <a href="#" class="review-card__link">
                                    Читать полностью
                                </a>
                            </div>
                        </article>
                        <article class="review-card rblock1 review-card_1">
                            <div class="review-card__top">
                                <div class="userinfo">
                                    <div class="userinfo__avatar">И</div>
                                    <div class="userinfo__data">
                                        <p class="userinfo__name">Инкогнито 5512</p>
                                        <p class="userinfo__date">10 февраля 2023</p>
                                        <div class="rating" title="Рейтинг: Без оценки">
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="review-card__preheader">
                                Отзыв:
                            </p>
                            <div class="review-card__bottom">
                                <div class="review-card__text">
                                    Рыбатекст используется дизайнерами, проектировщиками и фронтендерами, когда нужно быстро заполнить
                                    макеты или прототипы
                                    содержимым. Это тестовый контент, который не должен нести никакого смысла...
                                </div>
                                <a href="#" class="review-card__link">
                                    Читать полностью
                                </a>
                                <div class="photos-list">
                                    <a href="img/content/big-photo1.jpg" data-fancybox="review1" class="photos-list__item">
                                        <img src="img/content/card-photo01.jpg" alt="" />
                                    </a>
                                    <a href="img/content/big-photo2.jpg" data-fancybox="review1" class="photos-list__item">
                                        <img src="img/content/card-photo02.jpg" alt="" />
                                    </a>
                                </div>
                            </div>
                        </article>
                        <article class="review-card rblock1 review-card_1">
                            <div class="review-card__top">
                                <div class="userinfo">
                                    <div class="userinfo__avatar userinfo__avatar_photo">
                                        <img aria-hidden="true" src="img/content/avatar.jpg" alt="" />
                                    </div>
                                    <div class="userinfo__data">
                                        <p class="userinfo__name" title="Очень длинное имя пользователя">Очень длинное имя пользователя
                                        </p>
                                        <p class="userinfo__date">10 февраля 2023</p>
                                        <div class="rating rating_5" title="Рейтинг: Отлично">
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                            <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="review-card__preheader">
                                Отзыв:
                            </p>
                            <div class="review-card__bottom">
                                <div class="review-card__text">
                                    Рыбатекст используется дизайнерами, проектировщиками и фронтендерами, когда нужно быстро заполнить
                                    макеты или прототипы
                                    содержимым. Это тестовый контент, который не должен нести никакого смысла...
                                </div>
                                <a href="#" class="review-card__link">
                                    Читать полностью
                                </a>
                            </div>
                        </article>
                    </div>
                </div>
                <div class="grid1__col-narrow">
                    <div class="filter1 rblock1 rblock1_content2">
                        <button data-action="popupReview" type="button" class="btn btn_primary btn_wide">Оставить отзыв</button>
                        <form>
                            <ul class="checklist">
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_sort" value="1" checked>
																<span class="radio__visual"></span>
															</span>
                                        <span class="checkbox-text__label">Сначала новые</span>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_sort" value="2">
																<span class="radio__visual"></span>
															</span>
                                        <span class="checkbox-text__label">Сначала полезные</span>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_sort" value="3">
																<span class="radio__visual"></span>
															</span>
                                        <span class="checkbox-text__label">Сначала с высокой оценкой</span>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_sort" value="4">
																<span class="radio__visual"></span>
															</span>
                                        <span class="checkbox-text__label">Сначала с низкой оценкой</span>
                                    </label>
                                </li>
                            </ul>
                            <hr class="hr1">
                            <ul class="checklist">
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_stars" value="5" checked>
																<span class="radio__visual"></span>
															</span>
                                        <div class="checkbox-text__label">
                                            <div class="rating rating_5" title="Рейтинг: Отлично">
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                            </div>
                                            &nbsp;
                                            (3 отзыва)
                                        </div>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_stars" value="4">
																<span class="radio__visual"></span>
															</span>
                                        <div class="checkbox-text__label">
                                            <div class="rating rating_4" title="Рейтинг: Хорошо">
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                            </div>
                                            &nbsp;
                                            (5 отзыва)
                                        </div>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_stars" value="3">
																<span class="radio__visual"></span>
															</span>
                                        <div class="checkbox-text__label">
                                            <div class="rating rating_3" title="Рейтинг: Нормально">
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                            </div>
                                            &nbsp;
                                            (2 отзыва)
                                        </div>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_stars" value="2">
																<span class="radio__visual"></span>
															</span>
                                        <div class="checkbox-text__label">
                                            <div class="rating rating_2" title="Рейтинг: Плохо">
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                                <svg class="rating__star" aria-hidden="true" width="16" height="15">
                                                    <use xlink:href="img/sprite.svg#star1"></use>
                                                </svg>
                                            </div>
                                            &nbsp;
                                            (0 отзывов)
                                        </div>
                                    </label>
                                </li>
                                <li class="checklist__item">
                                    <label class="checkbox-text">
															<span class="radio">
																<input class="radio__input" type="radio" name="reviews_stars" value="0" checked>
																<span class="radio__visual"></span>
															</span>
                                        <span class="checkbox-text__label">Любой</span>
                                    </label>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
            <template id="popupTpl_review">
                <swal-title>Оставить отзыв</swal-title>
                <swal-html>
                    <form data-validate action="" class="form-grid2">
                        <div class="form-grid2__row">
                            <div class="form-group">
                                <label class="form-group__label">Ваша оценка</label>
                                <div data-rating class="rating-select">
                                    <input data-rating-input class="rating-select__input" name="rating" value="" type="hidden">
                                    <div class="rating">
                                        <button type="button" data-rating-star="5" class="rating-select__item" title="Отлично">
                                            <svg class="rating__star" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </button>
                                        <button type="button" data-rating-star="4" class="rating-select__item" title="Хорошо">
                                            <svg class="rating__star" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </button>
                                        <button type="button" data-rating-star="3" class="rating-select__item" title="Нормально">
                                            <svg class="rating__star" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </button>
                                        <button type="button" data-rating-star="2" class="rating-select__item" title="Плохо">
                                            <svg class="rating__star" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </button>
                                        <button type="button" data-rating-star="1" class="rating-select__item" title="Очень плохо">
                                            <svg class="rating__star" width="16" height="15">
                                                <use xlink:href="img/sprite.svg#star1"></use>
                                            </svg>
                                        </button>
                                    </div>
                                    <span class="rating-select__label">&mdash;</span>
                                    <span data-rating-label class="rating-select__label">Без оценки</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-grid2__row">
                            <div class="form-group">
                                <label class="form-group__label form-group__label_req">Ф.И.О.</label>
                                <div data-container class="form-group__inner">
                                    <input class="field-input1 form-group__field" type="text" name="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-grid2__row">
                            <div class="form-group">
                                <label class="form-group__label">E-mail</label>
                                <input class="field-input1 form-group__field" type="email" name="email">
                            </div>
                        </div>
                        <div class="form-grid2__row">
                            <div class="form-group">
                                <label class="form-group__label form-group__label_req">Текст отзыва</label>
                                <textarea class="field-input1 form-group__field" name="text" required></textarea>
                            </div>
                        </div>
                        <div class="form-grid2__row">
                            <label class="field-input1 field-file1">
                                <input class="field-file1__input" type="file">
                                <svg class="field-file1__icon" aria-hidden="true">
                                    <use xlink:href="img/sprite.svg#file1"></use>
                                </svg>
                                <span>Прикрепить файл</span>
                            </label>
                        </div>
                        <div class="form-grid2__row popup__text2">
                            <p>Нажимая на кнопку «Опубликовать отзыв», Вы соглашаетесь на <a target="_blank" href="#">обработку персональных данных</a> и с <a target="_blank" href="#">публичной офертой</a></p>
                        </div>
                        <div class="form-grid2__row form-grid2__row_end">
                            <button type="submit" class="swal2-confirm btn btn_primary btn_wide btn_submit">Опубликовать отзыв</button>
                        </div>
                    </form>
                </swal-html>
            </template>

        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail4" class="tabs1__content" data-tab-content="4">
            <div class="tabs2" data-container>
                <div class="heading-cols1">
                    <h2 class="title2">Наличие в магазинах</h2>
                    <div>
                        <ul class="tabs-nav3" role="tablist">
                            <li class="tabs-nav3__item" role="presentation">
                                <button data-action="contactsMode" data-tab-btn="list" class="btn-iconed btn-iconed_active" type="button" role="tab" aria-selected="true">
                                    <svg aria-hidden="true" width="17" height="16">
                                        <use xlink:href="img/sprite.svg#list1"></use>
                                    </svg>
                                    <span>Списком</span>
                                </button>
                            </li>
                            <li class="tabs-nav3__item" role="presentation">
                                <button data-action="contactsMode" data-tab-btn="map" class="btn-iconed" type="button" role="tab" aria-selected="false">
                                    <svg aria-hidden="true" width="17" height="16">
                                        <use xlink:href="img/sprite.svg#map1"></use>
                                    </svg>
                                    <span>На карте</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div tabindex="0" data-tab-content="list" class="tabs2__content tabs2__content_active">
                    <div class="data-table2 data-table2_contacts">
                        <div class="data-table2__row">
                            <div class="data-table2__cell">Адрес</div>
                            <div class="data-table2__cell">Наличие</div>
                            <div class="data-table2__cell">Режим работы</div>
                        </div>
                        <div class="data-table2__row">
                            <div class="data-table2__cell contacts-item">
                                <p class="contacts-item__addr">г. Москва, ул. 3-я Трудовая, 106</p>
                                <ul class="contacts-item__list">
                                    <li>
                                        <a class="contacts-item__tel" href="tel:+79251234567">+7 (925) 123-45-67</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="data-table2__cell">
                                <p class="text-status">В наличии 100 шт</p>
                            </div>
                            <div class="data-table2__cell">
                                Пн - Вс: 10:00 - 22:00
                            </div>
                        </div>
                        <div class="data-table2__row">
                            <div class="data-table2__cell contacts-item">
                                <p class="contacts-item__addr">г. Москва, ул. Кошкина, 122</p>
                                <ul class="contacts-item__list">
                                    <li>
                                        <a class="contacts-item__tel" href="tel:+79251234567">+7 (925) 123-45-67</a>
                                    </li>
                                    <li>
                                        <a class="contacts-item__tel" href="tel:+79251234567">+7 (925) 123-45-67</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="data-table2__cell">
                                <p class="text-status">В наличии 100 шт</p>
                            </div>
                            <div class="data-table2__cell">
                                Пн - Вс: 10:00 - 22:00
                            </div>
                        </div>
                        <div class="data-table2__row">
                            <div class="data-table2__cell contacts-item">
                                <p class="contacts-item__addr">г. Санкт-Петербург, Невский проспект, 35</p>
                                <ul class="contacts-item__list">
                                    <li>
                                        <a class="contacts-item__tel" href="tel:+79251234567">+7 (925) 123-45-67</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="data-table2__cell">
                                <p class="text-status">В наличии 100 шт</p>
                            </div>
                            <div class="data-table2__cell">
                                Пн - Вс: 10:00 - 22:00
                            </div>
                        </div>
                    </div>
                </div>
                <div tabindex="0" data-tab-content="map" class="tabs2__content">
                    <div class="map-points">
                        <div class="map-points__list">
                            <div class="scrollable scrollable_top">
                                <div class="scrollable__inner">
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="1" data-map-point-lat="55.64737392024934" data-map-point-long="37.52380371093751">
                                        <span class="contacts-item__addr">г. Москва, ул. 3-я Трудовая, 106</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="2" data-map-point-lat="55.657446674862335" data-map-point-long="37.72155761718751">
                                        <span class="contacts-item__addr">г. Москва, ул. Кошкина, 122</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="3" data-map-point-lat="55.69693784746126" data-map-point-long="37.62817382812501">
                                        <span class="contacts-item__addr">г. Санкт-Петербург, Невский проспект, 35</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="4" data-map-point-lat="55.70235509327093" data-map-point-long="37.53204345703126">
                                        <span class="contacts-item__addr">г. Москва, ул. 3-я Трудовая, 106</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="5" data-map-point-lat="55.721696271058356" data-map-point-long="37.45925903320313">
                                        <span class="contacts-item__addr">г. Москва, ул. Кошкина, 122</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="6" data-map-point-lat="55.767303495700936" data-map-point-long="37.46749877929688">
                                        <span class="contacts-item__addr">г. Москва, ул. 3-я Трудовая, 106</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="7" data-map-point-lat="55.78815682273133" data-map-point-long="37.57598876953126">
                                        <span class="contacts-item__addr">г. Москва, ул. Кошкина, 122</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="8" data-map-point-lat="55.86221164929645" data-map-point-long="37.67898559570313">
                                        <span class="contacts-item__addr">г. Санкт-Петербург, Невский проспект, 35</span>
                                        <ul class="map-points-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="9" data-map-point-lat="55.78970106974952" data-map-point-long="37.76550292968751">
                                        <span class="contacts-item__addr">г. Москва, ул. 3-я Трудовая, 106</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="map-points-item contacts-item" tabindex="0" role="button" data-map-item data-map-point-id="10" data-map-point-lat="55.74102787471819" data-map-point-long="37.821807861328125">
                                        <span class="contacts-item__addr">г. Москва, ул. Кошкина, 122</span>
                                        <ul class="contacts-item__list">
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <a class="contacts-item__tel" href="tel:+7 (925) 123-45-67">+7 (925) 123-45-67</a>
                                            </li>
                                            <li>
                                                <p class="text-status">В наличии 100 шт</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="contactsMap" class="map-points__map"></div>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail5" class="tabs1__content" data-tab-content="5">
            <div class="grid5">
                <div data-container class="video">
                    <img class="video__cover" src="https://i3.ytimg.com/vi/fgniwAG76_0/maxresdefault.jpg" alt="">
                    <button type="button" data-action="youtube" data-id="fgniwAG76_0" class="video__play">
                        <svg aria-hidden="true" width="22" height="24">
                            <use xlink:href="img/sprite.svg#play1"></use>
                        </svg>
                    </button>
                </div>
                <div class="video">
                    <video data-videojs class="video-js" controls preload="auto" width="1280" height="720" data-setup="{}">
                        <source src="img/content/video.mp4" type="video/mp4" />
                    </video>
                </div>
                <div data-container class="video">
                    <img class="video__cover" src="https://i3.ytimg.com/vi/iHsguNSUewM/maxresdefault.jpg" alt="">
                    <button type="button" data-action="youtube" data-id="iHsguNSUewM" class="video__play">
                        <svg aria-hidden="true" width="22" height="24">
                            <use xlink:href="img/sprite.svg#play1"></use>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail6" class="tabs1__content" data-tab-content="6">
            <div class="textblock textblock_small">
                <p>Оплачивайте покупки удобным способом. В интернет-магазине доступно 3 варианта оплаты:</p>
                <ol>
                    <li>Наличные при самовывозе или доставке курьером. Специалист свяжется с вами в день доставки, чтобы уточнить время и заранее подготовить сдачу с любой купюры. Вы подписываете товаросопроводительные документы, вносите денежные средства, получаете товар и чек.</li>
                    <li>Безналичный расчет при самовывозе или оформлении в интернет-магазине: карты Visa и MasterCard. Чтобы оплатить покупку, система перенаправит вас на сервер системы ASSIST. Здесь нужно ввести номер карты, срок действия и имя держателя.</li>
                    <li>Электронные системы при онлайн-заказе: PayPal, WebMoney и Яндекс.Деньги. Для совершения покупки система перенаправит вас на страницу платежного сервиса. Здесь необходимо заполнить форму по инструкции.</li>
                </ol>
            </div>
        </div>
        <div role="tabpanel" tabindex="0" aria-labelledby="catalogDetail7" class="tabs1__content" data-tab-content="7">
            <div class="textblock textblock_small">
                <p>Экономьте время на получении заказа. В интернет-магазине доступно 4 варианта доставки:</p>
                <ol>
                    <li>Курьерская доставка работает с 9.00 до 19.00. Когда товар поступит на склад, курьерская служба свяжется для уточнения деталей. Специалист предложит выбрать удобное время доставки и уточнит адрес. Осмотрите упаковку на целостность и соответствие указанной комплектации.</li>
                    <li>Самовывоз из магазина. Список торговых точек для выбора появится в корзине. Когда заказ поступит на склад, вам придет уведомление. Для получения заказа обратитесь к сотруднику в кассовой зоне и назовите номер.</li>
                    <li>Постамат. Когда заказ поступит на точку, на ваш телефон или e-mail придет уникальный код. Заказ нужно оплатить в терминале постамата. Срок хранения — 3 дня.</li>
                    <li>Почтовая доставка через почту России. Когда заказ придет в отделение, на ваш адрес придет извещение о посылке. Перед оплатой вы можете оценить состояние коробки: вес, целостность. Вскрывать коробку самостоятельно вы можете только после оплаты заказа. Один заказ может содержать не больше 10 позиций и его стоимость не должна превышать 100 000 р.</li>
                </ol>
            </div>
        </div>
    </div>
</div>