'use strict';

window.addEventListener('load', function() {
    var _api = wp.customize;

    /* FOOTER WIDGET COLUMN
    ***************************************************/
    _api('ft_column', function(value) {
        value.bind(function(newval) {
            var _widget = document.getElementsByClassName('widget-box')[0];
            if (!_widget) return;
            _widget.className = 'row widget-box footer-col-' + newval;
        });
    });
});
