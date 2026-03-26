const AW = {};

$.validator.addMethod(
  "mobileRu",
  function (phone_number, element) {
    const ruPhone_number = phone_number.replace(/\(|\)|\s+|-/g, "");
    return (
      this.optional(element) ||
      (ruPhone_number.length > 9 &&
        /^((\+7|7|8)+([0-9]){10})$/.test(ruPhone_number))
    );
  },
  "Please specify a valid mobile number.",
);

AW.FANCYBOX_DEFAULTS = {
  hideScrollbar: false,
  Hash: false,
  Thumbs: {
    type: "classic",
  },
  Toolbar: {
    display: {
      left: ["infobar"],
      middle: ["zoomIn", "zoomOut"],
      right: ["close"],
    },
  },
};

AW.modal = new HystModal({
  linkAttributeName: "data-hystmodal",
  closeOnOverlay: false,
  afterClose: (modal) => {
    switch ($(modal.element).attr('id')) {
      case 'modalConfirm': {

        break;
      }
    }
  },
});

AW.initMask = function ($field) {
  const type = $field.attr("data-mask");
  switch (type) {
    case "phone":
      mask = IMask($field[0], {
        mask: "+{7} (000) 000-00-00",
        lazy: true,
        placeholderChar: "_",
      });
      $field.on("focus", () => {
        if (mask.value === "") mask.value = "+7 ";
      });
      break;
  }
};

AW.validateForm = function ($el) {
  if ($el.length === 0) return;

  const validator = $el.validate({
    ignore: [],
    errorClass: "form-group__error",
    errorPlacement: function (error, element) {
      const $parent = $(element).closest(".form-group").length
        ? $(element).closest(".form-group")
        : $(element).closest(".form-group1");
      $parent.append(error);
    },
    highlight: function (element) {
      const $parent = $(element).closest(".form-group").length
        ? $(element).closest(".form-group")
        : $(element).closest(".form-group1");
      $parent.addClass("form-group_error");
    },
    unhighlight: function (element) {
      const $parent = $(element).closest(".form-group").length
        ? $(element).closest(".form-group")
        : $(element).closest(".form-group1");
      $parent.removeClass("form-group_error");
    },
    submitHandler: function (form, event) {
      event.preventDefault();
      const trigger = $el.attr("data-onsubmit-trigger");
      if (trigger) {
        $(document).trigger(trigger, { event, form });
      } else {
        form.submit();
      }
    },
  });

  $el.find(".field-input1, .checkbox__input, select").each(function () {
    if ($(this).is(":required")) {
      if ($(this).attr("name") === "agreement") {
        $(this).rules("add", {
          required: true,
          messages: {
            required: "Вы должны согласиться",
          },
        });
      } else {
        $(this).rules("add", {
          required: true,
          messages: {
            required: "Заполните это поле",
          },
        });
      }
    }

    if ($(this).attr("data-type") === "phone") {
      $(this).rules("add", {
        mobileRu: true,
        messages: {
          mobileRu: "Неверный формат",
        },
      });
    }

    if ($(this).attr("data-type") === "email") {
      $(this).rules("add", {
        email: true,
        messages: {
          email: "Неверный формат",
        },
      });
    }
  });

  return validator;
};

/**
 * Показ модального окна с текстовым уведомлением
 * @param {string} title Заголовок окна
 * @param {string} text Текст в окне
 * @param {string} btnText Текст в кнопке
 */
AW.showModalNotification = function(title, text, btnText = 'Закрыть') {
  $('#modalNotification [data-title]').html(title);
  $('#modalNotification [data-text]').html(text);
  $('#modalNotification [data-btn]').html(btnText);
  if (AW.modal && typeof AW.modal.open === 'function') {
    AW.modal.open("#modalNotification");
  } else {
    console.error('AW.modal is not defined or does not have an open method');
  }
}

AW.initSwiperProducts = function ($el) {
  const $navNext = $el
    .closest('[data-swiper="container"]')
    .find(".swiper-nav_next");
  const $navPrev = $el
    .closest('[data-swiper="container"]')
    .find(".swiper-nav_prev");
  return new Swiper($el[0], {
    loop: false,
    spaceBetween: 6,
    slidesPerView: 'auto',
    speed: 300,
    navigation: {
      nextEl: $navNext[0],
      prevEl: $navPrev[0],
    },
    breakpoints: {
      600: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1000: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      1280: {
        slidesPerView: 4,
        spaceBetween: 30,
      },
    },
  });
};

