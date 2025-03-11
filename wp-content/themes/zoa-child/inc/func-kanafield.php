<?php

add_filter('woocommerce_shipping_fields', 'custom_woocommerce_shipping_fields');
function custom_woocommerce_shipping_fields($fields) {
    $locale = function_exists('trp_get_current_language') ? trp_get_current_language() : get_locale();

    $fields['shipping_last_name_kana'] = array(
        'label' => __('Last Name Kana', 'zoa'),
        'required' => true,
        'class' => array('form-row-first')
    );
    $fields['shipping_first_name_kana'] = array(
        'label' => __('First Name Kana', 'zoa'),
        'required' => true,
        'class' => array('form-row-last'),
        'clear' => true
    );

    if ( 'ja' == $locale ) {
        $fields['shipping_phone'] = array(
            'label' => __('Phone', 'woocommerce'),
            'placeholder' => '',
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true
        );

        $fields['shipping_last_name']['class'] = array('form-row-first');
        $fields['shipping_first_name']['class'] = array('form-row-last');
        $fields['shipping_postcode']['class'] = array('form-row-last', 'address-field');
        $fields['shipping_postcode']['maxlength'] = 7;
        $fields['shipping_state']['class'] = array('form-row-wide', 'address-field');
        $fields['shipping_city']['class'] = array('form-row-first');
        $fields['shipping_address_1']['class'] = array('form-row-wide');
        $fields['shipping_address_2']['class'] = array('form-row-wide');
        $fields['shipping_country']['class'] = array('form-row-wide');

        // change order
        $order = array(
            'shipping_last_name',
            'shipping_first_name',
            'shipping_last_name_kana',
            'shipping_first_name_kana',
            'shipping_country',
            'shipping_postcode',
            'shipping_state',
            'shipping_city',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_phone',
        );
    } else {

        // Remove kana field if not japan language
        unset($fields['shipping_last_name_kana']);
        unset($fields['shipping_first_name_kana']);

        $fields['shipping_first_name']['class'] = array('form-row-first');
        $fields['shipping_last_name']['class'] = array('form-row-last');
        $fields['shipping_country']['class'] = array('form-row-wide');
        $fields['shipping_address_1']['class'] = array('form-row-first');
        $fields['shipping_address_2']['class'] = array('form-row-last');
        $fields['shipping_city']['class'] = array('form-row-first');
        $fields['shipping_state']['class'] = array('form-row-last', 'address-field');
        $fields['shipping_postcode']['class'] = array('form-row-first', 'address-field');
        $fields['shipping_phone'] = array(
            'label' => __('Phone', 'woocommerce'),
            'placeholder' => _x('', 'placeholder', 'woocommerce'),
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true
        );

        // change order
        $order = array(
            'shipping_last_name',
            'shipping_first_name',
            'shipping_country',
            'shipping_postcode',
            'shipping_state',
            'shipping_city',
            'shipping_address_1',
            'shipping_address_2',
            'shipping_phone',
        );
    }

    $ordered_fields = array();
    foreach ($order as $indexField => $field) {
        if (isset($fields[$field])) {
            $fields[$field]['priority'] = ($indexField + 1) * 10;
            $ordered_fields[$field] = $fields[$field];
        }
    }

    $fields = $ordered_fields;

    return $fields;
}

add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');
function custom_woocommerce_billing_fields($fields) {
    $locale = function_exists('trp_get_current_language') ? trp_get_current_language() : get_locale();

    $fields['billing_last_name_kana'] = array(
        'label' => __('Last Name Kana', 'zoa'),
        'required' => true,
        'class' => array('form-row-first')
    );
    $fields['billing_first_name_kana'] = array(
        'label' => __('First Name Kana', 'zoa'),
        'required' => true,
        'class' => array('form-row-last'),
        'clear' => true
    );


    $fields['billing_last_name']['class'] = array('form-row-first');
    $fields['billing_first_name']['class'] = array('form-row-last');

    $fields['billing_email']['class'] = array('form-row-wide');
    $fields['billing_phone']['class'] = array('form-row-wide');

    $fields['billing_postcode']['class'] = array('form-row-first');
    $fields['billing_postcode']['maxlength'] = 7;
    $fields['billing_state']['class'] = array('form-row-last');

    $fields['billing_city']['class'] = array('form-row-wide');
    $fields['billing_address_1']['class'] = array('form-row-wide');
    $fields['billing_address_2']['class'] = array('form-row-wide');


    //change order
    $order = array(
        'billing_last_name',
        'billing_first_name',
        'billing_last_name_kana',
        'billing_first_name_kana',
        'billing_email',
        'billing_country',
        'billing_postcode',
        'billing_state',
        'billing_city',
        'billing_address_1',
        'billing_address_2',
        'billing_phone',
    );

    if ( 'ja' != $locale ) {
        unset($fields['billing_first_name_kana']);
        unset($fields['billing_first_name_kana']);
    }

    $ordered_fields = array();
    foreach ($order as $indexField => $field) {
        if (isset($fields[$field])) {
            $fields[$field]['priority'] = ($indexField + 1) * 10;
            $ordered_fields[$field] = $fields[$field];
        }
    }

    $fields = $ordered_fields;
    return $fields;
}
add_filter('woocommerce_billing_fields', 'zoa_filter_address_by_locale', 100, 2);
add_filter('woocommerce_shipping_fields', 'zoa_filter_address_by_locale', 100, 2);
add_filter('woocommerce_address_to_edit', 'zoa_filter_address_by_locale', 100, 2);

