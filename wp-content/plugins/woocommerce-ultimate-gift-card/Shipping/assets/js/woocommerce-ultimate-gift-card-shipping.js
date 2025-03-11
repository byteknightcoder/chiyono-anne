jQuery(document).ready(function($){

/*if($('#mwb_wgm_shipping_setting_enable').prop("checked") == true){
	$('.mwb_name_fieldss').show();
}
$('#mwb_wgm_shipping_setting_enable').click(function(){
  
  if($(this).prop("checked") == true){
    $('.mwb_name_fieldss').show();
  }
  else{
    $('.mwb_name_fieldss').hide();
  }
});*/


$( '.mwb_wgm_send_giftcard' ).change( function(){

		var radioVal = $(this).val();
		if(radioVal == "normal_mail"){
				
         	$('.mwb_name_fieldss').hide();
	     }
	     else if( radioVal == "download" ){
	     	$('.mwb_name_fieldss').show(); 
	     }
	     else if( radioVal == "shipping" ){
	     	$('.mwb_name_fieldss').show();
	     }
	     else if( radioVal == "customer_choose" ){
	     	$('.mwb_name_fieldss').show();
	     }
		
	} );
});