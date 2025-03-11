<?php

/*SALE FLASH*/
add_filter( 'woocommerce_sale_flash', 'zoa_sale_flash' );
function zoa_sale_flash() {
    global $product;

    $sale       = $product->is_on_sale();
    $price_sale = $product->get_sale_price();
    $price      = $product->get_regular_price();
    $simple     = $product->is_type( 'simple' );

    if ( $sale ) :
    ?>
        <span class="onsale">
            <?php
                if( $simple ){
                    $final_price = ( ( $price - $price_sale ) / $price) * 100;
                    echo '-' . round( $final_price ) . '%';
                }else{
                    esc_html_e( 'Sale!', 'zoa' );
                }
            ?>
        </span>
    <?php
    endif;
}

/*QUICKVIEW*/

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'zoa_get_variation_attribute_options_html', 10, 2 );
function zoa_get_variation_attribute_options_html( $html, $args ) {
    if( ! function_exists( 'TA_WCVS' ) ) return $html;

    $swatch_types = TA_WCVS()->types;
    $attr         = TA_WCVS()->get_tax_attribute( $args['attribute'] );

    if ( empty( $attr ) ) {
        return $html;
    }

    if ( ! array_key_exists( $attr->attribute_type, $swatch_types ) ) {
        return $html;
    }

    $options   = $args['options'];
    $product   = $args['product'];
    $attribute = $args['attribute'];
    $class     = "variation-selector variation-select-{$attr->attribute_type}";
    $swatches  = '';

    if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
        $attributes = $product->get_variation_attributes();
        $options    = $attributes[$attribute];
    }

    if ( array_key_exists( $attr->attribute_type, $swatch_types ) ) {
        if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
            $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

            foreach ( $terms as $term ) {
                if ( in_array( $term->slug, $options ) ) {
                    $swatches .= apply_filters( 'tawcvs_swatch_html', '', $term, $attr, $args );
                }
            }
        }

        if ( ! empty( $swatches ) ) {
            $class .= ' hidden';

            $swatches = '<div class="tawcvs-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
            $html     = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
        }
    }

    return $html;
}

add_filter( 'tawcvs_swatch_html', 'zoa_swatch_html', 5, 4 );
function zoa_swatch_html( $html, $term, $attr, $args ) {
    if( ! function_exists( 'TA_WCVS' ) ) return $html;

    $selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
    $name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );

    switch ( $attr->attribute_type ) {
        case 'color':
            $color = get_term_meta( $term->term_id, 'color', true );
            list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );
            $html = sprintf(
                '<span class="swatch swatch-color swatch-%s %s" style="background-color:%s;color:%s;" title="%s" data-value="%s">%s</span>',
                esc_attr( $term->slug ),
                $selected,
                esc_attr( $color ),
                "rgba($r,$g,$b,0.5)",
                esc_attr( $name ),
                esc_attr( $term->slug ),
                $name
            );
            break;

        case 'image':
            $image = get_term_meta( $term->term_id, 'image', true );
            $image = $image ? wp_get_attachment_image_src( $image ) : '';
            $image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
            $html  = sprintf(
                '<span class="swatch swatch-image swatch-%s %s" title="%s" data-value="%s"><img src="%s" alt="%s">%s</span>',
                esc_attr( $term->slug ),
                $selected,
                esc_attr( $name ),
                esc_attr( $term->slug ),
                esc_url( $image ),
                esc_attr( $name ),
                esc_attr( $name )
            );
            break;

        case 'label':
            $label = get_term_meta( $term->term_id, 'label', true );
            $label = $label ? $label : $name;
            $html  = sprintf(
                '<span class="swatch swatch-label swatch-%s %s" title="%s" data-value="%s">%s</span>',
                esc_attr( $term->slug ),
                $selected,
                esc_attr( $name ),
                esc_attr( $term->slug ),
                esc_html( $label )
            );
            break;
    }

    return $html;
}

