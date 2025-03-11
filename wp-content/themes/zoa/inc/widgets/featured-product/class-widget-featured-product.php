<?php
// @codingStandardsIgnoreStart
defined( 'ABSPATH' ) || exit;

class zoa_Widget_featured_product extends WP_Widget {
	function __construct() {
		$options = array(
			'description' => esc_html__( 'Display a slider featured products.', 'zoa' ),
			'classname'   => 'widget_featured_product'
		);

		parent::__construct( false, esc_html__( 'Featured Carousel Product', 'zoa' ), $options );
	}

	function widget( $args, $instance ) {

		extract( $args );
		$nav      = '<div class="widget-featured-carousel-product-arrow">';
		$nav      .= '<span class="ion-ios-arrow-left prev-arrow"></span>';
		$nav      .= '<span class="ion-ios-arrow-right next-arrow"></span>';
		$nav      .= '</div>';

		$title    = $before_title . $instance['title'] . $nav . $after_title;
		$number   = $instance['number'];
		$max      = $instance['max'];
		$orderby  = $instance['orderby'];
		$order    = $instance['order'];

		$filepath = get_template_directory() . '/inc/widgets/featured-product/views/widget.php';

		if ( file_exists( $filepath ) ) {
			include( $filepath );
		}
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'   => esc_html__( 'Featured Products', 'zoa' ),
				'number'  => 3,
				'max'     => 12,
				'orderby' => 'DESC',
				'order'   => 'ID',
			)
		);
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title', 'zoa' ); ?>
			</label>

			<input
				type="text"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			   	value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"
			   	id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
				<?php esc_html_e( 'Product per slide:', 'zoa' ); ?>
			</label>

			<input
				type="number"
				name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>"
			   	value="<?php echo esc_attr( $instance['number'] ); ?>" class="widefat"
		    	id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>">
				<?php esc_html_e( 'Max of products:', 'zoa' ); ?>
			</label>
			
			<input
				type="number"
				name="<?php echo esc_attr( $this->get_field_name( 'max' ) ); ?>"
			   	value="<?php echo esc_attr( $instance['max'] ); ?>" class="widefat"
		    	id="<?php echo esc_attr( $this->get_field_id( 'max' ) ); ?>"/>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php esc_html_e( 'Orderby:', 'zoa' ); ?>
			</label>

	    	<select
	    		class="widefat"
	    		name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>"
	    		id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" >
	    		<option <?php echo 'id' == $instance['orderby'] ? 'selected' : ''; ?> value="id"><?php esc_html_e( 'ID', 'zoa' ); ?></option>
	    		<option <?php echo 'title' == $instance['orderby'] ? 'selected' : ''; ?> value="title"><?php esc_html_e( 'Title', 'zoa' ); ?></option>
	    		<option <?php echo 'date' == $instance['orderby'] ? 'selected' : ''; ?> value="date"><?php esc_html_e( 'Date', 'zoa' ); ?></option>
	    		<option <?php echo 'modified' == $instance['orderby'] ? 'selected' : ''; ?> value="modified"><?php esc_html_e( 'Modified', 'zoa' ); ?></option>
	    		<option <?php echo 'author' == $instance['orderby'] ? 'selected' : ''; ?> value="author"><?php esc_html_e( 'Author', 'zoa' ); ?></option>
	    		<option <?php echo 'rand' == $instance['orderby'] ? 'selected' : ''; ?> value="rand"><?php esc_html_e( 'Random', 'zoa' ); ?></option>
	    	</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php esc_html_e( 'Order:', 'zoa' ); ?>
			</label>

	    	<select
	    		class="widefat"
	    		name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>"
	    		id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" >
	    		<option <?php echo 'DESC' == $instance['order'] ? 'selected' : ''; ?> value="DESC"><?php esc_html_e( 'DESC', 'zoa' ); ?></option>
	    		<option <?php echo 'ASC' == $instance['order'] ? 'selected' : ''; ?> value="ASC"><?php esc_html_e( 'ASC', 'zoa' ); ?></option>
	    	</select>
		</p>
		<?php
	}
}
