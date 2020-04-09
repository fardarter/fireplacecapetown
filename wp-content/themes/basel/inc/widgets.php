<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'basel_widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */

include_once( 'widgets/class-widget-price-filter.php');
include_once( 'widgets/class-widget-layered-nav.php');

include_once( 'widgets/class-wp-nav-menu-widget.php');
include_once( 'widgets/class-widget-search.php');
include_once( 'widgets/class-widget-sorting.php');
include_once( 'widgets/class-user-panel-widget.php');
include_once( 'widgets/class-author-area-widget.php');
include_once( 'widgets/class-banner-widget.php');
include_once( 'widgets/class-instagram-widget.php');
include_once( 'widgets/class-static-block-widget.php');

if( ! function_exists( 'basel_widgets_init' ) ) {
	function basel_widgets_init() {
		if ( !is_blog_installed() )
			return;

		register_widget( 'BASEL_WP_Nav_Menu_Widget' );
		register_widget( 'BASEL_Banner_Widget' );
		register_widget( 'BASEL_Author_Area_Widget' );
		register_widget( 'BASEL_Instagram_Widget' );
		register_widget( 'BASEL_Static_Block_Widget' );

		if(	basel_woocommerce_installed() ) {
			register_widget( 'BASEL_User_Panel_Widget' );
			register_widget( 'BASEL_Widget_Layered_Nav' );
			register_widget( 'BASEL_Widget_Sorting' );
			register_widget( 'BASEL_Widget_Price_Filter' );
			register_widget( 'BASEL_Widget_Search' );
		}

	}

	add_action('widgets_init', 'basel_widgets_init');
}



