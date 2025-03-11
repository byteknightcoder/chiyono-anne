/**
 * Easyzoom hanle
 *
 * @package zoa
 */

'use strict';

// Run scripts only elementor loaded.
var onElementorLoaded = function( callback ) {
	if ( undefined === window.elementorFrontend || undefined === window.elementorFrontend.hooks ) {
		setTimeout( function() {
			onElementorLoaded( callback )
		} );

		return;
	}

	callback();
}

// Use in product-variation.js.
function easyZoomHandle() {

	if ( window.matchMedia( '( max-width: 991px )' ).matches ) {
		return;
	}

	var image = jQuery( '.pro-carousel-image .pro-img-item' );

	if ( ! image.length || document.body.classList.contains( 'quick-view-open' ) ) {
		return;
	}

	var zoom = image.easyZoom(),
		api  = zoom.data( 'easyZoom' );

	api.teardown();
	api._init();
}

document.addEventListener( 'DOMContentLoaded', function() {
	// Setup image zoom.
	if ( window.matchMedia( '( min-width: 992px )' ).matches ) {
		jQuery( '.ez-zoom' ).easyZoom({
			loadingNotice: ''
		});
	}

	// For Elementor Preview Mode.
	onElementorLoaded( function() {
		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function() {
			easyZoomHandle();
		} );
	} );
} );
