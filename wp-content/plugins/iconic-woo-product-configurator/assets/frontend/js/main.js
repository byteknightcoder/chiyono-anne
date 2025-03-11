window.addEventListener('load', function () {
  'use strict';

  window.iconic_wpc_layer_updated_timeout = 0;
  var $ = jQuery,
    iconic_wpc = {
      $image_wrap: null,
      has_swatches: $('[data-attribute-name]').length > 0,
      has_configurator: $('.iconic-pc-images').length > 0,
      has_woothumbs: $('.iconic-woothumbs-images').length > 0,
      $variations_form: $('.product .variations_form'),
      variation_selects: '.variations select',
      default_images: {},
      loaded_images: {},
      load_ids: {
        global: 0
      },
      /**
       * On page load.
       */
      on_load() {
        iconic_wpc.set_elements();
        iconic_wpc.setup_default_images();
        iconic_wpc.setup_image_switcher();
        iconic_wpc.setup_swatches();
        iconic_wpc.setup_inventory();
        iconic_wpc.setup_woothumbs();
        iconic_wpc.setup_variation_swatches_photos();
        iconic_wpc.setup_variation_swatches_for_woocommerce();
        if (iconic_wpc.has_swatches) {
          iconic_wpc.reset_layers();
        }
        iconic_wpc.$variations_form.find('select').trigger('change');
      },
      /**
       * Setup default images.
       */
      setup_default_images() {
        iconic_wpc.$image_wrap.find('div').each(function (index, div) {
          const $div = $(div),
            id = $div.attr('class').match(/iconic-pc-image--([^\s]+)/);
          if (!id) {
            return;
          }
          const id_full_match = id[0];
          iconic_wpc.default_images['.' + id_full_match] = $div.html();
        });
      },
      /**
       * Sanitise string. Similar to generating a slug.
       *
       * @param  string str
       * @param  bool   prefix
       * @param  str
       * @param  prefix
       * @return {*}
       */
      sanitise_str(str, prefix) {
        prefix = typeof prefix !== 'undefined' ? prefix : true;
        if (str && str !== '') {
          if (prefix) {
            str = 'jckpc-' + str;
          }
          str = str.replace('/', '').replace(/ +/g, '-').replace(/[^a-zA-Z0-9-_]/g, '').replace(/(-)\1+/g, '-').replace(/(_)\1+/g, '_');
          return str.toLowerCase();
        }
        return '';
      },
      /**
       * Get layer ID.
       *
       * @param  selectedAttName
       * @return {string}
       */
      get_layer_id(selectedAttName) {
        const prefix = '.iconic-pc-image--';
        if (0 === selectedAttName.indexOf(prefix)) {
          return selectedAttName;
        }
        selectedAttName = selectedAttName.replace('attribute_', '');
        return prefix + selectedAttName;
      },
      /**
       * Get default image.
       *
       * @param layer_id
       */
      get_default_image(layer_id) {
        return iconic_wpc.default_images[layer_id];
      },
      /**
       * Clear layer.
       *
       * @param layer_id
       */
      clear_layer(layer_id) {
        layer_id = layer_id.indexOf('.') === 0 ? layer_id : iconic_wpc.get_layer_id(layer_id);
        const select_id = layer_id.replace('.iconic-pc-image--jckpc-', ''),
          $select = $('[data-iconic_pc_layer_id="' + select_id + '"]');
        if (typeof $select === 'undefined') {
          return;
        }
        const select_value = $select.val(),
          $img = '' === select_value ? iconic_wpc.get_default_image(layer_id) : '';
        $(layer_id).html($img);
      },
      /**
       * Load image layer.
       *
       * @param selectedVal
       * @param selectedAtt
       */
      load_image_layer(selectedVal, selectedAtt) {
        const product_id = iconic_wpc.$image_wrap.data('iconic_pc_product_id');
        selectedAtt = selectedAtt.replace('attribute_', '');
        const layer_id = iconic_wpc.get_layer_id(selectedAtt),
          load_id = iconic_wpc.get_load_id(layer_id);
        if (typeof iconic_wpc.loaded_images[layer_id] === 'undefined') {
          iconic_wpc.loaded_images[layer_id] = {};
        }
        const sanitizedSelectedVal = selectedVal.replace(/%/g, '');
        if (typeof iconic_wpc.loaded_images[layer_id][sanitizedSelectedVal] !== 'undefined') {
          iconic_wpc.update_image_layer(layer_id, iconic_wpc.loaded_images[layer_id][sanitizedSelectedVal], load_id);
          return;
        }
        iconic_wpc.set_loading(true);
        iconic_wpc.load_json_images();
      },
      load_json_images() {
        const product_id = iconic_wpc.$image_wrap.data('iconic_pc_product_id'),
          selected_attributes = [],
          form_attributes = iconic_wpc.get_selected_attributes();
        for (const attribute_name in form_attributes) {
          selected_attributes[attribute_name.replace('attribute_', 'jckpc-')] = form_attributes[attribute_name];
        }
        const conditional_images = iconic_wpc.get_conditional_images(selected_attributes);
        const json_layers = JSON.parse($('.iconic-pc-layers-' + product_id).html());
        if (!json_layers) {
          iconic_wpc.set_loading(false);
          return;
        }
        for (const attribute in selected_attributes) {
          let layer_id = iconic_wpc.get_layer_id(attribute),
            image_html = '',
            load_id = iconic_wpc.get_load_id(layer_id),
            selected_attr = selected_attributes[attribute].replace(/%/g, '');
          if (typeof conditional_images[attribute] !== 'undefined') {
            const conditional_image = conditional_images[attribute][selected_attr];
            if (typeof conditional_image !== 'undefined') {
              image_html = conditional_image.image_html;
            }
          }
          if ('' === image_html) {
            const default_layer = json_layers[attribute][selected_attr];
            image_html = typeof default_layer === 'undefined' ? '' : default_layer.image_html;
          }
          iconic_wpc.update_image_layer(layer_id, image_html, load_id);
        }
      },
      /**
       * Get conditional images
       *
       * @param selected_attributes
       */
      get_conditional_images(selected_attributes) {
        const selected_images = [],
          product_id = iconic_wpc.$image_wrap.data('iconic_pc_product_id'),
          $conditionals = $('.iconic-pc-layers-conditionals-' + product_id);
        if ($conditionals.length <= 0) {
          return selected_images;
        }
        const conditionals = JSON.parse($conditionals.html());
        for (const attribute in conditionals) {
          for (const rule_set in conditionals[attribute]) {
            let rules_met = 0;
            for (const current_rule in conditionals[attribute][rule_set].rules) {
              const rule = conditionals[attribute][rule_set].rules[current_rule],
                selected_attr = selected_attributes[rule.attribute];
              if (selected_attr) {
                if ('is_equal_to' === rule.condition) {
                  if (selected_attr === rule.value) {
                    rules_met++;
                  }
                } else if ('is_not_equal_to' === rule.condition) {
                  if (selected_attr !== rule.value) {
                    rules_met++;
                  }
                }
              }
            }
            if (rules_met === conditionals[attribute][rule_set].rules.length) {
              for (const current_value in conditionals[attribute][rule_set].value) {
                const term = conditionals[attribute][rule_set].value[current_value];
                if ('undefined' === typeof selected_images[attribute]) {
                  selected_images[attribute] = [];
                }
                selected_images[attribute][term.attribute.replace('jckpc-', '')] = term.value;
              }
            }
          }
        }
        return selected_images;
      },
      /**
       * Set loading status.
       *
       * @param loading
       */
      set_loading(loading) {
        if ('clear' === loading) {
          iconic_wpc.load_ids.global = 0;
          return;
        }
        if (loading) {
          iconic_wpc.load_ids.global++;
          return;
        }
        if (iconic_wpc.load_ids.global > 0) {
          iconic_wpc.load_ids.global--;
        }
      },
      /**
       * Update image layer.
       *
       * @param layer_id
       * @param image
       * @param request_id
       */
      update_image_layer(layer_id, image, request_id) {
        const current_load_id = iconic_wpc.get_load_id(layer_id);
        iconic_wpc.set_loading(false);
        if (request_id !== current_load_id) {
          return;
        }
        if (image) {
          if (jckpc.preload_layers === '1') {
            iconic_wpc.preload_img_and_modify_dom(layer_id, image);
          } else {
            $(layer_id).html(image);
          }
        } else {
          iconic_wpc.clear_layer(layer_id);
        }
        $(document.body).trigger('iconic_pc_image_layer_updated');
      },
      /**
       * Preload the image and modify the DOM.
       *
       * @param layer_id
       * @param image
       */
      preload_img_and_modify_dom(layer_id, image) {
        const parser = new DOMParser(),
          tempDoc = parser.parseFromString(image, 'text/html'),
          parsedImgEl = tempDoc.querySelector('img'),
          imageToLoad = document.createElement('img');
        imageToLoad.onload = function () {
          $(layer_id).html(image);

          // This should only fire for standard WC Galleries.
          if (!iconic_wpc.has_woothumbs) {
            const $zoom_target = $('.woocommerce-product-gallery__image--jckpc').first();
            if ($zoom_target.length) {
              $zoom_target.trigger('zoom.destroy');
              $zoom_target.css('pointer-events', 'none');
              iconic_wpc.update_large_image();
            }
          }
        };
        if (parsedImgEl.sizes) {
          imageToLoad.sizes = parsedImgEl.sizes;
        }
        if (parsedImgEl.srcset) {
          imageToLoad.srcset = parsedImgEl.srcset;
        }
        imageToLoad.src = parsedImgEl.src;
      },
      /**
       * Generate dynamic image url.
       *
       * @param  productId
       * @return {*|string|string|boolean}
       */
      generate_image_url(productId) {
        let url = jckpc.ajaxurl;
        url += url.indexOf('?') < 0 ? '?' : '&';
        url += 'action=iconic_pc_generate_image';
        url += '&prodid=' + productId;
        url += '&' + iconic_wpc.get_selected_attributes('string');
        return url;
        // http://iconic-plugins.local/wp-admin/admin-ajax.php?action=iconic_pc_generate_image&prodid=1177&attribute_strap=tan-leather&attribute_case=rose-gold&attribute_face=blue&attribute_pa_size=9&attribute_continents=anguilla
      },
      /**
       * Get selected attributes.
       *
       * @param  format
       * @return {*}
       */
      get_selected_attributes(format) {
        format = format || 'array';
        const selected = {},
          $selects = $('.variations').filter(':first,:not(.iconic-wlv-variations)').find("select[name^='attribute_']"),
          selects_processed = [];
        $selects.each(function (index, select) {
          const select_attribute = $(select).data('attribute_name');

          // Ensure that we only process <select> fields once for
          // each attribute. This avoids clashes with themes such
          // Goya that add the attribute selections in a drawer.
          if (-1 === selects_processed.indexOf(select_attribute)) {
            const $select = $(select),
              select_data = iconic_wpc.get_select_field_data($select);
            if ('' === select_data.value) {
              const layer_id = iconic_wpc.get_layer_id(select_data.attribute),
                $layer = $(layer_id);
              if (!$layer.length) {
                return;
              }
              const default_layer = $layer.data('iconic_pc_default_layer');
              select_data.value = default_layer || '';
            }
            selected[select_data.attribute_stripped] = select_data.value;
            selects_processed.push(select_attribute);
          }
        });
        if ('string' === format) {
          return $.param(selected, true);
        }
        return selected;
      },
      /**
       * Setup image switcher.
       */
      setup_image_switcher() {
        if (iconic_wpc.has_configurator) {
          $(document).on('change', iconic_wpc.variation_selects, function () {
            iconic_wpc.update_attribute_layers($(this));
          });

          // Throttle the update to avoid multiple image requests.
          let layer_updated_timeout;
          $(document.body).on('iconic_pc_image_layer_updated', function () {
            clearTimeout(layer_updated_timeout);
            layer_updated_timeout = setTimeout(function () {
              iconic_wpc.update_large_image();
            }, 100);
          });
        }
      },
      /**
       * Increment load ID.
       *
       * @param attribute
       */
      increment_load_id(attribute) {
        const $selects = $('.variations').filter(':first').find('select');
        $selects.each(function (index, select) {
          const $select = $(select),
            select_data = iconic_wpc.get_select_field_data($select);
          const layer_id = iconic_wpc.get_layer_id(select_data.attribute);
          if (!iconic_wpc.get_load_id(layer_id)) {
            iconic_wpc.load_ids[layer_id] = 0;
          }
          iconic_wpc.load_ids[layer_id]++;
        });
      },
      /**
       * Get current load ID.
       *
       * @param  attribute
       * @return {*}
       */
      get_load_id(attribute) {
        const layer_id = iconic_wpc.get_layer_id(attribute);
        if (typeof iconic_wpc.load_ids[layer_id] === 'undefined') {
          return false;
        }
        return iconic_wpc.load_ids[layer_id];
      },
      /**
       * Setup swatches.
       */
      setup_swatches() {
        if (iconic_wpc.has_swatches && iconic_wpc.has_configurator) {
          $('.swatch-anchor').on('click', function () {
            const $selectedSwatchAnchor = $(this),
              $variations_form = $selectedSwatchAnchor.closest('form'),
              variationsMap = JSON.parse($variations_form.attr('data-variations_map')),
              select = $selectedSwatchAnchor.closest('.select'),
              swatch = $selectedSwatchAnchor.closest('.select-option'),
              selectedAttName = select.attr('data-attribute-name'),
              selectedValHash = swatch.attr('data-value'),
              selectedAtt = iconic_wpc.sanitise_str(selectedAttName),
              selectedVal = variationsMap[selectedAttName][selectedValHash];
            if (!swatch.hasClass('selected')) {
              iconic_wpc.load_image_layer(selectedVal, selectedAtt);
            } else {
              iconic_wpc.clear_layer(selectedAtt);
            }
            $(document.body).trigger('iconic_pc_image_layer_updated');
          });
        }
      },
      /**
       * get swatch value.
       *
       * @param att_name
       * @param att_val_hash
       */
      get_swatch_value(att_name, att_val_hash) {
        const variationsMap = JSON.parse(iconic_wpc.$variations_form.attr('data-variations_map'));
        return variationsMap[att_name][att_val_hash];
      },
      /**
       * Reset layers.
       */
      reset_layers() {
        $('#variations_clear').on('click', function () {
          $.each(iconic_wpc.default_images, function (layer_id, image) {
            iconic_wpc.$image_wrap.find(layer_id).html(image);
          });
        });
      },
      /**
       * Setup inventory.
       */
      setup_inventory() {
        iconic_wpc.$variations_form.on('woocommerce_update_variation_values', function () {
          if (typeof jckpc_inventory !== 'undefined') {
            $.each(jckpc_inventory, function (product_id, product_data) {
              $.each(product_data, function (attribute_name, values) {
                const $select = $('.variations_form[data-product_id="' + product_id + '"] select[data-attribute_name="attribute_' + attribute_name + '"]');
                if ($select.length > 0) {
                  $.each(values, function (attribute_option, inventory) {
                    const option_value = attribute_option.replace('jckpc-', ''),
                      $option = $select.find('option[value|="' + option_value + '"]'),
                      $va_picker = $('.va-picker[data-attribute="' + attribute_name + '"][data-term="' + attribute_option + '"]');
                    if (inventory !== '' && parseInt(inventory) <= 0) {
                      $option.attr('disabled', 'disabled').removeClass('enabled');
                      if ($va_picker.length > 0) {
                        $va_picker.hide();
                      }
                    }
                  });
                }
              });
            });
          }
        });

        // inventory for swatches plugin
        if (iconic_wpc.has_swatches && iconic_wpc.has_configurator && typeof jckpc_inventory !== 'undefined' && typeof iconic_wpc.$variations_form.attr('data-variations_map') !== 'undefined') {
          const $attribute_fields = $('[data-attribute-name]');
          $attribute_fields.each(function () {
            let $element = $(this),
              attribute_name = $element.attr('data-attribute-name'),
              $options = null;
            if ($element.is('select')) {
              $options = $element.find('option');
              $options.each(function (index, option) {
                const attribute_hash = $(option).val();
                if (attribute_hash !== '') {
                  const attribute_value = iconic_wpc.get_swatch_value(attribute_name, attribute_hash),
                    attribute_name_formatted = attribute_name.replace('attribute_', ''),
                    attribute_value_formatted = attribute_value.replace('jckpc-', ''),
                    inventory = jckpc_inventory[attribute_name_formatted][attribute_value_formatted];
                  if (inventory !== '' && parseInt(inventory) <= 0) {
                    $(option).remove();
                  }
                }
              });
            } else {
              $options = $element.find('.select-option');
              $options.each(function (index, option) {
                const attribute_hash = $(option).attr('data-value');
                if (attribute_hash !== '') {
                  const attribute_value = iconic_wpc.get_swatch_value(attribute_name, attribute_hash),
                    attribute_name_formatted = attribute_name.replace('attribute_', ''),
                    attribute_value_formatted = attribute_value.replace('jckpc-', ''),
                    inventory = jckpc_inventory[attribute_name_formatted][attribute_value_formatted];
                  if (inventory !== '' && parseInt(inventory) <= 0) {
                    $(option).remove();
                  }
                }
              });
            }
          });
        }
      },
      /**
       * Set global elements.
       */
      set_elements() {
        iconic_wpc.$image_wrap = $('.iconic-pc-image-wrap');
      },
      /**
       * Setup WooThumbs.
       */
      setup_woothumbs() {
        if (!iconic_wpc.has_woothumbs) {
          return;
        }

        /**
         * Refresh WooThumbs after updated PC layers.
         */
        $(document.body).on('iconic_pc_image_layer_updated', function () {
          const $images = $('.iconic-woothumbs-images.slick-initialized');
          if ($images.length <= 0) {
            return;
          }
          const index = $images.find('.iconic-pc-images').closest('.slick-slide').data('slick-index') || 0;
          clearTimeout(window.iconic_wpc_layer_updated_timeout);
          window.iconic_wpc_layer_updated_timeout = setTimeout(function () {
            $images.slick('slickGoTo', index);
          }, 50);
        });

        /**
         * Trigger change after WooThumbs reloads.
         */
        $('.iconic-woothumbs-all-images-wrap').on('iconic_woothumbs_images_loaded', function (event, product_object) {
          const $first_attribute_select = product_object.variations_form.find('.variations').filter(':first').find('select').first();
          if ($first_attribute_select.length <= 0) {
            return;
          }
          iconic_wpc.update_attribute_layers($first_attribute_select);
        });
      },
      /**
       * Update layers relating to a specific attribute.
       *
       * @param $selectField
       * @return void
       */
      update_attribute_layers($selectField) {
        $(document.body).trigger('iconic_pc_image_before_layer_updated');
        const select_data = iconic_wpc.get_select_field_data($selectField);
        iconic_wpc.increment_load_id(select_data.attribute);
        if (select_data.value) {
          iconic_wpc.load_image_layer(select_data.value, select_data.attribute);
        } else {
          iconic_wpc.clear_layer(select_data.attribute);
        }

        // Make sure wp-post-image class is not added to PC images
        // else the images will be overriden by WooCommerce.
        jQuery('.iconic-pc-image img').removeClass('wp-post-image');
        setTimeout(function () {
          $(document.body).trigger('iconic_pc_image_layer_updated');
        }, 100);
      },
      /**
       * Update large image.
       */
      update_large_image() {
        const product_id = parseInt($('.iconic-pc-image-wrap').data('iconic_pc_product_id')),
          url = iconic_wpc.generate_image_url(product_id),
          $zoom_target = $('.woocommerce-product-gallery__image--jckpc'),
          $zoom_img = $('.woocommerce-product-gallery__image--jckpc .zoomImg');
        $('.iconic-pc-image--background img').attr('data-large_image', url).attr('data-src', url);

        // update default Woo zoom image.
        if (!iconic_wpc.has_woothumbs && jckpc.preload_layers === '1' && $zoom_target.length > 0) {
          $zoom_target.trigger('woocommerce_gallery_init_zoom');
          setTimeout(function () {
            $zoom_target.first().css('pointer-events', 'auto');
          }, 2500);
        } else if ($zoom_img.length > 0) {
          $zoom_img.attr('src', url);
        }
      },
      /**
       * Get select field data.
       *
       * @param  $select
       * @return {{attribute: null, value: null}}
       */
      get_select_field_data($select) {
        const data = {
          attribute_stripped: null,
          attribute: null,
          value: null
        };
        if (iconic_wpc.has_swatches && typeof iconic_wpc.$variations_form.attr('data-variations_map') !== 'undefined') {
          const variationsMap = JSON.parse(iconic_wpc.$variations_form.attr('data-variations_map')),
            selectedAttName = $select.attr('data-attribute-name'),
            selectedValHash = $select.val();
          data.attribute = iconic_wpc.sanitise_str(selectedAttName);
          data.attribute_stripped = data.attribute.replace('jckpc-', '');
          data.value = variationsMap[selectedAttName][selectedValHash];
          return data;
        }
        data.attribute = iconic_wpc.sanitise_str($select.attr('name'));
        data.attribute_stripped = data.attribute.replace('jckpc-', '');
        data.value = $select.val();
        return data;
      },
      /**
       * Compatibility with WooCommerce Variations Swatches and Photos Plugin.
       */
      setup_variation_swatches_photos() {
        if (jQuery('.variations_form.swatches').length) {
          const variationAttributesInputs = '.variations_form.swatches .variation_form_section input:hidden';
          iconic_wpc.variation_selects += ', ' + variationAttributesInputs;
          iconic_wpc.has_swatches = false;
          $(variationAttributesInputs).each(function () {
            const $input = $(this);
            $input.attr('data-attribute-name', $input.attr('name'));
            $input.parent().find('.select-option a').click(function () {
              setTimeout(function () {
                $input.trigger('change');
              }, 200);
            });
          });
        }
      },
      /**
       * Compatibility with Variation Swatches for WooCommerce.
       */
      setup_variation_swatches_for_woocommerce() {
        // Trigger change to ensure existing attribute selections trigger
        // the loading of layers.
        $(document).on('wvs-item-updated', 'li.variable-item', function () {
          // Only trigger the change on the initial load.
          if (!$(this).closest('.variations_form').hasClass('wvs-loaded')) {
            $(this).closest('.woo-variation-items-wrapper').find('select').trigger('change');
          }
        });
      }
    };

  // Compatibility with WooCommerce Product Bundles.
  const $bundled_products_bundle_form = $('.cart.bundle_form'),
    $bundled_products_bundle_data = $('.cart.bundle_data'),
    variations_form_selector = '.product .variations_form'; // WCPB adds .variations_form dynamically.

  if ($bundled_products_bundle_data.length > 0) {
    if (!$bundled_products_bundle_form.hasClass('initialized')) {
      // If WCPB is enabled, we don't want to fire our on_load event
      // until their JS has initialized.
      //
      // This doesn't always work as sometimes, especially in Chrome,
      // this event fires before our JS loads.
      $bundled_products_bundle_data.on('woocommerce-product-bundle-initialized', function (e, data) {
        iconic_wpc.$variations_form = $(variations_form_selector);
        iconic_wpc.on_load();
      });
    } else {
      // So the alternative is to manually trigger our JS if the plugin
      // has already initialized.
      iconic_wpc.$variations_form = $(variations_form_selector);
      iconic_wpc.on_load();
    }

    // Trigger the selects to be safe.
    iconic_wpc.$variations_form.find('select').trigger('change');
  } else {
    // Load as normal in all other contexts.
    iconic_wpc.on_load();
  }
});