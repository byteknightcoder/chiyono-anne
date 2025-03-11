// JavaScript Document
jQuery(document).ready(function($){
	$('<li class="form__step"><div class="form__step-container"><span class="form__step-nr">4</span><span class="form__step-title">'+ gl_confirmation_text +'</span></div></li>').appendTo('ul.form__steps');
	$('.form__steps').wrap('<div class="form__steps_wrap"></div>');
	$('.acf-fields > .acf-field-select > .acf-input > select').addClass('input-select justselect').wrap('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
	
	var $el = $('.birs_form_field > .birs_field_content > select'); 
	$el.each(function(){
		var option = $(this).find('option');
		console.log(option.length);
		if(option.length === 1){
			option.parent().parent().parent().hide();
		}
	});
	//reservation form checkbox
	$('ul.acf-checkbox-list li input[type=checkbox]').each(function(index, element){
		var label = $(this).closest('label');
		var label_name = label.text();
		$(this).attr('data-labelauty', label_name);
		if (label.hasClass('selected'))
		{
			$(this).attr('checked', 'checked');
		}
		var input_clone = $(this);
		label.replaceWith(input_clone);
	});
	if ($('ul.acf-checkbox-list li input[type=checkbox]').length)
	{
		$('ul.acf-checkbox-list li input[type=checkbox]').labelauty({
			// Development Mode
			// This will activate console debug messages
			development: false,
			
			// Trigger Class
			// This class will be used to apply styles
			class: "labelauty",
			
			// Use icon?
			// If false, then only a text label represents the input
			icon: true,
			
			// Use text label ?
			// If false, then only an icon represents the input
			label: true,
			
			// This value will be used to apply a minimum width to the text labels
			minimum_width: false,
			
			// Use the greatest width between two text labels ?
			// If this has a true value, then label width will be the greatest between labels
			same_width: true
		});
	}
	//reservation form radio button
	$('ul.acf-radio-list li input[type=radio]').each(function(index, element){
		var label = $(this).closest('label');
		var label_name = label.text();
		$(this).attr('data-labelauty', label_name);
		if (label.hasClass('selected'))
		{
			$(this).attr('checked', 'checked');
		}
		var input_clone = $(this);
		label.replaceWith(input_clone);
	});
	
	if ($('ul.acf-radio-list li input[type=radio]').length)
	{
		$('ul.acf-radio-list li input[type=radio]').labelauty({
			// Development Mode
			// This will activate console debug messages
			development: false,
			
			// Trigger Class
			// This class will be used to apply styles
			class: "labelauty",
			
			// Use icon?
			// If false, then only a text label represents the input
			icon: true,
			
			// Use text label ?
			// If false, then only an icon represents the input
			label: true,
			
			// This value will be used to apply a minimum width to the text labels
			minimum_width: false,
			
			// Use the greatest width between two text labels ?
			// If this has a true value, then label width will be the greatest between labels
			same_width: true
		});
	}
	
	$('body').on('change', '#cancel_policy_agree_checkbox', function(e){
		if ($(this).prop('checked'))
		{
			$('.js-next').removeClass('disabled');
			$(this).closest('label.check-label').addClass('checked');
		}
		else {
			$('.js-next').addClass('disabled');
			$(this).closest('label.check-label').removeClass('checked');
		}
	});
	$('body').on('change', '#is_register', function(e){
		if ($(this).prop('checked'))
		{
			$(this).closest('label.check-label').addClass('checked');
		}
		else {
			$(this).closest('label.check-label').removeClass('checked');
		}
	});
	
	$('body').on('click', 'span.delete_photo', function(e){
		e.preventDefault();
		var delete_el = $(this);
		if (confirm(gl_remove_photo_text))
		{
			$('body').LoadingOverlay('show');
			$.ajax({
				type: "post",
				url: gl_ajax_url,
				data: {action: 'remove_booking_photo'},
				dataType: 'json',
			}).done(function(response){
				delete_el.closest('li').remove();
				$('body').LoadingOverlay('hide');
			});	
		}
	})
});