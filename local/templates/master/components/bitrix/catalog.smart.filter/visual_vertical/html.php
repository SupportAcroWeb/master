<div class="catalog-filter expanded">
    <button data-action="toggleCatalogFilter" class="btn-toggle" type="button">
        <span>Фильтр подбора</span>
        <svg aria-hidden="true" width="14" height="9">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#chevron2"></use>
        </svg>
    </button>
    <div class="grid1__inner1 catalog-filter__data" style="display: block;">
        <div class="title2">Фильтр</div>
        <button data-action="hideFilter" class="catalog-filter__close">
            <svg aria-hidden="true" width="14" height="14">
                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#close1"></use>
            </svg>
        </button>
        <form action="filter.php" class="filter">
            <div class="filter__items">
                <fieldset class="filter__group">
                    <legend class="filter__title1">Форма пластины</legend>
                    <div class="bx_filter_container_modef">
                        <select data-select class="ts-wrapper_wide" name="filter_field1">
                            <option value="1">Все</option>
                            <option value="2">Круглые</option>
                            <option value="3">Квадратные</option>
                            <option value="4">Прямые</option>
                            <option value="5">Прямые</option>
                            <option value="6">Прямые</option>
                            <option value="7">Прямые</option>
                        </select>
                        <a href="#" class="bx_filter_popup_result">
                            Показать (2)
                        </a>
                    </div>
                </fieldset>
                <fieldset class="filter__group bx_filter_container_modef">
                    <legend class="filter__title1">Размер пластины</legend>
                    <select data-select class="ts-wrapper_wide" name="filter_field2">
                        <option value="1">Все</option>
                        <option value="2">Малые</option>
                        <option value="3">Крохотные</option>
                        <option value="4">Гигантские</option>
                    </select>
                </fieldset>
                <fieldset class="filter__group bx_filter_container_modef">
                    <legend class="filter__title1">Материал обработки</legend>
                    <div data-simplebar class="filter__scroll">
                        <ul class="checklist">
                            <li class="checklist__item">
                                <label class="checkbox-text">
                                                <span class="checkbox1">
                                                    <input class="checkbox1__input" type="checkbox" name="filter_field3" value="1">
                                                    <span class="checkbox1__visual">
                                                        <svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
                                                            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
                                                        </svg>
                                                    </span>
                                                </span>
                                    <span class="checkbox-text__label">Сталь</span>
                                </label>
                            </li>
                            <li class="checklist__item">
                                <label class="checkbox-text">
																<span class="checkbox1">
																	<input class="checkbox1__input" type="checkbox" name="filter_field3" value="2">
																	<span class="checkbox1__visual">
																		<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																			<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																		</svg>
																	</span>
																</span>
                                    <span class="checkbox-text__label">Нержавеющая сталь</span>
                                </label>
                            </li>
                            <li class="checklist__item">
                                <label class="checkbox-text">
																<span class="checkbox1">
																	<input class="checkbox1__input" type="checkbox" name="filter_field3" value="3">
																	<span class="checkbox1__visual">
																		<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																			<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																		</svg>
																	</span>
																</span>
                                    <span class="checkbox-text__label">Сталь</span>
                                </label>
                            </li>
                            <li class="checklist__item">
                                <label class="checkbox-text">
                                    <span class="checkbox1">
                                        <input class="checkbox1__input" type="checkbox" name="filter_field3" value="4">
                                        <span class="checkbox1__visual">
                                            <svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
                                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
                                            </svg>
                                        </span>
                                    </span>
                                    <span class="checkbox-text__label">Нержавеющая сталь</span>
                                </label>
                            </li>
                            <li class="checklist__item">
                                <label class="checkbox-text">
																<span class="checkbox1">
																	<input class="checkbox1__input" type="checkbox" name="filter_field3" value="5">
																	<span class="checkbox1__visual">
																		<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																			<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																		</svg>
																	</span>
																</span>
                                    <span class="checkbox-text__label">Сталь</span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </fieldset>
                <fieldset class="filter__group bx_filter_container_modef">
                    <legend class="filter__title1">Обрабатываемый материал</legend>
                    <div class="filter-search">
                        <div class="filter-search__field">
                            <svg aria-hidden="true" width="15" height="16">
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#search1"></use>
                            </svg>
                            <input type="text" placeholder="Найти">
                        </div>
                        <div class="filter-search__results">
                            <div data-simplebar class="filter__scroll">
                                <ul class="checklist">
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="1">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="2">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Нержавеющая сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="3">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="4">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Нержавеющая сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="5">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="6">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Нержавеющая сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="7">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="8">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Нержавеющая сталь</span>
                                        </label>
                                    </li>
                                    <li class="checklist__item">
                                        <label class="checkbox-text">
																		<span class="checkbox1">
																			<input class="checkbox1__input" type="checkbox" name="filter_field4" value="9">
																			<span class="checkbox1__visual">
																				<svg class="checkbox1__mark" width="12" height="11" aria-hidden="true">
																					<use xlink:href="<?= SITE_TEMPLATE_PATH ?>/img/sprite.svg#mark1"></use>
																				</svg>
																			</span>
																		</span>
                                            <span class="checkbox-text__label">Сталь</span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="filter__buttons1">
                <button class="btn btn_black btn_small" type="submit">Применить</button>
                <button class="btn btn_black btn_hollow btn_small" type="reset">Сбросить</button>
            </div>
        </form>
    </div>
</div>