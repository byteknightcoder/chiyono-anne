<?php

class WCCM_CustomerImport {

    var $errors;
    var $messages;

    public function __construct() {
        if (is_admin())
            add_action('wp_ajax_upload_csv', array(&$this, 'process_csv_upload_ajax'));
    }

    function process_csv_upload_ajax() {

        $csv_array = explode("<#>", $_POST['csv']);
        $this->process_uploaded_file($csv_array);
        if (!empty($this->messages)) {
            foreach ($this->messages as $msg)
                echo '<div id="message chien1" class="updated"><p>' . $msg . '</p></div>';
        }
        if (!empty($this->errors)) {
            ?>
            <div class="error">
                <ul>
                    <?php
                    foreach ($this->errors as $error)
                        echo "<li>" . $error . "</li>\n";
                    ?>
                </ul>
            </div>
            <?php
        }
        wp_die();
    }

    private function process_uploaded_file($csv_array = null) {
        global $wccm_customer_model;
        global $wpdb;
        $customerAdded = 0;
        $customerUpdated = 0;
        $this->errors = array();
        $this->messages = array();
        $columns_names = array("ID",
            "Name",
            "Surname",
            "Login",
            "Role",
            "Password",
            "Password hash",
            "Email",
            "Notes",
            "Billing name",
            "Billing surname",
            "Billing email",
            "Billing phone",
            "Billing company",
            "Billing address",
            "Billing address 2",
            "Billing postcode",
            "Billing city",
            "Billing state",
            "Billing country",
            "Shipping name",
            "Shipping surname",
            "Shipping phone",
            "Shipping company",
            "Shipping address",
            "Shipping address 2",
            "Shipping postcode",
            "Shipping city",
            "Shipping state",
            "Shipping country",
            "billing_last_name_kana",
            "billing_first_name_kana",
            "shipping_last_name_kana",
            "shipping_first_name_kana",
            "birthday",
            "phone"
        );
        $colum_index_to_name = array();

        $import_type = $_POST['import_type'];
        /* if($imageFileType != "csv" ) 
          {
          array_push($this->errors , new WP_Error('empty', __('Sorry, only CSV files are allowed.', 'woocommerce-customers-manager')));
          $uploadOk = 0;
          }
          else */ {

            $row = 1;
            //colum detection
            //if (($handle = fopen($_FILES["fileToUpload"]["tmp_name"], "r")) !== FALSE) 
            if ($csv_array != null) {
                //while (($data = fgetcsv($handle)) !== FALSE) 
                $wpuef_extra_fields = array();
                $check_header = array();
                foreach ($csv_array as $csv_row) {
                    //wccm_var_dump($csv_row);
                    $csv_row = str_replace('\"', '"', $csv_row);
                    $data = str_getcsv($csv_row);
                    $num = count($data);
                    $user = array();

                    //empty row
                    if (empty($csv_row) || $csv_row == "" || $csv_row == '""')
                        continue;
                    /* if(!is_array($data) || count($data) < 2)
                      continue; */

                    for ($c = 0; $c < $num; $c++) {
                        if ($row == 1) {
                            foreach ($columns_names as $title)
                                if ($title == $data[$c])
                                    $colum_index_to_name[$c] = $title;
                                elseif (strpos(strtolower($data[$c]), 'wpuef_') !== false) {
                                    $id = explode("_", $data[$c]);
                                    $wpuef_extra_fields[$c] = array('id' => $id[1], 'data' => "");
                                }
                            //check header to exist import start
                            if (isset($data[$c]) && !empty($data[$c]) && !in_array($data[$c], $columns_names)) {
                                $check_header[] = $data[$c];
                            }
                        } else {

                            if (isset($colum_index_to_name[$c])) {
                                //echo $c." ".$colum_index_to_name[$c].": ".$data[$c]."<br />\n";
                                $user[$colum_index_to_name[$c]] = $data[$c];
                            } elseif (isset($wpuef_extra_fields[$c]))
                                $wpuef_extra_fields[$c]['data'] = $data[$c];
                        }
                    }
                    if (!empty($check_header)) {
                        array_push($this->errors, 'Some columns name in your csv not same with core name: <i style="color:red">' . implode(", ", $check_header) . '</i><br/>Please check carefull to sure them exist in list core name bellow. if don not exist in list core name bellow please delete them in your csv and import again.<br/><i style="color:blue">' . implode(", ", $columns_names) . '</i>');
                        return;
                    }

                    if ($user != null) {
                        if (!empty($user['Billing country'])) {
                            $billing_state = WC()->countries->get_states($user['Billing country']);
                            if (!empty($user['Billing state'])) {
                                $user['Billing state'] = array_search(trim($user['Billing state']), $billing_state);
                            }
                        }
                        if (!empty($user['Shipping country'])) {
                            $shipping_state = WC()->countries->get_states($user['Shipping country']);
                            if (!empty($user['Shipping state'])) {
                                $user['Shipping state'] = array_search(trim($user['Shipping state']), $shipping_state);
                            }
                        }
                        $user['Email'] = isset($user['Email']) && $user['Email'] != '' ? $user['Email'] : '';
                        $mail_exists_id = email_exists($user['Email']);

                        if (!isset($user['Login']) || $user['Login'] == 'N/A' || $user['Login'] == '') {
                            if (isset($user['Email']) && !empty($user['Email'])) {
                                $user['Login'] = trim($user['Email']);
                            } else {
                                $user['Login'] = rand(10000000, 28000000);
                            }
                        }
                        if ((!isset($user['Password']) || $user['Password'] == '' || $user['Password'] == 'N/A') && (!isset($user['Password hash']) || $user['Password hash'] == '' || $user['Password hash'] == 'N/A' ))
                            $user['Password'] = rand(10000000, 28000000);

                        $user_id = username_exists($user['Login']);
                        $is_valid_id = isset($user['ID']) && $user['ID'] != '' && $user['ID'] != 'N/A' ? is_numeric($user['ID']) : true;


                        if ($is_valid_id > 0) {
                            $wp_user = get_userdata($user['ID']);
                            $user_id = ( $wp_user === false ) ? false : $user['ID'];
                        }

                        //check user by mobile phone
                        $phone_exist = false;
                        if (!empty($user['phone'])) {
                            $phone = trim($user['phone']);
                            $phone = str_replace("+81", "", $phone);
                            $firstCharacter = substr($phone, 0, 1);
                            if ($firstCharacter == '0') {
                                $phone = substr($phone, 1);
                            }
                            $b = "digits_phone_no";
                            $usermerow = $wpdb->get_row(
                                    $wpdb->prepare(
                                            'SELECT * FROM ' . $wpdb->usermeta . '
        WHERE meta_value = %s AND meta_key= %s ORDER BY umeta_id DESC LIMIT 1', $phone, $b
                                    )
                            );
                            if ($usermerow) {
                                $res_check_u_mb = get_user_by('id', $usermerow->user_id);
                                if ($res_check_u_mb) {
                                    $user_id = $res_check_u_mb->ID;
                                    $phone_exist = true;
                                }
                            }
                        }
                        //
                        //update if exists
                        if (($mail_exists_id||$phone_exist) && $import_type == 'update') {
                            //if($mail_exists_id == false || ($mail_exists_id == $user_id) || (isset($user[ 'ID' ]) && $mail_exists_id == $user[ 'ID' ]))
                            //{
                            //Update
                            if($mail_exists_id>0){
                                $user_id = $mail_exists_id;
                            }
                            $user_id = wp_update_user(array('ID' => $user_id, 'user_email' => $user['Email'], 'user_login' => $user['Login']));
                            /* if(is_wp_error( $user_id ))
                              array_push( $this->error, $result->get_error_message());
                              else */
                            $wccm_customer_model->update_user_metas($user_id, $user);

                            if (isset($user['Password']))
                                wp_set_password($user['Password'], $user_id);

                            //Role
                            $roles = isset($user['Role']) && $user['Role'] != '' && $user['Role'] != 'N/A' ? $user['Role'] : 'customer';
                            //$roles_temp = array();
                            //$roles_temp[$role] = true;
                            $wccm_customer_model->update_user_roles($user_id, $roles);

                            //wpuef
                            $wccm_customer_model->bulk_update_wpuef_fields($user_id, $wpuef_extra_fields);
                            $customerUpdated++;
                            //}
                            /* else
                              array_push($this->errors, new WP_Error('empty', __('Mail already taken for user: $user_id', 'woocommerce-customers-manager'))); */
                        }
                        //else {

                        elseif ($is_valid_id && !$user_id and $mail_exists_id == false AND ( (isset($user['Password']) && $user['Password'] != '' && $user['Password'] != 'N/A') || (isset($user['Password hash']) && $user['Password hash'] != '' && $user['Password hash'] != 'N/A')) && $phone_exist == false) {

                            $userdata = array(
                                'user_login' => $user['Login'],
                                'user_pass' => isset($user['Password hash']) && $user['Password hash'] != '' && $user['Password hash'] != 'N/A' ? $user['Password hash'] : "none",
                                'user_email' => $user['Email'],
                                'first_name' => isset($user['Name']) ? $user['Name'] : "",
                                'last_name' => isset($user['Surname']) ? $user['Surname'] : ""/* ,
                                  'role' => $role */
                            );
                            /* if(isset($user[ 'ID' ]))
                              $userdata['ID'] = $user[ 'ID' ]; */
                            $user_id = $wccm_customer_model->wccm_custom_insert_user($userdata);

                            if (isset($user['Password']))
                                wp_set_password($user['Password'], $user_id);

                            //Role
                            $roles = isset($user['Role']) && $user['Role'] != '' && $user['Role'] != 'N/A' ? $user['Role'] : 'customer';
                            /* $roles_temp = array();
                              $roles_temp[$role] = true; */
                            $wccm_customer_model->update_user_roles($user_id, $roles);

                            if (is_wp_error($user_id)) {
                                $this->error = array_push($this->errors, $user_id);
                            } else {
                                $customerAdded++;

                                //metadata
                                $wccm_customer_model->update_user_metas($user_id, $user);
                            }

                            //wpuef import 
                            $wccm_customer_model->bulk_update_wpuef_fields($user_id, $wpuef_extra_fields);


                            //Notification email
                            if (isset($_POST['send-notification-email']) && $_POST['send-notification-email'] == 'yes') {
                                $mail = new WCCM_Email();
                                $mail->send_new_user_notification_email($user['Email'], $user['Login'], $user['Password'], $user_id);
                                /* $mail = new WCCM_Email();
                                  $subject = __('New account', 'woocommerce-customers-manager');
                                  $text = sprintf (__('New account has been created. Login using the following credentials.<br/>User: %s<br/>Password: %s<br/>', 'woocommerce-customers-manager'), $user['Login'],$user['Password']);
                                  $mail->trigger($user[ 'Email' ], $subject,$text); */
                            }
                        } else {
                            if ($mail_exists_id != false)
                                array_push($this->errors, sprintf(__("Email address %s for user %s already taken.", 'woocommerce-customers-manager'), $user['Email'], $user['Login']));
                            else if ($phone_exist != false)
                                array_push($this->errors, sprintf(__("Mobile number %s for user %s already taken.", 'woocommerce-customers-manager'), $user['phone'], $user_id));
                            else //if(!isset($user['Password']) || $user['Password'] == '')
                                array_push($this->errors, sprintf(__("User %s already present.", 'woocommerce-customers-manager'), $user_id));
                        }
                        //}
                        //update extra meta data
                        if ($user_id) {
                            update_user_meta($user_id, 'billing_last_name_kana', $user['billing_last_name_kana']);
                            update_user_meta($user_id, 'billing_first_name_kana', $user['billing_first_name_kana']);
                            update_user_meta($user_id, 'shipping_last_name_kana', $user['shipping_last_name_kana']);
                            update_user_meta($user_id, 'shipping_first_name_kana', $user['shipping_first_name_kana']);
                            if (!empty($user['birthday'])) {
                                $arr_brth = explode("-", $user['birthday']);
                                $bod = array('year' => $arr_brth[0], 'month' => $arr_brth[1] . "月", 'day' => $arr_brth[2]);
                                update_user_meta($user_id, 'account_birth', $bod);
                            }
                            if (!empty($user['phone'])) {
                                $coutrnycode = '+81';
                                $firstCharacter = substr($user['phone'], 0, 1);
                                if ($firstCharacter == '0') {
                                    $user['phone'] = substr($user['phone'], 1);
                                }
                                $digits_phone_no = trim($user['phone']);
                                $digits_phone = $coutrnycode . $user['phone'];
                            } else {
                                $coutrnycode = '';
                                $digits_phone_no = '';
                                $digits_phone = '';
                            }
                            update_user_meta($user_id, 'digt_countrycode', $coutrnycode);
                            update_user_meta($user_id, 'digits_phone_no', $digits_phone_no);
                            update_user_meta($user_id, 'digits_phone', $digits_phone);
                            update_user_meta($user_id, 'is_user_imported', 'yes');
                        }
                        //end
                    }
                    $row++;
                }
                if ($customerAdded > 0)
                    array_push($this->messages, sprintf(__('Added %d customers!', 'woocommerce-customers-manager'), $customerAdded));
                if ($customerUpdated > 0)
                    array_push($this->messages, sprintf(__('Updated %d customers!', 'woocommerce-customers-manager'), $customerUpdated));
                //fclose($handle);
            }
        }
    }

