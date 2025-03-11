"use strict";
function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  let expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function resetEncouragedCookies() {
  setCookie("yaydp_hide_shop_notice", "");
  setCookie("yaydp_hide_product_notice", "");
  setCookie("yaydp_hide_cart_notice", "");
  setCookie("yaydp_hide_checkout_notice", "");
}

(function ($) {
  jQuery(document).ready(function ($) {
    const localizeData = window.yaydp_frontend_data ?? {};

    function update_encouraged_notice() {
      resetEncouragedCookies();
      const loadingNotices =
        "<div class='yaydp-loading-notice yaydp-notice yaydp-notice-info'>Loading events...</div>";

      const productPricingNoticeElements = $(
        ".product-pricing-encouraged-notice"
      );
      const cartDiscountNoticeElements = $(".cart-discount-encouraged-notice");
      const checkoutFeeNoticeElements = $(".checkout-fee-encouraged-notice");

      const hasProductPricingNotice = productPricingNoticeElements.length > 0;
      const hasCartDiscountNotice = cartDiscountNoticeElements.length > 0;
      const hasCheckoutFeeNotice = checkoutFeeNoticeElements.length > 0;
      const hasNotice =
        hasProductPricingNotice ||
        hasCartDiscountNotice ||
        hasCheckoutFeeNotice;

      if (hasNotice) {
        if ($(productPricingNoticeElements).html()) {
          $(productPricingNoticeElements).html(loadingNotices);
        }
        if ($(cartDiscountNoticeElements).html()) {
          $(cartDiscountNoticeElements).html(loadingNotices);
        }
        if ($(checkoutFeeNoticeElements).html()) {
          $(checkoutFeeNoticeElements).html(loadingNotices);
        }
      }
      const data = {
        nonce: window.yaydp_frontend_data.nonce,
        action: "yaydp-update-encouraged-notice",
        content_types: JSON.stringify([
          ...(hasProductPricingNotice ? ["product_pricing"] : []),
          ...(hasCartDiscountNotice ? ["cart_discount"] : []),
          ...(hasCheckoutFeeNotice ? ["checkout_fee"] : []),
        ]),
      };
      setTimeout(() => {
        $.ajax({
          method: "POST",
          url: window.yaydp_frontend_data.admin_ajax,
          data,
        }).done(function ({ success, data }) {
          if (success) {
            $(productPricingNoticeElements).html(data.product_pricing ?? "");
            $(cartDiscountNoticeElements).html(data.cart_discount ?? "");
            $(checkoutFeeNoticeElements).html(data.checkout_fee ?? "");
          }
        });
      }, 2000);
    }

    $(document).on("wc_update_cart added_to_cart", function () {
      update_encouraged_notice();
    });
    /** Add to cart */
    $(document).on("wc_update_cart added_to_cart", function () {
      update_encouraged_notice();
    });
    /** Update cart */
    $(document).on("submit", ".woocommerce-cart-form", function (evt) {
      update_encouraged_notice();
    });
    $(document).on(
      "click",
      ".woocommerce-cart-form .product-remove > a",
      function (evt) {
        update_encouraged_notice();
      }
    );
    $(document).on("click", ".woocommerce-cart .restore-item", function (evt) {
      update_encouraged_notice();
    });
    $(document.body).on("removed_from_cart", function () {
      update_encouraged_notice();
    });
    $(document.body).on("click", "[name='add-to-cart']", function () {
      resetEncouragedCookies();
    });

    function hideNotice() {
      setCookie(`yaydp_hide_${localizeData.current_page}_notice`, true);
      $(
        "#yaydp-bottom-encouraged-notice .yaydp-encouraged-notice-wrapper"
      ).remove();
    }
    jQuery(document).ready(function ($) {
      $(".yaydp-encouraged-notice-close-icon").on("click", hideNotice);
    });
  });
})(jQuery);
