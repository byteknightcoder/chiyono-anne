jQuery(function($){
	var validateForm = $("form#wppb-register-user");
	validateForm.find('input, select').each(function(){
		var required = $(this).attr('required');
		if (typeof required !== typeof undefined && required !== false) {
			$(this).addClass('validate[required]');
			$(this).attr("data-parsley-required","true").attr("data-parsley-trigger","focusout");
		}
	});
	
	function populateBirthField()
	{
		$('#birth_year option:eq(0)').text(translation.year_label);
		$('#birth_month option:eq(0)').text(translation.month_label);
		$('#birth_date option:eq(0)').text(translation.day_label);
		
		for (var i=1950; i<=2015; i++){
			$('#birth_year').append('<option value="'+ i +'">'+ i +'</option>');
		}
	
		for (var i=0; i < gl_month_array.length; i++) {
			$('#birth_month').append('<option value="'+ (i + 1) +'">'+ gl_month_array[i] +'</option>');
		}

		for (var i=1; i<=31; i++) {
			$('#birth_date').append('<option value="'+ i +'">'+ i +'</option>');
		}
	}
	populateBirthField();
	
	$.fn.autoKana('#first_name', '#first_name_kana');
	$.fn.autoKana('#last_name', '#last_name_kana');
	$('form#wppb-register-user').attr('data-parsley-validate', '');
	
	$('body').on('change', '#email', function(e){
		$('#username').val($('#email').val());
	});
	
	var validateForm = $("form#wppb-register-user");
	
	validateForm.parsley().on('field:validated', function() {
	    var ok = $('.parsley-error').length === 0;
	  })
	  .on('form:submit', function() {
	    return true;
	  });
	
	$('body').on('click', '#register', function(e){
		$('#username').val($('#email').val());
		
		/*$('.wppb-user-forms > ul > li > .value > ul.column_wrap > li').each(function () {
			if ($(this).find('.formError')) {
				$(this).addClass('has_error');
				$(this).find('label, input').wrapAll('<span class="flex_input"></span>');
			} else {
				if ($(this).hasClass('has_error')) {
					$(this).removeClass('has_error');
				}
				if ($(this).find('span.flex_input')) {
					$(this).find('label, input').unwrap();
				}
			}
		});*/
	});
	/*$('form#wppb-register-user input').focusout(function(e) {
		e.preventDefault();
		$('.wppb-user-forms > ul > li > .value > ul.column_wrap > li').each(function () {
			if ($(this).find('.formError')) {
				if (!$(this).hasClass('has_error')) {
					$(this).addClass('has_error');
				}
				if (!$(this).find('span.flex_input')) {
					$(this).find('label, input').wrapAll('<span class="flex_input"></span>');
				}
			} else {
				if ($(this).hasClass('has_error')) {
					$(this).removeClass('has_error');
				}
				if ($(this).find('span.flex_input')) {
					$(this).find('label, input').unwrap();
				}
			}
		});
	});*/
});