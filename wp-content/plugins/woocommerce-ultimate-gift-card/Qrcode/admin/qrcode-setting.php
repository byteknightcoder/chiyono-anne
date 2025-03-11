<?php 
/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$saved = false;
$reset = false;
if(isset($_POST['mwb_wgm_qrcode_reset_save'])){
	unset($_POST['mwb_wgm_qrcode_reset_save']);
	delete_option('mwb_wgm_qrcode_enable');
	delete_option('mwb_wgm_qrcode_ecc_level');
	delete_option('mwb_wgm_qrcode_size');
	delete_option('mwb_wgm_qrcode_margin');
	delete_option('mwb_wgm_barcode_display_enable');
	delete_option('mwb_wgm_barcode_codetype');
	delete_option('mwb_wgm_barcode_size');
	unset($_POST);
	$reset = true;

}
if(isset($_POST['mwb_wgm_qrcode_setting_save']))
{
	unset($_POST['mwb_wgm_qrcode_setting_save']);
	
	if(!isset($_POST['mwb_wgm_qrcode_ecc_level']))
	{
		$_POST['mwb_wgm_qrcode_ecc_level'] = 'L';
	}
	if(!isset($_POST['mwb_wgm_qrcode_size']) || empty($_POST['mwb_wgm_qrcode_size']))
	{
		$_POST['mwb_wgm_qrcode_size'] = 3;
	}
	if(!isset($_POST['mwb_wgm_qrcode_margin']))
	{
		$_POST['mwb_wgm_qrcode_margin'] = 4;
	}
	if(!isset($_POST['mwb_wgm_barcode_display_enable']))
	{
		$_POST['mwb_wgm_barcode_display_enable'] = 'off';
	}
	if(!isset($_POST['mwb_wgm_barcode_codetype']))
	{
		$_POST['mwb_wgm_barcode_codetype'] = 'code39';
	}
	if(!isset($_POST['mwb_wgm_barcode_size']) || empty($_POST['mwb_wgm_barcode_size']))
	{
		$_POST['mwb_wgm_barcode_size'] = 20;
	}
	
	$postdata = $_POST;
	
	foreach($postdata as $key=>$data)
	{	
		update_option($key, $data);	
		$saved = true;	
	}	
}
if($saved){
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e('Settings saved',MWB_WGM_QR_DOM); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e('Dismiss this notice',MWB_WGM_QR_DOM); ?></span>
		</button>
	</div>
	<?php
}
if($reset){
	?>
	<div class="notice notice-success is-dismissible"> 
		<p><strong><?php _e('Settings are Reset',MWB_WGM_QR_DOM); ?></strong></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php _e('Dismiss this notice',MWB_WGM_QR_DOM); ?></span>
		</button>
	</div>
	<?php
}
$qrcode_enable = get_option("mwb_wgm_qrcode_enable", false);
$qrcode_level = get_option("mwb_wgm_qrcode_ecc_level", "L");
$qrcode_size = get_option("mwb_wgm_qrcode_size", 3);
$qrcode_margin = get_option("mwb_wgm_qrcode_margin", 4);

