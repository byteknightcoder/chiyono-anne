/**
 * Scripts for the attribute add/edit pages
 * @param $
 * @param document
 */

(function ($, document) {
  var iconic_pc_attribute = {
    cache() {
      iconic_pc_attribute.els = {};
      iconic_pc_attribute.vars = {};

      // common elements
      iconic_pc_attribute.els.upload = $('.jckpc-attribute-image__upload');
      iconic_pc_attribute.els.remove = $('.jckpc-attribute-image__remove');
    },
    on_ready() {
      // on ready stuff here
      iconic_pc_attribute.cache();
      iconic_pc_attribute.setup_image_fields();
    },
    /**
     * Setup image swatch fields
     */
    setup_image_fields() {
      // Uploading files
      let file_frame;
      iconic_pc_attribute.els.upload.on('click', function (event) {
        event.preventDefault();
        const $image_upload = $(this),
          $image_wrapper = $image_upload.closest('.jckpc-attribute-image'),
          $image_field = $image_wrapper.find('.jckpc-attribute-image__field'),
          $image_preview = $image_wrapper.find('.jckpc-attribute-image__preview'),
          $image_remove = $image_wrapper.find('.jckpc-attribute-image__remove');

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: $(this).data('title'),
          button: {
            text: $(this).data('button-text')
          },
          multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function () {
          // We set multiple to false so only get one image from the uploader
          attachment = file_frame.state().get('selection').first().toJSON();
          attachment_url = typeof attachment.sizes.thumbnail !== 'undefined' ? attachment.sizes.thumbnail.url : attachment.url;
          $image_field.val(attachment.id);
          $image_preview.html('<img src="' + attachment_url + '" class="attachment-thumbnail size-thumbnail">');
          $image_upload.addClass('jckpc-attribute-image__upload--edit');
          $image_remove.show();
          $(document.body).trigger('pc_image_selected');
        });

        // Finally, open the modal
        file_frame.open();
      });
      iconic_pc_attribute.els.remove.on('click', function (event) {
        event.preventDefault();
        const $image_wrapper = $(this).closest('.jckpc-attribute-image'),
          $image_field = $image_wrapper.find('.jckpc-attribute-image__field'),
          $image_preview = $image_wrapper.find('.jckpc-attribute-image__preview'),
          $image_upload = $image_wrapper.find('.jckpc-attribute-image__upload');
        $image_field.val('');
        $image_preview.html('');
        $image_upload.removeClass('jckpc-attribute-image__upload--edit');
        $(this).hide();
      });
    }
  };
  window.iconic_pc_attribute = iconic_pc_attribute;
  $(document).ready(iconic_pc_attribute.on_ready());
})(jQuery, document);
/**
 * Main.js introduced to manage enabled/disabled state.
 * @param $
 * @param document
 */

(function ($, document) {
  var iconic_pc_main = {
    /**
     * Setup main.js variables.
     */
    cache() {
      iconic_pc_main.vars = {};
      iconic_pc_main.vars.enabled = '#jckpc_enabled';
      iconic_pc_main.vars.wrapper = '#jckpc_options';
      iconic_pc_main.vars.toolbar = '.jckpc-meta-toolbar';
      iconic_pc_main.vars.static_layer = '#jckpc-add-static-layer';
    },
    on_ready() {
      iconic_pc_main.cache();
      iconic_pc_main.setup_blockUI();

      // create action that can be triggered elsewhere, eg toggle.js.
      $(document).on('jckpc_toggled', function () {
        iconic_pc_main.setup_blockUI();
      });
    },
    /**
     * Setup blockUI logic.
     */
    setup_blockUI() {
      // If PC toolbar is rendered then we are enabled and variations exist.
      if ($(iconic_pc_main.vars.toolbar).length <= 0) {
        return;
      }

      // If PC is not enabled, block the panel.
      if (!$(iconic_pc_main.vars.enabled).is(':checked')) {
        $(iconic_pc_main.vars.wrapper).block({
          message: null,
          overlayCSS: {
            background: '#fff',
            opacity: 0.6
          }
        });

        // disable static layer button.
        $(iconic_pc_main.vars.static_layer).attr('disabled', true);
      } else {
        // Otherwise, unblock the panel.
        $(iconic_pc_main.vars.wrapper).unblock();

        // enable static layer button.
        $(iconic_pc_main.vars.static_layer).attr('disabled', false);
      }
    }
  };
  window.iconic_pc_main = iconic_pc_main;
  $(document).ready(iconic_pc_main.on_ready());
})(jQuery, document);
/**
 * Scripts for the attribute add/edit pages
 * @param $
 * @param document
 */
