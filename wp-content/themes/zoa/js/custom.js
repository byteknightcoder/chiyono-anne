'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// @codingStandardsIgnoreStart
document.addEventListener('DOMContentLoaded', function () {
	var _body = document.body;

	/*! BEFORE LOAD
	------------------------------------------------->*/
	window.addEventListener('beforeunload', function () {
		if (document.getElementsByClassName('is-page-loading')[0]) {
			var themeContainer = document.getElementById('theme-container');

			if (themeContainer) {
				themeContainer.classList.add('is-loading');
				themeContainer.classList.remove('is-ready');
			}
		}
	});

	/*! PRELOADER `start()`
	------------------------------------------------->*/
	if (document.getElementsByClassName('is-page-loading')[0]) {
		NProgress.configure({
			template: '<div class="bar" role="bar"></div>',
			parent: '#page-loader',
			showSpinner: true,
			easing: 'ease',
			minimum: 0.3,
			speed: 500
		});
		NProgress.start();
	}

	/*! SIDEBAR MENU
	------------------------------------------------->*/
	var theme_menu = function () {
		/*TOGGLE SIDEBAR MENU*/
		var container = document.getElementById('theme-container'),
			btn = document.getElementById('menu-toggle-btn'),
			menuContent = document.getElementById('sidebar-menu-content'),
			menuOverlay = document.getElementById('menu-overlay');

		if (!btn) {
			return;
		}

		btn.addEventListener('click', function () {
			container.classList.add('menu-open');
		});

		menuOverlay.addEventListener('click', function () {
			container.classList.remove('menu-open');
		});

		_body.addEventListener('keyup', function (e) {
			if (27 === e.keyCode) {
				container.classList.remove('menu-open');
			}
		});

		/*MENU ACCORDION*/
		jQuery('.theme-sidebar-menu a').on('click', function (e) {
			e.preventDefault();

			var t = jQuery(this),
				s = t.siblings(),
				l = s.length;

			/*GO TO URL IF NOT SUB-MENU*/
			if (!l) {
				window.location.href = t.prop('href');
			}

			if (t.next().hasClass('show')) {
				t.next().removeClass('show');
				t.next().slideUp(200);
			} else {
				t.parent().parent().find('li .sub-menu').removeClass('show');
				t.parent().parent().find('li .sub-menu').slideUp(200);
				t.next().toggleClass('show');
				t.next().slideToggle(200);
			}
		});
	}();

	/*! SCROLL TO TOP
	------------------------------------------------->*/
	var $scrollTop = jQuery('.js-to-top');

	jQuery(window).on('scroll', function () {
		if (jQuery(this).scrollTop() > 200) {
			$scrollTop.addClass('is-visible');
		} else {
			$scrollTop.removeClass('is-visible');
		}
	});

	$scrollTop.on('click', function (e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: 0 }, 500);
	});

	/*! SIZE GUIDE
	  ------------------------------------------------->*/
	var sizeGuideWrapper = document.querySelector('.js-size-guide-wrapper');
	var openSizeGuide = function openSizeGuide() {
		document.body.classList.add('size-guide--is-visible');
		sizeGuideWrapper.classList.add('is-visible', 'fadeInUp', 'animated');
	};
	var closeSizeGuide = function closeSizeGuide() {
		document.body.classList.remove('size-guide--is-visible');
		sizeGuideWrapper.classList.remove('is-visible', 'fadeInUp', 'animated');
	};
	document.addEventListener('click', function (e) {
		if (e.target.classList.contains('js-open-size-guide')) {
			openSizeGuide();
		};

		if (e.target.closest('.js-close-size-guide')) {
			closeSizeGuide();
		}
	}, false);

	document.addEventListener('keyup', function (e) {
		if (e.keyCode === 27) {
			if (document.body.classList.contains('size-guide--is-visible')) {
				closeSizeGuide();
			}
		}
	});

	/*! FLEXIBLE SIDEBAR
	------------------------------------------------->*/
	var sidebarToggle = document.querySelector('.js-sidebar-toggle');
	var toggleIcon = document.querySelector('.toggle-icon');
	var shopSidebar = document.querySelector('.shop-sidebar');
	var sidebarOverlay = document.querySelector('.sidebar-overlay');
	if (sidebarToggle && sidebarOverlay && toggleIcon) {
		sidebarToggle.addEventListener('click', function (e) {
			e.preventDefault();
			shopSidebar.classList.toggle('is-visible');
			sidebarOverlay.classList.toggle('is-visible');
			document.body.classList.toggle('hide-scrollbar');
			this.classList.toggle('is-active');
			if (toggleIcon.classList.contains('ion-android-options')) {
				toggleIcon.classList.remove('ion-android-options');
				toggleIcon.classList.add('ion-android-close');
			} else {
				toggleIcon.classList.remove('ion-android-close');
				toggleIcon.classList.add('ion-android-options');
			}
		});

		sidebarOverlay.addEventListener('click', function () {
			shopSidebar.classList.remove('is-visible');
			this.classList.remove('is-visible');
			document.body.classList.remove('hide-scrollbar');
			sidebarToggle.classList.remove('is-active');
			toggleIcon.classList.remove('ion-android-close');
			toggleIcon.classList.add('ion-android-options');
		});
	}

	/*! STICKY HEADER
	------------------------------------------------->*/
	var $stickyHeader = jQuery('.js-sticky-header');
	var $headerDOM = jQuery('#theme-menu-layout');

	if ($stickyHeader.length && $headerDOM) {
		var $headerOffsetTop = $headerDOM.offset().top;
		var $headerHeight = $headerDOM.height();
		var $stickyHeaderOffset = $headerOffsetTop + $headerHeight;

		jQuery(window).on('scroll', function () {
			if (jQuery(this).scrollTop() > $stickyHeaderOffset) {
				$stickyHeader.addClass('is-visible');
			} else {
				$stickyHeader.removeClass('is-visible');
			}
		});
	}

	/*! MEGA MENU
	------------------------------------------------->*/
	var mega_menu = function () {
		if (window.width < 992) {
			return;
		}

		var menu_layouts = Array.from(document.querySelectorAll('.theme-primary-menu:not(.theme-sidebar-menu)'));
		if (!menu_layouts) {
			return;
		}

		menu_layouts.forEach(function (menu_layout) {
			var mega = menu_layout.getElementsByClassName('menu-item-has-mega-menu');
			if (!mega) {
				return;
			}

			for (var i = 0, mega_length = mega.length; i < mega_length; i++) {
				var mega_row = mega[i].getElementsByClassName('mega-menu-row')[0],
					mega_col = mega_row ? mega_row.getElementsByClassName('mega-menu-col') : false,
					mega_col_length = mega_col ? mega_col.length : false;

				if (!mega_row) {
					continue;
				}

				var _rect = mega_row.getBoundingClientRect(),
					_ww = _body.clientWidth,
					_right = _ww - _rect.right;

				mega_row.classList.add('mega-menu-col-' + mega_col_length);

				if (_right < 0) {
					mega_row.classList.add('mega-menu-in-right');
				} else {
					mega_row.classList.remove('mega-menu-in-right');
				}
			}
		});
	}();
	/*! PRODUCT SWATCH LIST
	------------------------------------------------->*/
	var swatchList = function () {

		jQuery(_body).on('click', '.p-attr-swatch', function () {
			var img_src = void 0,
				t = jQuery(this),
				src = t.data('src'),
				product = t.closest('.product'),
				img_wrap = product.find('.product-image-wrapper'),
				img = img_wrap.find('img'),
				origin_src = img.data('origin_src');

			img.prop('srcset', '');

			if (t.hasClass('active')) {
				img_src = origin_src;
				t.removeClass('active');
			} else {
				img_src = src;
				t.addClass('active').siblings().removeClass('active');
			}

			if (img.prop('src') == img_src) {
				return;
			}

			img_wrap.addClass('image-is-loading');

			img.prop('src', img_src).one('load', function () {
				return img_wrap.removeClass('image-is-loading');
			});
		});
	}();

	/*! PRODUCT ACTION
	------------------------------------------------->*/
	var product_action = function () {
		var wc = _body.classList.contains('woocommerce-js'),
			_overlay = document.getElementById('shop-overlay');
		if (!wc) return;

		/*OPEN CART SIDEBAR*/
		var open_cart_sidebar = function open_cart_sidebar() {
			_body.classList.add('cart-sidebar-open');
		};

		/*VAR*/
		var shopping_cart_btns = Array.from(document.querySelectorAll('.js-cart-button')),
			close_cart_sidebar_btn = document.getElementById('close-cart-sidebar');

		/* OPEN CART SIDEBAR BY HEADER BUTTON */
		shopping_cart_btns.forEach(function (shopping_cart_btn) {
			shopping_cart_btn.addEventListener('click', function (e) {
				e.preventDefault();

				if (_body.classList.contains('woocommerce-cart')) return;

				open_cart_sidebar();
				close_cart_sidebar();
			});
		});

		/*CLOSE SIDEBAR ACTION*/
		var close_cart_sidebar = function close_cart_sidebar() {

			/*USE `ESC` KEY*/
			_body.addEventListener('keyup', function (e) {
				if (27 === e.keyCode) {
					_body.classList.remove('cart-sidebar-open');
				}
			});

			/*USE CLOSE BUTTON*/
			close_cart_sidebar_btn.addEventListener('click', function () {
				return _body.classList.remove('cart-sidebar-open');
			});

			/*USE OVERLAY*/
			_overlay.addEventListener('click', function () {
				_body.classList.remove('cart-sidebar-open');
			});
		};

		/*RE-INIT EASY ZOOM*/
		var reinit_easy_zoom = function reinit_easy_zoom(selector) {

			if (!selector.length || window.matchMedia('( max-width: 991px )').matches || _body.classList.contains('quick-view-open')) {
				return;
			}

			var easyZoom = selector.easyZoom(),
				api = easyZoom.data('easyZoom');

			api.teardown();
			api._init();
		};

		/*MINUS && PLUS BUTTON FOR QUANTITY INPUT*/
		var quantity = function quantity() {
			var _selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'form.woocommerce-cart-form, form.cart';

			var j_quick_view = jQuery(_selector),
				_qty = j_quick_view.find('.quantity');

			if (!_qty.length || jQuery(_qty).hasClass('hidden')) return;

			_qty.prepend('<span class=\'modify-qty\' data-click=\'minus\'></span>').append('<span class=\'modify-qty\' data-click=\'plus\'></span>');

			var _qty_btn = j_quick_view.find('.modify-qty');

			jQuery(_qty_btn).on('click', function () {
				var t = jQuery(this),
					_input = t.parent().find('input'),
					currVal = parseInt(_input.val(), 10),
					max = parseInt(_input.prop('max'));

				if ('minus' === t.attr('data-click')) {
					if (currVal <= 0) {
						return;
					}

					if ('quantity' == _input.prop('name') && currVal <= 1) {
						return;
					}

					_input.val(currVal - 1).trigger('change');
				}

				if ('plus' === t.attr('data-click')) {
					if (currVal >= max) return;
					_input.val(currVal + 1).trigger('change');
				}

				jQuery('[name=\'update_cart\']').prop('disabled', false);
			});
		};
		quantity();

		/*UPDATE SWATCH IMAGE WHEN VARIATION CLICK*/
		var product_variation = function product_variation() {
			var popup = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

			var _gallery = jQuery('.single-product-gallery');

			if (true == popup) {
				_gallery = jQuery('#shop-quick-view #quick-view-gallery');
			}

			if (!_gallery.length) {
				return;
			}

			_gallery.each(function (i) {
				var galleryItem = jQuery(this);

				/*PRODUCT IMAGE*/
				var _image = galleryItem.find('.pro-img-item:eq(0)'),
					_image_src = _image.find('img').prop('src'),


					/*PRODUCT THEMBNAIL*/
					_thumb = galleryItem.find('.pro-thumb:eq(0)'),
					_thumb_src = _thumb.find('img').prop('src'),


					/*EASY ZOOM ATTR*/
					_zoom = _image.data('zoom');

				reinit_easy_zoom(_image);

				/*event when variation changed=========*/
				jQuery(_body).on('found_variation', 'form.variations_form:eq(' + i + ')', function (event, variation) {
					/*get image url form `variation`*/
					if (variation.image) {
						var img_url = variation.image.full_src,
							thumb_url = variation.image.thumb_src;

						/*change `src` image*/
						_image.find('img').prop('src', img_url);
						_thumb.find('img').prop('src', thumb_url);
						_image.attr('data-zoom', img_url);

						_image.addClass('image-is-loading');
						_image.find('img').prop('src', img_url).one('load', function () {
							return _image.removeClass('image-is-loading');
						});

						reinit_easy_zoom(_image);
					}
				});

				/*reset variation========*/
				jQuery('.reset_variations').on('click', function (e) {
					e.preventDefault();

					/*change `src` image*/
					_image.find('img').prop('src', _image_src);
					_thumb.find('img').prop('src', _thumb_src);
					_image.attr('data-zoom', _zoom);

					reinit_easy_zoom(_image);
				});
			});
		};
		product_variation();

		/*AJAX SINGLE ADD TO CART*/
		var single_add_to_cart = function single_add_to_cart() {
			if (jQuery('.config-cart-form-parent-wrap').length || jQuery('.product-type-bundle').length) {// this mean product has custom design of product-woo-configurator plugin OR it is product bundle type
				return;
			}
			var popup = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

			if (!_body.classList.contains('ajax-single-add-to-cart') || !_body.classList.contains('zoa-child-theme')) {
				return;
			}

			var _cart = document.querySelectorAll('form.cart');

			if (true == popup) {
				_cart = document.getElementById('shop-quick-view').getElementsByClassName('cart')[0];
			}

			if (!_cart) {
				return;
			}

			var _loop = function _loop(i, j) {

				var _input = _cart[i].getElementsByClassName('qty')[0],
					_max = _input ? parseInt(_input.getAttribute('max')) : 0,
					_btn = _cart[i].getElementsByClassName('single_add_to_cart_button')[0],
					_in_cart = _cart[i].getElementsByClassName('in-cart-qty')[0],
					_in_stock = _in_cart ? _in_cart.getAttribute('data-in_stock') : false,
					_out_of_stock = _in_cart ? _in_cart.getAttribute('data-out_of_stock') : false,
					_not_enough = _in_cart ? _in_cart.getAttribute('data-not_enough') : false,
					_valid_qty = _in_cart ? _in_cart.getAttribute('data-valid_qty') : false;

				if (!_btn || 'A' == _btn.tagName || _cart[i].classList.contains('grouped_form') || _cart[i].classList.contains('mnm_form') || !_input) {
					return {
						v: void 0
					};
				}

				_btn.addEventListener('click', function (e) {
					e.preventDefault();
					if (_cart[i].classList.contains('variations_form')) {
						var ch_product_id = _cart[i].querySelector('[name="product_id"]').value;
						var ch_variation_id = _cart[i].querySelector('[name="variation_id"]').value;
					} else {
						var ch_product_id = _cart[i].querySelector('[name="add-to-cart"]').value;
						var ch_variation_id = 0;
					}
					//for try fit your size function
					var bra_product_id = [];
					jQuery('.bra_product_id').each(function (i) {
						if (jQuery(this).is(':checked')) {
							if (jQuery(this).val() !== '' && jQuery(this).val() != null) {
								bra_product_id[i] = jQuery(this).val();
							}
						}
					});
					var bra_size = [];
					jQuery('.bra_size').each(function (i) {
						if (jQuery(this).is(':checked')) {
							if (jQuery(this).val() !== '' && jQuery(this).val() != null) {
								bra_size[i] = jQuery(this).val();
							}
						}
					});
					var bra_size_soft = [];
					jQuery('.bra_size_soft').each(function (i) {
						if (jQuery(this).is(':checked')) {
							if (jQuery(this).val() !== '' && jQuery(this).val() != null) {
								bra_size_soft[i] = jQuery(this).val();
							}
						}
					});
					var bra_size_full_cup = [];
					jQuery('.bra_size_full_cup').each(function (i) {
						if (jQuery(this).is(':checked')) {
							if (jQuery(this).val() !== '' && jQuery(this).val() != null) {
								bra_size_full_cup[i] = jQuery(this).val();
							}
						}
					});
					var try_fit_type = '';
					if (jQuery('#try_fit_type').length) {
						try_fit_type = jQuery('#try_fit_type').val();
					}
					var online_consultation = '';
					if (jQuery('.online_consultation').length) {
						if (jQuery('.online_consultation').is(':checked')) {
							online_consultation = jQuery('.online_consultation').val();
						}
					}
					//end
					//for event products
					var ch_type_orf = '';
					var ch_orf_sub_type_even = '';
					if (jQuery('#ch_type_orf').length) {
						ch_type_orf = jQuery('#ch_type_orf').val();
					}
					if (jQuery('#ch_orf_sub_type_even').length) {
						ch_orf_sub_type_even = jQuery('#ch_orf_sub_type_even').val();
					}
					//end
					//for gift options
					var gift_options = [];
					jQuery('.gp_option_item').each(function (i) {
						if (jQuery(this).find('input').is(':checked')) {
							if (jQuery(this).find('input').val() !== '' && jQuery(this).find('input').val() != null) {
								if (jQuery(this).find('.gp_atr').length) {
									gift_options[i] = jQuery(this).find('.gp_atr').val();
								} else {
									gift_options[i] = jQuery(this).find('input').val();
								}
							}
						}
					});
					//end
					jQuery.ajax({
						url: zoa_ajax.url,
						data: {
							'action': 'validate_cart_ajax_request',
							'product_id': ch_product_id,
							'variation_id': ch_variation_id,
							'bra_product_id': bra_product_id,
							'bra_size': bra_size,
							'bra_size_soft': bra_size_soft,
							'bra_size_full_cup': bra_size_full_cup,
							'try_fit_type': try_fit_type,
							'ch_type_orf': ch_type_orf,
							'gift_options': gift_options
						},

						success: function (data) {

							var regexToRemove = /<!-- function enqueue_language_popup_script\(\) {[\s\S]*?add_action\('wp_enqueue_scripts', 'enqueue_language_popup_script'\); -->/g;

							// Remove the specified text from the response data
							data = data.replace(regexToRemove, '');
							if (data.toLowerCase() === 'ok') {
								jQuery('div.woocommerce-notices-wrapper').remove();
								var _cart_sidebar = document.getElementsByClassName('cart-sidebar-content')[0],
									_item_count = document.getElementsByClassName('shop-cart-count'),
									_in_cart_qty = parseInt(_in_cart.value),
									single_atc_id = 0,
									_qty = '',
									variation_id = null,
									items = {},
									xhr = new XMLHttpRequest();

								xhr.open('post', zoa_ajax.url);
								xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
								var vari_params = '';
								if (_cart[i].classList.contains('variations_form')) {
									single_atc_id = _cart[i].querySelector('[name="product_id"]').value;
									variation_id = _cart[i].querySelector('[name="variation_id"]').value;
									_qty = parseInt(_input.value);

									var product_attr = _cart[i].querySelectorAll('select[name^="attribute"]');

									product_attr.forEach(function (x) {
										var attr_name = x.name,
											attr_value = x.value;
										items[attr_name] = attr_value;
										vari_params += '&' + attr_name + '=' + attr_value;
									});
								} else {
									single_atc_id = _cart[i].querySelector('[name="add-to-cart"]').value;
									_qty = parseInt(_input.value);
								}

								/*ALERT IF NOT VALID QUANTITY*/
								if (_qty < 1 || isNaN(_qty)) {
									alert(_valid_qty);
									return;
								}

								/*CONDITION IF STOCK MANAGER ENABLE*/
								if ('yes' == _in_stock) {
									if (_in_cart_qty == _max) {
										alert(_out_of_stock);
										return;
									}

									if (+_qty + +_in_cart_qty > _max) {
										alert(_not_enough);
										return;
									}
								}

								/*UPDATE in_cart VALUE*/
								_in_cart.value = +_in_cart.value + +_input.value;
								/*OPEN && CLOSE CART SIDEBAR ACTION*/
								event_cart_sidebar_open();
								open_cart_sidebar();
								close_cart_sidebar();
								// Add loading animation.
								_btn.classList.add('loading');
								xhr.addEventListener('readystatechange', function () {
									if (xhr.readyState === 4) {
										// Clean response to remove any script tags
										var cleanedResponse = xhr.responseText
											.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, '')  // Remove <script> tags
											.replace(/<!-- function enqueue_language_popup_script\(\) {[\s\S]*?add_action\('wp_enqueue_scripts', 'enqueue_language_popup_script'\); -->/g, '');  // Remove specific comment block

										//console.log(cleanedResponse);
										try {
											// Try parsing the cleaned response as JSON
											var s_data = JSON.parse(cleanedResponse);
											// console.log('Parsed Response:', s_data);

											if (xhr.status === 200) {
												/* UPDATE PRODUCT COUNT */
												for (var c = 0, cl = _item_count.length; c < cl; c++) {
													_item_count[c].innerHTML = s_data.item;
												}
												/* APPEND CONTENT */
												_cart_sidebar.innerHTML = s_data.content;
											}
										} catch (e) {
											console.error('Error parsing JSON:', e);
										}
									}
								});


								xhr.addEventListener('load', function () {
									event_cart_sidebar_close();
									_btn.classList.remove('loading');
								});

								if (!jQuery('.mwb_wgm_added_wrapper').length && !jQuery('.product-type-bundle').length) {
									var additional_param = '';
									if (jQuery('.yith-wapo-container').length) {
										jQuery('form.cart').find('input[name="add-to-cart"]').attr('disabled', true);
										additional_param = '&' + jQuery('form.cart').serialize();
									}
									else {
										jQuery('form.cart').find('input[name="add-to-cart"]').attr('disabled', false);
									}

									xhr.send('action=single_add_to_cart&nonce=' + zoa_ajax.nonce + '&product_id=' + single_atc_id + '&product_qty=' + _qty + '&variation_id=' + variation_id + '&variations=' + JSON.stringify(items) + additional_param + vari_params + '&yith_wapo_is_single=1' + '&bra_product_id=' + bra_product_id + '&bra_size=' + bra_size + '&bra_size_soft=' + bra_size_soft + '&bra_size_full_cup=' + bra_size_full_cup + '&try_fit_type=' + try_fit_type + '&online_consultation=' + online_consultation + '&ch_type_orf=' + ch_type_orf + '&ch_orf_sub_type_even=' + ch_orf_sub_type_even + '&gift_options=' + gift_options);
								}

							} else {
								jQuery('div.woocommerce-notices-wrapper').remove();
								jQuery("div.shop-content").prepend('<div class="woocommerce-notices-wrapper"><div class="woocommerce-notice-wrapper"><ul class="woocommerce-error woo-notice-box TYTY" role="alert"><li>' + data + '</li></ul></div></div>');
								jQuery("html, body").animate({ scrollTop: 0 }, 1000);
								return false;
							}
						},
						error: function (errorThrown) {
							console.log(errorThrown);
						}
					});

				});
			};

			for (var i = 0, j = _cart.length; i < j; i++) {
				var _ret = _loop(i, j);

				if ((typeof _ret === 'undefined' ? 'undefined' : _typeof(_ret)) === "object") return _ret.v;
			}
		};
		single_add_to_cart();

		/*QUICK VIEW*/
		var quick_view_ajax = function () {

			var qv_btn = document.getElementsByClassName('product-quick-view-btn'),
				qv_box = document.getElementById('shop-quick-view'),
				qv_content = qv_box ? qv_box.getElementsByClassName('quick-view-content')[0] : false,
				qv_close_btn = qv_box ? qv_box.getElementsByClassName('quick-view-close-btn')[0] : false;

			if (!qv_box || !qv_btn.length) {
				return;
			}

			var _loop2 = function _loop2(_i, _j) {
				qv_btn[_i].addEventListener('click', function () {

					var qv_product_id = qv_btn[_i].getAttribute('data-pid'),
						qv_id = qv_box.getAttribute('data-view_id'),
						xhr = new XMLHttpRequest();

					if (qv_product_id === qv_id) {
						_body.classList.add('quick-view-open');
						return;
					}

					qv_content.innerHTML = '';

					_body.classList.add('quick-view-open', 'quick-viewing');

					var quick_view_close = function quick_view_close() {
						_body.classList.remove('quick-view-open');
					};

					_body.addEventListener('keyup', function (e) {
						if (27 === e.keyCode) {
							quick_view_close();
						}
					});

					qv_close_btn.addEventListener('click', function () {
						quick_view_close();
					});

					_overlay.addEventListener('click', function () {
						quick_view_close();
					});

					qv_box.setAttribute('data-view_id', qv_product_id);

					xhr.open('post', zoa_ajax.url);
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
					xhr.send('action=quick_view&nonce=' + zoa_ajax.nonce + '&product_id=' + qv_product_id);

					xhr.addEventListener('readystatechange', function () {
						if (4 === xhr.readyState) {
							var _data = JSON.parse(xhr.responseText);

							if (200 === _data.status) {
								qv_content.innerHTML = _data.content;
							}
						}
					});

					xhr.addEventListener('load', function () {
						_body.classList.remove('quick-viewing');

						quantity('#shop-quick-view .cart');

						/*TINY SLIDER FOR QUICKVIEW*/
						var qv_slider = function () {
							var qv_gallery = document.getElementById('quick-view-gallery');

							if (!qv_gallery || !qv_gallery.classList.contains('quick-view-slider')) {
								return;
							}

							var qv_carousel = tns({
								loop: false,
								container: '#quick-view-gallery',
								items: 1,
								mouseDrag: true
							});

							/*RESET SLIDER*/
							jQuery(document.body).on('found_variation', 'form.variations_form', function (event, variation) {
								qv_carousel.goTo('first');
							});

							jQuery('.reset_variations').on('click', function () {
								qv_carousel.goTo('first');
							});
						}();

						/*VARIATION PRODUCT FOR QUICKVIEW*/
						var qv_swatch = function () {
							var var_form = jQuery(_body).find('#shop-quick-view .variations_form');
							if (!var_form.length) {
								return;
							}

							if ((typeof wc_add_to_cart_variation_params === 'undefined' ? 'undefined' : _typeof(wc_add_to_cart_variation_params)) !== undefined) {
								var_form.wc_variation_form();
								var_form.find('.variations select').change();
							}

							if (_typeof(jQuery.fn.tawcvs_variation_swatches_form) !== undefined) {
								var_form.tawcvs_variation_swatches_form();
							}
						}();

						product_variation(true);

						single_add_to_cart(true);
					});

					xhr.addEventListener('error', function () {
						return alert('Sorry, something went wrong. Please refresh this page to try again!');
					});
				});
			};

			for (var _i = 0, _j = qv_btn.length; _i < _j; _i++) {
				_loop2(_i, _j);
			}
		}();

		/*UPDATE BODY CLASS WHEN CART SIDEBAR OPEN && CLOSE*/
		var event_cart_sidebar_open = function event_cart_sidebar_open() {
			_body.classList.add('updating-cart');
			_body.classList.remove('cart-updated');
		};
		var event_cart_sidebar_close = function event_cart_sidebar_close() {
			_body.classList.add('cart-updated');
			_body.classList.remove('updating-cart');
		};

		/*GLOBAL*/
		jQuery(_body).on('adding_to_cart', function () {
			open_cart_sidebar();
			event_cart_sidebar_open();
		}).on('added_to_cart', function () {
			close_cart_sidebar();
			event_cart_sidebar_close();
		}).on('click', '.add_to_wishlist', function () {
			/*ADDING TO WISHLIST*/
			this.classList.add('adding-to-wishlist');
		}).on('removed_from_cart', function () {
			/*RUN AFTER REMOVED PRODUCT FROM CART*/
			var run_after_removed_from_cart = function () {
				var _pid = '',
					_cart = document.getElementsByClassName('cart')[0],
					_btn = _cart ? _cart.getElementsByClassName('single_add_to_cart_button')[0] : false,
					_in_cart = _cart ? _cart.getElementsByClassName('in-cart-qty')[0] : false,
					_in_stock = _in_cart ? _in_cart.getAttribute('data-in_stock') : 'no',
					_qty = '',
					xhr = new XMLHttpRequest();

				if (!_cart || !_btn || 'no' == _in_stock) {
					return;
				}

				if (_cart.classList.contains('variations_form')) {
					_pid = _cart.querySelector('input[name="product_id"]').value;
				} else {
					_pid = _btn.value;
				}

				xhr.open('post', zoa_ajax.url);
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send('action=get_count_product_already_in_cart&product_id=' + _pid);
				xhr.addEventListener('readystatechange', function () {
					if (4 === xhr.readyState) {
						var i_data = JSON.parse(xhr.responseText);
						_in_cart.value = i_data.in_cart;
					}
				});
			}();
		}).on('updated_cart_totals', function () {
			quantity();
		});
	}();

	/**
	 * Ajax search form
	 */
	(function () {
		var $searchForm = jQuery('.js-search-form');
		var $searchButton = jQuery('.js-search-button');
		var $closeButton = jQuery('.js-close-search-form');
		var $searchField = jQuery('.js-search-field');
		var $loader = jQuery('.zoa-search-form__loader');

		$searchButton.on('click', function () {
			$searchForm.addClass('is-open');
			$searchField.focus();
		});

		$closeButton.on('click', function (e) {
			e.preventDefault();
			$searchForm.removeClass('is-open');
		});

		/* HIT `ESC` KEY TO CLOSE DIALOG SEARCH */
		document.body.addEventListener('keyup', function (e) {
			if (27 === e.keyCode) {
				$searchForm.removeClass('is-open');
			}
		});

		jQuery('#autocomplete').autocomplete({
			serviceUrl: global.url,
			deferRequestBy: 300,
			params: {
				action: 'zoa_ajax_search_handler',
				_ajax_nonce: global.nonce
			},
			appendTo: jQuery(".zoa-search-form__suggestions"),
			onSearchStart: function onSearchStart() {
				$loader.addClass('fa-spin is-active');
			},
			onSearchComplete: function onSearchComplete(query, suggestions) {
				$loader.removeClass('fa-spin is-active');
			},
			formatResult: function formatResult(suggestion, currentValue) {
				var result = '';

				if (suggestion.id === -1) {
					result = suggestion.value;
					return result;
				}

				if (suggestion.thumbnail) {
					result += '<div class="autocomplete-suggestion__thumbnail"><a href="' + suggestion.url + '">' + suggestion.thumbnail + '</a></div>';
				}

				result += '<div class="autocomplete-suggestion__content">';
				result += '<h3 class="autocomplete-suggestion__title"><a href="' + suggestion.url + '">' + suggestion.value + '</a></h3>';
				result += suggestion.price;

				if (suggestion.excerpt) {
					result += '<div class="autocomplete-suggestion__excerpt">' + suggestion.excerpt + '</div>';
				}

				result += '</div>';

				return result;
			}
		});
	})();

	/* LOAD
	 ***************************************************/
	window.addEventListener('load', function () {

		/*! PRELOADER `done()`
		------------------------------------------------->*/
		if (document.getElementsByClassName('is-page-loading')[0]) {
			var themeContainer = document.getElementById('theme-container');

			if (themeContainer) {
				themeContainer.classList.remove('is-loading');
				themeContainer.classList.add('is-ready');
			}
			NProgress.done();
		}
	});

	// Sticky menu on mobile.
	(function () {
		if (document.body.classList.contains('mobile-header-menu-sticky') && window.matchMedia('( max-width: 991px )').matches) {
			var setup = function setup(pos, adminBarHeight) {
				if (pos >= adminBarHeight) {
					document.body.classList.add('mobile-header-fixed');
				} else {
					document.body.classList.remove('mobile-header-fixed');
				}
			};

			var themeMenuLayout = document.getElementById('theme-menu-layout'),
				menuLayout = themeMenuLayout.getElementsByClassName('menu-layout')[0],
				adminBar = document.getElementById('wpadminbar');

			// Set height for parent div.
			themeMenuLayout.style.height = menuLayout.offsetHeight + 'px';

			if (!adminBar) {
				return;
			}

			var adminBarHeight = adminBar.offsetHeight,
				pos = window.scrollY;

			setup(pos, adminBarHeight);

			window.addEventListener('scroll', function () {
				pos = window.scrollY, adminBarHeight = adminBar.offsetHeight;

				setup(pos, adminBarHeight);
			});
		}
	})();

	// Widget: Accordion product category.
	(function () {
		var accordion = jQuery('.product-categories');
		if (!accordion.length) {
			return;
		}

		accordion.each(function (index) {
			var hasChild = jQuery(this).find('.cat-parent');
			if (!hasChild.length) {
				return;
			}

			hasChild.each(function (i) {
				// Create Toggle Button.				
				var toggle = jQuery('<span class="accordion-cat-toggle ion-ios-arrow-right"></span>');

				// Append Toggle Button.
				var parent = jQuery(this);
				jQuery(parent).append(toggle);

				// Toggle Button click.
				toggle.on('click', function () {
					var button = jQuery(this),
						buttonParent = button.parent(),
						child = buttonParent.find('>ul'),
						state = button.data('state') || 1;

					// State update.
					switch (state) {
						case 1:
							button.data('state', 2);
							break;
						case 2:
							button.data('state', 1);
							break;
					}

					// Toggle child category.
					child.slideToggle(300);

					// Add active class.
					if (1 === state) {
						button.addClass('active');
						buttonParent.addClass('active');
					} else {
						button.removeClass('active');
						buttonParent.removeClass('active');
					}
				});
			});
		});
	})();

	// Widget: Featured products.
	(function () {
		var featured = jQuery('.widget-featured-carousel-product');
		if (!featured.length) {
			return;
		}

		featured.each(function () {
			var t = jQuery(this),
				perRow = t.data('slider_per_row'),
				arrows = t.parent().find('.widget-featured-carousel-product-arrow'),
				arrowPrev = arrows.find('.prev-arrow'),
				arrowNext = arrows.find('.next-arrow');

			t.slick({
				rows: 1,
				slidesPerRow: perRow,
				prevArrow: arrowPrev,
				nextArrow: arrowNext
			});
		});
	})();
});