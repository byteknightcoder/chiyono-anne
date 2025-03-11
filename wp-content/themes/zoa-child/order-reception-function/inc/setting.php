<?php

add_action('admin_menu', 'setup_menu_orf_setting');
function setup_menu_orf_setting() {
    add_submenu_page('woocommerce', __('Order Reception Function Setting', 'zoa'), __('Order Reception Function Setting', 'zoa'), 'manage_options', 'ch_orf_settings', 'ch_orf_settings', '', 15);
}

function ch_orf_settings() {
    if (isset($_POST['ch_save_orf_setting'])) {
        update_option('ch_orf_tag', $_REQUEST['ch_orf_tag']);
        update_option('ch_orf_end_date', $_REQUEST['ch_orf_end_date']);
        update_option('ch_order_type_even', $_REQUEST['ch_order_type_even']);
        update_option('ch_orf_sub_type_even', $_REQUEST['ch_orf_sub_type_even']);
    }
    ob_start();
    if (isset($_POST['ch_save_orf_setting'])) {
        ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
            <p><strong><?php esc_html_e('Settings saved.', 'zoa'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e('Dismiss this notice.', 'zoa'); ?></span></button></div>
        <?php
    }
?>
    <h3><?php esc_html_e('Order Reception Function Setting Page', 'zoa'); ?>:</h3>
    <hr/>
    <form action="" method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label><?php esc_html_e('Order type apply to Event', 'zoa'); ?></label></th>
                    <td>
                        <select name="ch_order_type_even">
                            <?php
                                $o_type = array(
                                    'オンラインストア',
                                    'アトリエ',
                                    '展示会',
                                    '旧オンラインストア',
                                    '百貨店',
                                    'メール',
                                    '受注会',
                                    'ダイレクト',
                                    'その他'
                                );
                                foreach ($o_type as $value) {
                                    $sl = '';
                                    if (get_option('ch_order_type_even') == $value) {
                                        $sl = ' selected="true"';
                                    }
                            ?>
                                <option <?php echo $sl; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>
                            <?php
                                } // endforeach
                            ?>
                        </select><i><?php esc_html_e('This value will use for ?type=value&sub_type=value in url of product.', 'zoa') ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Order sub type', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_orf_sub_type_even" value="<?php echo get_option('ch_orf_sub_type_even'); ?>"/><i><?php esc_html_e('This value will use for ?type=value&sub_type=value in url of product.', 'zoa') ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Tag of product', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_orf_tag" value="<?php echo get_option('ch_orf_tag'); ?>"/><i><?php esc_html_e('all product has this tag will show in new shop page.', 'zoa') ?></i>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('End date of Event Online Shop', 'zoa'); ?></label></th>
                    <td>
                        <input style="width: 300px;" type="text" name="ch_orf_end_date" value="<?php echo get_option('ch_orf_end_date'); ?>"/><i><?php esc_html_e('Format: Y-m-d H:i:s.  Example: 2021-09-15 23:59:59', 'zoa') ?>. if it is empty, that mean event never expire.</i>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="submit" name="ch_save_orf_setting" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'zoa'); ?>">
    </form>
    <?php
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
}
