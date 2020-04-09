<?php
/**
 *
 * The framework's functions and definitions
 *
 */

/**
 * ------------------------------------------------------------------------------------------------
 * Define constants.
 * ------------------------------------------------------------------------------------------------
 */
define( 'BASEL_THEME_DIR', 		get_template_directory_uri() );
define( 'BASEL_THEMEROOT', 		get_template_directory() );
define( 'BASEL_IMAGES', 		BASEL_THEME_DIR . '/images' );
define( 'BASEL_SCRIPTS', 		BASEL_THEME_DIR . '/js' );
define( 'BASEL_STYLES', 		BASEL_THEME_DIR . '/css' );
define( 'BASEL_FRAMEWORK', 		BASEL_THEMEROOT . '/inc' );
define( 'BASEL_DUMMY', 			BASEL_THEME_DIR . '/inc/dummy-content' );
define( 'BASEL_CLASSES', 		BASEL_THEMEROOT . '/inc/classes' );
define( 'BASEL_CONFIGS', 		BASEL_THEMEROOT . '/inc/configs' );
define( 'BASEL_3D', 			BASEL_FRAMEWORK . '/third-party' );
define( 'BASEL_ASSETS', 		BASEL_THEME_DIR . '/inc/assets' );
define( 'BASEL_ASSETS_IMAGES', 	BASEL_ASSETS    . '/images' );
define( 'BASEL_API_URL', 		'https://xtemos.com/licenses/api/' );
define( 'BASEL_SLUG', 			'basel' );

/**
 * ------------------------------------------------------------------------------------------------
 * Load all CORE Classes and files
 * ------------------------------------------------------------------------------------------------
 */
require_once( apply_filters('basel_require', BASEL_FRAMEWORK . '/autoload.php') );

