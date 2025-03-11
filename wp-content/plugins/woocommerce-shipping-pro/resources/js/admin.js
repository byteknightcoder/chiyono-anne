jQuery(document).ready(function(){
	// Toggle the Strict AND Logic settings display based on AND Logic Status
	ph_shipping_pro_toggle_based_on_checkbox_status( "#woocommerce_wf_woocommerce_shipping_pro_and_logic", "#woocommerce_wf_woocommerce_shipping_pro_strict_and_logic" );
	jQuery("#woocommerce_wf_woocommerce_shipping_pro_and_logic").change( function() {
		ph_shipping_pro_toggle_based_on_checkbox_status( "#woocommerce_wf_woocommerce_shipping_pro_and_logic", "#woocommerce_wf_woocommerce_shipping_pro_strict_and_logic" );
	})
});

/**
 * 
 * @param {string} tocheck Checkbox id with #
 * @param {string} to_toggle Element id or class which need to be hidden if checkbox is now checked use # with id and . with class
 */
function ph_shipping_pro_toggle_based_on_checkbox_status( tocheck, to_toggle ){
	if(  jQuery(tocheck).prop('checked') ) {
		jQuery(to_toggle).closest('tr').show();
	}
	else{
		jQuery(to_toggle).closest('tr').hide();
	}
}