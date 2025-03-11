<?php

namespace Elementor;

class zoa_widget_product_flash_sale extends Widget_Base {

    public function get_categories() {
        return array( 'zoa-theme' );
    }

    public function get_name() {
        return 'product-flash-sale';
    }

    public function get_title() {
        return esc_html__( 'Woo - Product Flash Sale', 'zoa' );
    }

    public function get_icon() {
        return 'eicon-woocommerce';
    }

    public function get_script_depends() {
        return array( 'countdown' );
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'product_flash_sale_content',
            array(
                'label' =>  esc_html__( 'General', 'zoa' ),
            )
        );

        $this->add_control(
            'pro_flash_sale',
            array(
                'label'     =>  esc_html__( 'Sale products', 'zoa' ),
                'type'      =>  Controls_Manager::SELECT2,
                'options'   =>  $this->_product_flash_sale_ids()
            )
        );

        $this->add_control(
            'pro_flash_sale_thumbnail',
            array(
                'label'     =>  esc_html__( 'Thumbnail size', 'zoa' ),
                'type'      =>  Controls_Manager::SELECT2,
                'options'   =>  get_intermediate_image_sizes()
            )
        );

        $this->end_controls_section();
    }

    protected function _product_flash_sale_ids() {
        $output = array();

        if( class_exists( 'woocommerce' ) ){
            $args = array(
                'post_type'      => 'product',
                'type'           => array( 'simple', 'external' ),
                'posts_per_page' => -1,
                'meta_query'     => WC()->query->get_meta_query(),
                'post__in'       => array_merge( array( 0 ), wc_get_product_ids_on_sale() )
            );

            $qr     = new \WP_Query( $args );
            $output = wp_list_pluck( $qr->posts, 'post_title', 'ID' );
        }

        return $output;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = (int) $settings['pro_flash_sale'];

        if( ! $id ) return;

        $this->renderCountdown();

        /*PRODUCT*/
        $product        = wc_get_product( $id );

        /*IMAGE*/
        $thumbnail      = $settings['pro_flash_sale_thumbnail'];
        $thumbnail_size = ! empty( $thumbnail ) ? get_intermediate_image_sizes()[$thumbnail] : 'medium';
        $image_id       = $product->get_image_id();
        $image_alt      = zoa_img_alt( $image_id, esc_attr__( 'Product image', 'zoa' ) );
        $image_src      = $image_id ? wp_get_attachment_image_src( $image_id, $thumbnail_size )[0] : wc_placeholder_img_src();

        /*RATING*/
        $rating_count   = $product->get_rating_count();
        $average        = $product->get_average_rating();
        $rating         = wc_get_rating_html( $average, $rating_count );

        /*PRICE*/
        $price          = $product->get_price_html();

        /*SALE DATE*/
        $meta_date      = '';
        ?>

        <div class="wd-pro-flash-sale">
            <?php
                if( ! $product->is_type( 'variable' ) ){
                    $meta_date = get_post_meta( $id, '_sale_price_dates_to', true );

                    if( empty( $meta_date ) ){
                        return esc_html_e( 'You are not yet set sale schedule for this product', 'zoa' );
                    }

                    $end_date  = date_i18n( 'm/d/Y', $meta_date );

                    ?>
                        <a href="<?php echo get_permalink( $id ) ?>" class="pfs-image">
                            <img src="<?php echo esc_url( $image_src ) ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
                        </a>

                        <div class="flash-sale-cd" data-date="<?php echo esc_attr( $end_date ); ?>">
                            <div class="cd-item">
                                <span id="<?php echo esc_attr( uniqid( 'cd-days-' ) ); ?>" class="cd-time"></span>
                                <span class="cd-text"><?php esc_html_e( 'days', 'zoa' ); ?></span>
                            </div>

                            <div class="cd-item">
                                <span id="<?php echo esc_attr( uniqid( 'cd-hours-' ) ); ?>" class="cd-time"></span>
                                <span class="cd-text"><?php esc_html_e( 'hours', 'zoa' ); ?></span>
                            </div>

                            <div class="cd-item">
                                <span id="<?php echo esc_attr( uniqid( 'cd-minutes-' ) ); ?>" class="cd-time"></span>
                                <span class="cd-text"><?php esc_html_e( 'mins', 'zoa' ); ?></span>
                            </div>

                            <div class="cd-item">
                                <span id="<?php echo esc_attr( uniqid( 'cd-seconds-' ) ); ?>" class="cd-time"></span>
                                <span class="cd-text"><?php esc_html_e( 'secs', 'zoa' ); ?></span>
                            </div>
                        </div>

                        <h3 class="cd-title"><a href="<?php echo get_permalink( $id ) ?>"><?php echo get_the_title_product_chiyono( $id ); ?></a></h3>

                        <div class="price">
                            <?php echo wp_kses_post( $price ); ?>
                        </div>

                        <div class="flash-sale-atc">
                            <?php
                                $args     = array();

                                $defaults = array(
                                    'quantity'   => 1,
                                    'class'      => implode( ' ', array_filter( array(
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
                                    esc_html__( 'Add to cart', 'zoa' )
                                );
                            ?>
                        </div>
                    <?php
                }else{
                    esc_html_e( 'This widget support simple product only', 'zoa' );
                }
            ?>
        </div>

        <?php
    }

    protected function renderCountdown(){
        wp_add_inline_script(
            'countdown',
            "document.addEventListener( 'DOMContentLoaded', function(){
                var el = document.getElementsByClassName( 'flash-sale-cd' ),
                    elen = el.length,
                    i;
                if( elen < 1 ) return;

                for( i = 0; i < elen; i++ ){
                    var _date = el[i].getAttribute( 'data-date' ),
                        days_id = el[i].getElementsByClassName( 'cd-time' )[0].id,
                        hours_id = el[i].getElementsByClassName( 'cd-time' )[1].id,
                        mins_id = el[i].getElementsByClassName( 'cd-time' )[2].id,
                        secs_id = el[i].getElementsByClassName( 'cd-time' )[3].id;

                    var counter = Doom( {
                        targetDate: _date,
                        ids: {
                            days: days_id,
                            hours: hours_id,
                            mins: mins_id,
                            secs: secs_id,
                        },
                    } );

                    counter.doom();
                }
            } );",
            'after'
        );
    }
}
Plugin::instance()->widgets_manager->register_widget_type( new zoa_widget_product_flash_sale() );

?>