(function ($, document) {
  var iconic_pc_product = {
    cache() {
      iconic_pc_product.els = {};
      iconic_pc_product.vars = {};
      iconic_pc_product.tmpl = {};
      iconic_pc_product.vars.file_frame = false;
      iconic_pc_product.vars.upload_class = '.jckpc-image-button--upload';
      iconic_pc_product.vars.remove_class = '.jckpc-image-button--remove';
      iconic_pc_product.vars.collapse_class = '.jckpc-layer-options__title--collapse';
      iconic_pc_product.vars.conditional_group_class = '.iconic-pc-conditional-group';
      iconic_pc_product.vars.conditional_group_class_attribute = '.iconic-pc-conditional-group__attribute';
      iconic_pc_product.vars.add_conditional_group_button_class = '.iconic-pc-add-conditional-group';
      iconic_pc_product.vars.add_conditional_rule_button_class = '.iconic-pc-conditional-group__add-rule';
      iconic_pc_product.vars.remove_conditional_group_class = '.iconic-pc-conditional-group__remove';
      iconic_pc_product.vars.remove_conditional_rule_class = '.iconic-pc-conditional-group__rule-remove';
      iconic_pc_product.vars.conditional_rule_class = '.iconic-pc-conditional-group__rule';
      iconic_pc_product.els.add_static_layer = $('#jckpc-add-static-layer');
      iconic_pc_product.els.sort_order_input = $('#jckpc_sort_order');
      iconic_pc_product.els.sortable_items = $('#jckpc_sortable');

      // templates
      iconic_pc_product.tmpl.static_layer = wp.template('jckpc-static-layer');
    },
    on_ready() {
      // on ready stuff here
      iconic_pc_product.cache();
      iconic_pc_product.setup_image_fields();
      iconic_pc_product.setup_sorting();
      iconic_pc_product.setup_collapse();
      iconic_pc_product.setup_static_layers();
      iconic_pc_product.setup_conditional_groups();
    },
    /**
     * Setup image swatch fields
     */
    setup_image_fields() {
      $(document).on('click', iconic_pc_product.vars.upload_class, function (event) {
        event.preventDefault();
        const $theBtn = $(this);

        // Create the media frame.
        iconic_pc_product.vars.file_frame = wp.media.frames.file_frame = wp.media({
          title: $theBtn.attr('data-uploader_title'),
          button: {
            text: $theBtn.attr('data-uploader_button_text')
          },
          multiple: false,
          library: {
            type: 'image/png'
          }
        });

        // When an image is selected, run a callback.
        iconic_pc_product.vars.file_frame.on('select', function () {
          // We set multiple to false so only get one image from the uploader
          const attachment = iconic_pc_product.vars.file_frame.state().get('selection').first().toJSON();
          if (attachment.mime !== 'image/png') {
            alert(jckpc_vars.i18n.png_only);
            return;
          }
          const $theFiled = $($theBtn.attr('data-uploader_field'));
          const $theThumbWrap = $($theBtn.attr('data-uploader_field') + '_thumbwrap');
          const image_src = typeof attachment.sizes.thumbnail !== 'undefined' ? attachment.sizes.thumbnail.url : attachment.url;
          $theFiled.val(attachment.id);
          $theThumbWrap.find('img').remove();
          $theThumbWrap.prepend('<img src="' + image_src + '" width="80" height="80" />');
          $(document.body).trigger('pc_image_selected');
        });

        // Finally, open the modal
        iconic_pc_product.vars.file_frame.open();
      });
      $(document).on('click', iconic_pc_product.vars.remove_class, function () {
        const $theBtn = $(this);
        const $imgField = $theBtn.attr('data-uploader_field');
        $($imgField).val('');
        $($imgField + '_thumbwrap img').remove();
        return false;
      });
    },
    /**
     * Setup sorting of layers
     */
    setup_sorting() {
      iconic_pc_product.els.sortable_items.sortable({
        revert: true,
        handle: '.jckpc-layer-options__handle',
        update(event, ui) {
          iconic_pc_product.update_sort_field();
        }
      });
    },
    /**
     * Update sort field
     */
    update_sort_field() {
      if (iconic_pc_product.els.sort_order_input.length <= 0) {
        return;
      }
      const $sortable_items = iconic_pc_product.els.sortable_items.find('.jckpc-layer-options'),
        the_order = [];
      $sortable_items.each(function () {
        const attr_slug = $(this).attr('data-layer-id');
        the_order.push(attr_slug);
      });
      iconic_pc_product.els.sort_order_input.val(the_order.join(','));
      $(document.body).trigger('pc_layer_order_changed');
    },
    /**
     * Setup collapsable layers
     */
    setup_collapse() {
      $(document).on('click', iconic_pc_product.vars.collapse_class, function () {
        const $title = $(this),
          $toggle = $title.find('.jckpc-layer-options__toggle'),
          $layer = $title.closest('.jckpc-layer-options'),
          $content = $layer.find('.jckpc-layer-options__content-wrapper');
        $content.toggle();
        $toggle.toggleClass('jckpc-layer-options__toggle--collapsed');
      });
    },
    /**
     * Setup static layers
     */
    setup_static_layers() {
      iconic_pc_product.els.add_static_layer.on('click', function () {
        $('#jckpc_sortable').prepend(iconic_pc_product.tmpl.static_layer({
          index: iconic_pc_product.get_highest_static_layer_index() + 1
        }));
        iconic_pc_product.update_sort_field();
        $('html,body').animate({
          scrollTop: $('#woocommerce-product-data').offset().top - 120
        }, 100);
      });
      $(document).on('click', '.jckpc-layer-options__remove', function () {
        $(this).closest('.jckpc-layer-options').remove();
        iconic_pc_product.update_sort_field();
      });
    },
    /**
     * Get highest static layer index
     */
    get_highest_static_layer_index() {
      const num = $('[data-static-layer-index]').map(function () {
        return $(this).data('static-layer-index');
      }).get();
      let highest_index = Math.max.apply(Math, num);
      highest_index = isFinite(highest_index) ? highest_index : -1;
      return highest_index;
    },
    /**
     * Setup conditional layers.
     */
    setup_conditional_groups() {
      /**
       * Add conditional group.
       */
      $(document.body).on('click', iconic_pc_product.vars.add_conditional_group_button_class, function (e) {
        e.preventDefault();
        const $button = $(this),
          original_data = $button.data('iconic-pc-add-conditional-group'),
          data = original_data;
        $button.attr('disabled', true);
        if (typeof data === 'undefined') {
          return;
        }
        data.action = 'iconic_pc_get_conditional_group';
        data.nonce = jckpc_vars.nonce;
        $.post(ajaxurl, data, function (response) {
          $button.attr('disabled', false);
          if (!response.success) {
            return;
          }
          original_data.condition_id = parseInt(original_data.condition_id) + 1;
          $button.before(response.data.html).data('iconic-pc-add-conditional-group', original_data);
        });
      });

      /**
       * Add conditional rule.
       */
      $(document.body).on('click', iconic_pc_product.vars.add_conditional_rule_button_class, function (e) {
        e.preventDefault();
        const $button = $(this),
          $rules = $button.closest('.iconic-pc-conditional-group__rules'),
          $rule = $rules.find('.iconic-pc-conditional-group__rule').first().clone(),
          $selected_attributes = $rules.find('.iconic-pc-conditional-group__rule select.iconic-pc-conditional-group__attribute'),
          $rule_selects = $rule.find('select'),
          rule_index = parseInt($button.data('iconic-pc-rule-id'));
        $rule_selects.val('');
        $rule_selects.each(function (index, rule_select) {
          const $rule_select = $(rule_select),
            name = $rule_select.attr('name'),
            new_name = name.replace('[rules][0]', '[rules][' + rule_index + ']');
          $rule_select.attr('name', new_name);
          if ($rule_select.hasClass('iconic-pc-conditional-group__attribute')) {
            // loop through $selected_attributes.
            $selected_attributes.each(function (index, select) {
              // check which attributes are already preselected.
              const $selected_attribute = $(select).find('option:selected').val();

              // hide and disable preselected options.
              $rule_select.find('option[value="' + $selected_attribute + '"]').hide();
              $rule_select.find('option[value="' + $selected_attribute + '"]').attr('disabled', true);
            });

            // if rule select only has one visible, non disabled option left, select it and change optgroups to match
            if (1 === $rule_select.find('option:not(:disabled)[value!=""]').length) {
              $rule_select.val($rule_select.find('option:not(:disabled)[value!=""]').val());
              const $value_select = $rule_select.closest('.iconic-pc-conditional-group__rule').find('.iconic-pc-conditional-group__value');
              $value_select.val($value_select.find('optgroup[data-slug="' + $rule_select.find('option:not(:disabled)[value!=""]').val() + '"]').find('option:first').val());
              $value_select.children().hide();
              $value_select.find('optgroup[data-slug="' + $rule_select.find('option:not(:disabled)[value!=""]').val() + '"]').show();
              $rules.find('.iconic-pc-conditional-group__add-rule').attr('disabled', true);
            }
          }
          if ('' !== $rule_select.find('option:first').val()) {
            $rule_select.val($rule_select.find('option:first').val());
          }
        });
        $rules.find('tbody').append($rule);
      });

      /**
       * Remove conditional group.
       */
      $(document.body).on('click', iconic_pc_product.vars.remove_conditional_group_class, function (e) {
        e.preventDefault();
        const $button = $(this),
          $conditional_group = $button.closest(iconic_pc_product.vars.conditional_group_class);
        $conditional_group.remove();
      });

      /**
       * Remove conditional rule.
       */
      $(document.body).on('click', iconic_pc_product.vars.remove_conditional_rule_class, function (e) {
        e.preventDefault();
        const $button = $(this),
          $rule = $button.closest(iconic_pc_product.vars.conditional_rule_class),
          $rules = $button.closest('.iconic-pc-conditional-group__rules');
        $rules.find('.iconic-pc-conditional-group__add-rule').attr('disabled', false);
        $rule.remove();
      });

      /**
       * On attribute select, pre fix value select.
       */
      $(document.body).on('change', iconic_pc_product.vars.conditional_group_class_attribute, function (e) {
        const $attribute_select = $(this);
        const $attribute_value_selected = $attribute_select.val();
        const $value_select = $(this).closest('.iconic-pc-conditional-group__rule').find('.iconic-pc-conditional-group__value');
        if ('' === $attribute_value_selected) {
          // if attr is empty, reset.
          $value_select.children().show();
          $value_select.val(null);
        } else {
          $value_select.val($value_select.find('optgroup[data-slug="' + $attribute_value_selected + '"]').find('option:first').val());
          $value_select.children().hide();
          $value_select.find('optgroup[data-slug="' + $attribute_value_selected + '"]').show();
        }
      });

      /**
       * On load, hide any non-relevant optgroups from conditional layer select boxes
       */
      $(document.body).ready(function () {
        $(iconic_pc_product.vars.conditional_group_class_attribute).each(function () {
          const $attribute_value_selected = $(this).val();
          const $value_select = $(this).closest('.iconic-pc-conditional-group__rule').find('.iconic-pc-conditional-group__value');
          $value_select.children().hide();
          $value_select.find('optgroup[data-slug="' + $attribute_value_selected + '"]').show();
        });
      });

      /**
       * On Woocommerce reload, show the reload notice
       */
      $(document.body).on('reload', function () {
        $('.jckpc-reload-notice').show();
      });
    }
  };
  window.iconic_pc_product = iconic_pc_product;
  $(document).ready(iconic_pc_product.on_ready());
})(jQuery, document);
/**
 * Scripts to toggle PC panel
 * @param $
 * @param document
 */

