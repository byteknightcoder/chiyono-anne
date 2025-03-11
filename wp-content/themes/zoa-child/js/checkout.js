
jQuery(document).ready(function ($) {
    $('.form__steps').wrap('<div class="form__steps_wrap"></div>');
    $('body').on('change', '#copy_to_billing', function (e) {
        e.preventDefault();
        var addresFields = [
            'last_name',
            'first_name',
            'last_name_kana',
            'first_name_kana',
            'email',
            'phone',
            'postcode',
            'country',
            'state',
            'city',
            'address_1',
            'address_2',
        ];

if ($(this).prop('checked')) {

    $.each(addresFields, function (index, field) {
        var billingFieldId = field === 'phone' ? 'username' : 'billing_' + field;
        
       var shippingValue = $('#shipping_phone').val();

           if (field === 'phone') {
    var billingPhoneField = $('input[name="mobile/email"][data-dig-main="billing_phone"]');

    if (billingPhoneField.length) {
        billingPhoneField.val(shippingValue);
        console.log("Phone number updated:", shippingValue); // For debugging
    }
}
        if ($('#shipping_' + field).length && $('#shipping_' + field).val()) {
            $('#' + billingFieldId).val($('#shipping_' + field).val());
            
            if ($('#' + billingFieldId).is('select')) {
                $('#' + billingFieldId).closest('.justwrap').find('.selectbox__option').each(function () {
                    if ($(this).attr('data-value') == $('#' + billingFieldId).val()) {
                        var field_name = $(this).text();
                        $(this).closest('.selectbox').find('.selectbox__label').text(field_name);
                    }
                });
            }

        }
    });
       // Trigger the country change event to ensure state dropdown updates
    $('#billing_country').trigger('change');

    // Wait for the state dropdown to load, then set the state value
    setTimeout(function() {
        var shippingStateValue = $('#shipping_state').val(); // Replace with the correct shipping state field ID
        var $billingStateField = $('#billing_state'); // Replace with the correct billing state field ID

        // Set the state value in the billing field
        $billingStateField.val(shippingStateValue);

        // Update the select box UI, if necessary
        $billingStateField.closest('.justwrap').find('.selectbox__option').each(function () {
            if ($(this).attr('data-value') == shippingStateValue) {
                var state_name = $(this).text();
                $(this).closest('.selectbox').find('.selectbox__label').text(state_name);
            }
        });
    }, 500);  // Adjust delay time as needed
}


    });
    //validate Next button
    function check_validate() {
        var showRequiredError = false;
        $('#checkout .is-active').find('.validate-required').each(function (i, field) {
            var v_input = $(field).find('input[type="text"]');
            var v_select = $(field).find('select');
            var v_phone=$(field).find('input[type="tel"]');
            if (v_input.attr('type') != 'hidden' && (v_input.val() == '' || v_select.val() == ''||v_phone.val()=='')||$(field).find('#shipping_phone').hasClass('parsley-error')||$(field).find('input[type="tel"]').hasClass('parsley-error')) {
                showRequiredError = true;
            }
        });
        if (showRequiredError == false) {
            $('#checkout .is-active .js-next').removeClass('ch-disable-add-to-cart');
        } else {
            $('#checkout .is-active .js-next').addClass('ch-disable-add-to-cart');
        }
        for_no_shipping();
    }
    $('#checkout').find('.validate-required').each(function (i, field) {
        var v_input = $(field).find('input[type="text"]');
        var v_select = $(field).find('select');
        var v_phone=$(field).find('input[type="tel"]');
		if ($('#step-1').find('label.no-shipping-text').length ) {
                    $('#step-1').find('.js-next').removeClass('ch-disable-add-to-cart');
		} else if (v_input.attr('type') != 'hidden' && (v_input.val() == '' || v_select.val() == ''||v_phone.val()=='')) {
            $('#checkout .js-next').addClass('ch-disable-add-to-cart');
        }
        for_no_shipping();
    });
    function for_no_shipping(){
        if($('#checkout').length){
            if ($('label.no-shipping-text').length ) {
                setTimeout(function () {
                    $('#step-1').find('.js-next').removeClass('ch-disable-add-to-cart');
                }, 200);
            }
        }
    }
    check_validate();
    $("input[type='text'],input[type='email'],input[type='tel'],select,#copy_to_billing,#shipping_phone").change(function (e) {
        e.preventDefault();
        setTimeout(function () {
            check_validate();
        }, 200);
    });

    $("#createaccount").change(function () {
        if (this.checked) {
            if ($("#account_password").val() == '') {
                check_validate();
                $('#checkout .is-active .js-next').addClass('ch-disable-add-to-cart');
            } else {
                $('#checkout .is-active .js-next').removeClass('ch-disable-add-to-cart');
                check_validate();
            }
            $("#account_password").change(function () {
                var showRequiredError = false;
                if ($(this).val() == '') {
                    showRequiredError = true;
                }
                if (showRequiredError == false) {
                    $('#checkout .is-active .js-next').removeClass('ch-disable-add-to-cart');
                    check_validate();
                } else {
                    $('#checkout .is-active .js-next').addClass('ch-disable-add-to-cart');
                }
            });
        } else {
            check_validate();
        }
        for_no_shipping();
    });
    //end
    // $('#step-1 .js-next').click(function () {
    //     console.log('click next on step 1 checkout');
    //     setTimeout(function () {
    //         check_validate();
    //     }, 200);
    // });
    // バリデーションエラーを収集する関数
    function collectValidationErrors() {
        var errors = [];
        $('#checkout .is-active').find('.validate-required').each(function (i, field) {
            var v_input = $(field).find('input[type="text"]');
            var v_select = $(field).find('select');
            var v_phone = $(field).find('input[type="tel"]');
            var fieldName = $(field).find('label').text().trim();
            
            if (v_input.attr('type') != 'hidden' && v_input.val() == '') {
                errors.push(fieldName + 'が入力されていません');
            }
            if (v_select.length && v_select.val() == '') {
                errors.push(fieldName + 'が選択されていません');
            }
            if (v_phone.length && v_phone.val() == '') {
                errors.push(fieldName + 'が入力されていません');
            }
            if (v_phone.hasClass('parsley-error')) {
                errors.push(fieldName + 'の形式が正しくありません');
            }
        });
        return errors;
    }

    // サブミットボタンのクリックイベントを追加
    $('form.checkout').on('submit', function(e) {
        var errors = collectValidationErrors();
        if (errors.length > 0) {
            e.preventDefault(); // フォームの送信を防止
            console.log('バリデーションエラー:');
            errors.forEach(function(error) {
                console.log('- ' + error);
            });
        }
    });
});