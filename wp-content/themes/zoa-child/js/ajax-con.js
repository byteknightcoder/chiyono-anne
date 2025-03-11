var j = jQuery.noConflict();
j(document).ajaxComplete(function () {
    j('.checkbox-radio-block > input[type=radio], .checkbox-radio-block > input[type=checkbox]').each(function () {
        var label = j(this).next('label');
        var label_name = label.text();
        if (label.find('.labelauty-unchecked').text() !== '') {
            label_name = label.find('.labelauty-unchecked').text();
        }
        j(this).attr('data-labelauty', label_name);

        var input_clone = j(this);
        label.replaceWith(input_clone);
    });
    if (j('.checkbox-radio-block input[type=radio], .checkbox-radio-block input[type=checkbox]').length)
    {
        j('.checkbox-radio-block input[type=radio], .checkbox-radio-block input[type=checkbox]').labelauty({
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
	if (j('.field select').length) {
		j('.field select').addClass('input-select justselect').wrap('<div class="selectric-wrapper selectric-input-select selectric-responsive"></div>');
	}
	/*var listWrap = j('.cf-block > .field');
	listWrap.each(function() {
		if (j('.checkbox-radio-block input[type=radio]').length) {
			j('.checkbox-radio-block input[type=radio]')
			var wrapTag = '<div class="radio-wrap">';
			j('.checkbox-radio-block input[type=radio]').parent('.checkbox-radio-block').wrapAll(wrapTag);
		}
	});*/
});