<div style="position: relative;" class="booked-settings-wrap wrap">
    <h3><?php esc_html_e('Appointments List', 'booked'); ?></h3>
    <link rel="stylesheet" href="<?php echo BOOKED_PLUGIN_URL; ?>/assets/css/bootstrap.min.css">
    <script src="<?php echo BOOKED_PLUGIN_URL; ?>/assets/js/bootstrap.min.js"></script>
    <link href="<?php echo BOOKED_PLUGIN_URL; ?>/assets/datatables/media/css/jquery.dataTables.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo BOOKED_PLUGIN_URL; ?>/assets/datatables/media/js/jquery.dataTables.js"></script>
    <style>
        table.dataTable{
            border-collapse: collapse;
        }
        .ch-filter2{
            position: absolute; left: 19%; top: 0.5%;z-index: 1
        }
        .ch_user_select2{
            width:300px;
        }
        .btn_filter{
            margin-top: 19px !important;
        }
        .loading-gif {
            position: fixed;
            top: 50%;
            left: 50%;
        }
    </style>
    <div class="ch-filter2">
        <select class="ch_user_select2" name="ch_user_select2">
            <?php
            $user_id = $_REQUEST['user_id'];
            if ($user_id > 0) {
                $user_info = get_userdata($user_id);
                $name = '';
                if ($user_info->user_email != '') {
                    $name = get_user_meta($user_id, 'last_name', true) . ' ' . get_user_meta($user_id, 'first_name', true) . ' (' . $user_info->user_email . ')';
                }
                ?>
                <option value="<?php echo $user_id; ?>"><?php echo $name; ?></option>
                <?php
            } else {
                ?>
                <option value="ALL"><?php esc_html_e('Filter by customer', 'booked'); ?></option>
            <?php } ?>
        </select>
        <input type="submit" name="filter_action" id="ch_filter_by_user" class="button btn_filter" value="Filter">
    </div>
    <table id="table_orders" class="table table-responsive table-condensed table-striped">
        <thead>
        <th><strong><?php esc_html_e('ID', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('Appointment Date/Time', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('User', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('Email', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('Phone', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('Appointment Information', 'booked'); ?></strong></th>
        <th><strong><?php esc_html_e('Created Date', 'booked'); ?></strong></th>
        </thead>
    </table>
    <input type="hidden" value="<?php echo $_REQUEST['user_id']; ?>" name="user_id" id="user_id"/>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            datatable = jQuery('#table_orders').DataTable({
                language: {
                    processing: "<img class='loading-gif' width='120px' src='<?php echo BOOKED_PLUGIN_URL; ?>/templates/images/tenor.gif'>",
                },
                stateSave: true,
                "aoColumns": [
                    {"bSortable": false},
                    {"bSortable": true},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false},
                    {"bSortable": false}
                ],
                "processing": true,
                "serverSide": true,
                "order": [[ 0, "desc" ]],
                "ajax": {
                    "url": ajaxurl + '?action=get_app_list_ajax',
                    "data": function (d) {
                        d.keyword = jQuery("#col6_filter").val();
                        d.user_id = jQuery(".ch_user_select2").val();
                    }, complete: function () {
                        jQuery("html, body").animate({scrollTop: jQuery('body').offset().top - 52}, 800);
                    }
                }
            });
            jQuery("#ch_filter_by_user").click(function () {
                datatable.ajax.reload();
            });
            jQuery('.ch_user_select2').select2({
                minimumInputLength: 1,
                allowClear: true,
                placeholder: {
                    id: "",
                    placeholder: "<?php esc_html_e('Leave blank', 'booked'); ?>"
                },
                ajax: {
                    url: ajaxurl + '?action=get_user_list_ajax',
                    type: 'POST',
                    dataType: 'json',
                    data: function (params) {
                        return {
                            keyWord: params.term
                        };
                    },
                    processResults: function (data, params) {
                        return {
                            results: jQuery.map(data.items, function (item) {
                                return {
                                    text: item.content,
                                    id: item.id,
                                    data: item
                                };
                            })
                        };
                    }
                }
            });
        });
    </script>
</div>