AW.initSwiperGallery = function ($el) {
  const $navNext = $el.find(".swiper-nav_next");
  const $navPrev = $el.find(".swiper-nav_prev");
  return new Swiper($el[0], {
    loop: true,
    spaceBetween: 0,
    slidesPerView: 1,
    speed: 300,
    navigation: {
      nextEl: $navNext[0],
      prevEl: $navPrev[0],
    }
  });
};

AW.initDetailSwipers = function ($container) {
  const $scope = $container && $container.length ? $container : $(document);
  const $preview = $scope.find('[data-swiper="preview"]').first();
  const $photos = $scope.find('[data-swiper="photos"]').first();

  if (!$preview.length || !$photos.length) {
    return;
  }

  const previewSlider = new Swiper($preview[0], {
    loop: false,
    spaceBetween: 12,
    slidesPerView: 5,
    watchSlidesProgress: true,
    direction: "horizontal",
    freeMode: true,
    watchSlidesProgress: true,
  });
  const photosSlider = new Swiper($photos[0], {
    loop: true,
    spaceBetween: 0,
    slidesPerView: 1,
    thumbs: {
      swiper: previewSlider,
    },
    pagination: {
      el: $photos.closest(".swiper-photos-wrapper").find(".swiper-pagination")[0],
      type: "fraction",
    },
    navigation: {
      nextEl: $photos.closest(".swiper-photos-wrapper").find(".swiper-nav_next")[0],
      prevEl: $photos.closest(".swiper-photos-wrapper").find(".swiper-nav_prev")[0],
    },
  });
};

