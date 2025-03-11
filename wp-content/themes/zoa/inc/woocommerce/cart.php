<?php

/*EMPTY CART*/
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
    global $woocommerce;

    if ( isset( $_GET['empty-cart'] ) ) {
        $woocommerce->cart->empty_cart(); 
    }
}

/*ADD CLEAR CART BUTTON*/
add_action( 'woocommerce_cart_actions', 'zoa_clear_cart_url' );
function zoa_clear_cart_url() {
    $cart_empty_url = wc_get_cart_url() . '?empty-cart';
    ?>

    <a class="cart-empty-btn" href="<?php echo esc_url( $cart_empty_url ); ?>"><?php esc_html_e( 'Clear Shopping Cart', 'zoa' ); ?></a>
    <?php
}

?>