add_action( 'wp_ajax_quick_view', 'zoa_quick_view' );
add_action( 'wp_ajax_nopriv_quick_view', 'zoa_quick_view' );
function zoa_quick_view(){
    $response = array(
        'status'  => 500,
        'message' => esc_html__( 'Something is wrong, please try again later...', 'zoa' ),
        'content' => false,
    );

    if( ! isset( $_POST['product_id'] ) ||
        ! isset( $_POST['nonce'] ) ||
        ! wp_verify_nonce( $_POST['nonce'], 'zoa_product_nonce' ) ):

        echo json_encode( $response );
        exit();
    endif;

    $product_id  = intval( $_POST['product_id'] );

    /*FOR `cross-sells` CART PAGE*/
    $get_product = wc_get_product( $product_id );
    $parent_id   = $get_product->get_parent_id();
    if( $parent_id ){
        $product_id  = $parent_id;
    }

    wp( 'p=' . $product_id . '&post_type=product' );

    ob_start();

        if( have_posts() ){
            while ( have_posts() ):
                the_post();
                ?>
                <div <?php wc_product_class() ?>>
                    <?php
                        $product   = wc_get_product( $product_id );
                        $image_id  = $product->get_image_id();
                        $image_alt = zoa_img_alt( $image_id, esc_attr__( 'Product image', 'zoa' ) );

                        if( $image_id ){
                            $image_medium_src    = wp_get_attachment_image_src( $image_id, 'woocommerce_single' );
                        }else{
                            $image_medium_src[0] = wc_placeholder_img_src();
                        }

                        $gallery_id       = $product->get_gallery_image_ids();
                        $attr             = '';

                        if( ! empty( $gallery_id ) ){
                            $attr = 'class="quick-view-slider"';
                        }
                    ?>
                    <div class="quick-view-images">
                        <div id="quick-view-gallery" <?php echo wp_kses_post( $attr ); ?>>
                            <div class="pro-img-item">
                                <img src="<?php echo esc_url( $image_medium_src[0] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
                            </div>

                            <?php
                                if( ! empty( $gallery_id ) ):
                                    foreach( $gallery_id as $key ):
                                        $g_full_img_src   = wp_get_attachment_image_src( $key, 'full' );
                                        $g_medium_img_src = wp_get_attachment_image_src( $key, 'medium_large' );
                                        $g_img_alt        = zoa_img_alt( $key, esc_attr__( 'Product image', 'zoa' ) );
                                    ?>
                                    <div class="pro-img-item">
                                        <img src="<?php echo esc_url( $g_medium_img_src[0] ); ?>" alt="<?php echo esc_attr( $g_img_alt ); ?>">
                                    </div>
                            <?php
                                    endforeach;
                                endif;
                            ?>
                        </div>

                        <?php /* PRODUCT LABEL */ ?>
                        <?php
                            echo zoa_product_label( $product );
                        ?>
                    </div>

                    <div class="summary entry-summary">
                        <?php do_action( 'woocommerce_single_product_summary' ); ?>
                    </div>
                </div>

                <?php
                $response = array(
                    'status' => 200
                );
            endwhile;
        }else{
            $response = array(
                'status'  => 201,
                'message' => esc_html__( 'Sorry, nothing found', 'zoa' ),
            );
        }

    $response['content'] = ob_get_clean();

    echo json_encode( $response );
    exit();
}

/* GET PRODUCT THUMBNAIL */
if ( ! function_exists( 'zoo_get_product_thumbnail' ) ) {
    function zoo_get_product_thumbnail( $size = 'woocommerce_thumbnail' ) {
        global $product;

        if( ! $product ) {
            return '';
        }

        $variation  = $product->is_type( 'variable' );
        $img_id     = $product->get_image_id();
        $img_alt    = zoa_img_alt( $img_id, esc_attr__(  'Product image', 'zoa'  ) );
        $img_src    = array();
        $img_origin = wp_get_attachment_image_src( $img_id, $size );
        $image_attr = array(
            'alt'             => $img_alt,
            'data-origin_src' => $img_origin[0]
        );
        $image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

        if( $variation ) {
            $vars         = $product->get_available_variations();
            $default_attr = method_exists( $product, 'get_default_attributes' ) ? $product->get_default_attributes() : array();
            $attributes   = $product->get_attributes();
            $output       = '';

            foreach( $attributes as $key ) {
                $attr_type = $key['name'];

                foreach( $vars as $key ) {
                    $slug = isset( $key['attributes']['attribute_' . $attr_type] ) ? $key['attributes']['attribute_' . $attr_type] : '';

                    if( isset( $default_attr[$attr_type] ) && $default_attr[$attr_type] === $slug ) {
                        /**
                         *  $img_src get default image url
                         *  $default_alt alt for variation image
                         *  $img_props image attribute
                         */
                        $img_src     = wp_get_attachment_image_src( $key['image_id'], $size );
                        $default_alt = zoa_img_alt( $key['image_id'], esc_attr__(  'Product image', 'zoa'  ) );
                        $img_props   = wc_get_product_attachment_props( $key['image_id'], $product );

                        $default_image_attr = array(
                            'width'           => $img_props['thumb_src_w'],
                            'height'          => $img_props['thumb_src_h'],
                            'src'             => $img_src[0],
                            'alt'             => $default_alt,
                            'data-origin_src' => $img_origin[0],
                            'srcset'          => $img_props['srcset']
                        );

                        $default_image_attr['sizes']  = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $img_id, 'woocommerce_single' ) : false;


                        $output = implode( ' ', array_map(
                            function ( $v, $k ) {
                                return sprintf( "%s='%s'", $k, $v );
                            },
                            $default_image_attr,
                            array_keys( $default_image_attr )
                        ) );
                        break;
                    }
                }
            }

            if ( ! empty( $img_src ) ) {
                return '<img ' . wp_kses_post( $output ) . ' />';
            }
        }

        return $product->get_image( $image_size, $image_attr );
    }
}