$(document).ready(() => {
  Fancybox.defaults.Hash = false;
  Fancybox.defaults.l10n = {
    CLOSE: "Закрыть",
    NEXT: "Следующий",
    PREV: "Предыдущий",
    MODAL: "Вы можете закрыть это окно нажав на клавишу ESC",
    ERROR: "Что-то пошло не так, пожалуйста, попробуйте еще раз",
    IMAGE_ERROR: "Изображение не найдено",
    ELEMENT_NOT_FOUND: "HTML элемент не найден",
    AJAX_NOT_FOUND: "Ошибка загрузки AJAX : Не найдено",
    AJAX_FORBIDDEN: "Ошибка загрузки AJAX : Нет доступа",
    IFRAME_ERROR: "Ошибка загрузки страницы",
    ZOOMIN: "Увеличить",
    ZOOMOUT: "Уменьшить",
    TOGGLE_THUMBS: "Галерея",
    TOGGLE_SLIDESHOW: "Слайдшоу",
    TOGGLE_FULLSCREEN: "На весь экран",
    DOWNLOAD: "Скачать",
  };

  Fancybox.bind("[data-fancybox]", AW.FANCYBOX_DEFAULTS);

  // Этот хак помогает избежать прыжков анимации при загрузке страницы
  $("body").removeClass("preload");

  $("[data-mask]").each(function () {
    AW.initMask($(this));
  });

  $("[data-validate]").each(function () {
    AW.validateForm($(this));
  });

  $("[data-select1]").each(function () {
    new TomSelect($(this)[0], {
      controlInput: null,
      create: true,
      render: {
        item: function (data, escape) {
          return `
            <div class="item">
              ${escape(data.text)}
            </div>
          `;
        },
      },
      onInitialize: function () {
        $(this.control).append(`
          <svg aria-hidden="true" width="12" height="8">
            <use xlink:href="/local/templates/master/img/sprite.svg#chevron1"></use>
          </svg>
        `);
      },
    });
  });

  $("[data-expandable-handle]").click(function () {
    const $parent = $(this).closest("[data-expandable]");
    const $accordion = $(this).closest('[data-container="accordion"]');
    if ($parent.attr("data-expandable") === "collapsed") {
      $accordion
        .find('[data-expandable="expanded"] [data-expandable-clip]')
        .css("overflow", "hidden");
      $accordion
        .find('[data-expandable="expanded"]')
        .attr("data-expandable", "collapsed");
      $parent.attr("data-expandable", "expanded");
      setTimeout(() => {
        // Небольшой костыль для ровной работы экспандера
        $parent.find("[data-expandable-clip]").css("overflow", "visible");
      }, 250);
    } else {
      $parent.find("[data-expandable-clip]").css("overflow", "hidden");
      $parent.attr("data-expandable", "collapsed");
    }
  });

  $('[data-swiper="products"]').each(function () {
    AW.initSwiperProducts($(this));
  });

  $('[data-swiper="gallery"]').each(function () {
    AW.initSwiperGallery($(this));
  });

  $(".catalog-detail-wrapper").each(function () {
    AW.initDetailSwipers($(this));
  });

  $(".btn-catalog").on("click", function () {
    $(".header").toggleClass("header_catalog");
  });

  $(".menu-catalog__nav1 a").on("click", function (event) {
    event.preventDefault();
    $(".menu-catalog__nav1 a").removeClass("active");
    $(this).addClass("active");
    const category = $(this).attr("data-category");
    if (category) {
      console.log(category);
      $(".menu-catalog__category").removeClass("active");
      $(`.menu-catalog__category[data-category="${category}"]`).addClass(
        "active",
      );
    }
  });

  $("body").on("click", function (event) {
    if (
      $(event.target).closest(".phone-selector__dd").length === 0 &&
      !$(event.target).hasClass("phone-selector__dd") &&
      !$(event.target).hasClass("btn-chevron") &&
      $(event.target).closest(".btn-chevron").length === 0
    ) {
      $('.phone-selector').removeClass('active');
    }

    if (
      $(event.target).closest(".menu-catalog").length === 0 &&
      !$(event.target).hasClass("menu-catalog") &&
      !$(event.target).hasClass("btn-catalog") &&
      $(event.target).closest(".btn-catalog").length === 0
    ) {
      $(".header").removeClass("header_catalog");
    }

    if (
      $(event.target).closest(".menu-catalog").length === 0 &&
      !$(event.target).hasClass("menu-catalog") &&
      !$(event.target).hasClass("header__burger") &&
      $(event.target).closest(".header__burger").length === 0
    ) {
      $(".header").removeClass("header_menu-mob");
    }
  });

  $(".table-variants").on("mouseover", ".table-variants__item", function () {
    const $parentTd = $(this).closest("td");
    const $parentTr = $(this).closest("tr");
    $(".table-variants td").removeClass("target");
    if ($parentTd.index() !== 0 && $parentTr.index() !== 0) {
      $parentTd.addClass("target");
      $(".table-variants table td").each(function () {
        if (
          ($(this).index() === $parentTd.index() &&
            $(this).closest("tr").index() <= $parentTr.index()) ||
          ($(this).closest("tr").index() === $parentTr.index() &&
            $(this).index() <= $parentTd.index())
        ) {
          $(this).addClass("highlighted");
        } else {
          $(this).removeClass("highlighted");
        }
      });
    }
  });

  $(".table-variants").on("mouseleave", function () {
    $(".table-variants td").removeClass("target");
    $(".table-variants td").removeClass("highlighted");
  });

  $("body").on("click", "[data-action]", function (event) {
    const alias = $(this).attr("data-action");

    switch (alias) {
      case "tab1": {
        const $container = $(this).closest('[data-tab="container"]');
        const alias = $(this).attr("data-alias");
        if ($container.length && alias) {
          $container
            .find('[data-action="tab1"], [data-tab="content"]')
            .removeClass("active");
          $(this).addClass("active");
          $container
            .find(`[data-tab="content"][data-alias="${alias}"]`)
            .addClass("active");
        }
        break;
      }

      case "togglePhones": {
        $(this).closest('.phone-selector').toggleClass('active');
        break;
      }

      case "showMenuMob": {
        $('.header').addClass('header_menu-mob');
        break;
      }

      case "hideMenuMob": {
        $('.header').removeClass('header_menu-mob');
        break;
      }

      case "focusSearch": {
        const $search = $('.searchbox__input, .header-search__input');
        if ($search.length) {
          $search.first().focus();
        }
        break;
      }
    }
  });

  const $checkoutBar = $(".checkout-bar");
  const $orderSummary = $(".order-summary");

  function isInViewport($element) {
    if (!$element || !$element.length) return false;

    const offset = $element.offset();
    if (!offset) return false;

    const elementTop = offset.top;
    const elementBottom = elementTop + $element.outerHeight();

    const viewportTop = $(window).scrollTop();
    const viewportBottom = viewportTop + $(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
  }

  function toggleCheckoutBar() {
    if (!$orderSummary.length || !$checkoutBar.length) return;

    if (isInViewport($orderSummary)) {
      $checkoutBar.stop(true, true).fadeOut(200);
    } else {
      $checkoutBar.stop(true, true).fadeIn(200);
    }
  }

  $(function () {
    if (!$orderSummary.length) return;

    toggleCheckoutBar();

    $(window).on("scroll resize", function () {
      toggleCheckoutBar();
    });
  });
});

$(".custom-select").on("click", function () {
  $(this).toggleClass("open");
});

$(".custom-select__item").on("click", function (e) {
  e.stopPropagation();
  const $select = $(this).closest(".custom-select");
  $select.find(".custom-select__placeholder").text($(this).text());
  $select.find('input[type="hidden"]').val($(this).data("value"));
  $select.removeClass("open");
});

$(document).on("click", function (e) {
  if (!$(e.target).closest(".custom-select").length) {
    $(".custom-select.open").removeClass("open");
  }
});

// переключения видимости пароля.
const PasswordToggle = {
  init() {
    this.bindEvents();
  },

  bindEvents() {
    $(".password-toggle__btn--masked").on("click", (e) => {
      this.togglePassword(
        $(e.currentTarget).closest(".form-group1--password"),
        true,
      );
    });

    $(".password-toggle__btn--plain").on("click", (e) => {
      this.togglePassword(
        $(e.currentTarget).closest(".form-group1--password"),
        false,
      );
    });
  },

  togglePassword($group, show) {
    const $input = $group.find(".form-group__field");

    $input.attr("type", show ? "text" : "password");

    $group.find(".password-toggle__btn--masked").toggle(!show);
    $group.find(".password-toggle__btn--plain").toggle(show);
  },
};

// переключения видимости пароля.
const LkAside = {
  init() {
    this.updateMobile();
    this.bindEvents();
  },

  bindEvents() {
    $(window).on("resize", () => this.updateMobile());
    $(".lk-aside__toggle").on("click", () => this.onToggleClick());
  },

  updateMobile() {
    const isMobile = $(window).width() <= 920;
    $(".lk-aside__item").show();

    if (isMobile) {
      const $activeLink = $(".lk-aside__link--active");
      $(".lk-aside__toggle-text").text($activeLink.text());
      $activeLink.closest(".lk-aside__item").hide();
    }
  },

  onToggleClick() {
    $(".lk-aside__nav").slideToggle();
    $(".lk-aside__toggle").toggleClass("lk-aside__toggle--active");
  },
};

const OrderTabs = {
  init() {
    const tabs = document.querySelectorAll(".order-tabs__item");

    tabs.forEach((tab) => {
      tab.addEventListener("click", function () {
        tabs.forEach((t) => t.classList.remove("is-active"));
        this.classList.add("is-active");

        const target = this.dataset.tab;
        document.querySelectorAll(".order-tabs__content").forEach((panel) => {
          panel.classList.toggle("is-active", panel.dataset.tab === target);
        });
      });
    });
  },
};

//открыть/закрыть фильтр в каталоге
$(document).ready(function () {
  ToggleBlock.init();
});

const ToggleBlock = {
  init() {
    this.bind(".filter-btn", ".filter", ".filter__close");

    this.bind(".catalog-header__back", ".catalog-nav", ".catalog-nav__close");
  },

  bind(openBtn, block, closeBtn) {
    const _this = this;

    // открыть
    $(document).on("click", openBtn, function (e) {
      e.preventDefault();
      _this.open(block);
    });

    // закрыть по кнопке
    $(document).on("click", closeBtn, function () {
      _this.close(block);
    });

    // закрыть по клику вне блока
    $(document).on("click", function (e) {
      if (
        $(block).hasClass("active") &&
        !$(e.target).closest(block + ", " + openBtn).length
      ) {
        _this.close(block);
      }
    });
  },

  open(block) {
    $(block).addClass("active");
    $("body").addClass("filter-open");
  },

  close(block) {
    $(block).removeClass("active");
    $("body").removeClass("filter-open");
  },
};

$(document).ready(function () {
  LkAside.init();
  PasswordToggle.init();
  OrderTabs.init();
  ToggleBlock.init();
});