    public function render_page() {
        /* if(isset($_POST["submit"])) 
          $this->process_uploaded_file(); */

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-progressbar');
        //wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('jquery-style', WCCM_PLUGIN_PATH . '/css/jquery-ui.css');
        wp_enqueue_style('wccm-common', WCCM_PLUGIN_PATH . '/css/common.css');
        wp_enqueue_style('customer-import-css', WCCM_PLUGIN_PATH . '/css/customers-import.css');
        //wp_enqueue_script('csv-jquery-lib',  WCCM_PLUGIN_PATH.'/js/jquery.csv-0.71.min.js'); 
        wp_enqueue_script('csv-jquery-lib', WCCM_PLUGIN_PATH . '/js/jquery.csv-0.81.js');
        wp_enqueue_script('ajax-csv-importer', WCCM_PLUGIN_PATH . '/js/admin-import-ajax.js?v=' . microtime());
        ?>
        <script>
            var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
        </script>
        <div id="wpbody">


            <h2 id="add-new-user"> 
                <?php _e('Import customers', 'woocommerce-customers-manager'); ?> 
            </h2>

            <?php if (isset($this->errors) && !empty($this->errors)): ?>
                <div class="error">
                    <ul>
                        <?php
                        foreach ($this->errors as $error)
                            echo "<li>" . $error . "</li>\n";
                        ?>
                    </ul>
                </div>
                <?php
            endif;

            if (!empty($this->messages)) {
                foreach ($this->messages as $msg)
                    echo '<div id="message chien2" class="updated"><p>' . $msg . '</p></div>';
            }
            ?>

            <?php if (isset($add_user_errors) && is_wp_error($add_user_errors)) : ?>
                <div class="error">
                    <?php
                    foreach ($add_user_errors->get_error_messages() as $message)
                        echo "<p>$message</p>";
                    ?>
                </div>
            <?php endif; ?>


            <div tabindex="0" aria-label="Main content" id="import-box-content">	
                <div id="upload-istruction-box">
                    <h3 class="wccm_section_title wccm_no_margin_top"><?php _e('Instruction', 'woocommerce-customers-manager'); ?></h3>
                    <p class="wccm_description">
                        <?php _e('The .csv file must use "," as field separator. To <strong>update</strong> an user, use the special <strong>ID</strong> column. Data will be imported if any of the following column titles are present in your csv:', 'woocommerce-customers-manager'); ?>
                    </p>
                    <ul>
        <!--                        <li>ID <span class="normal"><?php //_e('(If present, the existing user will be <strong>updated</strong> with the new data. Login and Email fields cannot be updated with new ones)', 'woocommerce-customers-manager');    ?></span></li>-->
                        <li>Name</li>
                        <li>Surname </li>
                        <li>Login <span class="normal"><?php _e('(if not specified the plugin will generate an automatic username. If email colum has value will make username as same as email)', 'woocommerce-customers-manager'); ?></span></li> 
                        <li>Role <span class="normal"><?php _e('(Example: "shop_manager","customer","subscriber",etc. If not specified the default value will be "customer". In case of multiple roles specify them separating by single space. Ex: "role_1 role_2 role_3")', 'woocommerce-customers-manager'); ?></span></li>
                        <li>Password <span class="normal"><?php _e('(If not present the "Password hash" column will be used instead. If both columns are empty a random password will be generated)', 'woocommerce-customers-manager'); ?></span></li>
                        <li>Password hash <span class="normal"><?php _e('(Used for importing already encrypted passwords. Usefull if you have a .csv export file generated with this plugin)', 'woocommerce-customers-manager'); ?></span></li>
                        <li>Email <span class="normal"><?php _e('(If not present, a random one will be generated)', 'woocommerce-customers-manager'); ?></span></li>
                        <li>Notes</li>
                        <li>Billing name</li>
                        <li>Billing surname</li>
                        <li>Billing email</li>
                        <li>Billing phone</li>
                        <li>Billing company</li>
                        <li>Billing address</li>
                        <li>Billing address 2</li>
                        <li>Billing postcode</li>
                        <li>Billing city</li>
                        <li>Billing country</li>
                        <li>Billing state</li>
                        <li>Shipping name</li>
                        <li>Shipping surname</li>
                        <li>Shipping phone</li>
                        <li>Shipping company</li>
                        <li>Shipping address</li>
                        <li>Shipping address 2</li>
                        <li>Shipping postcode</li>
                        <li>Shipping city</li>
                        <li>Shipping state</li>
                        <li>Shipping country</li>
                        <li>billing_last_name_kana</li>
                        <li>billing_first_name_kana</li>
                        <li>shipping_last_name_kana</li>
                        <li>shipping_first_name_kana</li>
                        <li>birthday <span class="normal">(with format: year-month-day)</span></li>
                        <li>phone <span class="normal">(use this field if don't have email and id when import. This will use in Digits plugin to login without email.)</span></li>
                        <?php if (wccm_wpuef_plugin_installed()): ?>
                            <li>wpuef_{id} <span class="normal"><?php _e('(To import wpuef user extra field the columns have to have the following format: wpuef_c13, where c13 is the field id. If you are importing a <i>Country & State</i> its content has to be in the format: "country,state" (with double quotes). For example: "United states,New York", or "Italy,Rome")', 'woocommerce-customers-manager'); ?></span></li>
                        <?php endif; ?>
                        <ul>
                            <div style="display:block; height:25px; width:400px; "></div>
                            <form class="import-form" method="post" enctype="multipart/form-data">
                                <h4><?php _e('Send an email to customer with login info? (This feature is not available if you import password using "Password Hash" column or if updating users data)', 'woocommerce-customers-manager'); ?></h4>
                                <p>
                                    <select id="wccm-send-notification-email">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </p>
                                <h4><?php _e('Import type', 'woocommerce-customers-manager'); ?></h4>
                                <p>
                                    <select name="import_type" id="wccm_import_type">
                                        <option value="add_new">add new</option>
                                        <option value="update">allow update</option>
                                    </select>
                                </p>
                                <p>
                                    <strong><?php _e('Select .csv file to import', 'woocommerce-customers-manager'); ?> </strong>
                                    <input type="file" name="fileToUpload" id="fileToUpload"></input>
                                </p>
                                <p>
                                    <strong><?php _e('NOTE', 'woocommerce-customers-manager'); ?>:</strong> <?php _e('You can use the follow .csv example as template to import data:', 'woocommerce-customers-manager'); ?> <a href="http://www.codecanyon.eu/images/WCCM/WCCM-template.csv"><?php _e('Example', 'woocommerce-customers-manager'); ?></a>
                                </p>
                                <input type="submit" class="button-primary" id="impor-submit-button" value="<?php _e('Upload', 'woocommerce-customers-manager'); ?> " name="submit" accept=".csv"></input>
                            </form>
                            </div>
                            <h3 id="ajax-progress-title"><?php _e('Importing Progress', 'woocommerce-customers-manager'); ?></h3>
                            <div id="ajax-progress"></div>
                            <div id="progressbar"></div>
                            <h3 id="ajax-response-title"><?php _e('Importing Result', 'woocommerce-customers-manager'); ?></h3>				
                            <div id="ajax-response"></div>

                            <div class="clear"></div>
                            </div><!-- wpbody-content -->
                            <div class="clear"></div>
                            <?php
                        }

                    }
                    ?>