(function ($, document) {
  var iconic_pc_toggle = {
    /**
     * Cache.
     */
    cache() {
      iconic_pc_toggle.vars = {};
      iconic_pc_toggle.vars.toggles = '.jckpc_toggle';
      iconic_pc_toggle.vars.toggle_elements = '.jckpc-toggle-element';
    },
    /**
     * On ready.
     */
    on_ready() {
      iconic_pc_toggle.cache();
      iconic_pc_toggle.setup_toggles();
      iconic_pc_toggle.trigger_toggles();
    },
    /**
     * Setup toggle logic.
     */
    setup_toggles() {
      const $toggles = $(iconic_pc_toggle.vars.toggles);
      $toggles.each(function (index, toggle) {
        const $toggle_input = $(toggle).find('input');
        const $toggle_id = $toggle_input.attr('id');
        const $toggle_label = $(toggle).find('label');

        // create tooltip.
        const $tooltip = $('<span class="woocommerce-help-tip"></span>');
        $tooltip.attr('data-tip', $toggle_label.html());

        // create button element.
        const $toggle_element = $('<button type="button" class="jckpc-toggle-element"></button>');
        $toggle_element.attr('data-id', $toggle_id);

        // if matched checkbox is checked.
        if ($toggle_input.is(':checked')) {
          $toggle_element.addClass('checked');
        }

        // append our created "checkbox".
        $(toggle).append($toggle_element);
        $(toggle).append($tooltip);

        // hide "real" checkbox and label.
        $toggle_input.hide();
        $toggle_label.hide();
      });
    },
    /**
     * Setup actions to trigger on toggle.
     */
    trigger_toggles() {
      $(document).on('click', iconic_pc_toggle.vars.toggle_elements, function () {
        const $toggle_id = $(this).attr('data-id'),
          $matched_toggle = $('input#' + $toggle_id);
        if ($matched_toggle.is(':checked')) {
          $matched_toggle.prop('checked', false);
          $matched_toggle.trigger('change');
          $(this).removeClass('checked');
        } else {
          $matched_toggle.prop('checked', true);
          $matched_toggle.trigger('change');
          $(this).addClass('checked');
        }
        $(document).trigger('jckpc_toggled');
      });
    }
  };
  window.iconic_pc_toggle = iconic_pc_toggle;
  $(document).ready(iconic_pc_toggle.on_ready());
})(jQuery, document);