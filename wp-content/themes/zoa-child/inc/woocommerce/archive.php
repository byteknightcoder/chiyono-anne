<?php

/*TOP BAR*/
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop', 'zoa_result_count', 20 );
function zoa_result_count(){
?>
    <div class="shop-top-bar">
    <?php
        woocommerce_result_count();
}

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'zoa_catalog_ordering', 30 );
function zoa_catalog_ordering(){
    woocommerce_catalog_ordering();
    ?>
    </div>
    <?php
}

/* ADD SWATCHES LIST */
add_filter( 'woocommerce_after_shop_loop_item_title', 'zoa_loop_add_to_cart', 20 );
function zoa_loop_add_to_cart() {
    echo zoa_swatches_list();
}

/*CHANGE ADD TO CART TEXT*/
add_filter( 'woocommerce_product_add_to_cart_text', 'zoa_modify_cart_button_text' );
function zoa_modify_cart_button_text() {
    return '';
}

/*SWATCH LIST FOR VARIABLE PRODUCT ON ARCHIVE && PRODUCTS WIDGET*/
if( ! function_exists( 'zoa_swatches_list' ) ) {
    function zoa_swatches_list( $size = 'woocommerce_single' ) {
        global $product;

        $output       = '';
        $color_output = $image_output = $label_output = '';
        $pid          = $product->get_id();

        if( empty( $pid ) || ! $product->is_type( 'variable' ) ) return $output;


        $default_attr = method_exists( $product, 'get_default_attributes' ) ? $product->get_default_attributes() : array();
        $vars         = $product->get_available_variations();
        $attributes   = $product->get_attributes();

        if( empty( $attributes ) ) return $output;

        foreach( $attributes as $key ){
            /*SWATCHES TYPE, EX: `pa_size`, `pa_color`, `pz_image`...*/
            $attr_name = $key['name'];

            $terms     = wc_get_product_terms( $pid, $attr_name, array( 'fields' => 'all' ) );

            /*GET TYPE OF PRODUCT ATTRIBUTE BY ID*/
            $attr_type = wc_get_attribute( $key['id'] );

            if( empty( $terms ) ) return $output;
            
            $id_slug = $id_name = array();

            foreach( $terms as $val ){
                $id_slug[$val->term_id] = $val->slug;
                $id_name[$val->name]    = $val->slug;
            }

            $color     = $img_id = $label = '';
            $empty_arr = array();

            foreach( $vars as $key ){
                $slug = isset( $key['attributes']['attribute_' . $attr_name] ) ? $key['attributes']['attribute_' . $attr_name] : '';

                if( ! in_array( $slug, $empty_arr ) ) {
                    array_push( $empty_arr, $slug );
                }else{
                    continue;
                }

                if( empty( $slug ) ) continue;
                
                $_id    = array_search( $slug, $id_slug );
                $name   = array_search( $slug, $id_name );
                $src    = wp_get_attachment_image_src( $key['image_id'], $size );
                $_class = ( isset( $default_attr[$attr_name] ) && $slug == $default_attr[$attr_name] ) ? 'active' : '';
                
                switch( $attr_type->type ){
                    case 'color':
                        $color        = get_term_meta( $_id, 'color', true );
                        $color_output .= '<span class="p-attr-swatch p-attr-color '. esc_attr( $_class) .'" title="'. esc_attr( $name ) .'" data-src="'. esc_attr( $src[0] ) .'" style="background-color:'. esc_attr( $color ) .'"></span>';
                        break;

                    case 'image':
                        $img_id       = get_term_meta( $_id, 'image', true );
                        $img_alt      = zoa_img_alt( $img_id, esc_attr__( 'Swatch image', 'zoa' ) );
                        $image_output .= '<span class="p-attr-swatch p-attr-image '. esc_attr( $_class) .'" title="'. esc_attr( $name ) .'" data-src="'. esc_attr( $src[0] ) .'"><img src="' . wp_get_attachment_url( $img_id ) . '" alt="'. esc_attr( $img_alt ) .'"></span>';
                        break;

                    case 'label':
                        $label        = get_term_meta( $_id, 'label', true );
                        $label_output .= '<span class="p-attr-swatch p-attr-label '. esc_attr( $_class) .'" title="'. esc_attr( $name ) .'" data-src="'. esc_attr( $src[0] ) .'">'. esc_html( $label ) .'</span>';
                        break;
                }
            }
        }
        
        if( ! empty( $color_output ) ){
            $output .= $color_output;
        }elseif( ! empty( $image_output ) ){
            $output .= $image_output;
        }else{
            $output .= $label_output;
        }
		return '';

        //return '<div class="pro-swatch-list">' . $output . '</div>';
    }
}

?>