<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * AJAX search widget
 *
 */

if( ! class_exists( 'BASEL_Widget_Search' ) ) {
	class BASEL_Widget_Search extends WPH_Widget {

		function __construct() {
			if( ! basel_woocommerce_installed() ) return;

			// Configure widget array
			$args = array( 
				// Widget Backend label
				'label' => __( 'BASEL AJAX Search', 'basel' ),
				// Widget Backend Description								
				'description' =>__( 'Search form by products with AJAX', 'basel' ),
			 );
		
			// Configure the widget fields
		
			// fields array
			$args['fields'] = array(
				array(
					'id'	=> 'title',
					'type'  => 'text',
					'std'   => __( 'Search products', 'basel' ),
					'name' 	=> __( 'Title', 'basel' )
				),
				array(
					'id'	=> 'number',
					'type'  => 'number',
					'std'   => 4,
					'name' 	=> __( 'Number of products to show', 'basel' ),
				),
				array(
					'id'	=> 'price',
					'type'  => 'checkbox',
					'std'   => 1,
					'name' 	=> __( 'Show price', 'basel' ),
				),
				array(
					'id'	=> 'thumbnail',
					'type'  => 'checkbox',
					'std'   => 1,
					'name' 	=> __( 'Show thumbnail', 'basel' ),
				),
			);

			// create widget
			$this->create_widget( $args );
		}
		

		function widget( $args, $instance )	{

			echo $args['before_widget'];

			$number = empty( $instance['number'] ) ? 3 : absint( $instance['number'] );
			$thumbnail = empty( $instance['thumbnail'] ) ? 0 : absint( $instance['thumbnail'] );
			$price = empty( $instance['price'] ) ? 0 : absint( $instance['price'] );

			if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			wc_get_template( 'searchform-ajax.php', array(
				'ajax' => true,
				'categories' => false,
				'ajax_args' => array(
					'count' => $number,
					'thumbnail' => $thumbnail,
					'price' => $price,
				)
			) );

			echo $args['after_widget'];
		}

		function form( $instance ) {
			parent::form( $instance );
		}
	}
}

