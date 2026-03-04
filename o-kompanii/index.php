<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("«Люк Мастер.РФ» — российский производитель ревизионных люков, которому доверяют крупные строительные компании и архитектурно-проектные бюро по всей стране.");
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
    <section class="contact-form">
        <div class="container">
            <div class="block-form">
                <form class="container-grid1__inner">
                    <h2 class="title2 title">Остались вопросы?</h2>
                    <p class="desk">Оставьте заявку и наш менеджер свяжется с Вами в ближайшее время!</p>

                    <div class="form-grid1">
                        <div class="form-grid1__row">
                            <p class="form-group1__title">ФИО <span class="req">*</span></p>
                            <div class="form-group1">
                                <input id="name" class="field-input1 form-group1__field" type="text" placeholder="" required="">
                                <label class="form-group1__label" for="name">Введите ФИО</label>
                            </div>
                        </div>

                        <div class="form-grid1__row">
                            <p class="form-group1__title">Телефон <span class="req">*</span></p>
                            <div class="form-group1">
                                <input id="tel" class="field-input3 form-group1__field" type="tel" placeholder=" " required="">
                                <label class="form-group1__label" for="tel">
                                    <b>+7 </b>(999)-99-99</label>
                            </div>
                        </div>

                        <div class="form-grid1__row">
                            <label class="checkbox1">
                                <input type="checkbox" class="checkbox1__input">
                                <span class="checkbox1__box">
												<svg width="14" height="14" aria-hidden="true" class="checkbox1__icon">
													<use xlink:href="/local/templates/master/img/sprite.svg#chevron2"></use>
												</svg>
											</span>
                                <p>Даю согласие на обработку своих <a href="#">персональных данных</a>
                                </p>
                            </label>
                        </div>

                    </div>

                    <div class="btn-form">
                        <button type="submit" class="btn btn_arr btn_primary btn_big">
                            <span>Отправить</span>
                            <svg width="14" height="14" aria-hidden="true">
                                <use xlink:href="/local/templates/master/img/sprite.svg#arrow1"></use>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="block-form__r" style="background-image: url('/local/templates/master/img/contacts.jpg');"></div>
            </div>
        </div>
    </section>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>