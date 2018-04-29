<?php

class MM_Business_Reviews extends WP_Widget {
	var $defaults = array(
		'mm-br-title' => 'Review Us',
		'mm-br-style' => 'stars',
	);
	public function __construct() {
		parent::__construct(
			'mm_br_widget',
			'Business Reviews',
			array( 'description' => __( 'Business reviews powered by Bluehost.', 'mm-br-widget' ) )
		);
	}
	public function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'mm-br-title' ); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'mm-br-title' ); ?>" name="<?php echo $this->get_field_name( 'mm-br-title' ); ?>" type="text" value="<?php echo esc_attr( $instance['mm-br-title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_name( 'mm-br-style' ); ?>">Style:</label>
			<select  class="widefat mm-br-wid-type" id="<?php echo $this->get_field_id( 'mm-br-style' ); ?>" name="<?php echo $this->get_field_name( 'mm-br-style' ); ?>">
				<option value='stars' <?php selected( $instance['mm-br-style'], 'stars', true ); ?>>Stars 1 - 5</option>
				<option value='thumbs' <?php selected( $instance['mm-br-style'], 'thumbs', true ); ?>>Thumbs Up / Down</option>
			</select>
		</p>
		<?php
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
		$query = array();
		$title = apply_filters( 'widget_title', $instance['mm-br-title'] );
		$destination = 'https://my.bluehost.com/reviews/' . mm_site_bin2hex();
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>
		<div class="mm-br-wrapper">
		<?php
		if ( 'stars' == $instance['mm-br-style'] ) {
			?>
			<div class="mm-br-stars">
				<a href="<?php echo add_query_arg( array( 'rating' => 1 ), $destination ); ?>">
					<span class="dashicons dashicons-star-empty"></span>
				</a>
				<a href="<?php echo add_query_arg( array( 'rating' => 2 ), $destination ); ?>">
					<span class="dashicons dashicons-star-empty"></span>
				</a>
				<a href="<?php echo add_query_arg( array( 'rating' => 3 ), $destination ); ?>">
					<span class="dashicons dashicons-star-empty"></span>
				</a>
				<a href="<?php echo add_query_arg( array( 'rating' => 4 ), $destination ); ?>">
					<span class="dashicons dashicons-star-empty"></span>
				</a>
				<a href="<?php echo add_query_arg( array( 'rating' => 5 ), $destination ); ?>">
					<span class="dashicons dashicons-star-empty"></span>
				</a>
			</div>
			<?php
		} else {
			?>
			<div class="mm-br-thumbs">
				<a href="<?php echo add_query_arg( array( 'rating' => 0 ), $destination ); ?>">
					<span class="dashicons dashicons-thumbs-down"></span>
				</a>
				<a href="<?php echo add_query_arg( array( 'rating' => 5 ), $destination ); ?>">
					<span class="dashicons dashicons-thumbs-up"></span>
				</a>
			</div>
			<?php
		}
		?>
		</div>
		<style type="text/css">
		#<?php echo $args['widget_id']; ?> .mm-br-wrapper {
			text-align: center;
		}
		#<?php echo $args['widget_id']; ?> .mm-br-wrapper .mm-br-thumbs a {
			text-decoration: none;
			display: inline-block;
			box-shadow: inset 0 0;
			width: 40%;
			padding: 5%;
			font-size: 48px;
		}
		#<?php echo $args['widget_id']; ?> .mm-br-wrapper .mm-br-stars a {
			text-decoration: none;
			display: inline-block;
			box-shadow: inset 0 0;
			width: 14%;
			padding: 3%;
			font-size: 48px;
		}
		#<?php echo $args['widget_id']; ?> .mm-br-wrapper .mm-br-thumbs a span{
			font-size: 48px;
			display: block;
		}
		#<?php echo $args['widget_id']; ?> .mm-br-wrapper .mm-br-stars a span{
			font-size: 32px;
			display: block;
		}
		</style>
		<?php
		wp_enqueue_style( 'dashicons' );
		echo $args['after_widget'];
	}
}

function mm_register_business_review_widget() {
	if ( false !== strpos( strtolower( mm_brand() ), 'bluehost' )  && mm_is_module_active( 'business-reviews' ) ) {
		register_widget( 'MM_Business_Reviews' );
	}
}
add_action( 'widgets_init', 'mm_register_business_review_widget' );