$barcode_display = get_option("mwb_wgm_barcode_display_enable", false);
$barcode_type = get_option("mwb_wgm_barcode_codetype", "code39");
$barcode_size = get_option("mwb_wgm_barcode_size", 40);
?>
<div class="mwb_table">
<table class="mwb_qrcode form-table mwb_wgm_general_setting">
	<tbody>		
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_qrcode_setting_enable"><?php _e('Enable QRCode', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('Check this box to enable QRCode. QRCode will be displayed instead of coupon Code', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_qrcode_enable">
					<input type="radio" <?php echo ($qrcode_enable == 'qrcode')?"checked='checked'":""?> name="mwb_wgm_qrcode_enable" id="mwb_wgm_qrcode_enable" class="input-text" value="qrcode"> <?php _e('Enable QRCode to display in Email Template',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_qrcode_ecc_level"><?php _e('ECC Level', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('ECC (Error Correction Capability) level. This compensates for dirt, damage or fuzziness of the barcode. ', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_qrcode_ecc_level">
					<select name ="mwb_wgm_qrcode_ecc_level" class="mwb_wgm_new_woo_ver_style_select">
						<option value="L" <?php selected($qrcode_level,'L'); ?>><?php _e('L-Smallest',MWB_WGM_QR_DOM); ?></option>
						<option value="M" <?php selected($qrcode_level,'M'); ?>><?php _e('M',MWB_WGM_QR_DOM); ?></option>			
						<option value="Q" <?php selected($qrcode_level,'Q'); ?>><?php _e('Q',MWB_WGM_QR_DOM); ?></option>
						<option value="H" <?php selected($qrcode_level,'H'); ?>><?php _e('H-Best',MWB_WGM_QR_DOM); ?></option>
					</select>
					<?php _e('Select the ECC Level',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_qrcode_size"><?php _e('Size', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('It is the Size of QR Code', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_qrcode_size">
					<input type="number" min="1" name="mwb_wgm_qrcode_size" id="mwb_wgm_qrcode_size" class="input-text mwb_wgm_new_woo_ver_style_text" value="<?php echo $qrcode_size; ?>"> <?php _e('Enter the size of the QRCode.',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_qrcode_margin"><?php _e('Margin', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('It is the Margin of QR Code', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_qrcode_margin">
					<input type="number" min="1" name="mwb_wgm_qrcode_margin" id="mwb_wgm_qrcode_margin" class="input-text mwb_wgm_new_woo_ver_style_text" value="<?php echo $qrcode_margin; ?>"> <?php _e('Enter the margin of the QRCode.',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
	</tbody>
</table>
<table class="mwb_barcode form-table mwb_wgm_general_setting">
	<tbody>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_barcode_enable"><?php _e('Enable Barcode', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('Check this box to enable Barcode. QRCode will be displayed instead of coupon Code', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_barcode_enable">
					<input type="radio" <?php echo ($qrcode_enable == 'barcode')?"checked='checked'":""?> name="mwb_wgm_qrcode_enable" id="mwb_wgm_barcode_enable" class="input-text" value="barcode"> <?php _e('Enable Barcode to display in Email Template',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_barcode_display_enable"><?php _e('Display Code', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('Check this box to display Coupon Code below Barcode.', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_barcode_display_enable">
					<input type="checkbox" <?php echo ($barcode_display == 'on')?"checked='checked'":""?> name="mwb_wgm_barcode_display_enable" id="mwb_wgm_barcode_display_enable" class="input-text"> <?php _e('Enable this to display Coupon Code',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_barcode_codetype"><?php _e('CodeType', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('It is the Code Type of Barcode', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_barcode_codetype">
					<select name ="mwb_wgm_barcode_codetype" class="mwb_wgm_new_woo_ver_style_select">
						<option value="code39" <?php selected($barcode_type,'code39'); ?>><?php _e('Code39',MWB_WGM_QR_DOM); ?></option>
						<option value="code25" <?php selected($barcode_type,'code25'); ?>><?php _e('Code25',MWB_WGM_QR_DOM); ?></option>			
						<option value="codabar" <?php selected($barcode_type,'codabar'); ?>><?php _e('Codeabar',MWB_WGM_QR_DOM); ?></option>
						<option value="code128" <?php selected($barcode_type,'code128'); ?>><?php _e('Code128',MWB_WGM_QR_DOM); ?></option>
						<option value="code128a" <?php selected($barcode_type,'code128a'); ?>><?php _e('Code128a',MWB_WGM_QR_DOM); ?></option>
						<option value="code128b" <?php selected($barcode_type,'code128b'); ?>><?php _e('Code128b',MWB_WGM_QR_DOM); ?></option>
					</select>
					<?php _e('Select the CodeType',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
		<tr valign="top">		
			<th scope="row" class="titledesc">
				<label for="mwb_wgm_barcode_size"><?php _e('Size', MWB_WGM_QR_DOM)?></label>
			</th>
			<td class="forminp forminp-text">
				<?php 
				$attribute_description = __('It is the Size of Barcode', MWB_WGM_QR_DOM);
				echo wc_help_tip( $attribute_description );
				?>
				<label for="mwb_wgm_barcode_size">
					<input type="number" min="1" name="mwb_wgm_barcode_size" id="mwb_wgm_barcode_size" class="input-text mwb_wgm_new_woo_ver_style_text" value="<?php echo $barcode_size; ?>"> <?php _e('Enter the size of the Barcode.',MWB_WGM_QR_DOM);?>
				</label>						
			</td>
		</tr>
	</tbody>
</table>
</div>
<p class="submit">
	<input type="submit" value="<?php _e('Save changes', MWB_WGM_QR_DOM); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_qrcode_setting_save" id="mwb_wgm_qrcode_setting_save" >
	<input type="submit" value="<?php _e('Reset', MWB_WGM_QR_DOM); ?>" class="button-primary woocommerce-save-button" name="mwb_wgm_qrcode_reset_save" id="mwb_wgm_qrcode_setting_save" >

</p>
<div class="clear"></div>