$basel_theme = new BASEL_Theme();

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue styles
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_enqueue_styles' ) ) {
	add_action( 'wp_enqueue_scripts', 'basel_enqueue_styles', 10000 );

	function basel_enqueue_styles() {
		$version = basel_get_theme_info( 'Version' );

		if( basel_get_opt( 'minified_css' ) ) {
			$main_css_url = get_template_directory_uri() . '/style.min.css';
		} else {
			$main_css_url = get_stylesheet_uri();
		}

		wp_dequeue_style( 'yith-wcwl-font-awesome' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css' );
		wp_dequeue_style( 'vc_pageable_owl-carousel-css-theme' );
		wp_enqueue_style( 'font-awesome-css', BASEL_STYLES . '/font-awesome.min.css', array(), $version );
		wp_enqueue_style( 'bootstrap', BASEL_STYLES . '/bootstrap.min.css', array(), $version );
		wp_enqueue_style( 'basel-style', $main_css_url, array( 'bootstrap' ), $version );
		wp_enqueue_style( 'js_composer_front', false, array(), $version );
		
		// load typekit fonts
		$typekit_id = basel_get_opt( 'typekit_id' );

		if ( $typekit_id ) {
			wp_enqueue_style( 'basel-typekit', 'https://use.typekit.net/' . esc_attr ( $typekit_id ) . '.css', array(), $version );
		}

		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue scripts
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'basel_enqueue_scripts', 10000 );

	function basel_enqueue_scripts() {
		
		$version = basel_get_theme_info( 'Version' );
		
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply', false, array(), $version );
		
		wp_register_script( 'maplace', get_template_directory_uri() . '/js/maplace-0.1.3.min.js', array('jquery', 'google.map.api'), $version, true );
		
		if( ! basel_woocommerce_installed() )
			wp_register_script( 'jquery-cookie', get_template_directory_uri() . '/js/jquery.cookie.js', array('jquery'), $version, true );

		wp_enqueue_script( 'basel_html5shiv', get_template_directory_uri() . '/js/html5.js', array(), $version );
		wp_script_add_data( 'basel_html5shiv', 'conditional', 'lt IE 9' );

		wp_dequeue_script( 'flexslider' );
		wp_dequeue_script( 'photoswipe-ui-default' );
		wp_dequeue_script( 'prettyPhoto-init' );
		wp_dequeue_style( 'photoswipe-default-skin' );

		if( basel_get_opt( 'image_action' ) != 'zoom' ) {
			wp_dequeue_script( 'zoom' );
		}

		wp_enqueue_script( 'isotope', get_template_directory_uri() . '/js/isotope.pkgd.min.js', array( 'jquery' ), $version, true );
		wp_enqueue_script( 'waypoints' );
		wp_enqueue_script( 'wpb_composer_front_js' );

		if( basel_get_opt( 'minified_js' ) ) {
			wp_enqueue_script( 'basel-theme', get_template_directory_uri() . '/js/theme.min.js', array( 'jquery', 'jquery-cookie' ), $version, true );
		} else {
			wp_enqueue_script( 'basel-libraries', get_template_directory_uri() . '/js/libraries.js', array( 'jquery', 'jquery-cookie' ), $version, true );
			wp_enqueue_script( 'basel-functions', get_template_directory_uri() . '/js/functions.js', array( 'jquery', 'jquery-cookie' ), $version, true );
		}

		// Add virations form scripts through the site to make it work on quick view
		if( basel_get_opt( 'quick_view_variable' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation', false, array(), $version );
		}


		$translations = array(
			'adding_to_cart' => esc_html__('Processing', 'basel'),
			'added_to_cart' => esc_html__('Product was successfully added to your cart.', 'basel'),
			'continue_shopping' => esc_html__('Continue shopping', 'basel'),
			'view_cart' => esc_html__('View Cart', 'basel'),
			'go_to_checkout' => esc_html__('Checkout', 'basel'),
			'countdown_days' => esc_html__('days', 'basel'),
			'countdown_hours' => esc_html__('hr', 'basel'),
			'countdown_mins' => esc_html__('min', 'basel'),
			'countdown_sec' => esc_html__('sc', 'basel'),
			'loading' => esc_html__('Loading...', 'basel'),
			'close' => esc_html__('Close (Esc)', 'basel'),
			'share_fb' => esc_html__('Share on Facebook', 'basel'),
			'pin_it' => esc_html__('Pin it', 'basel'),
			'tweet' => esc_html__('Tweet', 'basel'),
			'download_image' => esc_html__('Download image', 'basel'),
			'wishlist' => ( class_exists( 'YITH_WCWL' ) ) ? 'yes' : 'no',
			'cart_url' => ( basel_woocommerce_installed() ) ?  esc_url( wc_get_cart_url() ) : '',
			'ajaxurl' => admin_url('admin-ajax.php'),
			'add_to_cart_action' => ( basel_get_opt( 'add_to_cart_action' ) ) ? esc_js( basel_get_opt( 'add_to_cart_action' ) ) : 'widget',
			'categories_toggle' => ( basel_get_opt( 'categories_toggle' ) ) ? 'yes' : 'no',
			'enable_popup' => ( basel_get_opt( 'promo_popup' ) ) ? 'yes' : 'no',
			'popup_delay' => ( basel_get_opt( 'promo_timeout' ) ) ? (int) basel_get_opt( 'promo_timeout' ) : 1000,
			'popup_event' => basel_get_opt( 'popup_event' ),
			'popup_scroll' => ( basel_get_opt( 'popup_scroll' ) ) ? (int) basel_get_opt( 'popup_scroll' ) : 1000,
			'popup_pages' => ( basel_get_opt( 'popup_pages' ) ) ? (int) basel_get_opt( 'popup_pages' ) : 0,
			'promo_popup_hide_mobile' => ( basel_get_opt( 'promo_popup_hide_mobile' ) ) ? 'yes' : 'no',
			'product_images_captions' => ( basel_get_opt( 'product_images_captions' ) ) ? 'yes' : 'no',
			'all_results' => __('View all results', 'basel'),
			'product_gallery' => basel_get_product_gallery_settings(),
			'zoom_enable' => ( basel_get_opt( 'image_action' ) == 'zoom') ? 'yes' : 'no',
			'ajax_scroll' => ( basel_get_opt( 'ajax_scroll' ) ) ? 'yes' : 'no',
			'ajax_scroll_class' => apply_filters( 'basel_ajax_scroll_class' , '.main-page-wrapper' ),
			'ajax_scroll_offset' => apply_filters( 'basel_ajax_scroll_offset' , 100 ),
			'product_slider_auto_height' => ( basel_get_opt( 'product_slider_auto_height' ) ) ? 'yes' : 'no',
			'product_slider_autoplay' => apply_filters( 'basel_product_slider_autoplay' , false ),
			'ajax_add_to_cart' => ( apply_filters( 'basel_ajax_add_to_cart', true ) ) ? basel_get_opt( 'single_ajax_add_to_cart' ) : false,
			'cookies_version' => ( basel_get_opt( 'cookies_version' ) ) ? (int)basel_get_opt( 'cookies_version' ) : 1,
			'header_banner_version' => ( basel_get_opt( 'header_banner_version' ) ) ? (int)basel_get_opt( 'header_banner_version' ) : 1,
			'header_banner_close_btn' => basel_get_opt( 'header_close_btn' ),
			'header_banner_enabled' => basel_get_opt( 'header_banner' ),
			'promo_version' => ( basel_get_opt( 'promo_version' ) ) ? (int)basel_get_opt( 'promo_version' ) : 1,
			'pjax_timeout' => apply_filters( 'basel_pjax_timeout' , 5000 ),
			'split_nav_fix' => apply_filters( 'basel_split_nav_fix' , false ),
		);

		wp_localize_script( 'basel-functions', 'basel_settings', $translations );
		wp_localize_script( 'basel-theme', 'basel_settings', $translations );
		
		if( ( is_home() || is_singular( 'post' ) || is_archive() ) && basel_get_opt('blog_design') == 'masonry' ) {
			// Load masonry script JS for blog
			wp_enqueue_script( 'masonry', false, array(), $version );
		}

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Enqueue google fonts
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_enqueue_google_fonts' ) ) {
	add_action( 'wp_enqueue_scripts', 'basel_enqueue_google_fonts', 10000 );

	function basel_enqueue_google_fonts() {
		$default_google_fonts = 'Karla:400,400italic,700,700italic|Lora:400,400italic,700,700italic';

		if( ! class_exists('Redux') )
   			wp_enqueue_style( 'basel-google-fonts', basel_get_fonts_url( $default_google_fonts ), array(), basel_get_theme_info( 'Version' ) );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get google fonts URL
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_get_fonts_url') ) {
	function basel_get_fonts_url( $fonts ) {
	    $font_url = '';

        $font_url = add_query_arg( 'family', urlencode( $fonts ), "//fonts.googleapis.com/css" );

	    return $font_url;
	}
}
