<?php
/**
 * Plugin Name: Birthday Email
 * Plugin URI: http://praywish.in.nf
 * Description: This plugin allow choose product for each month, then get to send in birthday email and this will setup in cron job of server.
 * Version: 1.0.0
 * Author: Chien Le
 * Author URI: http://praywish.in.nf
 */
if (!defined('BE_PLUGIN_DIR')) {
    $plugin_dir = plugin_dir_path(__FILE__);
    define('BE_PLUGIN_DIR', $plugin_dir);
}
if (!defined('BE_PLUGIN_URL')) {
    $plugin_url = plugins_url('/', __FILE__);
    define('BE_PLUGIN_URL', $plugin_url);
}

function plugin_css_admin_file() {
    wp_enqueue_style('birthday-ui-admin-css', BE_PLUGIN_URL . 'css/birthday-email-admin.css');
    wp_enqueue_script('birthday-ui-admin-js', BE_PLUGIN_URL . 'js/birthday-email-admin.js', array('jquery'));
}

add_action('admin_enqueue_scripts', 'plugin_css_admin_file');

add_action('admin_footer', 'products_data_json_call');

function products_data_json_call() {
    if ($_REQUEST['page'] != 'birthday-email') {
        return;
    }
    if (!function_exists('wc_get_products')) {
        require_once '/includes/wc-product-functions.php';
    }

    $args = array(
        'posts_per_page' => -1
    );
    $result = wc_get_products($args);

    $arr = array();
    if (!empty($result)) {
        foreach ($result as $value) {
            if (!empty($value->sku)) {
                $arr[] = array("value" => (string) $value->sku, "label" => (string) $value->name . ' - ' . $value->sku);
            }
        }
    }

    echo "<script type='text/javascript'>var products_json=" . json_encode($arr) . "</script>";
}

function register_birthday_email_submenu_page() {
    add_submenu_page('woocommerce', 'Birthday Auto Email Page', 'Birthday Auto Email', 'manage_options', 'birthday-email', 'birthday_email_submenu_page_callback');
}