function zoa_filter_address_by_locale($address, $load_address) {
    // Remove kana field if not japan language
    // $locale = get_locale();
    $locale = function_exists('trp_get_current_language') ? trp_get_current_language() : get_locale();
    if ( 'ja' != $locale ) {
        $clone_address = $address;
        $aFieldRemove = array('billing_last_name_kana', 'billing_first_name_kana', 'shipping_last_name_kana', 'shipping_first_name_kana');
        foreach ($aFieldRemove as $field_name) {
            if (isset($address[$field_name])) {
                unset($address[$field_name]);
            }
        }
    }
    return $address;
}

add_filter('default_checkout_billing_first_name_kana', 'default_value_kana_outside_japan', 100, 2);
add_filter('default_checkout_billing_last_name_kana', 'default_value_kana_outside_japan', 100, 2);
add_filter('default_checkout_shipping_first_name_kana', 'default_value_kana_outside_japan', 100, 2);
add_filter('default_checkout_shipping_last_name_kana', 'default_value_kana_outside_japan', 100, 2);
function default_value_kana_outside_japan($value, $input) {
    $locale = function_exists('trp_get_current_language') ? trp_get_current_language() : get_locale();
    $aFieldRemove = array('billing_last_name_kana', 'billing_first_name_kana', 'shipping_last_name_kana', 'shipping_first_name_kana');
    if ( 'ja' != $locale && in_array($input, $aFieldRemove) ) {
        $value = '...';
    }
    return $value;
}

add_filter('woocommerce_order_formatted_billing_address', 'look_woocommerce_order_formatted_billing_address', 10000, 2);
function look_woocommerce_order_formatted_billing_address($args, $order) {
    if ( 'ja' === get_locale() ) {
        $args['kananame'] = $order->billing_last_name_kana . $order->billing_first_name_kana;
    } else {
        $args['kananame'] = ''; // 日本語以外のロケールでは空文字列を設定
    }
    $args['country'] = $args['country'] ?: 'JP';
    return $args;
}

add_filter('woocommerce_localisation_address_formats', 'elsey_woocommerce_localisation_address_formats', 1000);
function elsey_woocommerce_localisation_address_formats($formats) {
    $format_string = "{last_name} {first_name}\n{kananame}\n{company}\n〒{postcode}\n{state}{city}{address_1}\n{address_2}";
    $formats['JP'] = $formats['default'] = $format_string;
    return $formats;
}

add_filter('woocommerce_formatted_address_replacements', 'look_woocommerce_formatted_address_replacements', 10000, 2);
function look_woocommerce_formatted_address_replacements($fields, $args) {
    if ( 'ja' === get_locale() ) {
        // Check if 'kananame' exists in $args before accessing it
        $fields['{kananame}'] = $args['kananame'] ?: '';
    } else {
        // Set an empty string for non-Japanese locales
        $fields['{kananame}'] = ''; 
    }
    return $fields;
}

add_filter('woocommerce_order_formatted_shipping_address', 'look_woocommerce_order_formatted_shipping_address', 10000, 2);
function look_woocommerce_order_formatted_shipping_address($args, $order) {
    if ( 'ja' === get_locale() ) {
        $args['kananame'] = $order->shipping_last_name_kana . $order->shipping_first_name_kana;
    } else {
        $args['kananame'] = ''; // 日本語以外のロケールでは空文字列を設定
    }
    $args['country'] = $args['country'] ?: 'JP';
    return $args;
}

add_filter('woocommerce_customer_meta_fields', 'zoa_woocommerce_customer_meta_fields', 10000, 1);
function zoa_woocommerce_customer_meta_fields($show_fields) {
    $order_fields = array(
        'last_name',
        'first_name',
        'last_name_kana',
        'first_name_kana',
        'email',
        'phone',
        'country',
        'postcode',
        'state',
        'city',
        'address_1',
        'address_2',
    );
    $show_fields['billing']['fields']['billing_last_name_kana'] = $show_fields['shipping']['fields']['shipping_last_name_kana'] = array(
        'label' => __('Last Name Kana', 'zoa'),
        'required' => true,
        'class' => array('form-row-first')
    );
    $show_fields['billing']['fields']['billing_first_name_kana'] = $show_fields['shipping']['fields']['shipping_first_name_kana'] = array(
        'label' => __('First Name Kana', 'zoa'),
    );
    $billing_fields = $show_fields['billing']['fields'];
    $shipping_fields = $show_fields['shipping']['fields'];
    $show_fields['billing']['fields'] = $show_fields['shipping']['fields'] = array();
    foreach ($order_fields as $order_field) {
        if (isset($billing_fields['billing_' . $order_field])) {
            $show_fields['billing']['fields']['billing_' . $order_field] = $billing_fields['billing_' . $order_field];
        }

        if (isset($shipping_fields['shipping_' . $order_field])) {
            $show_fields['shipping']['fields']['shipping_' . $order_field] = $shipping_fields['shipping_' . $order_field];
        }
    }

    return $show_fields;
}

// Begin - customize order detail ADMIN
add_filter('woocommerce_admin_billing_fields', 'look_woocommerce_admin_extra_fields', 10, 1);
add_filter('woocommerce_admin_shipping_fields', 'look_woocommerce_admin_extra_fields', 10, 1);
function look_woocommerce_admin_extra_fields($fields) {
    $fieldExtras = array();
    $fieldExtras['first_name_kana'] = array(
        'label' => __('名(ふりがな)', 'woocommerce'),
        'show' => false
    );

    $fieldExtras['last_name_kana'] = array(
        'label' => __('姓(ふりがな)', 'woocommerce'),
        'show' => false
    );


    $fields = insertAtSpecificIndex($fields, $fieldExtras, array_search('last_name', array_keys($fields)) + 1);

    return $fields;
}
