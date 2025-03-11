/*(function($) {
	$(function() {
		console.info("$jQuery2_2_4 = " + $.fn.jquery);
	$('.elementor-tabs-content-wrapper .elementor-tab-content').each(function(i){
        $(this).find('form').attr('id','form0' + (i+1));
    });
	$('.required-field').attr("data-parsley-required","true").attr("data-parsley-trigger","keyup");
	$('#form01').parsley();
	});
})($jQuery2_2_4);*/
jQuery(document).ready(function($) {
	$('.elementor-tabs-content-wrapper .elementor-tab-content').each(function(i){
        $(this).find('form').attr('id','form0' + (i+1));
    });
	
	//required
	$('.required-field').attr("data-parsley-required","true").attr("data-parsley-trigger","focusout");
	//woo field required
	$('.validate-required input').attr("data-parsley-required","true").attr("data-parsley-trigger","focusout");
	//email
	$('.email-field').attr("email-field","email");
	if ($('body').hasClass('ja')) {
		//postcode insert label text
		$('.validate-postcode label .required').before('<small class="field-tip">ハイフンなし</small>');
		$('.validate-required#shipping_phone_field label .required, .validate-required#billing_phone_field label .required').before('<small class="field-tip">ハイフンなし</small>');
		//name
		$('.name-field, #shipping_last_name, #shipping_first_name, #billing_last_name, #billing_first_name').attr("pattern","[^\x20-\x7E]*");
		$('.name-field, #shipping_last_name, #shipping_first_name').attr("data-parsley-pattern-message","日本語で入力してください");
		//kana
		$('.kana-field, #shipping_last_name_kana, #billing_first_name_kana, #billing_last_name_kana, #billing_first_name_kana').attr("pattern","[\u30A1-\u30FC]*");
		$('.kana-field, #shipping_last_name_kana, #billing_first_name_kana, #billing_last_name_kana, #billing_first_name_kana').attr("data-parsley-pattern-message","カタカナで入力してください");
		//postcode /^\d{3}[-]\d{4}$|^\d{3}[-]\d{2}$|^\d{3}$/
		$('#shipping_postcode, #billing_postcode').attr("pattern","/^\\d{7}$/");
		$('#shipping_postcode, #billing_postcode').attr("data-parsley-pattern-message","郵便番号の形式が違います");
		//$('#shipping_phone, #billing_phone').attr("pattern","/^(0[5-9]0[0-9]{8}|0[1-9][1-9][0-9]{7})$/");
		$('#shipping_phone, #billing_phone').attr("data-parsley-pattern-message","電話番号の形式が違います");
	} else {
		//name
		$('.name-field, #shipping_last_name, #shipping_first_name, #billing_last_name, #billing_first_name').attr("pattern","[a-zA-Z]*");
		$('.name-field, #shipping_last_name, #shipping_first_name').attr("data-parsley-pattern-message","Please use only letters.");
	}
	$('.tel-field').attr("data-parsley-errors-container","#error-tel");
	$('.message-field').attr("minlength","6").attr("data-parsley-minlength","6");
	//gift card product
	//$('input#mwb_wgm_from_name').attr("data-parsley-required","true").attr("data-parsley-trigger","focusout").attr("pattern","^[a-zA-Z]+$");
	//$('form.cart').parsley(); //Single Product
	$('#form01').parsley(); //Customer Contact
	$('#form02').parsley(); //Press Contact
	$('form#checkout').parsley(); //Woo Checkout
	var inputVal = $('form').find('input');
	if($(inputVal).attr('data-parsley-trigger') !== undefined) {
		var VformSubmit = $('input[name="submitConfirm"]');
		VformSubmit.prop('disabled', true);
		console.log('parsley form!');
	}
	$('.elementor-tabs-content-wrapper .elementor-tab-content').each(function() {
		$('body').on('blur', '.required-field', function() {
			var form = $(this).closest('form');			
			setTimeout(function(){
				var formSubmit = $(form).find('input[name="submitConfirm"]');
				if (form.find('.parsley-error').length) {
					formSubmit.prop('disabled', true);
					return false;
				} else {
					formSubmit.prop('disabled', false);
				}
			}, 40);
		});
	});
});