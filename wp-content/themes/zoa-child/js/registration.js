jQuery(document).ready(function($) {
	$('input#last_name').parent().addClass('column_2 column_name');
	$('input#first_name').parent().addClass('column_2 column_name');
	$('input#last_name_kana').parent().addClass('column_2 column_kana');
	$('input#first_name_kana').parent().addClass('column_2 column_kana');
	$('select#birth_year').parent().addClass('column_3');
	$('select#birth_month').parent().addClass('column_3');
	$('select#birth_date').parent().addClass('column_3');
	var userForm = $('.wppb-user-forms > ul');
	userForm.children('li.column_3').wrapAll( '<ul class="column_wrap birth_column"></ul>' );
	userForm.children('li.column_2.column_name').wrapAll( '<ul class="column_wrap has_label name_column"></ul>' );
	userForm.children('li.column_2.column_kana').wrapAll( '<ul class="column_wrap has_label kana_column"></ul>' );
	/*$('.wppb-user-forms > ul > li').each(function () {
		if ($(this).find('ul.name_column').find('input#last_name_kana') || $('ul.name_column').find('input#first_name_kana')) {
			$(this).addClass('kana_column');
		}
	});*/
	$('ul.birth_column').wrap('<span class="value birth_date_value"></span>');
	$('ul.name_column').wrap('<span class="value names_value"></span>');
	$('ul.kana_column').wrap('<span class="value kana_value"></span>');
	$('span.birth_date_value').wrap('<li class="wppb-form-field birth_date"></li>');
	$('span.names_value').wrap('<li class="wppb-form-field names_field"></li>');
	$('span.kana_value').wrap('<li class="wppb-form-field kanas_field"></li>');
	var birthLabel = translation.dbirth_label;
	var nameLabel = translation.name_label;
	var kanaLabel = translation.kana_label;
	$('li.birth_date').prepend('<label class="form-row__label">'+birthLabel+'</div>');
	$('li.names_field').prepend('<label class="form-row__label">'+nameLabel+'</div>');
	$('li.kanas_field').prepend('<label class="form-row__label">'+kanaLabel+'</div>');
	userForm.children('li').children('label').addClass('form-row__label');
	userForm.children('li').children('input').wrap('<span class="value"></span>');
	$('select.custom_field_select').addClass('input-select justselect');
	$('select.custom_field_select').wrap('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
});