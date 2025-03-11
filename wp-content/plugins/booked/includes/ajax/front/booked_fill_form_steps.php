<?php

$date = isset($_POST['date']) ? $_POST['date'] : '';
$title = isset($_POST['title']) ? $_POST['title'] : '';
$timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : '';
$timeslot = isset($_POST['timeslot']) ? $_POST['timeslot'] : '';
$customer_type = isset($_POST['customer_type']) ? $_POST['customer_type'] : '';

$calendar_id = (isset($_POST['calendar_id']) ? $_POST['calendar_id'] : false);
$calendar_id_for_cf = $calendar_id;
if ($calendar_id):
    $calendar_id = array($calendar_id);
    $calendar_id = array_map('intval', $calendar_id);
    $calendar_id = array_unique($calendar_id);
endif;

$name_requirements = get_option('booked_registration_name_requirements', array('require_name'));
$name_requirements = ( isset($name_requirements[0]) ? $name_requirements[0] : false );
$is_new_registration = $customer_type == 'new' && !isset($_POST['date']) && !isset($_POST['timestamp']) && !isset($_POST['timeslot']);

$time_format = get_option('time_format');
$date_format = get_option('date_format');
$appointment_default_status = get_option('booked_new_appointment_default', 'draft');
$hide_end_times = get_option('booked_hide_end_times', false);

// Get custom field data (new in v1.2)
$custom_fields = array();

if ($calendar_id_for_cf) {
    if (is_array($calendar_id_for_cf))
    {
        $calendar_id_for_cf = $calendar_id_for_cf[0];
    }
    $custom_fields = json_decode(stripslashes(get_option('booked_custom_fields_' . $calendar_id_for_cf)), true);
}

if (!$custom_fields) {
    $custom_fields = json_decode(stripslashes(get_option('booked_custom_fields')), true);
}

$custom_field_data = array();
$cf_meta_value = '';

if (!empty($custom_fields)):

    $previous_field = false;

    foreach ($custom_fields as $key => $field):

        $field_name = $field['name'];
        $field_title = $field['value'];

        $field_title_parts = explode('---', $field_name);
        if ($field_title_parts[0] == 'radio-buttons-label' || $field_title_parts[0] == 'checkboxes-label'):
            $current_group_name = $field_title;
        elseif ($field_title_parts[0] == 'single-radio-button' || $field_title_parts[0] == 'single-checkbox'):
        // Don't change the group name yet
        else :
            $current_group_name = $field_title;
        endif;

        if ($field_name != $previous_field) {

            if (isset($_POST[$field_name]) && $_POST[$field_name]):

                $field_value = $_POST[$field_name];
                if (is_array($field_value)) {
                    $field_value = implode(', ', $field_value);
                }

                $custom_field_data[$key] = array(
                    'label' => $current_group_name,
                    'value' => $field_value
                );

            endif;

            $previous_field = $field_name;
        }

    endforeach;

    $custom_field_data = apply_filters('booked_custom_field_data', $custom_field_data);

    if (!empty($custom_field_data)):
        foreach ($custom_field_data as $key => $data):
            $cf_meta_value .= '<div class="form-row"><div class="cf-meta-value field-wrapper"><label class="form-row__label light-copy">' . $data['label'] . '</label><div class="text_output"><span confirm-text-value>' . $data['value'] . '</span></div></div></div>';//changed
        endforeach;
    endif;

endif;
echo $cf_meta_value;
exit();
