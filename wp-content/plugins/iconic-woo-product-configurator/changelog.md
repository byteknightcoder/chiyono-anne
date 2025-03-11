**v1.23.0** (19 Jun 2024)  
[new] Compatibility with the block based Product Editor  
[update] Store and output images scaled down images on the cart page  
[fix] Image change doesn't work when used with Iconic Linked Variation  
[fix] Fatal error in Elementor editor  

**v1.22.0** (12 Apr 2024)  
[new] Compatibility with the single product block.  
[new] Added several developer filters to support offloading generated images to a CDN.  
[new] Added new logic and the `iconic_pc_disallowed_params` developer filter to strip unwanted parameters causing generated thumbnail images to break.  
[update] Refactored caching implementation and introduced automatic cleanup of cached image files when the transients expire.
[update] Slight refactor to front-end JavaScript to improve compatibility with plugins that hide the variations table and provide their own variations UI e.g Extra Product Options & Add-Ons.  
[update] Telemetry now shares a dependency injection container with other Iconic products for performance.  

**v1.21.2** (11 Dec 2023)  
[update] Iconic dependencies.  
[fix] Image zoom with preloading enabled no longer requires moving the mouse away and back again to trigger  

**v1.21.1** (16 Nov 2023)  
[fix] Update the large zoom image after preloading completes  

**v1.21.0** (15 Nov 2023)  
[new] Added a new image preloading setting to avoid the brief flash when loading large / high quality images  
[update] Updated documentation URLs  
[fix] Generated images now correctly display in place of order item thumbnails  
[fix] Zoom images now correctly update in Chrome/Webkit browsers on variation change  
[fix] Data for unavailable images will no longer be added to layers  

**v1.20.1** (31 Aug 2023)  
[fix] Resolved WooThumbs + Product Configurator compatibility issues  
[fix] Fix license validation issues and type errors  

**v1.20.0** (24 Aug 2023)  
[new] New licensing system  

**v1.9.2** (21 Jul 2023)  
[new] Filter to rename generated image: `iconic_pc_image_name`  
[update] Updated Iconic dependencies  
[fix] Conditional images now load properly when default attribute values are set  

**v1.9.1** (5 Jul 2023)  
[update] Updated dependencies  

**v1.9.0** (1 Jun 2023)  
[new] Declared compatibility with WooCommerce's HPOS feature  
[update] Debounced large image updates to reduce HTTP requests  
[update] Replaced the default order item thumbnail with the generated product image  
[update] Prevented variation changes from causing excessive refreshing of WooThumbs  
[update] Admin AJAX image generation URLs can now have `&redirect=1` appended to redirect to the generated image file  
[fix] Only query select fields inside visible variations tables  
[fix] Compatibility with Be Theme builder  
[fix] Stripped % characters from attribute slugs when loading JSON layer images  

**v1.8.7** (24 Jan 2023)  
[update] Avoid recursion issue when WooThumbs images load  

**v1.8.6** (2 Jan 2023)  
[fix] "&" characters in custom product attribute terms no longer break image generation  

**v1.8.5** (5 Dec 2022)  
[fix] Prevented a fatal error in PHP 8 when JPG images are (incorrectly) used for configurator layers  
[fix] Prevented the duplication of configurator markup when the featured image is used again in the gallery  

**v1.8.4** (18 Nov 2022)  
[fix] Prevent fatal error when fetching product meta  

**v1.8.3** (7 Oct 2022)  
[update] Settings framework updated  

**v1.8.2** (5 Oct 2022)  
[fix] Fixed bug in Variation Swatches for WooCommerce compatibility to prevent recursion issues  

**v1.8.1** (3 Oct 2022)  
[fix] Sanitised attribute values are no longer forced to lowercase  

**v1.8.0** (27 Sep 2022)  
[update] Improved WPML compatibility  
[update] Improved logic to prevent errors when non-select elements with the `data-attribute-name` attribute exist  
[fix] Resolved compatibility issue with Goya theme  
[fix] Resolved compatibility issue with Variation Swatches for WooCommerce plugin  

**v1.7.1** (17 Aug 2022)  
[fix] Silenced an error when fetching generated product image URLs, caused by some 3rd party plugins  

**v1.7.0** (12 Aug 2022)  
[fix] Compatibility with WC Product Bundles  

**v1.6.2** (29 Jun 2022)  
[update] Refactored front-end JavaScript to fire on window load instead of document ready.  

