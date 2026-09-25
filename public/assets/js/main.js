/**
* Select Image
* Button Quantity
* Delete File
* Go Top
* Variant Picker
* Sidebar Mobile
* Stagger Wrap
* Modal Toggle
* Header Sticky
* Auto Popup
* Variant Total Price
* Progress Bar
* Footer Accordion
* Infinite Slider
* Wishlist
* Sidebar Filter
* Estimate Shipping
* Copy Coupon
* Parallax
* Compare Table
* Wishlist Remove
* Toggle Active
* Mobile Menu
* Product Color Swatch
* Custom Dropdown
* Sticky Bottom
* Show Password
* Dashboard Image
* Category Select
* Pin Hover
* Rating
* Checkout Transfer
* Odometer Counter
* Counter
* Bundle Total
* Isotope Filter
* Reveal Animation
* Lookbook Hover
* Notice Popup
* Quick View
* Product Action
* Review Form
* Product Grid Scroll
* Circle Text
* Drag Cursor
* Preloader
 */

(function ($) {
    "use strict";

    /* Select Image
    -------------------------------------------------------------------------*/
    var dropdownSelect = function () {
        if ($(".tf-dropdown-select").length > 0) {
            const selectIMG = $(".tf-dropdown-select");

            selectIMG.find("option").each((idx, elem) => {
                const selectOption = $(elem);
                const imgURL = selectOption.attr("data-thumbnail");
                if (imgURL) {
                    selectOption.attr("data-content", `<img src="${imgURL}" alt="Country" /> ${selectOption.text()}`);
                }
            });
            selectIMG.selectpicker({
                dropupAuto: false
            });
            selectIMG.each(function () {
                if ($(this).hasClass("is-openup")) {
                    $(this).parent().addClass("dropup");
                }
            });
        }
    };

    /* Button Quantity
    -------------------------------------------------------------------------*/
    var btnQuantity = function () {
        $(".minus-btn").on("click", function (e) {
            e.preventDefault();
            var $this = $(this);
            var $input = $this.closest("div").find("input");
            var value = parseInt($input.val(), 10);

            if (value > 1) {
                value = value - 1;
            }
            $input.val(value);
        });

        $(".plus-btn").on("click", function (e) {
            e.preventDefault();
            var $this = $(this);
            var $input = $this.closest("div").find("input");
            var value = parseInt($input.val(), 10);

            if (value > -1) {
                value = value + 1;
            }
            $input.val(value);
        });
    };

    /* Delete File 
    -------------------------------------------------------------------------*/
    var deleteFile = function (e) {
        function updateCount() {
            var count = $(".list-file-delete .file-delete").length;
            $(".prd__count").text(count);
        }

        function updateTotalPrice() {
            var total = 0;

            $(".list-file-delete .tf-mini-cart-item").each(function () {
                var priceText = $(this).find(".tf-mini-card-price").text().replace("$", "").replace(",", "").trim();
                var price = parseFloat(priceText);
                if (!isNaN(price)) {
                    total += price;
                }
            });

            var formatted = total.toLocaleString("en-US", { style: "currency", currency: "USD" });
            $(".tf-totals-total-value").text(formatted);
        }

        function updatePriceEach() {
            $(".each-prd").each(function () {
                var priceText = $(this).find(".each-price").text().replace("$", "").replace(",", "").trim();
                var price = parseFloat(priceText);
                var quantity = parseInt($(this).find(".quantity-product").val(), 10);
                if (!isNaN(price) && !isNaN(quantity)) {
                    var subtotal = price * quantity;
                    var formatted = subtotal.toLocaleString("en-US", { style: "currency", currency: "USD" });
                    $(this).find(".each-subtotal-price").text(formatted);
                }
            });
        }

        function updateTotalPriceEach() {
            var total = 0;
            $(".each-list-prd .each-prd").each(function () {
                var priceText = $(this)
                    .find(".each-subtotal-price")
                    .text()
                    .replace(/[$,]/g, "")
                    .trim();

                var subtotal = parseFloat(priceText);

                if (!isNaN(subtotal)) {
                    total += subtotal;
                }
            });

            var formatted = total.toLocaleString("en-US", {
                style: "currency",
                currency: "USD"
            });

            $(".each-total-price").text(formatted);
            var discount = total + total * 0.1;

            var formattedDiscount = discount.toLocaleString("en-US", {
                style: "currency",
                currency: "USD"
            });

            $(".each-total-price_discount").text(formattedDiscount);
        }


        function checkListEmpty() {
            $(".wrap-empty_text").each(function () {
                var $listEmpty = $(this);
                var $textEmpty = $listEmpty.find(".box-text_empty");
                var $otherChildren = $listEmpty.find(".list-empty").children().not(".box-text_empty");
                var $boxEmpty = $listEmpty.find(".box-empty_clear");
                var $progress = $listEmpty
                    .closest(".popup-shopping-cart")
                    .find(".tf-progress-bar .value");


                if ($otherChildren.length > 0) {
                    $textEmpty.hide();
                } else {
                    $textEmpty.show();
                    $boxEmpty.hide();
                    if ($textEmpty.is(":visible")) {
                        $(".tf-mini-cart-items").css("height", "100%");
                        $progress.css("width", "0%");
                    }
                }
            });
        }

        if ($(".main-list-clear").length) {
            $(".main-list-clear").each(function () {
                var $mainList = $(this);

                $mainList.find(".clear-list-empty").on("click", function () {
                    $mainList.find(".list-empty").children().not(".box-text_empty").remove();
                    checkListEmpty();
                });
            });
        }
        function ortherDel() {
            $(".container .orther-del").remove();
        }
        $(".list-file-delete").on("input", ".quantity-product", function () {
            updateTotalPrice();
        });

        $(".list-file-delete,.each-prd").on("click", ".minus-quantity, .plus-quantity", function () {
            var $quantityInput = $(this).siblings(".quantity-product");
            var currentQuantity = parseInt($quantityInput.val(), 10);

            if ($(this).hasClass("plus-quantity")) {
                $quantityInput.val(currentQuantity + 1);
            } else if ($(this).hasClass("minus-quantity") && currentQuantity > 1) {
                $quantityInput.val(currentQuantity - 1);
            }

            updateTotalPrice();
            updatePriceEach();
            updateTotalPriceEach();
        });

        $(".remove").on("click", function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest(".file-delete").remove();
            updateCount();
            updateTotalPrice();
            checkListEmpty();
            updateTotalPriceEach();
            ortherDel();
        });

        $(".clear-file-delete").on("click", function (e) {
            e.preventDefault();
            $(this).closest(".list-file-delete").find(".file-delete").remove();
            updateCount();
            updateTotalPrice();
            checkListEmpty();
        });
        checkListEmpty();
        updateCount();
        updateTotalPrice();
        updatePriceEach();
        updateTotalPriceEach();
    };

    /* Go Top
    -------------------------------------------------------------------------*/
    var goTop = function () {
        var $goTop = $("#goTop");
        var $borderProgress = $(".border-progress");

        $(window).on("scroll", function () {
            var scrollTop = $(window).scrollTop();
            var docHeight = $(document).height() - $(window).height();
            var scrollPercent = (scrollTop / docHeight) * 100;
            var progressAngle = (scrollPercent / 100) * 360;

            $borderProgress.css("--progress-angle", progressAngle + "deg");

            if (scrollTop > 100) {
                $goTop.addClass("show");
            } else {
                $goTop.removeClass("show");
            }
        });

        $goTop.on("click", function () {
            $("html, body").animate({ scrollTop: 0 }, 0);
        });
    };

    /* Variant Picker
    -------------------------------------------------------------------------*/
    var variantPicker = function () {
        $(document).on("click", ".variant-picker-item .color-btn", function () {
            var $parent = $(this).closest(".variant-picker-item");

            $parent.find(".value-currentColor")
                .text($(this).data("color") || $(this).data("scroll"));
            $parent.find(".color-btn").removeClass("active");
            $(this).addClass("active");
        });

        $(document).on("click", ".variant-picker-item .size-btn", function () {
            var $wrapper = $(this).closest(".variant-picker-item");

            $wrapper.find(".size-btn").removeClass("active");
            $(this).addClass("active");

            $wrapper.find(".value-currentSize")
                .text($(this).data("size"));
        });
    };

    /* Sidebar Mobile
    -------------------------------------------------------------------------*/
    var sidebarMobile = function () {
        if ($(".sidebar-content-wrap").length > 0) {
            var sidebar = $(".sidebar-content-wrap").html();
            $(".sidebar-mobile-append").append(sidebar);
        }
    };

    /* Stagger Wrap
    -------------------------------------------------------------------------*/
    var staggerWrap = function () {
        if ($(".stagger-wrap").length) {
            var count = $(".stagger-item").length;
            for (var i = 1, time = 0.2; i <= count; i++) {
                $(".stagger-item:nth-child(" + i + ")")
                    .css("transition-delay", time * i + "s")
                    .addClass("stagger-finished");
            }
        }
    };

    /* Modal Second
    -------------------------------------------------------------------------*/
    var clickModalSecond = function () {
        $(".show-shopping-cart").on("click", function () {
            $("#shoppingCart").modal("show");
        });
        $(".btn-icon-action.wishlist").on("click", function () {
            $("#wishlist").modal("show");
        });

        $(".btn-add-to-cart").on("click", function () {
            $(".tf-add-cart-success").addClass("active");
        });
        $(".tf-add-cart-success .tf-add-cart-close").on("click", function () {
            $(".tf-add-cart-success").removeClass("active");
        });

        $(".btn-add-note, .btn-estimate-shipping, .btn-add-gift,.btn-add-coupon").on("click", function () {
            var classList = {
                "btn-add-note": ".add-note",
                "btn-estimate-shipping": ".estimate-shipping",
                "btn-add-gift": ".add-gift",
                "btn-add-coupon": ".add-coupon",
            };

            $.each(classList, function (btnClass, targetClass) {
                if ($(event.currentTarget).hasClass(btnClass)) {
                    $(targetClass).addClass("open");
                }
            });
        });

        $(".btn-checkout-note, .btn-checkout-shipping, .btn-checkout-gift").on("click", function () {
            const map = {
                "btn-checkout-note": ".active-checkout-note",
                "btn-checkout-shipping": ".active-checkout-shipping",
                "btn-checkout-gift": ".active-checkout-gift",
            };

            const $btn = $(this);
            let target = null;

            Object.keys(map).forEach(cls => {
                if ($btn.hasClass(cls)) {
                    target = map[cls];
                }
            });

            if (!target) return;

            const isOpen = $(target).hasClass("open");

            $(".active-checkout-note, .active-checkout-shipping, .active-checkout-gift").removeClass("open");

            if (!isOpen) {
                $(target).addClass("open");
            }
        });

        $(".tf-mini-cart-tool-close").on("click", function () {
            $(".tf-mini-cart-tool-openable").removeClass("open");
        });
    };

    /* Header Sticky
    -------------------------------------------------------------------------*/
    var headerSticky = function () {
        const customHeaderCategory = () => {
            const header = document.querySelector(".tf-header");

            if (!header || !header.classList.contains("has-by-category")) {
                return null;
            }

            const headerBottom = header.querySelector(".header-bottom_wrap");
            const btnOpen = header.querySelector(".btn-open-header-bottom");

            if (!headerBottom || !btnOpen) return null;

            btnOpen.addEventListener("click", () => {
                headerBottom.classList.toggle("hide");
            });

            return {
                hideHeaderBottom: () => headerBottom.classList.add("hide"),
                showHeaderBottom: () => headerBottom.classList.remove("hide"),
            };
        };

        const S3 = customHeaderCategory();

        let lastScrollTop = 0;
        let delta = 5;
        let navbarHeight = $("header").outerHeight();
        let didScroll = false;

        $(window).scroll(function () {
            didScroll = true;
        });

        setInterval(function () {
            if (didScroll) {
                let st = $(window).scrollTop();
                navbarHeight = $("header").outerHeight();

                if (st > navbarHeight) {

                    if (st > lastScrollTop + delta) {

                        $("header").css("top", `-${navbarHeight}px`);
                        $(".sticky-top,.sticky-md-top").css("top", "15px");
                        $(".sticky-top.no-offset").css("top", "0");

                        if (S3) S3.hideHeaderBottom();

                    } else if (st < lastScrollTop - delta) {

                        if ($("header").hasClass("offset-top")) {
                            $("header").css("top", "15px");
                        } else {
                            $("header").css("top", "0");
                        }

                        $("header").addClass("header-sticky");
                        $(".sticky-top,.sticky-md-top").css("top", `${30 + navbarHeight}px`);
                        $(".sticky-top.no-offset").css("top", `${0 + navbarHeight}px`);


                    }

                } else {

                    $("header").css("top", "unset");
                    $("header").removeClass("header-sticky");
                    $(".sticky-top").css("top", "15px");
                    $(".sticky-top.no-offset").css("top", "0");

                    if (S3) S3.showHeaderBottom();
                }

                lastScrollTop = st;
                didScroll = false;
            }
        }, 250);
    };

    /* Auto Popup
    -------------------------------------------------------------------------*/
    var autoPopup = function () {
        const showPopup = JSON.parse(sessionStorage.getItem("showPopup"));

        if (!showPopup) {
            setTimeout(function () {
                if ($(".auto-popup").length) {
                    $(".auto-popup").modal("show");
                }

                if ($(".auto-popup-offcanvas").length) {
                    $(".auto-popup-offcanvas").each(function () {
                        const offcanvas =
                            bootstrap.Offcanvas.getOrCreateInstance(this);
                        offcanvas.show();
                    });
                }
            }, 2000);
        }

        $(".btn-hide-popup").on("click", function () {
            sessionStorage.setItem("showPopup", true);
        });
    };

    /* Total Price Variant
    -------------------------------------------------------------------------*/
    var totalPriceVariant = function () {
        $("#tfProductInfoWrap").each(function () {
            var productItem = $(this);
            var priceEl = productItem.find(".price-on-sale");
            var quantityInput = productItem.find(".tf-product-variant .quantity-product");
            if (!priceEl.data("price")) {
                var initialPrice = parseFloat(priceEl.text().replace("$", "").replace(/,/g, ""));
                priceEl.data("price", initialPrice);
            }
            productItem.find(".size-btn").on("click", function () {
                var rawPrice = $(this).attr("data-price");
                var newPrice = parseFloat(rawPrice.replace(/,/g, "")) || basePrice;
                quantityInput.val(1);
                productItem.find(".price-on-sale")
                    .text(`$${newPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`)
                    .data("price", newPrice);
                updateTotalPrice(newPrice, productItem);
            });
            productItem.find(".color-btn").on("click", function () {
                var rawPrice = $(this).attr("data-price");
                if (!rawPrice) return;

                var newPrice = parseFloat(rawPrice.replace(/,/g, ""));
                quantityInput.val(1);

                productItem.find(".price-on-sale")
                    .text(`$${newPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`)
                    .data("price", newPrice);

                updateTotalPrice(newPrice, productItem);
            });
            productItem.find(".btn-increase").on("click", function () {
                var currentQuantity = parseInt(quantityInput.val(), 10);
                quantityInput.val(currentQuantity + 1);
                updateTotalPrice(null, productItem);
            });

            productItem.find(".btn-decrease").on("click", function () {
                var currentQuantity = parseInt(quantityInput.val(), 10);
                if (currentQuantity > 1) {
                    quantityInput.val(currentQuantity - 1);
                    updateTotalPrice(null, productItem);
                }
            });

            function updateTotalPrice(price, scope) {
                var currentPrice = price || parseFloat(scope.find(".price-on-sale").data("price"));
                var quantity = parseInt(scope.find(".quantity-product").val(), 10);

                var totalPrice = currentPrice * quantity;
                scope.find(".price-add").text(
                    `$${totalPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`
                );

                var oldPrice = currentPrice * 1.2;
                scope.find(".price-on-old").text(
                    `$${oldPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`
                );
            }
            updateTotalPrice(null, productItem);
        });
    };
    /* Handle Progress
    -------------------------------------------------------------------------*/
    var handleProgress = function () {
        if ($(".progress-cart,.progress-sold").length > 0) {
            var progressValue = $(".progress-cart .value,.progress-sold .value").data("progress");
            setTimeout(function () {
                $(".progress-cart .value,.progress-sold .value").css("width", progressValue + "%");
            }, 800);
        }

        function handleProgressBar(showEvent, hideEvent, target) {
            $(target).on(hideEvent, function () {
                $(".tf-progress-bar .value").css("width", "0%");
            });

            $(target).on(showEvent, function () {
                setTimeout(function () {
                    var progressValue = $(".tf-progress-bar .value").data("progress");
                    $(".tf-progress-bar .value").css("width", progressValue + "%");
                }, 600);
            });
        }

        if ($(".popup-shopping-cart").length > 0) {
            handleProgressBar("show.bs.offcanvas", "hide.bs.offcanvas", ".popup-shopping-cart");
        }

        if ($(".popup-shopping-cart").length > 0) {
            handleProgressBar("show.bs.modal", "hide.bs.modal", ".popup-shopping-cart");
        }
    };

    /* Handle Footer
    -------------------------------------------------------------------------*/
    var handleFooter = function () {
        var footerAccordion = function () {
            var args = { duration: 250 };
            $(".footer-heading-mobile").on("click", function () {
                var $parent = $(this).parent(".footer-col-block");
                var $content = $(this).next();

                $parent.toggleClass("open");

                if (!$parent.hasClass("open")) {
                    $content.slideUp(args);
                } else {
                    $content.slideDown(args);
                }
            });
        };

        function handleAccordion() {
            if (window.matchMedia("only screen and (max-width: 575px)").matches) {
                if (!$(".footer-heading-mobile").data("accordion-initialized")) {
                    footerAccordion();
                    $(".footer-heading-mobile").data("accordion-initialized", true);
                }
            } else {
                $(".footer-heading-mobile")
                    .off("click")
                    .removeData("accordion-initialized")
                    .each(function () {
                        $(this).parent(".footer-col-block").removeClass("open").end().next().removeAttr("style");
                    });
            }
        }

        handleAccordion();
        $(window).on("resize", handleAccordion);
    };

    /* Infinite Slide 
    -------------------------------------------------------------------------*/
    var infiniteSlide = function () {
        if ($(".infiniteSlide").length > 0) {
            $(".infiniteSlide").each(function () {
                var $this = $(this);
                var style = $this.data("style") || "left";
                var clone = $this.data("clone") || 2;
                var speed = $this.data("speed") || 50;
                $this.infiniteslide({
                    speed: speed,
                    direction: style,
                    clone: clone,
                });
            });
        }
    };

    /* Add Wishlist
    -------------------------------------------------------------------------*/
    var addWishList = function () {
        $(".btn-add-wishlist,.btn-wishlist, .card-product .wishlist").on("click", function (e) {
            e.preventDefault();
            let $this = $(this);
            let icon = $this.find(".icon");
            let tooltip = $this.find(".tooltip");
            let textWish = $this.find(".text-wish");

            $this.toggleClass("addwishlist");

            if ($this.hasClass("addwishlist")) {
                icon.removeClass("icon-Hearth2").addClass("icon-Trash");
                tooltip.text("Remove Wishlist");
                textWish.text("Remove Wishlist");
            } else {
                icon.removeClass("icon-Trash").addClass("icon-Hearth2");
                tooltip.text("Add to Wishlist");
                textWish.text("Add to Wishlist");

            }
        });
    };

    /* Handle Sidebar Filter 
    -------------------------------------------------------------------------*/
    var handleSidebarFilter = function () {
        $("#filterShop,.sidebar-btn").on("click", function () {
            if ($(window).width() <= 1200) {
                $(".sidebar-filter,.overlay-filter").addClass("show");
            }
        });
        $(".close-filter,.overlay-filter").on("click", function () {
            $(".sidebar-filter,.overlay-filter").removeClass("show");
        });
    };

    /* Estimate Shipping
    -------------------------------------------------------------------------*/
    var unitEstimateShipping = () => {
        $(".estimate-shipping").each(function () {
            const $wrapper = $(this);

            const $countrySelect = $wrapper.find(".shipping-country");
            const $provinceSelect = $wrapper.find(".shipping-province");
            const $zipcodeInput = $wrapper.find(".zipcode");
            const $zipcodeMessage = $wrapper.find(".zipcode-message.error");
            const $zipcodeSuccess = $wrapper.find(".zipcode-message.success");
            const $shippingForm = $wrapper.find(".shipping-form");

            function updateProvinces() {
                const $selectedOption = $countrySelect.find("option:selected");
                const provincesData = $selectedOption.attr("data-provinces");

                let provinces = [];
                try {
                    provinces = JSON.parse(provincesData || "[]");
                } catch (e) { }

                $provinceSelect.empty();

                if (!provinces.length) {
                    $provinceSelect.append(`<option>------</option>`);
                } else {
                    provinces.forEach(function (province) {
                        $provinceSelect.append(
                            `<option value="${province[0]}">${province[1]}</option>`
                        );
                    });
                }
            }

            function validateZipcode(zipcode, country) {
                let regex;

                switch (country) {
                    case "Australia":
                    case "Austria":
                    case "Belgium":
                    case "Denmark":
                        regex = /^\d{4}$/;
                        break;
                    case "Canada":
                        regex = /^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/;
                        break;
                    case "Czech Republic":
                    case "Finland":
                    case "France":
                    case "Germany":
                    case "Mexico":
                    case "South Korea":
                    case "Spain":
                    case "Italy":
                        regex = /^\d{5}$/;
                        break;
                    case "United States":
                        regex = /^\d{5}(-\d{4})?$/;
                        break;
                    case "United Kingdom":
                        regex = /^[A-Za-z]{1,2}\d[A-Za-z\d]? \d[A-Za-z]{2}$/;
                        break;
                    case "India":
                    case "Vietnam":
                        regex = /^\d{6}$/;
                        break;
                    case "Japan":
                        regex = /^\d{3}-\d{4}$/;
                        break;
                    default:
                        return true;
                }

                return regex.test(zipcode);
            }

            $countrySelect.on("change", updateProvinces);

            $shippingForm.on("submit", function (event) {
                event.preventDefault();

                const zipcode = $zipcodeInput.val().trim();
                const country = $countrySelect.val();

                if (!validateZipcode(zipcode, country)) {
                    $zipcodeMessage.show();
                    $zipcodeSuccess.hide();
                } else {
                    $zipcodeMessage.hide();
                    $zipcodeSuccess.show();
                }
            });

            updateProvinces();
        });
    }

    /* Coupon Copy
    -------------------------------------------------------------------------*/
    var textCopy = function () {
        $(".coupon-copy-wrap,.btn-coppy-text").on("click", function () {
            const couponCode = $(this).find(".coupon-code,.coppyText").text().trim();

            if (navigator.clipboard) {
                navigator.clipboard
                    .writeText(couponCode)
                    .then(function () {
                        alert("Copied! " + couponCode);
                    })
                    .catch(function (err) {
                        alert("Unable to copy: " + err);
                    });
            } else {
                const tempInput = $("<input>");
                $("body").append(tempInput);
                tempInput.val(couponCode).select();
                document.execCommand("copy");
                tempInput.remove();
                alert("Copied! " + couponCode);
            }
        });
    };

    /* Parallaxie 
    -------------------------------------------------------------------------*/
    var parallaxieInit = function () {
        var $window = $(window);

        function initParallax() {
            if ($window.width() > 991) {
                $(".parallaxie").each(function () {
                    var $this = $(this);
                    var $img = $this.find(".img-parallaxie");

                    if ($img.length) {
                        $this.css({
                            "background-image": 'url("' + $img.prop("src") + '")',
                        });
                    }

                    if (!$this.hasClass("parallaxie-init")) {
                        $this.parallaxie({
                            speed: 0.55,
                            offset: 0,
                        });

                        $this.addClass("parallaxie-init");
                    }
                });
            }
        }

        if ($(".parallaxie").length) {
            initParallax();

            $window.on("resize", function () {
                if ($window.width() > 991) {
                    initParallax();
                }
            });
        }
    };

    /* Update Compare Empty
    -------------------------------------------------------------------------*/
    var tableCompareRemove = function () {
        $(".remove").on("click", function () {
            let $clickedCol = $(this).closest(".compare-col");
            let colIndex = $clickedCol.index();
            let $rows = $(".compare-row");
            let visibleCols = $(".compare-row:first .compare-col:visible").length;

            if (visibleCols > 4) {
                $rows.each(function () {
                    $(this).find(".compare-col").eq(colIndex).fadeOut(300);
                });
            } else {
                $rows.each(function () {
                    let $cols = $(this).find(".compare-col");
                    let $colToMove = $cols.eq(colIndex);

                    $colToMove.children().fadeOut(300, function () {
                        let $parentRow = $(this).closest(".compare-row");
                        $colToMove.appendTo($parentRow);
                    });
                });
            }
        });
    };

    /* Delete Wishlist
    -------------------------------------------------------------------------*/
    var deleteWishList = function () {

        function checkEmpty() {
            var $sectionWish = $(".section-wishlist");
            var $wishlistInner = $(".wrapper-wishlist");
            var productCount = $wishlistInner.find(".card-product").length;

            var $favorite = $(".wishlist-favorite");
            var $empty = $(".tf-wishlist-empty");

            if (productCount === 0) {
                $empty.show();
                $favorite.show();
                $sectionWish.hide();
            } else {
                $empty.hide();
                $favorite.hide();
            }
        }

        $(".wrapper-wishlist").on("click", ".card-product .remove", function (e) {
            e.preventDefault();
            $(this).closest(".card-product").remove();
            checkEmpty();
        });

        checkEmpty();
    };

    /* Click Active 
    -------------------------------------------------------------------------*/
    var clickActive = function () {
        function isAllowed($container) {
            return !$container.hasClass("active-1600") || window.innerWidth < 1600;
        }

        let previousWidth = window.innerWidth;

        $(window).on("resize", function () {
            const currentWidth = window.innerWidth;

            const crossedBreakpoint = (previousWidth < 1600 && currentWidth >= 1600) || (previousWidth >= 1600 && currentWidth < 1600);

            if (crossedBreakpoint) {
                $(".main-action-active").each(function () {
                    $(this).find(".btn-active, .active-item").removeClass("active");
                });

                if (previousWidth < 1600 && currentWidth >= 1600) {
                    $(".main-action-active.active-1600").each(function () {
                        const $container = $(this);
                        const $btn = $container.find(".btn-active");
                        const $item = $container.find(".active-item");

                        $btn.addClass("active");
                        $item.addClass("active");
                    });
                }
            }

            previousWidth = currentWidth;
        });

        $(".btn-active").on("click", function (event) {
            const $container = $(this).closest(".main-action-active");

            if (!isAllowed($container)) return;

            event.stopPropagation();

            const $activeItem = $container.find(".active-item");
            const isResponsive = $container.hasClass("active-1600") && window.innerWidth < 1600;

            if (isResponsive) {
                $(".main-action-active").each(function () {
                    if (this !== $container[0]) {
                        $(this).find(".btn-active, .active-item").removeClass("active");
                    }
                });
            } else {
                $(".main-action-active").each(function () {
                    const $other = $(this);
                    if (this !== $container[0] && (!$other.hasClass("active-1600") || window.innerWidth < 1600)) {
                        $other.find(".btn-active, .active-item").removeClass("active");
                    }
                });
            }

            $(this).toggleClass("active");
            $activeItem.toggleClass("active");
        });

        $(document).on("click", function (event) {
            const isMobile = window.innerWidth < 1600;

            $(".main-action-active").each(function () {
                const $container = $(this);
                const is1600 = $container.hasClass("active-1600");

                if ((is1600 && isMobile) || !is1600) {
                    if (!$(event.target).closest($container).length) {
                        $container.find(".btn-active, .active-item").removeClass("active");
                    }
                }
            });
        });
        $(document).on("click", ".form-category-search .select-options li:not(.select-options_header)", function () {
            const text = $(this).text().trim();

            $(this)
                .closest(".category-search_select")
                .find(".btn-active .text")
                .text(text);
        });
    };

    /* Handle Mobile Menu
    -------------------------------------------------------------------------*/
    var handleMobileMenu = function () {
        const $desktopMenu = $(".box-nav-menu:not(.not-append)").clone();
        const $mobileMenu = $('<ul class="nav-ul-mb"></ul>');
        const $iconArrow = "ic-custom";
        const $iconArrow2 = "icon-ArrowCaretDown";

        $desktopMenu.find("> li.menu-item").each(function (i, menuItem) {
            const $item = $(menuItem);
            const $topLink = $item.find("> a.item-link").first();

            const text = $topLink
                .find(".text, .cus-text")
                .first()
                .clone()
                .children()
                .remove()
                .end()
                .text()
                .trim();

            const submenu = $item.find("> .sub-menu");
            const id = "dropdown-menu-" + i;
            const isTopActive = $topLink.hasClass("active");
            const topActiveClass = isTopActive ? "active" : "";

            if (!submenu.length) {
                const href = $topLink.attr("href") || "#";
                $mobileMenu.append(`
                <li class="nav-mb-item">
                    <a href="${href}" class="mb-menu-link ${topActiveClass}">
                        <span>${text}</span>
                    </a>
                </li>
            `);
                return;
            }

            const $liMb = $(`
            <li class="nav-mb-item">
                <a href="#${id}" class="collapsed mb-menu-link ${topActiveClass}"
                   data-bs-toggle="collapse" aria-expanded="false" aria-controls="${id}">
                    <span>${text}</span>
                    <span class="icon ${$iconArrow}"></span>
                </a>
                <div id="${id}" class="collapse"></div>
            </li>
        `);

            const $subNav = $('<ul class="sub-nav-menu"></ul>');
            const $groups = submenu.find(".mega-menu-item.menu-lv-2");

            const isHomeMenu = text.toLowerCase() === "home";
            const $homeDemos = $(".modal-demo .row-demo .demo-item a.demo-name");

            if (isHomeMenu && $homeDemos.length) {
                $homeDemos.each(function () {
                    const $a = $(this);
                    const $demoItem = $a.closest(".demo-item");
                    const href = $a.attr("href") || "#";
                    const isSoon = $demoItem.hasClass("soon");
                    const soonClass = isSoon ? "soon" : "";

                    const text = $a.clone().children(".demo-label").remove().end().text().trim();
                    const labelHTML = $demoItem
                        .find(".demo-label")
                        .map(function () {
                            return this.outerHTML;
                        })
                        .get()
                        .join("");

                    $subNav.append(`
                        <li>
                            <a href="${href}" class="sub-nav-link ${soonClass}">
                                ${text}
                                ${labelHTML}
                            </a>
                        </li>
                    `);
                });
            }

            if ($groups.length) {
                $groups.each(function (j) {
                    const $groupWrap = $(this);
                    const heading = $groupWrap.find(".menu-heading").first().text().trim();
                    if (!heading) return;

                    const subId = `${id}-group-${j}`;
                    const hasActiveChild = $groupWrap.find(".sub-menu_link.active").length > 0;
                    const groupActiveClass = hasActiveChild ? "active" : "";

                    const $group = $(`
                    <li>
                        <a href="#${subId}" class="collapsed sub-nav-link ${groupActiveClass}"
                           data-bs-toggle="collapse" aria-expanded="false" aria-controls="${subId}">
                            <span>${heading}</span>
                            <span class="icon ${$iconArrow2}"></span>
                        </a>
                        <div id="${subId}" class="collapse">
                            <ul class="sub-nav-menu sub-menu-level-2"></ul>
                        </div>
                    </li>
                `);

                    $groupWrap.find(".sub-menu_list > li > a.sub-menu_link").each(function () {
                        const $a = $(this);
                        const href = $a.attr("href") || "#";
                        const html = $a.html();
                        const activeClass = $a.hasClass("active") ? "active" : "";

                        if (html && html.trim()) {
                            $group.find(".sub-menu-level-2").append(`
                            <li>
                                <a href="${href}" class="sub-nav-link ${activeClass}">${html}</a>
                            </li>
                        `);
                        }
                    });

                    $subNav.append($group);
                });
            } else {
                submenu.find("> .sub-menu_list > li").each(function (j) {
                    const $li = $(this);
                    const $a = $li.find("> a.sub-menu_link").first();
                    const href = $a.attr("href") || "#";
                    const text = $a.find(".text").length
                        ? $a.find(".text").first().text().trim()
                        : $a.clone().children("i").remove().end().text().trim();

                    const activeClass = $a.hasClass("active") ? "active" : "";
                    const hasLv2 = $li.hasClass("has-menu-lv2");
                    const subIdLv2 = `${id}-lv2-${j}`;

                    if (!hasLv2) {
                        $subNav.append(`
                        <li>
                            <a href="${href}" class="sub-nav-link ${activeClass}">
                                ${text}
                            </a>
                        </li>
                    `);
                    } else {
                        const $group = $(`
                        <li>
                            <a href="#${subIdLv2}" class="collapsed sub-nav-link"
                               data-bs-toggle="collapse" aria-expanded="false" aria-controls="${subIdLv2}">
                                ${text}
                                <span class="icon ${$iconArrow2}"></span>
                            </a>
                            <div id="${subIdLv2}" class="collapse">
                                <ul class="sub-nav-menu sub-menu-level-2"></ul>
                            </div>
                        </li>
                    `);

                        $li.find("> .sub-menu-lv2 > .sub-menu_list > li > a.sub-menu_link").each(function () {
                            const $lv2a = $(this);
                            const lv2Href = $lv2a.attr("href") || "#";
                            const lv2Text = $lv2a.text().trim();
                            const lv2Active = $lv2a.hasClass("active") ? "active" : "";

                            $group.find(".sub-menu-level-2").append(`
                            <li>
                                <a href="${lv2Href}" class="sub-nav-link ${lv2Active}">
                                    ${lv2Text}
                                </a>
                            </li>
                        `);
                        });

                        $subNav.append($group);
                    }
                });
            }

            $liMb.find(`#${id}`).append($subNav);
            $mobileMenu.append($liMb);
        });

        $("#wrapper-menu-navigation").empty().append($mobileMenu);
    };
    /* Color Swatch Product
    -------------------------------------------------------------------------*/
    var swatchProduct = function () {
        if ($(".card-product, .banner-card_product").length > 0) {
            $(".action-swatch").on("click mouseover", function () {
                var $swatch = $(this);
                var swatchColor = $swatch.find("img:not(.swatch-img)").attr("src");
                var imgProduct = $swatch.closest(".card-product, .banner-card_product").find(".img-product");
                var colorLabel = $swatch.find(".color-label").text().trim();
                imgProduct.attr("src", swatchColor);
                $swatch.closest(".card-product, .banner-card_product").find(".quick-variant-color .variant-value").text(colorLabel);
                $swatch.closest(".card-product, .banner-card_product").find(".action-swatch.active").removeClass("active");
                $swatch.addClass("active");
            });
        }
    };

    /* Bottom Sticky
    -------------------------------------------------------------------------*/
    var scrollBottomSticky = function () {
        if (!$("footer").length) return;

        var footerOffset = $("footer").offset().top;

        $(window).on("scroll", function () {
            var addToCart = $(".section-product-single .btn-action-price")[0];
            var myElement = $(".tf-sticky-btn-atc");
            var scrollTopBtn = $(".shopify-section .scroll-top");
            var toolbar = $(".tf-toolbar");

            if (!addToCart) return;

            var rect = addToCart.getBoundingClientRect();

            var scrollBottom = $(window).scrollTop() + $(window).height();

            if (scrollBottom >= footerOffset) {
                myElement.removeClass("show");
                scrollTopBtn.css("bottom", "");
                return;
            }

            if (rect.bottom < 0) {
                myElement.addClass("show");

                if (myElement.hasClass("show")) {
                    var stickyHeight = myElement.outerHeight() + 10;
                    scrollTopBtn.css("bottom", stickyHeight + "px");

                    if (window.matchMedia("(max-width: 1199px)").matches && toolbar.length) {
                        stickyHeight += toolbar.outerHeight();
                    }

                    scrollTopBtn.css("bottom", stickyHeight + "px");
                }
            } else {
                myElement.removeClass("show");
                scrollTopBtn.css("bottom", "");
            }
        });
    };

    /* Show Password 
    -------------------------------------------------------------------------*/
    var showPassword = function () {
        $(".toggle-pass").on("click", function () {
            const wrapper = $(this).closest(".password-wrapper");
            const input = wrapper.find(".password-field");
            const icon = $(this);

            if (input.attr("type") === "password") {
                input.attr("type", "text");
                icon.removeClass("icon-EyeSlice").addClass("icon-EyeStroke");
            } else {
                input.attr("type", "password");
                icon.removeClass("icon-EyeStroke").addClass("icon-EyeSlice");
            }
        });
    };

    /* Hover Pin
    -------------------------------------------------------------------------*/
    var hoverPin = function () {
        $(".tf-lookbook-hover").each(function () {
            const $container = $(this);

            $container.find(".bundle-pin-item").on("mouseover", function () {
                const $hoverWrap = $container.find(".bundle-hover-wrap");
                $hoverWrap.addClass("has-hover");

                const $el = $container.find("." + this.id).show();
                $hoverWrap.find(".bundle-hover-item").not($el).addClass("no-hover");
            });

            $container.find(".bundle-pin-item").on("mouseleave", function () {
                const $hoverWrap = $container.find(".bundle-hover-wrap");
                $hoverWrap.removeClass("has-hover");
                $hoverWrap.find(".bundle-hover-item").removeClass("no-hover");
            });
        });
    };

    /* Rate Click
    -------------------------------------------------------------------------*/
    var rateClick = () => {
        const stars = document.querySelectorAll(".rate-click .icon");
        let selectedRating = 0;

        stars.forEach((star, idx) => {
            star.addEventListener("mouseenter", () => {
                resetHover();
                for (let i = 0; i <= idx; i++) {
                    stars[i].classList.add("hover");
                }
            });

            star.addEventListener("mouseleave", () => {
                resetHover();
            });
            star.addEventListener("click", () => {
                selectedRating = idx + 1;
                resetActive();
                for (let i = 0; i < selectedRating; i++) {
                    stars[i].classList.add("active");
                }
            });
        });

        function resetHover() {
            stars.forEach(s => s.classList.remove("hover"));
        }

        function resetActive() {
            stars.forEach(s => s.classList.remove("active"));
        }
    }

    /* Couter
        -------------------------------------------------------------------------------------*/
    var counterRun = function () {
        $(".view-counter").each(function () {
            $(this).data('counted', false);
        });

        var checkCounters = function () {
            $(".view-counter").each(function () {
                var $counter = $(this);

                if ($counter.data('counted')) {
                    return;
                }

                var counterTop = $counter.offset().top;
                var counterBottom = counterTop + $counter.outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                var isInViewport = counterTop < viewportBottom && counterBottom > viewportTop;

                if (isInViewport) {
                    if ($().countTo) {
                        $counter.find(".number").each(function () {
                            var to = $(this).data("to"),
                                speed = $(this).data("speed");
                            $(this).countTo({
                                to: to,
                                speed: speed,
                            });
                        });
                    }
                    $counter.data('counted', true);
                }
            });
        };

        checkCounters();

        $(window).scroll(checkCounters);
    };

    /* Update Bundle Total 
    -------------------------------------------------------------------------*/
    var updateBundleTotal = function () {
        var $bundleItems = $(".list-bundle-prd .bundle-item");

        var updateBundleTotal = function () {
            var totalPrice = 0;
            var savePrice = 0;
            $bundleItems.each(function () {
                var $this = $(this);
                if ($this.find(".tf-check").prop("checked")) {
                    var newPrice = parseFloat($this.find(".price__bundle").text().replace(/[$,]/g, "")) || 0;

                    totalPrice += newPrice;
                    savePrice += newPrice * 1.25;
                }
            });

            $(".total-price-bundle").text(`$${totalPrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`);
            $(".price-bundle_save").text(`$${savePrice.toLocaleString("en-US", { minimumFractionDigits: 2 })}`);
        };

        updateBundleTotal();

        $(".tf-check").on("change", function () {
            updateBundleTotal();
        });
    };

    /* Notice Popup
    -------------------------------------------------------------------------*/
    const noticePop = () => {
        if (!$(".minipop-auto").length) return;

        $(".minipop-auto").each(function () {
            var $popup = $(this);
            var $closeBtn = $popup.find($(".closePop"));

            if (!$popup.length) return;

            var showTime = 10000;
            var hideTime = 2000;
            var timerShow, timerHide;
            var stopped = false;

            function showPopup() {
                if (stopped) return;

                $popup.addClass("active");

                timerShow = setTimeout(function () {
                    hidePopup();
                }, showTime);
            }

            function hidePopup() {
                if (stopped) return;

                $popup.removeClass("active");

                timerHide = setTimeout(function () {
                    showPopup();
                }, hideTime);
            }

            $closeBtn.on("click", function () {
                stopped = true;
                clearTimeout(timerShow);
                clearTimeout(timerHide);
                $popup.removeClass("active");
            });

            setTimeout(function () {
                showPopup();
            }, hideTime);
        });
    }

    /* Popup Product Action
    -------------------------------------------------------------------------*/
    var popupProductVariant = () => {
        if ($(".tf-quick-prd_variant").length === 0) return;

        $(".tf-quick-prd_variant").each(function () {
            var $wrap = $(this);
            var basePrice = 0;
            var $activeSize = $wrap.find(".size_btn.active");

            if ($activeSize.length) {
                basePrice = parseFloat($activeSize.data("quick-price"));
            } else {
                var priceText = $wrap.find(".price-on-sale").text();
                basePrice = parseFloat(
                    priceText.replace(/[^0-9.]/g, "")
                );
            }

            if (isNaN(basePrice)) basePrice = 0;
            $wrap.data("basePrice", basePrice);

            $wrap.find(".price-add").text("$" + basePrice.toFixed(2));

            $wrap.find(".color_btn").on("click mouseover", function () {
                var $swatch = $(this);
                var colorLabel = $swatch.find(".color__label").text().trim();

                $wrap.find(".picker_color .variant__value").text(colorLabel);

                $wrap.find(".color_btn.active").removeClass("active");
                $swatch.addClass("active");
            });

            $wrap.find(".size_btn:not(.disabled)").on("click", function () {
                var $btn = $(this);
                var price = parseFloat($btn.data("quick-price"));
                var size = $btn.data("quick-size");

                if (isNaN(price)) return;

                $wrap.find(".size_btn.active").removeClass("active");
                $btn.addClass("active");

                $wrap.find(".picker_size .variant__value").text(size);
                $wrap.find(".quantity-product").val(1);
                $wrap.data("basePrice", price);

                $wrap.find(".price-on-sale").text("$" + price.toFixed(2));

                updateAddPrice();
            });

            $wrap.find(".btn-increase").on("click", function () {
                var $qty = $wrap.find(".quantity-product");
                var qty = parseInt($qty.val()) || 1;

                $qty.val(qty + 1);
                updateAddPrice();
            });

            $wrap.find(".btn-decrease").on("click", function () {
                var $qty = $wrap.find(".quantity-product");
                var qty = parseInt($qty.val()) || 1;

                if (qty > 1) {
                    $qty.val(qty - 1);
                    updateAddPrice();
                }
            });

            function updateAddPrice() {
                var basePrice = parseFloat($wrap.data("basePrice")) || 0;
                var qty = parseInt($wrap.find(".quantity-product").val()) || 1;

                var totalSale = basePrice * qty;
                var oldPrice = basePrice * 1.25;

                $wrap.find(".price-on-sale").text("$" + basePrice.toFixed(2));
                if ($wrap.find(".price-on-old").length) {
                    $wrap.find(".price-on-old").text("$" + oldPrice.toFixed(2));
                }
                $wrap.find(".price-add").text("$" + totalSale.toFixed(2));
            }
        });
    };

    /* Hover Menu Overlay
    -------------------------------------------------------------------------*/
    const hoverMenuOverlay = () => {
        if (!$(".box-navigation").length) return;
        const mowBody = $(".menu-overlay-enabled");
        $(".box-navigation .menu-item")
            .on("mouseenter", function () {
                mowBody.addClass("is-active");
            })
            .on("mouseleave", function () {
                mowBody.removeClass("is-active");
            });
    };
    /* Brand Filter
    -------------------------------------------------------------------------*/
    var brandFilter = () => {
        if (!$(".brand-group").length) return;

        const filterButtons = document.querySelectorAll(".btn-filter_brand");
        const brandGroups = document.querySelectorAll(".brand-group");
        const searchInput = document.getElementById("brandSearch");
        const searchForm = searchInput ? searchInput.closest(".form-search") : null;
        const existingLetters = new Set();

        brandGroups.forEach(group => {
            if (group.dataset.letter) {
                existingLetters.add(group.dataset.letter.toUpperCase());
            }
        });

        filterButtons.forEach(btn => {
            const letter = btn.dataset.letter?.toUpperCase();

            if (letter !== "ALL" && !existingLetters.has(letter)) {
                btn.classList.add("unable");
            }
        });

        const showByLetter = (letter) => {
            brandGroups.forEach(group => {
                const groupLetter = group.dataset.letter?.toUpperCase();

                if (letter === "ALL" || groupLetter === letter) {
                    group.style.display = "flex";
                } else {
                    group.style.display = "none";
                }
            });
        };

        const showBySearch = (keyword) => {
            const searchValue = keyword.trim().toLowerCase();

            if (!searchValue) {
                const activeBtn = document.querySelector(".btn-filter_brand.active");
                const activeLetter = activeBtn?.dataset.letter?.toUpperCase() || "ALL";
                showByLetter(activeLetter);
                return;
            }

            filterButtons.forEach(b => b.classList.remove("active"));

            brandGroups.forEach(group => {
                const brandItems = group.querySelectorAll(".name-brand_list li");
                let hasMatch = false;

                brandItems.forEach(item => {
                    const brandName = item.textContent.trim().toLowerCase();

                    if (brandName.includes(searchValue)) {
                        item.style.display = "";
                        hasMatch = true;
                    } else {
                        item.style.display = "none";
                    }
                });

                group.style.display = hasMatch ? "flex" : "none";
            });
        };

        const resetBrandItems = () => {
            document.querySelectorAll(".name-brand_list li").forEach(item => {
                item.style.display = "";
            });
        };

        filterButtons.forEach(btn => {
            btn.addEventListener("click", function () {
                if (this.classList.contains("unable")) return;

                filterButtons.forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                if (searchInput) {
                    searchInput.value = "";
                }

                resetBrandItems();

                const letter = this.dataset.letter?.toUpperCase() || "ALL";
                showByLetter(letter);
            });
        });

        let debounceTimer;

        if (searchForm) {
            searchForm.addEventListener("submit", function (e) {
                e.preventDefault();
            });
        }

        if (searchInput) {
            searchInput.addEventListener("input", function () {
                const value = this.value.trim();

                clearTimeout(debounceTimer);

                if (value === "") {
                    resetBrandItems();

                    filterButtons.forEach(b => b.classList.remove("active"));
                    const allButton = document.querySelector('.btn-filter_brand[data-letter="all"]');
                    if (allButton) {
                        allButton.classList.add("active");
                    }

                    showByLetter("ALL");
                    return;
                }

                debounceTimer = setTimeout(() => {
                    showBySearch(value);
                }, 500);
            });
        }
    };

    /* Scroll X
    -------------------------------------------------------------------------*/
    var scrollX = () => {
        $(".hoverScrollX").on("wheel", function (event) {
            event.preventDefault();
            this.scrollLeft += event.originalEvent.deltaY
        })
    }
    /* Submit Form Gift
    -------------------------------------------------------------------------*/
    var submitFormGift = () => {
        if (!$("#formApplyDis").length) return;
        document.getElementById("formApplyDis").addEventListener("submit", function (e) {
            e.preventDefault();

            const input = document.getElementById("discount-code");
            const errorMsg = this.querySelector(".msg-text_dis");
            const value = input.value.trim();
            const validCode = "SAVE30";

            errorMsg.style.display = "none";

            if (value.toUpperCase() === validCode) {
                this.classList.add("apply-success");
            } else {
                errorMsg.style.display = "block";
            }
        });
    }

    /* React Button
    -------------------------------------------------------------------------*/
    var reactBtn = () => {
        $(".group-react").each(function () {

            var $group = $(this);

            $group.find(".react-btn").on("click", function () {

                var $clicked = $(this);
                var $other = $group.find(".react-btn").not($clicked);

                var $clickedNumber = $clicked.find(".number_react");
                var $otherNumber = $other.find(".number_react");

                var clickedCount = parseInt($clickedNumber.text());
                var otherCount = parseInt($otherNumber.text());

                if ($clicked.hasClass("reacted")) {

                    $clicked.removeClass("reacted");
                    $clicked.find("i")
                        .removeClass("icon-LikeFill")
                        .addClass("icon-Like");

                    $clickedNumber.text(clickedCount - 1);

                } else {
                    if ($other.hasClass("reacted")) {

                        $other.removeClass("reacted");
                        $other.find("i")
                            .removeClass("icon-LikeFill")
                            .addClass("icon-Like");

                        $otherNumber.text(otherCount - 1);
                    }
                    $clicked.addClass("reacted");
                    $clicked.find("i")
                        .removeClass("icon-Like")
                        .addClass("icon-LikeFill");

                    $clickedNumber.text(clickedCount + 1);
                }

            });

        });
    }

    /* Check Fbt
    -------------------------------------------------------------------------*/
    var checkFbt = () => {
        $(".tf-product-fbt input[type='checkbox']").on("change", function () {
            const id = $(this).attr("id");
            $("label[for='" + id + "']").toggleClass("active", this.checked);
        });
    }

    /* Step Routine & Group Check Price
    -------------------------------------------------------------------------*/
    var initStepRoutine = () => {

        const updateGroupPrice = () => {
            let totalPrice = 0;
            let savePrice = 0;

            $(".list-groupPrd .group-prdItem").each(function () {
                if ($(this).find(".tf-check").prop("checked")) {
                    const price = parseFloat(
                        $(this).find(".priceEach").text().replace(/[$,]/g, "")
                    ) || 0;
                    totalPrice += price;
                    savePrice += price * 1.25;
                }
            });

            const format = (n) => `$${n.toLocaleString("en-US", { minimumFractionDigits: 2 })}`;
            $(".total-priceAll").text(format(totalPrice));
            $(".total-priceSave").text(format(savePrice));
        };

        document.querySelectorAll(".step-rountine_item").forEach((step, index, steps) => {
            step.querySelector(".order-check input").addEventListener("change", function () {
                if (this.checked) {
                    for (let i = 0; i <= index; i++) {
                        steps[i].classList.add("active");
                        steps[i].querySelector("input").checked = true;
                    }
                } else {
                    for (let i = index; i < steps.length; i++) {
                        steps[i].classList.remove("active");
                        const input = steps[i].querySelector("input");
                        if (input.type === "checkbox") input.checked = false;
                    }
                }
                updateGroupPrice();
            });
        });

        $(".group-prdItem .tf-check").on("change", updateGroupPrice);
        updateGroupPrice();
    };

    /* Active Action Box
    -------------------------------------------------------------------------*/
    var activeActionBox = () => {
        $(".btn-open_action").each(function () {
            let $btn = $(this);
            let $card = $btn.closest(".card-product");

            $btn.on("mouseenter", function () {
                $card.addClass("active");
            });

            $card.on("mouseleave", function () {
                $card.removeClass("active");
            });

            $btn.on("click", function (e) {
                e.preventDefault();
                $card.toggleClass("active");
            });
        });
    };

    /* Hover Image Move
    -------------------------------------------------------------------------*/
    var hoverImgCursor = function () {
        let offsetX = 20;
        let offsetY = 20;
        $(".hover-cursor-img").on("mousemove", function (e) {
            let hoverImage = $(this).find(".hover-image");
            hoverImage.css({
                top: e.clientY + offsetY + "px",
                left: e.clientX + offsetX + "px",
            });
        });

        $(".hover-cursor-img").on("mouseenter", function () {
            let hoverImage = $(this).find(".hover-image");
            hoverImage.css({
                transform: "scale(1)",
                opacity: 1,
            });
        });

        $(".hover-cursor-img").on("mouseleave", function () {
            let hoverImage = $(this).find(".hover-image");
            hoverImage.css({
                transform: "scale(0)",
                opacity: 0,
            });
        });
    };

    /* Opened Drop
    -------------------------------------------------------------------------*/
    const openedDrop = () => {
        if (typeof bootstrap === 'undefined') return;

        const closeDropdown = (item) => {
            const trigger = item.querySelector('[data-bs-toggle="dropdown"]');
            if (!trigger) return;

            const instance = bootstrap.Dropdown.getInstance(trigger);
            if (instance) {
                instance.hide();
                instance.dispose();
            }

            item.classList.remove('show');
            trigger.classList.remove('show');
            trigger.setAttribute('aria-expanded', 'false');

            const menu = item.querySelector('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
            }
        };

        const openDropdown = (item) => {
            const trigger = item.querySelector('[data-bs-toggle="dropdown"]');
            if (!trigger) return;

            let instance = bootstrap.Dropdown.getInstance(trigger);
            if (instance) {
                instance.dispose();
            }

            instance = new bootstrap.Dropdown(trigger, {
                autoClose: false
            });

            instance.show();
        };

        const handle = () => {
            const isDesktop = window.innerWidth > 767;

            document.querySelectorAll('.banner-lookbook').forEach((banner) => {
                const items = banner.querySelectorAll('.lookbook-item');
                const openedItems = banner.querySelectorAll('.lookbook-item.open-drop');

                items.forEach((item) => {
                    const trigger = item.querySelector('[data-bs-toggle="dropdown"]');
                    if (!trigger) return;

                    const instance = bootstrap.Dropdown.getInstance(trigger);
                    if (instance) {
                        instance.dispose();
                    }

                    if (isDesktop) {
                        if (item.classList.contains('open-drop')) {
                            openDropdown(item);
                        } else {
                            closeDropdown(item);
                        }
                    } else {
                        closeDropdown(item);
                    }

                    if (!item.dataset.lookbookBind) {
                        item.addEventListener('click', (e) => {
                            if (window.innerWidth <= 767) return;

                            const currentItem = e.currentTarget;

                            if (!currentItem.classList.contains('open-drop')) {
                                openedItems.forEach((openItem) => {
                                    closeDropdown(openItem);
                                });
                            }
                        });

                        item.dataset.lookbookBind = 'true';
                    }
                });
            });
        };

        handle();

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handle, 150);
        });
    };

    /* Explore Double Tab
    -------------------------------------------------------------------------*/
    var doubleTab = () => {
        document.querySelectorAll('.main-double-tab [data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                const targetClass = this.getAttribute('data-bs-target');
                const section = this.closest('.main-double-tab');

                if (!section) return;

                section.querySelectorAll('.tf-btn-tab').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                section.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('active', 'show');
                });

                section.querySelectorAll(targetClass).forEach(pane => {
                    pane.classList.add('active', 'show');
                });
            });
        });
    }

    /* Guide Direct Tab
    -------------------------------------------------------------------------*/
    var guideDirecTab = () => {
        document.querySelectorAll('.section-guide').forEach((section) => {
            const prevBtn = section.querySelector('.direc-prev');
            const nextBtn = section.querySelector('.direc-next');
            const tabButtons = [...section.querySelectorAll('.step-progress .tf-btn-tab')];

            if (!tabButtons.length) return;

            const getActiveIndex = () => {
                return tabButtons.findIndex((btn) => btn.classList.contains('active'));
            };

            const goToTab = (index) => {
                const tabTrigger = tabButtons[index];

                if (!tabTrigger) return;

                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
                } else {
                    tabTrigger.click();
                }
            };

            if (nextBtn) {
                nextBtn.addEventListener('click', () => {
                    const currentIndex = getActiveIndex();
                    const nextIndex = (currentIndex + 1) % tabButtons.length;
                    goToTab(nextIndex);
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    const currentIndex = getActiveIndex();
                    const prevIndex = (currentIndex - 1 + tabButtons.length) % tabButtons.length;
                    goToTab(prevIndex);
                });
            }
        });
    }

    /* Glitch Canvas
    -------------------------------------------------------------------------*/
    var glitchCanvas = () => {
        const items = document.querySelectorAll(".glitch-image");

        items.forEach((el) => {
            const image = el.dataset.image;
            if (!image) return;

            const config = {
                sliceCount: 32,
                xRange: 20,
                yRange: 50,
                activeChance: 0.5,
                scaleX: 1.01,
                stopDelay: 80
            };

            const slices = [];
            let rafId = null;
            let resetTimer = null;
            let isHover = false;

            function random(min, max) {
                return Math.random() * (max - min) + min;
            }

            function createSlices() {
                el.innerHTML = "";
                slices.length = 0;

                const w = 100 / config.sliceCount;

                for (let i = 0; i < config.sliceCount; i++) {
                    const slice = document.createElement("div");

                    slice.className = "glitch-slice";
                    slice.style.left = `${i * w}%`;
                    slice.style.width = `calc(${w}% + 2px)`;
                    slice.style.backgroundImage = `url("${image}")`;
                    slice.style.backgroundSize = `${config.sliceCount * 100}% 100%`;
                    slice.style.backgroundPosition = `${i * 100 / (config.sliceCount - 1)}% 50%`;
                    slice.style.transform = "translate3d(0,0,0) scaleX(1)";

                    el.appendChild(slice);
                    slices.push(slice);
                }
            }

            function applyRandomGlitch() {
                slices.forEach((slice, index) => {
                    const active = Math.random() < config.activeChance;

                    let x = active ? random(-config.xRange, config.xRange) : 0;
                    let y = active && Math.random() > 0.6 ? random(-10, config.yRange) : 0;

                    if (index < 4 || index > config.sliceCount - 5) {
                        x += random(-6, 6);
                    }

                    slice.style.transform = `translate3d(${x}px, ${y}px, 0) scaleX(${config.scaleX})`;
                });
            }

            function requestUpdate() {
                if (rafId) return;

                rafId = requestAnimationFrame(() => {
                    applyRandomGlitch();
                    rafId = null;
                });
            }

            function reset() {
                clearTimeout(resetTimer);

                slices.forEach((slice) => {
                    slice.style.transform = "translate3d(0,0,0) scaleX(1)";
                });
            }

            function scheduleReset() {
                clearTimeout(resetTimer);

                resetTimer = setTimeout(() => {
                    if (!isHover) return;
                    reset();
                }, config.stopDelay);
            }

            createSlices();

            el.addEventListener("mouseenter", () => {
                isHover = true;
            });

            el.addEventListener("mousemove", () => {
                if (!isHover) return;

                clearTimeout(resetTimer);
                requestUpdate();
                scheduleReset();
            });

            el.addEventListener("mouseleave", () => {
                isHover = false;
                clearTimeout(resetTimer);
                reset();
            });

            window.addEventListener("resize", () => {
                createSlices();
                reset();
            });
        });
    };

    /* Upload Image
    -------------------------------------------------------------------------*/
    const handleUploadImage = () => {
        const uploadBtn = document.querySelector(".btn-action_upload");
        const removeBtn = document.querySelector(".btn-action_remove-upload");
        const list = document.querySelector(".image-upload_list");

        if (!uploadBtn || !list) return;

        const input = document.createElement("input");
        input.type = "file";
        input.accept = "image/*";
        input.style.display = "none";
        document.body.appendChild(input);

        uploadBtn.addEventListener("click", (e) => {
            e.preventDefault();
            input.click();
        });

        input.addEventListener("change", () => {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (e) {
                const li = document.createElement("li");
                li.className = "image-upload_item";

                li.innerHTML = `
                <img src="${e.target.result}" width="104" height="104" alt="Image">
            `;

                list.appendChild(li);
            };

            reader.readAsDataURL(file);

            input.value = "";
        });

        if (removeBtn) {
            removeBtn.addEventListener("click", (e) => {
                e.preventDefault();
                list.innerHTML = "";
            });
        }
    };


    /* Auto Tab & Accordion
    -------------------------------------------------------------------------*/
    var autoTabAccordion = function () {
        const wrap = document.querySelector(".auto-tab");
        const items = document.querySelectorAll(".auto-tab .nav-tab-item");
        if (!items.length) return;

        let current = 0;
        let timer;

        function goToItem(index) {
            current = index;

            const tabBtn = items[current].querySelector(".tf-btn-tab");
            const accBtn = items[current].querySelector(".accordion-action");
            const target = accBtn?.getAttribute("data-bs-target");
            const collapse = target ? document.querySelector(target) : null;

            if (tabBtn) tabBtn.click();

            if (accBtn && collapse && !collapse.classList.contains("show")) {
                accBtn.click();
            }
        }

        function startAuto() {
            clearInterval(timer);
            timer = setInterval(() => {
                let next = (current + 1) % items.length;
                goToItem(next);
            }, 4000);
        }

        items.forEach((item, index) => {
            const tabBtn = item.querySelector(".tf-btn-tab");
            const accBtn = item.querySelector(".accordion-action");

            [tabBtn, accBtn].forEach(el => {
                if (!el) return;
                el.addEventListener("click", function () {
                    current = index;
                    startAuto();
                });
            });
        });

        if (wrap.classList.contains("is-hover")) {
            items.forEach((item, index) => {
                item.addEventListener("mouseenter", function () {
                    clearInterval(timer);
                    goToItem(index);
                });

                item.addEventListener("mouseleave", function () {
                    startAuto();
                });
            });
        }

        startAuto();
    };

    /* Format MM/YY
    -------------------------------------------------------------------------*/
    var formatMMYY = function () {
        const inputs = document.querySelectorAll(".formatMMYY");
        if (!inputs.length) return;

        inputs.forEach((input) => {
            input.setAttribute("inputmode", "numeric");
            input.setAttribute("maxlength", "5");

            input.addEventListener("input", function (e) {
                let raw = e.target.value.replace(/\D/g, "").slice(0, 4);

                let mm = raw.slice(0, 2);
                let yy = raw.slice(2, 4);

                if (mm.length >= 1) {
                    let first = mm[0];

                    if (parseInt(first, 10) > 1 && mm.length === 1) {
                        mm = "0" + first;
                    }
                }

                if (mm.length === 2) {
                    let month = parseInt(mm, 10);

                    if (month === 0) month = 1;
                    if (month > 12) month = 12;

                    mm = String(month).padStart(2, "0");
                }

                let value = mm;

                if (mm.length === 2) {
                    value += "/";
                }

                if (yy.length) {
                    value += yy;
                }

                e.target.value = value;
            });

            input.addEventListener("keydown", function (e) {
                if (
                    e.key === "Backspace" &&
                    this.value.length === 3 &&
                    this.value.includes("/")
                ) {
                    this.value = this.value.slice(0, 2);
                    e.preventDefault();
                }
            });
        });
    };
    /* Hover Video
    -------------------------------------------------------------------------*/
    var hoverVideo = function () {
        const items = document.querySelectorAll(".hover-video");
        if (!items.length) return;

        items.forEach((item) => {
            const video = item.querySelector("video");
            if (!video) return;

            video.pause();

            item.addEventListener("mouseenter", () => {
                video.play();
            });

            item.addEventListener("mouseleave", () => {
                video.pause();
            });
        });
    };
    /* Color Swatch Product
    -------------------------------------------------------------------------*/
    var swatchColor = function () {
        if ($(".card-product").length <= 0) return;

        var moveColorTrack = function ($swatch) {
            if (!$swatch.length || !$swatch.is(":visible")) return;

            var $list = $swatch.closest(".product-color_list");
            var $track = $list.find(".color-track");

            if (!$track.length) return;

            var swatchRect = $swatch[0].getBoundingClientRect();
            var listRect = $list[0].getBoundingClientRect();

            var relLeft = swatchRect.left - listRect.left + ($swatch.outerWidth() / 2);
            var relTop = swatchRect.top - listRect.top + ($swatch.outerHeight() / 2);

            $track.css({
                left: relLeft + "px",
                top: relTop + "px"
            });
        };

        var updateAllColorTrack = function () {
            $(".product-color_list").each(function () {
                var $active = $(this).find(".color-swatch.active:visible").first();

                if (!$active.length) {
                    $active = $(this).find(".color-swatch:visible").first();
                    $active.addClass("active");
                }
                moveColorTrack($active);
            });
        };

        var updateColorTrackSafe = function () {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    updateAllColorTrack();
                });
            });
        };

        $(".color-swatch").on("click mouseover", function () {
            var $swatch = $(this);
            var $card = $swatch.closest(".card-product,.recommend-item");

            var swatchColor = $swatch.find("img:not(.swatch-img)").attr("src");
            var imgProduct = $card.find(".img-product");
            var colorLabel = $swatch.find(".color-label, .tooltip").text().trim();

            if (swatchColor) {
                imgProduct.attr("src", swatchColor);
            }

            $card.find(".quick-variant-color .variant-value").text(colorLabel);

            $card.find(".color-swatch.active").removeClass("active");
            $swatch.addClass("active");

            updateColorTrackSafe();
        });

        $(".card-product").on("mouseenter", function () {
            var $card = $(this);

            var duration = parseFloat($card.find(".product-color_list").css("transition-duration")) * 1000 || 300;

            setTimeout(function () {
                $card.find(".product-color_list").each(function () {
                    var $active = $(this).find(".color-swatch.active:visible").first();

                    if (!$active.length) {
                        $active = $(this).find(".color-swatch:visible").first();
                        $active.addClass("active");
                    }

                    moveColorTrack($active);
                });
            }, duration);
        });

        updateColorTrackSafe();

        $(window).on("load", function () {
            var imgLoads = $(".card-product img").map(function () {
                if (this.complete) return Promise.resolve();
                return new Promise(function (res) {
                    $(this).one("load error", res);
                }.bind(this));
            }).get();

            Promise.all(imgLoads).then(updateColorTrackSafe);
        });

        $(".card-product img").each(function () {
            if (this.complete) return;

            $(this).one("load error", function () {
                updateColorTrackSafe();
            });
        });

        var resizeTimer;
        $(window).on("resize", function () {
            clearTimeout(resizeTimer);

            resizeTimer = setTimeout(function () {
                updateColorTrackSafe();
            }, 150);
        });
    };
    /* Height Table 
        -------------------------------------------------------------------------*/
    var heightTable = function () {
        function updateTableHeight() {
            const $originalModal = $("#orderDetail");

            const $clone = $originalModal
                .clone()
                .css({
                    display: "block",
                    visibility: "hidden",
                    position: "absolute",
                    top: 0,
                    left: 0,
                    zIndex: -1,
                })
                .appendTo("body");

            const $tableWrapper = $clone.find(".table-order-detail");
            const $table = $tableWrapper.find("table");
            const $thead = $table.find("thead");
            const $rows = $table.find("tbody tr");

            let totalRowHeight = 0;
            for (let i = 0; i < Math.min(2, $rows.length); i++) {
                totalRowHeight += $rows.eq(i).outerHeight();
            }

            const theadHeight = $thead.outerHeight();
            const maxHeight = totalRowHeight + theadHeight;

            $originalModal.find(".table-order-detail").css("max-height", maxHeight + 1 + "px");

            $clone.remove();
        }

        updateTableHeight();

        $(window).on("resize", updateTableHeight);
    };

    /* Toggle Control
    -------------------------------------------------------------------------*/
    var toggleControl = function () {
        const $showForm = $(".show-form-address");
        const $editForm = $(".edit-form-address");

        $(".btn-add-address").on("click", function () {
            const isVisible = $showForm.is(":visible");

            if (isVisible) {
                $showForm.hide();
            } else {
                $showForm.show();
                $editForm.hide().removeClass("show");
                $(".account-address-item").removeClass("editing");
            }
        });

        $(".btn-hide-address").on("click", function () {
            $showForm.hide();
        });

        $(".btn-delete-address").on("click", function () {
            const $item = $(this).closest(".account-address-item");
            $item.remove();
            if ($item.hasClass("editing")) {
                $editForm.hide().removeClass("show");
            }
        });

        $(".btn-edit-address").on("click", function () {
            const $item = $(this).closest(".account-address-item");
            const isEditing = $item.hasClass("editing");

            $showForm.hide();

            if (isEditing) {
                $editForm.hide().removeClass("show");
                $item.removeClass("editing");
            } else {
                $editForm.show().addClass("show");
                $(".account-address-item").removeClass("editing");
                $item.addClass("editing");
            }
        });

        $(".btn-hide-edit-address").on("click", function () {
            $editForm.hide().removeClass("show");
            $(".account-address-item").removeClass("editing");
        });

        function updateCountText() {
            var count = $("#updateCountMain .count_item").length;
            $("#updateCountMain .count_text").text(count);
        }
        $("#updateCountMain .count_del").on("click", function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest(".count_item").remove();
            updateCountText();
        });
    };

    /* Scroll Grid Product
    -------------------------------------------------------------------------*/
    var scrollGridProduct = function () {
        var scrollContainer = $(".wrapper-gallery-scroll");
        var activescrollBtn = null;
        var offsetTolerance = 20;

        function isHorizontalMode() {
            return window.innerWidth <= 767;
        }

        function getTargetScroll(target, isHorizontal) {
            if (isHorizontal) {
                return (
                    target.offset().left -
                    scrollContainer.offset().left +
                    scrollContainer.scrollLeft()
                );
            } else {
                return (
                    target.offset().top -
                    scrollContainer.offset().top +
                    scrollContainer.scrollTop()
                );
            }
        }

        $(".btn-scroll-target").on("click", function () {
            var scroll = $(this).data("scroll");
            var target = $(".item-scroll-target[data-scroll='" + scroll + "']");

            if (target.length > 0) {
                var isHorizontal = isHorizontalMode();
                var targetScroll = getTargetScroll(target, isHorizontal);

                if (isHorizontal) {
                    scrollContainer.animate({ scrollLeft: targetScroll }, 600);
                } else {
                    $("html, body").animate({ scrollTop: targetScroll }, 100);
                }

                $(".btn-scroll-target").removeClass("active");
                $(this).addClass("active");
                activescrollBtn = $(this);
            }
        });

        $(window).on("scroll", function () {
            var isHorizontal = isHorizontalMode();
            $(".item-scroll-target").each(function () {
                var target = $(this);
                var targetScroll = getTargetScroll(target, isHorizontal);

                if (isHorizontal) {
                    if (
                        $(window).scrollLeft() >= targetScroll - offsetTolerance &&
                        $(window).scrollLeft() <= targetScroll + target.outerWidth()
                    ) {
                        $(".btn-scroll-target").removeClass("active");
                        $(
                            ".btn-scroll-target[data-scroll='" + target.data("scroll") + "']"
                        ).addClass("active");
                    }
                } else {
                    if (
                        $(window).scrollTop() >= targetScroll - offsetTolerance &&
                        $(window).scrollTop() <= targetScroll + target.outerHeight()
                    ) {
                        $(".btn-scroll-target").removeClass("active");
                        $(
                            ".btn-scroll-target[data-scroll='" + target.data("scroll") + "']"
                        ).addClass("active");
                    }
                }
            });
        });
    };

    /* Hide Pop
    -------------------------------------------------------------------------*/
    var hideSearch = () => {
        const searchCanvas = document.getElementById("search");
        document.addEventListener("show.bs.modal", function (e) {
            if (
                searchCanvas.classList.contains("show") &&
                e.target.id !== "search"
            ) {
                bootstrap.Offcanvas.getInstance(searchCanvas)?.hide();
            }
        });
        document.addEventListener("show.bs.offcanvas", function (e) {
            if (
                searchCanvas.classList.contains("show") &&
                e.target.id !== "search"
            ) {
                bootstrap.Offcanvas.getInstance(searchCanvas)?.hide();
            }
        });
    }

    /* Drag Cursor
   -------------------------------------------------------------------------*/
    var dragCursor = () => {
        document.querySelectorAll(".list-prd-mini").forEach((slider) => {
            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            slider.addEventListener("mousedown", (e) => {
                isDown = true;
                slider.classList.add("dragging");

                startX = e.pageX;
                scrollLeft = slider.scrollLeft;
            });

            document.addEventListener("mouseup", () => {
                isDown = false;
                slider.classList.remove("dragging");
            });

            slider.addEventListener("mousemove", (e) => {
                if (!isDown) return;

                e.preventDefault();

                const walk = e.pageX - startX;
                slider.scrollLeft = scrollLeft - walk;
            });
        });
    }

    /* Preloader
    -------------------------------------------------------------------------*/
    function preloader() {
        setTimeout(function () {
            $("#preload").fadeOut(300, function () {
                $(this).remove();
            });
        }, 300);
    }


    // Dom Ready
    $(function () {
        headerSticky();
        dropdownSelect();
        btnQuantity();
        deleteFile();
        deleteWishList();
        goTop();
        sidebarMobile();
        staggerWrap();
        clickModalSecond();
        totalPriceVariant();
        handleProgress();
        handleFooter();
        infiniteSlide();
        addWishList();
        handleSidebarFilter();
        unitEstimateShipping();
        textCopy();
        parallaxieInit();
        tableCompareRemove();
        handleMobileMenu();
        scrollBottomSticky();
        showPassword();
        hoverPin();
        rateClick();
        counterRun();
        updateBundleTotal();
        noticePop();
        autoPopup();
        hoverVideo();
        swatchColor();
        heightTable();
        toggleControl();
        scrollGridProduct();
        clickActive();
        hideSearch();
        dragCursor();
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", function () {
                preloader();
            });
        } else {
            preloader();
        }
    });
})(jQuery);