/* REMOVE DEFAULT OPEN AND CLOSE A TAG WRAP PRODUCT IMAGE */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

/* ADD PERMALINK TO LOOP PRODUCT TITLE */
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', 'zoa_template_loop_product_title', 10 );
function zoa_template_loop_product_title() {
    echo '<h2 class="woocommerce-loop-product__title"><a href="' . get_permalink() . '">' . get_the_title_product_chiyono() . '</a></h2>';
}

/* WRAP PRODUCT THUMBNAIL INSIDE `.product-image-wrapper` */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
// add_action( 'woocommerce_before_shop_loop_item_title', 'zoa_wrap_product_image', 10 );
function zoa_wrap_product_image( $size = 'woocommerce_thumbnail', $args = array() ) {
    global $product;

    $image_size = apply_filters( 'single_product_archive_thumbnail_size', $size );

    $gallery = $product->get_gallery_image_ids();

    if( $product ) {
    ?>
        <div class="product-image-wrapper">
            <?php
                /* PRODUCT IMAGE */
                // open tag <a>
                woocommerce_template_loop_product_link_open();
                    echo zoo_get_product_thumbnail();

                    /* HOVER IMAHE */
                    if ( ! empty( $gallery ) ) {
                        $hover = wp_get_attachment_image_src( $gallery[0], $image_size );
                        ?>
                            <span class="hover-product-image" style="background-image: url(<?php echo esc_url( $hover[0] ); ?>);"></span>
                        <?php
                    }
                // close tag </a>
                woocommerce_template_loop_product_link_close();
            ?>

            <?php
            /* LOOP ACTION */
            $loop_action_classes = 'loop-action';
            $quick_action = get_theme_mod( 'quick_action', 'false' );
            if ( $quick_action ) {
            	$loop_action_classes .= ' loop-action--visible-on-mobile';
            }
            ?>
            <div class="<?php echo esc_attr( $loop_action_classes ); ?>">
                <?php /*SHOW IN QUICK VIEW BTN*/ ?>
                <span data-pid="<?php echo esc_attr( $product->get_id() ); ?>" class="product-quick-view-btn zoa-icon-quick-view"></span>
                <?php
                    /*ADD TO WISHLIST BUTTON*/
                    echo class_exists( 'TInvWL' ) ? do_shortcode( '[ti_wishlists_addtowishlist]' ) : '';

                    /*ADD TO CART BUTTON*/
                    if ( $product ) {
                        $defaults = array(
                            'quantity'   => 1,
                            'class'      => implode( ' ', array_filter( array(
                                'zoa-add-to-cart-btn',
                                'button',
                                'product_type_' . $product->get_type(),
                                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
                            ) ) ),
                            'attributes' => array(
                                'data-product_id'  => $product->get_id(),
                                'data-product_sku' => $product->get_sku(),
                                'aria-label'       => $product->add_to_cart_description()
                            ),
                        );

                        $args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

                        echo sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                            esc_url( $product->add_to_cart_url() ),
                            esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                            esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
                            isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                            esc_html( $product->add_to_cart_text()
                        ));
                    }
                ?>
            </div>

            <?php /* PRODUCT LABEL */ ?>
            <?php echo zoa_product_label( $product ); ?>
        </div>
    <?php
    }
}