**v1.6.1** (24 Jun 2022)  
[update] Developer update: allow the configurator as a non-zero indexed item in the gallery  

**v1.6.0** (28 Apr 2022)  
[new] Added a new getting started tab to the plugin settings.  

**v1.5.2** (7 Apr 2022)  
[update] Prevent fatal errors when a plugin attempts to render Product Configurator when the $product object is not available e.g Elementor's template preview.  
[update] Update dependencies  

**v1.5.1** (1 Mar 2022)  
[update] Updated Freemius SDK.  

**v1.5.0** (11 Jan 2022)  
[new] Compatibility with Yith Wishlist  
[new] Compatibility with WooCommerce QuickTray by Iconic  
[update] Settings framework updates  
[update] Update dependencies  
[update] Remove `wp-post-image` class from Product Configurator layer images  
[fix] Enforced 100% width on Product Configurator images when in a WooThumbs gallery context  
[fix] Silenced error caused by terms not having a default featured image  
[fix] Fixed class-product where (int) was being compared to a string and failing  
[fix] Fixed broken cart image when any of the attribute values has ampersand character(&)  

**v1.4.0** (03 Mar 2021)  
[new] Conditional layers  
[new] Clear cache button  
[new] Configurator tab toolbar  
[new] JSON loading method for faster image layer updates  
[new] Integration with the default WooCommerce gallery  
[update] Removing Ajax loading in favour of JSON loading methods  
[update] Divi Compatiblity  
[update] Elementor Compatiblity  
[update] Change hook: `jckpc-thumbnail-image-size` to `jckpc_thumbnail_image_size`  
[update] Update dependencies  
[fix] Fix html in notice within the Configurator tab  
[fix] Enqueue admin styles when $pagenow == post-new.php so admin notices appear  
[fix]  Attribute name sanitization (**Note**: test on staging to ensure sort order and images are all assigned correctly in product's Configurator tab)  
[fix] `jckpc_defaults` not saving  
[fix] WooThumbs compatibility when using a different folder name  
[fix] Can't delete default image on global attribute  
[fix] Old image is not deleted if new image is added with + button  
[fix] Default zoom image on page load  
[fix] Retina image is now loaded on page load  

**v1.3.9** (17 Aug 2020)  
[fix] Compatibility with WP 5.5  
[update] Divi Theme Compatiblity  
[update] Update dependencies  

**v1.3.8** (24 Apr 2020)  
[update] Update dependencies  
[update] Version compatibility  

**v1.3.7** (18 Mar 2020)  
[update] Version compatibility  

**v1.3.6** (1 Aug 2019)  
[update] Use small image in cart  
[update] Use full image in zoom/fullscreen  
[fix] Change startsWith to indexOf for IE11  
[fix] Ensure query params are added effectively  

**v1.3.5** (1 July 2019)  
[fix] Freemius Fix  

**v1.3.4** (2 March 2019)  
[fix] Security Fix  

**v1.3.3** (9 Jan 2019)  
[update] Update dependencies  
[update] Compatibility with Woo 3.5.0  
[fix] Ensure layers are loaded again after adding to cart  

**v1.3.2** (12 Sep 2018)  
[update] Add tool to reinstall db tables  
[update] Update POT file  
[fix] Ensure check for active Iconic plugins works  
[fix] Ensure WooThumbs zoom is triggered after layer change  

**v1.3.1** (10 Sep 2018)  
[update] Implement new Iconic core classes  
[update] Hash image names to prevent them being too long  
[fix] Ensure WooThumbs is at the latest version before attempting to use  
[fix] 0 value attribute names were not displayed in the configurator tab  
[fix] Add "no media icon" param for WooThumbs  
[fix] Don't float configurator in WooThumbs  
[fix] Ensure dummy zoom image is removed when fetching variation via AJAX  
[fix] Fix Compatibility issue with WooCommerce Variations Swatches and Photos Plugin  
[fix] Ensure inventory DB is created even after activation  

**v1.3.0** (6 Jun 2018)  
[update] Inventory: decrease even when stock isn't managed on the product.  
[update] Inventory: increase if order is cancelled/failed/refunded.  
[update] Inventory: check stock again before checking out.  
[update] Update POT file  
[update] Update Freemius  
[update] WooThumbs compatibility  
[update] Update classes and IDs  
[update] Cache images as they load for quicker switching  
[update] Add retina sizes to image layers  
[update] Return to default layer image when removing a selected option  
[update] Add plugin suggestions  
[update] Enable zoom and lightbox  
[fix] Fix thumbnail fullscreen issue  
[fix] Fix image size issue in Woo 3+  
[fix] Fix issue with some characters in attribute values (&|.|@|etc)  
[fix] Enhance layer loading so it can't be "tricked"  
[fix] Fix issue with Russian/foreign characters in layers  

**v1.2.2** (19/12/2017)  
[update] Allow loader to be disabled  
[update] Add \[iconic-wpc-gallery\] shortcode  
[update] Update Freemius  
[update] Compatibility with \[product_page\] shortcode  
[update] Improve configurator layer collapsing and sorting in admin  
[update] Improve image upload/remove in admin  
[update] update POT file  
[fix] Sync custom fields when using WPML  
[fix] Set language in AJAX requests  
[fix] Get correct taxonomy terms when taxonomy is translated  
[fix] Fix issue when using forward slashes in attribute value name  
[fix] Only validate BG image when configurator is enabled  
[fix] Issue with png validation for some hosts  
[fix] Strip query string from image URLs when generating

**v1.2.1** (10/08/2017)  
[update] Add WPML compatibility  
[update] New licence system  
[update] Renamed the plugin folder to match Iconic branding  
[fix] Missing galleries  
[fix] Invalid image URL in emails

**v1.2.0** (02/04/2017)  
[update] Compatibility with WooCommerce 3.0.0  
[update] Add static layer functionality  
[update] Remove redux and add settings framework  
[update] Tidy code and comments  
[fix] Fix issue with sorting layers  
[fix] Issue with uploading media to attribute  
[fix] Use WC ajax URL  
[fix] Compiled image in order emails  
[fix] Issue with product specific atts not loading layer on load  
[fix] Issue loading query selected layer with spaces

**v1.1.5** (22/12/2016)  
[fix] Some updates regarding image generation were missing  
[update] Envato market updater  
[update] Add filter to order email thumbnail

**v1.1.4** (07/09/16)  
[update] Author tags  
[fix] Check to see if WooCommerce is active - fix issue on multisites  
[fix] Some hosts don't allow remote images in getimagesize, changed to path so images are generated  
[fix] Error when no default image is set, but it was before  
[fix] Make sure correct image shows in email if it's enabled  
[update] Add validation to check for PNG images  
[fix] Attribute add default image button

**v1.1.3** (16/01/16)  
[fix] admin_url SSL issue

**v1.1.2** (09/09/15)  
[update] change watch to .variations select

**v1.1.1** (08/09/15)  
[fix] Errors when $post is not set  
[fix] Undefined param issue on add_to_cart_inventory_check  
[fix] Missing terms when product is draft

**v1.1.0** (13/07/15)  
[fix] Image layers not loading  
[fix] Image layer loading broken image when no image assigned  
[fix] Default image layer functionality  
[update] Added inventory functionality * Make sure to deactivate/reactivate to install the new DB table  
[fix] Removed images from order success and order email

**v1.0.8** (27/06/15)  
[fix] Moved check for woocommerce to try and fix header issues  
[fix] Remove invalid header error  
[fix] is_array errors  
[update] better pot file

**v1.0.7** (27/04/15)  
[fix] Moved check for woocommerce to try and fix header issues

**v1.0.6** (19/02/15)  
[Update] Add Cyr to Lat enhanced compatibility

**v1.0.5** (14/01/15)  
[Update] Allow for Russian (and other lang) attributes - caution: may affect previous configurations  
[Fix] Load admin scripts only on edit product page  
[Update] Check if WooCommerce is enabled  
[Fix] Fix compatibility with WooCommerce Variation Swatches and Photos v1.5.3

**v1.0.4** (06/08/14)  
[Fix] Fixed bug where TGM didn't notify that Redux was required

**v1.0.3** (27/07/14)  
[Update] Added "Default" image for attributes  
[Fix] Fixed bug where configurator was displayed on frontend even though it wasn't enabled  
[Update] Now works with WooCommerce Variation Swatches and Photos by Lucas Stark  
[Update] Added ability to order configurator options independantly, via drag/drop

**v1.0.2**  
[Fix] Removed tgmpa_load_bulk_installer error

**v1.0.1**  
[Fix] Configurator Enabled returned yes, not true. Added check for this.

**v1.0.0**  
Initial Release