function birthday_email_submenu_page_callback() {

    $month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    if (isset($_POST['save_bd_setting'])) {
        update_option('birthday_subject_email_rm', $_REQUEST['birthday_subject_email_rm']);
        update_option('birthday_content_email_rm', $_REQUEST['birthday_content_email_rm']);
        update_option('birthday_disable_cron', $_REQUEST['birthday_disable_cron']);
        foreach ($month as $k => $value) {
            $k++;
            update_option('be_month_' . $k, $_REQUEST['be_month_' . $k]);
        }
        update_option('brth_css_mobile', $_REQUEST['brth_css_mobile']);
        update_option('brth_css_desktop', $_REQUEST['brth_css_desktop']);
    }
    if (isset($_POST['test_email_submit'])) {
        if (!empty($_REQUEST['email_test'])) {
            $current_month = date_i18n('n');
            if($current_month==12){
                $current_month=1;
            }else{
                $current_month=$current_month+1;
            }
            $products = get_option('be_month_' . $current_month);
            $subject = trim(get_option('birthday_subject_email_rm', false));
            $message = trim(get_option('birthday_content_email_rm', false));
            $message = stripslashes($message);
            $pos = strpos($message, '{coupon_code}');
            if ($pos === false) {
                //don't create coupon code to save time run
            } else {
                $coupon_code = coupon_code_each();
                $message = str_replace('{coupon_code}', $coupon_code, $message);
            }
            //$message = nl2br($message);
            $contents = '';
            if (!empty($products)) {
                $products = trim($products);
                $last_chart = substr($products, -1);
                if ($last_chart == ',') {
                    $skus = substr($products, 0, strlen($products) - 1);
                } else {
                    $skus = $products;
                }
                $arr_products = explode(",", $skus);
                $contents = build_products_list($arr_products);
            }
            $message = str_replace('{recommend_products}', $contents, $message);
            $message = str_replace('test.chiyono-anne.com', 'chiyono-anne.com', $message);
            //get customers, who is birthday in current month
            //send email
            $site_title = get_bloginfo('name');
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
            $attachments = array();
            add_filter('wp_mail_content_type', 'ch_set_html_content_type');
            wp_mail(trim($_REQUEST['email_test']), $subject, $message, $headers, $attachments);
            remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
            echo '<strong style="color: green; background: greenyellow; padding: 5px 50px;">Sent email completed. Please check your email inbox.</strong>';
        }
    }
    ob_start();
    ?>
    <?php
    if (isset($_POST['save_bd_setting'])) {
        ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
        <?php
    }
    ?>
    <h3>Birthday Email Setting Page:</h3>
<!--    <i>Url to setup cron job on server: <span style="background: aqua;"><?php //echo admin_url('admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9'); ?></span></i><br/>-->
    <strong>Click on the link to get email to check:</strong><a class="button button-primary" href="<?php echo admin_url('admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9&send_type=getcsv'); ?>" target="_blank">Get BD email in next month to check before send.</a>
                <strong>Run manual by click this link:</strong><a class="button button-primary" onclick="return confirm('Are you sure send BD email to customers has BD in next month?')" href="<?php echo admin_url('admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9&send_type=manual&via=admin'); ?>" target="_blank">Send BD email to customers has BD in next month.</a>
                <hr/>
                <strong>Click on the link to get email to check:</strong><a class="button button-danger" href="<?php echo admin_url('admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9&send_type=getcsv&time=current'); ?>" target="_blank">Get BD email in CURRENT month to check before send.</a>
                <strong>Run manual by click this link:</strong><a class="button button-danger" onclick="return confirm('Are you sure send BD email to customers has BD in CURRENT month?')" href="<?php echo admin_url('admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9&send_type=manual&time=current&via=admin'); ?>" target="_blank">Send BD email to customers has BD in CURRENT month.</a>
                <hr/>
                <form action="" method="POST">
                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label>Disable Cronjob auto send email</label></th>
                                <td>
                                    <input type="checkbox" <?php if(get_option('birthday_disable_cron', '')=='yes'){echo 'checked="true"';} ?> value="yes" name="birthday_disable_cron"/>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>Email subject</label></th>
                                <td>
                                    <textarea rows="2" cols="100" name="birthday_subject_email_rm"><?php echo get_option('birthday_subject_email_rm'); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>Email content</label></th>
                                <td>
                                    <?php
                                    $common_notion_content_email = get_option('birthday_content_email_rm');
                                    $common_notion_content_email = stripslashes($common_notion_content_email);
                                    wp_editor($common_notion_content_email, 'birthday_content_email_rm', array('textarea_name' => 'birthday_content_email_rm', 'media_buttons' => false, 'editor_height' => 250, 'teeny' => true));
                                    ?>
                                    <p>
                                        Shortcode: {recommend_products}, {coupon_code}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>CSS for Mobile</label></th>
                                <td>
                                    <textarea rows="20" cols="100" name="brth_css_mobile"><?php echo get_option('brth_css_mobile'); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>CSS for Desktop</label></th>
                                <td>
                                    <textarea rows="20" cols="100" name="brth_css_desktop"><?php echo get_option('brth_css_desktop'); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Enter email to test email:</strong><input name="email_test" style="width: 300px;" /> <input type="submit" name="test_email_submit" id="test_email_submit" class="button button-primary" value="Test Email."><hr/></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Recommend products for each month in email content.<i> (Please type product name Or sku in textbox to filter and choose products).</i></strong><hr/></td>
                            </tr>
                            <?php
                            foreach ($month as $k => $value) {
                                $k++;
                                ?>
                                <tr>
                                    <td><?php echo $value; ?></td>
                                    <td>
                                        <input class="be_month" type="text" value="<?php echo get_option('be_month_' . $k); ?>" name="be_month_<?php echo $k; ?>" id="be_<?php echo $value; ?>"/>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <input type="submit" name="save_bd_setting" id="submit" class="button button-primary" value="Save">
                </form>
                <?php
                $contents = ob_get_contents();
                ob_end_clean();
                echo $contents;
            }

            add_action('admin_menu', 'register_birthday_email_submenu_page', 99);


            add_action('wp_ajax_birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9', 'birthday_email_cronjob_request');
            add_action('wp_ajax_nopriv_birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9', 'birthday_email_cronjob_request');

//http://localhost/wp551/wp-admin/admin-ajax.php?action=birthday_email_cronjob_request_asdAs324Af2424GsgKKsdDDD33D7J7K9L9
            function birthday_email_cronjob_request() {
                // if(get_option('birthday_disable_cron', '')=='yes'&&!isset($_REQUEST['via'])){
                //     wp_mail('kyoko@heart-hunger.com', 'Please check Birthday function in admin to send emails by manual. Cronjob auto send birthday email disabled.', 'Please check Birthday function in admin to send emails by manual. Cronjob auto send birthday email disabled.');
                //     exit();
                // }
               // $type_send='manual';//$_REQUEST['send_type'];//Email automatically every 20th of the month

               $type_send = isset($_REQUEST['send_type']) ? sanitize_text_field($_REQUEST['send_type']) : 'manual';




                $current_month = date_i18n('n');
                if($current_month==12){
                    $current_month=1;
                }else{
                    $current_month=$current_month+1;
                }
                if(isset($_REQUEST['time'])&&$_REQUEST['time']=='current'){
                    $current_month = date_i18n('n');
                }
                $products = get_option('be_month_' . $current_month);
                $subject = trim(get_option('birthday_subject_email_rm', false));
                $message = trim(get_option('birthday_content_email_rm', false));
                if(empty($message)||empty($subject)){
                    echo 'Please enter Subject and Content of Email.';
                    wp_mail('kyoko@heart-hunger.com', 'Bithday email.', 'Please enter Subject and Content of Email.');
                    exit();
                }
                $message = stripslashes($message);
                $pos = strpos($message, '{coupon_code}');
                //$message = nl2br($message);
                $contents = '';
                if (!empty($products)) {
                    $products = trim($products);
                    $last_chart = substr($products, -1);
                    if ($last_chart == ',') {
                        $skus = substr($products, 0, strlen($products) - 1);
                    } else {
                        $skus = $products;
                    }
                    $arr_products = explode(",", $skus);
                    $contents = build_products_list($arr_products);
                }
                $message = str_replace('{recommend_products}', $contents, $message);
                //get customers, who is birthday in current month
                //send email
                $site_title = get_bloginfo('name');
                $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $site_title . ' <hello@chiyono-anne.com>');
                $attachments = array();
                add_filter('wp_mail_content_type', 'ch_set_html_content_type');
               
              //  $blogusers = get_users(['role__in' => ['customer', 'subscriber'], 'fields' => array('ID', 'user_email')]);
                
                $args = array(
                    'role__in' => ['customer', 'subscriber'],
                    'fields' => array('ID', 'user_email'),  
                    'meta_query' => array(
                        array(
                            'key' => 'account_birth',  
                            'compare' => 'EXISTS'  
                        ),
                    ),
                    'no_found_rows' => true,  
                    'cache_results' => false, 
                );
                $blogusers = get_users($args);


                $k = 0;
                $sent=array();
                if (!empty($blogusers)) {
                    foreach ($blogusers as $value) {
                        $user_meta = get_user_meta($value->ID, 'account_birth');
                        if (!empty($user_meta[0]['year'])&&!empty($user_meta[0]['day'])&&!empty($user_meta[0]['month'])) {
                            $ch_month= str_replace("月", "", $user_meta[0]['month']);
                            $bth=$user_meta[0]['year'].'-'.$ch_month.'-'.$user_meta[0]['day'];
                            if($user_meta[0]['month'] == $current_month . '月' || $current_month == date("n", strtotime($bth))){
                                if (!empty($value->user_email)) {
                                    if($type_send=='manual'){
                                        if ($pos === false) {
                                            //don't create coupon code to save time run
                                        } else {
                                            $coupon_code = coupon_code_each();
                                            $message = str_replace('{coupon_code}', $coupon_code, $message);
                                        }
                                        wp_mail($value->user_email, $subject, $message, $headers, $attachments);
                                        update_user_meta($value->ID, 'is_sent_email_birthday', 'yes');
                                        update_user_meta($value->ID, 'is_sent_email_birthday_date', date_i18n('Y-m-d H:i:s'));
                                    }
                                    $k++;
                                    $sent[]=$value->user_email;
                                }
                            }
                        }
                    }
                }
                if($type_send =='manual'){
                    wp_mail('kyoko@heart-hunger.com', 'Bithday email sent', json_encode($sent));
                    remove_filter('wp_mail_content_type', 'ch_set_html_content_type');
                    echo 'You sent to ' . $k . ' customers!<br/>';
                    echo json_encode($sent);
                }else{
                    if (!empty($sent)) {
                        $file_name = date('YmdHis') . '_birthday_emails.csv';
                        header('Content-Type: application/csv');
                        header('Content-Disposition: attachment; filename="' . $file_name . '";');
                        $output = fopen('php://output', 'w');
                        fputcsv($output, array('email'));
                        foreach ($sent as $user) {
                            fputcsv($output, array($user));
                        }
                        fclose($output);
                        exit();
                    }else{
                        echo "Don't have any customer have birthday in next month.";
                        exit();
                    }
                }
                exit();
            }

            function build_products_list($arr_products) {
                $contents = '';
                if (!empty($arr_products)) {
                    ob_start();
                    $index = 0;
                    ?>
                    <div style="background-color:#fbfbfb;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                            <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                                <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                    <div style="width:100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                            <!--<![endif]-->
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 5px; padding-right: 10px; padding-bottom: 5px; padding-left: 10px;" valign="top">
                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" height="5" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 5px; width: 100%;" valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td height="5" style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div style="background-color:#fbfbfb;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                                <div class="col num12" style="min-width: 320px; max-width: 640px; display: table-cell; vertical-align: top; width: 640px;">
                                    <div style="width:100% !important;">
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                            <div align="center" class="img-container center autowidth ttl_image" style="padding-right: 0px;padding-left: 0px;"><img align="center" alt="Birthday Reccomend from chiyono" border="0" class="center autowidth" src="https://chiyono-anne.com/chiyono/wp-content/themes/zoa-child/images/email/brec_ttl_2020.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 260px; display: block;" title="Birthday Reccomend from chiyono" width="260"></div>
                                        </div>
                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                        <div style="color:#555555;font-family:ヒラギノ角ゴ Pro W3, Hiragino Kaku Gothic Pro, Osaka, メイリオ, Meiryo, ＭＳ Ｐゴシック, MS PGothic, sans-serif;line-height:1.5;padding-top:10px;padding-right:15px;padding-bottom:10px;padding-left:15px;">
                                            <div style="line-height: 1.5; font-size: 12px; font-family: ヒラギノ角ゴ Pro W3, Hiragino Kaku Gothic Pro, Osaka, メイリオ, Meiryo, ＭＳ Ｐゴシック, MS PGothic, sans-serif; color: #555555; mso-line-height-alt: 18px;">
                                                <p class="p_mb_12" style="font-size: 14px; line-height: 1.5; word-break: break-word; text-align: center;font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;mso-line-height-alt: 21px;margin: 5px 0 0 0;color: #555555;">誕生石の Citrine と Topaz の色のシルクで、自分らしく</p>

                                            </div>
                                        </div>
                                        <!--[if mso]></td></tr></table><![endif]-->
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background-color:#fbfbfb;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                            <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                                <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                    <div style="width:100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                            <!--<![endif]-->
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px;" valign="top">
                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" height="5" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 5px; width: 100%;" valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td height="5" style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <?php
                    foreach ($arr_products as $sku) {
                        if (!empty($sku)) {
                            $product_id = wc_get_product_id_by_sku(trim($sku));
                            if ($product_id) {
                                $product = wc_get_product($product_id);
                                if ($product) {
                                    $serie_cat = get_the_terms(get_post($product_id), 'series');
                                    $image_id = $product->get_image_id();
                                    $image_url = wp_get_attachment_image_url($image_id, 'large');
                                    if ($index % 2 == 0) {
                                        ?>
                                        <!--BD PRODUCTS -->
                                        <div style="background-color:#fbfbfb;">
                                            <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                                                <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fbfbfb;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                                                <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                                                    <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                                        <div style="width:100% !important;">
                                                            <!--[if (!mso)&(!IE)]><!-->
                                                            <div style="border-top:1px solid #E8E8E8; border-left:0px solid transparent; border-bottom: 0px solid transparent; border-right:0px solid transparent; padding: 0px;">
                                                                <!--<![endif]-->
                                                                <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                <div style="background-color:transparent;">
                                                                    <div class="block-grid two-up" style="Margin: 0 auto; min-width: 420px; max-width: 320px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
                                                                        <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                                                                            <div class="col num6" style="min-width: 110px; max-width: 210px; display: table-cell; vertical-align: top; width: 210px;">
                                                                                <div style="width:100% !important;">
                                                                                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:15px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                                                                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                                        <div style="border-bottom: 1px solid #E8E8E8; color:#555555;line-height:1.5;padding-top:10px;padding-right:5px;padding-bottom:10px;padding-left:15px;">
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #000000; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_11" style="font-size: 11px; line-height: 1.35; word-break: break-word; text-align: left;font-family: 'Archivo', sans-serif; font-weight: 500; letter-spacing: 2px;mso-line-height-alt: 21px;margin: 0 0 12px 0;color: #000000;"><?php echo strtoupper($serie_cat[0]->name); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #000000; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_14" style="font-size: 16px; line-height: 1.35; word-break: break-word; text-align: left;font-family: 'EB Garamond',Georgia,MSung PRC Medium,serif;mso-line-height-alt: 21px;margin: 0 0 12px 0;color: #000000;"><?php echo $product->get_name(); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #555555; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_11" style="font-size: 13px; line-height: 1.5; word-break: break-word; text-align: left;font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;mso-line-height-alt: 15px;margin: 0 0 12px 0;color: #555555;"><?php echo get_the_subtitle($product_id); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #555555; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_12" style="font-size: 13px; line-height: 1.5; word-break: break-word; text-align: left;font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;mso-line-height-alt: 15px;margin: 0 0 12px 0;color: #555555;"><span style="font-family: 'Archivo', sans-serif; font-weight: 400;"><?php echo $product->get_price_html(); ?></span> <small><span style="font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;"></span></small></p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!--[if mso]></td></tr></table><![endif]-->
                                                                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                                        <div style="padding-top:15px;padding-right:10px;padding-bottom:15px;padding-left:15px;">
                                                                                            <div align="center" class="img-container center fullwidthOnMobile fixedwidth" style="">
                                                                                                <a href="<?php echo get_permalink($product_id); ?>" tabindex="-1" target="_blank"><img align="center" alt="Shop now" border="0" class="center fullwidthOnMobile fixedwidth" src="https://chiyono-anne.com/chiyono/wp-content/themes/zoa-child/images/email/shop_now@2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 140px; display: block;" title="Shop now" width="140"/></a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!--[if mso]></td></tr></table><![endif]-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col num6" style="min-width: 110px; max-width: 210px; display: table-cell; vertical-align: top; width: 210px; border-left:1px solid #E8E8E8">
                                                                                <div style="width:100% !important;">
                                                                                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                                                                        <div align="center" class="img-container center fullwidthOnMobile fixedwidth" style="padding: 10px;">
                                                                                            <a href="<?php echo get_permalink($product_id); ?>" tabindex="-1" target="_blank"><img align="center" alt="Item01" border="0" class="center fullwidthOnMobile fixedwidth" src="<?php echo $image_url; ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 320px; display: block;" title="Item01" width="320"/></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--[if mso]></td></tr></table><![endif]-->

                                                            </div>
                                                            <!--<![endif]-->
                                                        </div>
                                                    </div>
                                                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                                    <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                                                </div>
                                                <!--copy to here-->
                                            </div>
                                        </div>
                                        <!--/BD PRODUCTS-->
                                        <?php
                                    } else {
                                        ?>
                                        <!--BD PRODUCTS -->
                                        <div style="background-color:#fbfbfb;">
                                            <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                                                <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                                                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fbfbfb;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                                                <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                                                    <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                                        <div style="width:100% !important;">
                                                            <!--[if (!mso)&(!IE)]><!-->
                                                            <div style="border-top:1px solid #E8E8E8; border-left:0px solid transparent; border-bottom: 0px solid transparent; border-right:0px solid transparent; padding: 0px;">
                                                                <!--<![endif]-->
                                                                <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                <div style="background-color:transparent;">
                                                                    <div class="block-grid two-up" style="Margin: 0 auto; min-width: 420px; max-width: 320px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: transparent;">
                                                                        <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                                                                            <div class="col num6" style="min-width: 110px; max-width: 210px; display: table-cell; vertical-align: top; width: 210px;">
                                                                                <div style="width:100% !important;">
                                                                                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:5px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                                                                        <div align="center" class="img-container center fullwidthOnMobile fixedwidth" style="padding: 10px;">
                                                                                            <a href="<?php echo get_permalink($product_id); ?>" tabindex="-1" target="_blank"><img align="center" alt="Item01" border="0" class="center fullwidthOnMobile fixedwidth" src="<?php echo $image_url; ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 320px; display: block;" title="Item01" width="320"/></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col num6" style="min-width: 110px; max-width: 210px; display: table-cell; vertical-align: top; width: 210px;border-left:1px solid #E8E8E8">
                                                                                <div style="width:100% !important;">
                                                                                    <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:15px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                                                                    <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                                        <div style="border-bottom: 1px solid #E8E8E8; color:#555555;line-height:1.5;padding-top:10px;padding-right:5px;padding-bottom:10px;padding-left:15px;">
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #000000; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_11" style="font-size: 11px; line-height: 1.35; word-break: break-word; text-align: left;font-family: 'Archivo', sans-serif; font-weight: 500; letter-spacing: 2px;mso-line-height-alt: 21px;margin: 0 0 12px 0;color: #000000;"><?php echo strtoupper($serie_cat[0]->name); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #000000; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_14" style="font-size: 16px; line-height: 1.35; word-break: break-word; text-align: left;font-family: 'EB Garamond',Georgia,MSung PRC Medium,serif;mso-line-height-alt: 21px;margin: 0 0 12px 0;color: #000000;"><?php echo $product->get_name(); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #555555; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_11" style="font-size: 13px; line-height: 1.5; word-break: break-word; text-align: left;font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;mso-line-height-alt: 15px;margin: 0 0 12px 0;color: #555555;"><?php echo get_the_subtitle($product_id); ?></p>
                                                                                            </div>
                                                                                            <div style="line-height: 1.5; font-size: 12px; color: #555555; mso-line-height-alt: 18px;">
                                                                                                <p class="p_mb_12" style="font-size: 13px; line-height: 1.5; word-break: break-word; text-align: left;font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;mso-line-height-alt: 15px;margin: 0 0 12px 0;color: #555555;"><span style="font-family: 'Archivo', sans-serif; font-weight: 400;"><?php echo $product->get_price_html(); ?></span> <small><span style="font-family: 'ヒラギノ角ゴ Pro W3', 'Hiragino Kaku Gothic Pro', Osaka, メイリオ, Meiryo, 'ＭＳ Ｐゴシック', 'MS PGothic', sans-serif;"></span></small></p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!--[if mso]></td></tr></table><![endif]-->
                                                                                        <!--[if mso]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 15px; padding-left: 15px; padding-top: 10px; padding-bottom: 10px; font-family: sans-serif"><![endif]-->
                                                                                        <div style="padding-top:15px;padding-right:10px;padding-bottom:15px;padding-left:15px;">
                                                                                            <div align="center" class="img-container center fullwidthOnMobile fixedwidth" style="">
                                                                                                <a href="<?php echo get_permalink($product_id); ?>" tabindex="-1" target="_blank"><img align="center" alt="Shop now" border="0" class="center fullwidthOnMobile fixedwidth" src="https://chiyono-anne.com/chiyono/wp-content/themes/zoa-child/images/email/shop_now@2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 140px; display: block;" title="Shop now" width="140"/></a>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!--[if mso]></td></tr></table><![endif]-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--[if mso]></td></tr></table><![endif]-->

                                                            </div>
                                                            <!--<![endif]-->
                                                        </div>
                                                    </div>
                                                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                                    <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                                                </div>
                                                <!--copy to here-->
                                            </div>
                                        </div>
                                        <!--/BD PRODUCTS-->
                                        <?php
                                    }
                                    $index++;
                                }
                            }
                        }
                    }
                    ?>
                    <div style="background-color:#fbfbfb;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fbfbfb;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                            <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:5px; padding-bottom:5px;"><![endif]-->
                                <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                    <div style="width:100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding: 0px;">
                                            <!--<![endif]-->
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 10px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px;" valign="top">
                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" height="10" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 0px solid transparent; height: 10px; width: 100%;" valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td height="10" style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <div style="background-color:#fbfbfb;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:transparent;">
                                <div class="col num12" style="min-width: 320px; max-width: 640px; display: table-cell; vertical-align: top; width: 640px;">
                                    <div style="width:100% !important;">
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:0px; padding-bottom:0px; padding-right: 0px; padding-left: 0px;">
                                            <div align="center" class="img-container center autowidth ttl_image" style="padding-right: 0px;padding-left: 0px;"><a href="https://chiyono-anne.com/shop-all/?utm_source=nl201102&amp;utm_medium=email&amp;utm_campaign=bdEmail&amp;utm_content=ViewMorelink" tabindex="-1" target="_blank"><img align="center" alt="View more" border="0" class="center autowidth" src="https://chiyono-anne.com/chiyono/wp-content/themes/zoa-child/images/email/btn_viewmore.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; border: 0; height: auto; width: 100%; max-width: 210px; display: block;" title="View more" width="210"></a></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background-color:transparent;">
                        <div class="block-grid" style="Margin: 0 auto; min-width: 320px; max-width: 700px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; background-color: #ffffff;">
                            <div style="border-collapse: collapse;display: table;width: 100%;background-color:#ffffff;">
                            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:transparent;"><tr><td align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:700px"><tr class="layout-full-width" style="background-color:#ffffff"><![endif]-->
                            <!--[if (mso)|(IE)]><td align="center" width="700" style="background-color:#ffffff;width:700px; border-top: 0px solid transparent; border-left: 0px solid transparent; border-bottom: 0px solid transparent; border-right: 0px solid transparent;" valign="top"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right: 0px; padding-left: 0px; padding-top:15px; padding-bottom:5px;"><![endif]-->
                                <div class="col num12" style="min-width: 320px; max-width: 700px; display: table-cell; vertical-align: top; width: 700px;">
                                    <div style="width:100% !important;">
                                        <!--[if (!mso)&(!IE)]><!-->
                                        <div style="border-top:0px solid transparent; border-left:0px solid transparent; border-bottom:0px solid transparent; border-right:0px solid transparent; padding-top:15px; padding-bottom:5px; padding-right: 0px; padding-left: 0px;">
                                            <!--<![endif]-->
                                            <table border="0" cellpadding="0" cellspacing="0" class="divider" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top" width="100%">
                                                <tbody>
                                                    <tr style="vertical-align: top;" valign="top">
                                                        <td class="divider_inner" style="word-break: break-word; vertical-align: top; min-width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; padding-top: 10px; padding-right: 0px; padding-bottom: 10px; padding-left: 0px;" valign="top">
                                                            <table align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="table-layout: fixed; vertical-align: top; border-spacing: 0; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-top: 1px solid #E8E8E8; width: 100%;" valign="top" width="100%">
                                                                <tbody>
                                                                    <tr style="vertical-align: top;" valign="top">
                                                                        <td style="word-break: break-word; vertical-align: top; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;" valign="top"><span></span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!--[if (!mso)&(!IE)]><!-->
                                        </div>
                                        <!--<![endif]-->
                                    </div>
                                </div>
                                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                                <!--[if (mso)|(IE)]></td></tr></table></td></tr></table><![endif]-->
                            </div>
                        </div>
                    </div>
                    <?php
                    $contents = ob_get_contents();
                    ob_end_clean();
                }
                return $contents;
            }

            function generate_coupon_code_each() {
                global $wpdb;

                // Get an array of all existing coupon codes
                $coupon_codes = $wpdb->get_col("SELECT post_name FROM $wpdb->posts WHERE post_type = 'shop_coupon'");

                for ($i = 0; $i < 1; $i++) {
                    $generated_code = strtolower(wp_generate_password(10, false));

                    // Check if the generated code doesn't exist yet
                    if (in_array($generated_code, $coupon_codes)) {
                        $i--; // continue the loop and generate a new code
                    } else {
                        break; // stop the loop: The generated coupon code doesn't exist already
                    }
                }
                return $generated_code;
            }

            function coupon_code_each() {
                // Here below define your coupons discount ammount

                $discount_amounts = array(10);

                // Set some coupon data by default
                $date_expires = date_i18n('Y-m-d', strtotime('+31 days'));
                $discount_type = 'percent';

                // Loop through the defined array of coupon discount amounts
                foreach ($discount_amounts as $coupon_amount) {
                    // Get an emty instance of the WC_Coupon Object
                    $coupon = new WC_Coupon();

                    // Generate a non existing coupon code name
                    $coupon_code = generate_coupon_code_each();

                    // Set the necessary coupon data (since WC 3+)
                    $coupon->set_code($coupon_code);
                    $coupon->set_discount_type($discount_type);
                    $coupon->set_amount($coupon_amount);

                    $coupon->set_date_expires($date_expires);
                    $coupon->set_usage_limit(1);
                    $coupon->set_usage_limit_per_user(1);
                    $coupon->set_individual_use(false);

                    // Create, publish and save coupon (data)
                    $coupon->save();
                }
                return strtoupper($coupon_code);
            }
            
add_filter( 'haet_mail_css_mobile', function( $css ){
    $css .= get_option('brth_css_mobile');
    return $css;
});
add_filter( 'haet_mail_css_desktop', function( $css ){
    $css .= get_option('brth_css_desktop');
    return $css;
});