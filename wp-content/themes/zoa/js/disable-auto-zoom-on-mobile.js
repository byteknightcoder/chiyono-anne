/**
 * Disable auto zoom on mobile
 *
 * @package zoa
 */

 'use strict';

function preventZoomOnFocus() {
	document.documentElement.addEventListener( "touchstart", onTouchStart );
	document.documentElement.addEventListener( "focusin", onFocusIn );
}

var dont_disable_for = [ 'checkbox', 'radio', 'file', 'button', 'image', 'submit', 'reset', 'hidden' ];

function onTouchStart( evt ) {
	var tn = evt.target.tagName;

	// No need to do anything if the initial target isn't a known element
	// which will cause a zoom upon receiving focus.
	if (
		'SELECT' != tn &&
		'TEXTAREA' != tn &&
		(
			'INPUT' != tn ||
			dont_disable_for.indexOf( evt.target.getAttribute( 'type' ) ) > -1
		)
	) {
		return;
	}

	// disable zoom.
	setViewport( "width=device-width, initial-scale=1.0, user-scalable=0" );
}

// NOTE: for now assuming this focusIn is caused by user interaction.
function onFocusIn( evt ) {
	// reenable zoom.
	setViewport( "width=device-width, initial-scale=1.0, user-scalable=1" );
}

// add or update the <meta name="viewport"> element.
function setViewport( newvalue ) {
	var vpnode = document.documentElement.querySelector( 'head meta[name="viewport"]' );
	if ( vpnode ) {
		vpnode.setAttribute( 'content', newvalue );
	} else {
		vpnode = document.createElement( 'meta' );
		vpnode.setAttribute( 'name', 'viewport' );
		vpnode.setAttribute( 'content', newvalue );
	}
}

document.addEventListener( 'DOMContentLoaded', function() {
	var iOS = navigator.platform && /iPad|iPhone|iPod/.test( navigator.platform );
	if ( iOS ) {
		preventZoomOnFocus();
	}
} );
