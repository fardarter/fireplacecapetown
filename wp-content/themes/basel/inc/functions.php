<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Body classes
// **********************************************************************// 

if( ! function_exists( 'basel_body_class' ) ) {
	function basel_body_class( $classes ) {

		$page_id = basel_page_ID();

		$site_width = basel_get_opt( 'site_width' );
		$cart_design = basel_get_opt( 'shopping_cart' );
		$wishlist = basel_get_opt( 'header_wishlist' );
		$header = basel_get_opt( 'header' );
		$header_overlap_opt = basel_get_opt( 'header-overlap' );
		$product_design = basel_product_design();
		$top_bar = basel_get_opt( 'top-bar' );
		$ajax_shop = basel_get_opt( 'ajax_shop' );
		$header_search = basel_get_opt( 'header_search' );
		$ajax_search = basel_get_opt( 'search_ajax' );
		$catalog_mode = basel_get_opt( 'catalog_mode' );
		$categories_toggle = basel_get_opt( 'categories_toggle' );
		$header = basel_get_opt( 'header' );
		$sticky_footer = basel_get_opt( 'sticky_footer' );
		$dark = basel_get_opt( 'dark_version' );
		$mobile_menu_position = basel_get_opt( 'mobile_menu_position' );
		
		$hide_sidebar_mobile = basel_get_opt( 'shop_hide_sidebar_mobile' );
		$hide_sidebar_tablet = basel_get_opt( 'shop_hide_sidebar_tablet' );
		$hide_sidebar_desktop = basel_get_opt( 'shop_hide_sidebar_desktop' );

		$header_overlap = $header_sticky = $disable_sticky = false;

		$disable = get_post_meta( $page_id, '_basel_title_off', true );

		$classes[] = 'wrapper-' . $site_width;
		$classes[] = 'global-cart-design-' . $cart_design;
		$classes[] = 'global-search-' . $header_search;
		$classes[] = 'global-header-' . $header;
		$classes[] = 'mobile-nav-from-' . $mobile_menu_position;

		if( is_singular( 'product') ) 
			$classes[] = 'basel-product-design-' . $product_design;
		
		if ( ( is_shop() || is_product_category() ) && ( $hide_sidebar_desktop && $sticky_footer ) ) {
			$classes[] = 'no-sticky-footer';
		}elseif( $sticky_footer ){
			$classes[] = 'sticky-footer-on';
		}
		
		$classes[] = ( $dark ) ? 'basel-dark' : 'basel-light';

		if( $catalog_mode ) {
			$classes[] = 'catalog-mode-on';
		} else {
			$classes[] = 'catalog-mode-off';
		}

		if( $categories_toggle ) {
			$classes[] = 'categories-accordion-on';
		} else {
			$classes[] = 'categories-accordion-off';
		}

		if( $wishlist ) {
			$classes[] = 'global-wishlist-enable';
		} else {
			$classes[] = 'global-wishlist-disable';
		}

		if( $top_bar ) {
			$classes[] = 'basel-top-bar-on';
		} else {
			$classes[] = 'basel-top-bar-off';
		}

		if( $ajax_shop ) {
			$classes[] = 'basel-ajax-shop-on';
		} else {
			$classes[] = 'basel-ajax-shop-off';
		}

		if( $ajax_search ) {
			$classes[] = 'basel-ajax-search-on';
		} else {
			$classes[] = 'basel-ajax-search-off';
		}
		
		//Header banner
		if ( !basel_get_opt( 'header_close_btn' ) && basel_get_opt( 'header_banner' ) ) {
			$classes[] = 'header-banner-display';
		}
		if ( basel_get_opt( 'header_banner' ) ) {
			$classes[] = 'header-banner-enabled';
		}

		// Sticky header settings
		if( basel_get_opt('sticky_header') ) {
			$classes[] = 'enable-sticky-header';
			$header_sticky = true;
		} else {
			$disable_sticky = true;
			$classes[] = 'disable-sticky-header';
		}

		// Force header full width class
		if(  is_singular( 'product') && basel_get_opt('force_header_full_width') && basel_product_design() == 'sticky' ) {
			$classes[] = 'header-full-width';
		}

		if( basel_get_opt('header_full_width') ) {
			$classes[] = 'header-full-width';
		}

		if( in_array( $header, array('menu-top') ) ) {
			$header_sticky = 'real';
			$classes[] = 'sticky-navigation-only';
		} else if( in_array( $header, array('base', 'simple', 'logo-center', 'categories') ) ) {
			$header_sticky = 'clone';
		}

		// Header overlaps content in the following cases:
		// 1. Header type is overlap
		// 2. Not on the single product page
		// 3. Not shop page and not disabled page title
		/*if( $header == 'overlap' 
			&& ! is_singular( 'product' )
			&& ! ( basel_woocommerce_installed() 
					&& ( is_shop() || is_product_category() || is_product_tag() || is_singular( "product" ) ) 
					&& $disable
				)
		) {
			$header_overlap = true;
			$header_sticky = 'real';
		} */

		// If header type is SHOP and overlap option is enabled
		if( $header == 'shop' || $header == 'split' ) {
			$header_sticky = 'real';
			if( $header_overlap_opt ) {
				$header_overlap = true;
			}
		}

		if( $header == 'simple' && $header_overlap_opt ) {
			$header_overlap = true;
			$header_sticky = 'real';
		}

		/*if( $header == 'simple' && $header_sticky == 'real' && ! $header_overlap ) {
			$classes[] = 'basel-header-smooth';
		}*/

		if( $header_overlap ) {
			$classes[] = 'basel-header-overlap';
		}

		if( $header_sticky == 'clone' && ! $disable_sticky ) {
			$classes[] = 'sticky-header-clone';
		} elseif( $header_sticky && ! $disable_sticky ) {
			$classes[] = 'sticky-header-real';
		}
		
		//Off canvas sidebar
		if( $hide_sidebar_mobile ) {
			$classes[] = 'offcanvas-sidebar-mobile';
		}

		if( $hide_sidebar_tablet ) {
			$classes[] = 'offcanvas-sidebar-tablet';
		}

		if( $hide_sidebar_desktop ) {
			$classes[] = 'offcanvas-sidebar-desktop';
		}

		return $classes;
	}

	add_filter('body_class', 'basel_body_class');
}


/**
 * ------------------------------------------------------------------------------------------------
 * Filter wp_title
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_wp_title' ) ) {
	function basel_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

		return $title;
	}
	add_filter( 'wp_title', 'basel_wp_title', 10, 2 );

}

/**
 * ------------------------------------------------------------------------------------------------
 * Remove admin bar
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_remove_admin_bar' )) {
    function basel_remove_admin_bar() {	
		if( basel_get_opt( 'admin_bar' ) )
			add_filter('show_admin_bar', '__return_false');
    } 

    add_action( 'wp', 'basel_remove_admin_bar', 10 );
}



/**
 * ------------------------------------------------------------------------------------------------
 * Get predefined footer configuration by index
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_footer_config' ) ) {
	function basel_get_footer_config( $index ) {

		if( $index > 20 || $index < 1) {
			$index = 1;
		}

		$configs = apply_filters( 'basel_footer_configs_array', array(
			1 => array(
				'cols' => array(
					'col-sm-12'
				),
				
			),
			2 => array(
				'cols' => array(
					'col-sm-6',
					'col-sm-6',
				),
			),
			3 => array(
				'cols' => array(
					'col-sm-4',
					'col-sm-4',
					'col-sm-4',
				),
			),
			4 => array(
				'cols' => array(
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					2 => 'sm'
				)
			),
			5 => array(
				'cols' => array(
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
				),
				'clears' => array(
					3 => 'sm'
				)
			),
			6 => array(
				'cols' => array(
					'col-md-3 col-sm-4',
					'col-md-6 col-sm-4',
					'col-md-3 col-sm-4',
				),
			),
			7 => array(
				'cols' => array(
					'col-md-6 col-sm-4',
					'col-md-3 col-sm-4',
					'col-md-3 col-sm-4',
				),
			),
			8 => array(
				'cols' => array(
					'col-md-3 col-sm-4',
					'col-md-3 col-sm-4',
					'col-md-6 col-sm-4',
				),
			),
			9 => array(
				'cols' => array(
					'col-md-12 col-sm-12',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					1 => 'md',
					1 => 'lg',
					3 => 'sm',
				),
			),
			10 => array(
				'cols' => array(
					'col-md-6 col-sm-12',
					'col-md-6 col-sm-12',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					2 => 'md',
					2 => 'lg',
					4 => 'sm',
				),
			),
			11 => array(
				'cols' => array(
					'col-md-6 col-sm-12',
					'col-md-6 col-sm-12',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-4 col-sm-12',
				),
				'clears' => array(
					2 => 'md',
					2 => 'lg',
					4 => 'sm',
				),
			),
			12 => array(
				'cols' => array(
					'col-md-12 col-sm-12',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-4 col-sm-12',
				),
				'clears' => array(
					1 => 'md',
					1 => 'lg',
					3 => 'sm',
				),
			),
		) );

		return (isset( $configs[$index] )) ? $configs[$index] : array();
	}
}


// **********************************************************************// 
// ! Theme 3d plugins
// **********************************************************************// 


if(!defined('YITH_REFER_ID')) {
    define('YITH_REFER_ID', '1040314');
}


if( ! function_exists( 'basel_3d_plugins' )) {
    function basel_3d_plugins() {
        if( function_exists( 'set_revslider_as_theme' ) ){
            set_revslider_as_theme();
        }
    } 

    add_action( 'init', 'basel_3d_plugins' );
}

if( ! function_exists( 'basel_vcSetAsTheme' ) ) {

    function basel_vcSetAsTheme() {
        if( function_exists( 'vc_set_as_theme' ) ){
            vc_set_as_theme();
        }
    } 

    add_action( 'vc_before_init', 'basel_vcSetAsTheme' );
}


// **********************************************************************// 
// ! Function to get taxonomy meta data
// **********************************************************************// 

if( ! function_exists( 'basel_tax_data' ) ) {
	function basel_tax_data($taxonomy, $term_id, $meta_key) {
		$data = '';

		if( basel_new_meta() ) {
			return get_term_meta( $term_id, $meta_key, true);
		}
		
		if( class_exists('Taxonomy_MetaData') )
			$data = Taxonomy_MetaData::get( $taxonomy, $term_id, $meta_key );

		return $data;
	}
}

// **********************************************************************// 
// ! Obtain real page ID (shop page, blog, portfolio or simple page)
// **********************************************************************// 

/**
 * This function is called once when initializing BASEL_Layout object
 * then you can use function basel_page_ID to get current page id
 */
if( ! function_exists( 'basel_get_the_ID' ) ) {
	function basel_get_the_ID( $settings = array() ) {
		global $post;

		$page_id = 0;

		$page_for_posts    = get_option( 'page_for_posts' );
		$page_for_shop     = get_option( 'woocommerce_shop_page_id' );
		$page_for_projects = basel_tpl2id( 'portfolio.php' );
		
		if(isset($post->ID)) $page_id = $post->ID;

		if( isset($post->ID) && ( is_singular( 'page' ) || is_singular( 'post' ) ) ) { 
			$page_id = $post->ID;
		} else if( is_home() || is_singular( 'post' ) || is_search() || is_tag() || is_category() || is_date() || is_author() ) {
			$page_id = $page_for_posts;
		} else if( is_archive('portfolio') && get_post_type() == 'portfolio' ) {
			$page_id = $page_for_projects;
		}

		if( basel_woocommerce_installed() && function_exists( 'is_shop' )  ) {
			if( isset( $settings['singulars'] ) && in_array( 'product', $settings['singulars']) && is_singular( "product" ) ) {
				// keep post id
			} else if( is_shop() || is_product_category() || is_product_tag() || is_singular( "product" ) || basel_is_product_attribute_archieve() )
				$page_id = $page_for_shop;
		}

		return $page_id;
	}
}


// **********************************************************************// 
// ! Function to get HTML block content
// **********************************************************************// 

if( ! function_exists( 'basel_get_html_block' ) ) {
	function basel_get_html_block($id) {
		$content = get_post_field('post_content', $id);

		$content = do_shortcode($content);


		$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$content .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			$content .= $shortcodes_custom_css;
			$content .= '</style>';
		}

		return $content;
	}

}

if( ! function_exists( 'basel_get_static_blocks_array' ) ) {
	function basel_get_static_blocks_array() {
		$args = array( 'posts_per_page' => 200, 'post_type' => 'cms_block' );
		$blocks_posts = get_posts( $args );
		$array = array();
		foreach ( $blocks_posts as $post ) : 
			setup_postdata( $post ); 
			$array[$post->post_title] = $post->ID; 
		endforeach;
		wp_reset_postdata();
		return $array;
	}
}


// **********************************************************************// 
// ! Support shortcodes in text widget
// **********************************************************************// 

add_filter('widget_text', 'do_shortcode');



// **********************************************************************// 
// ! Set excerpt length and more btn
// **********************************************************************// 

add_filter( 'excerpt_length', 'basel_excerpt_length', 999 );

if( ! function_exists( 'basel_excerpt_length' ) ) {
	function basel_excerpt_length( $length ) {
		return 20;
	}
}

add_filter('excerpt_more', 'basel_new_excerpt_more');

if( ! function_exists( 'basel_new_excerpt_more' ) ) {
	function basel_new_excerpt_more( $more ) {
		return '';
	}
}

// **********************************************************************// 
// ! Add scroll to top buttom 
// **********************************************************************// 

add_action( 'wp_footer', 'basel_scroll_top_btn' );

if( ! function_exists( 'basel_scroll_top_btn' ) ) {
	function basel_scroll_top_btn( $more ) {
		if( !basel_get_opt( 'scroll_top_btn' ) ) return;
		?>
			<a href="#" class="scrollToTop basel-tooltip"><?php esc_attr_e( 'Scroll To Top', 'basel' ); ?></a>
		<?php
	}
}


// **********************************************************************// 
// ! Return related posts args array
// **********************************************************************// 

if( ! function_exists( 'basel_get_related_posts_args' ) ) {
	function basel_get_related_posts_args( $post_id ) {
	    $taxs = wp_get_post_tags( $post_id );
	    $args = array();
	    if ( $taxs ) {
	        $tax_ids = array();
	        foreach( $taxs as $individual_tax ) $tax_ids[] = $individual_tax->term_id;
	         
	        $args = array(
	            'tag__in'               => $tax_ids,
	            'post__not_in'          => array( $post_id ),
	            'posts_per_page'        => 12,
	            'ignore_sticky_posts'   => 1
	        );  
	        
	    }

	    return $args;
	}
}





// **********************************************************************// 
// ! Navigation walker
// **********************************************************************// 

if( ! class_exists( 'BASEL_Mega_Menu_Walker' )) {
	class BASEL_Mega_Menu_Walker extends Walker_Nav_Menu {

		private $color_scheme = 'dark';

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);

			if( $depth == 0) {
				$output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
				$output .= "\n$indent<div class=\"container\">\n";

			}
			if( $depth < 1 ) {
				$sub_menu_class = "sub-menu";
			} else {
				$sub_menu_class = "sub-sub-menu";
			}
			
			$output .= "\n$indent<ul class=\"$sub_menu_class color-scheme-" . $this->color_scheme . "\">\n";

			if( $this->color_scheme == 'light') $this->color_scheme = 'dark';
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see Walker::end_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
			if( $depth == 0) {
				$output .= "$indent</div>\n";
				$output .= "$indent</div>\n";
			}
		}

		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$design   = $width = $height = $icon = $label = $label_out = '';
			$design   = get_post_meta( $item->ID, '_menu_item_design',   true );
			$width    = get_post_meta( $item->ID, '_menu_item_width',    true );
			$height   = get_post_meta( $item->ID, '_menu_item_height',   true );
			$icon     = get_post_meta( $item->ID, '_menu_item_icon',     true );
			$event    = get_post_meta( $item->ID, '_menu_item_event',    true );
			$label    = get_post_meta( $item->ID, '_menu_item_label',    true );
			$opanchor = get_post_meta( $item->ID, '_menu_item_opanchor', true );
			$callbtn  = get_post_meta( $item->ID, '_menu_item_callbtn', true );
			$color_scheme = get_post_meta( $item->ID, '_menu_item_colorscheme', true );

			if( $color_scheme == 'light' ) $this->color_scheme = 'light';

			if( empty($design) ) $design = 'default';
			$classes[] = 'menu-item-design-' . $design;

			$event = (empty($event)) ? 'hover' : $event;
			$classes[] = 'item-event-' . $event;

			if( $opanchor == 'enable' ) {
				 $classes[] = 'onepage-link';
				if(($key = array_search('current-menu-item', $classes)) !== false) {
					unset($classes[$key]);
				}
			}

			if( $callbtn == 'enable' ) {
				$classes[] = 'callto-btn';
			}

			if( !empty( $label ) ) {
				$classes[] = 'item-with-label';
				$classes[] = 'item-label-' . $label;
				$label_text = '';
				switch ( $label ) {
					case 'hot':
						$label_text = esc_html__('Hot', 'basel');
					break;
					case 'sale':
						$label_text = esc_html__('Sale', 'basel');
					break;
					case 'new':
						$label_text = esc_html__('New', 'basel');
					break;
				}
				$label_out = '<span class="menu-label menu-label-' . $label . '">' . esc_attr( $label_text ) . '</span>';
			}

			if( ! empty( $item->description ) ) {
				$classes[] = 'menu-item-has-children';
			}

			/**
			 * Filter the CSS class(es) applied to a menu item's list item element.
			 *
			 * @since 3.0.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's list item element.
			 *
			 * @since 3.0.1
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			/**
			 * Filter the HTML attributes applied to a menu item's anchor element.
			 *
			 * @since 3.6.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array $atts {
			 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
			 *
			 *     @type string $title  Title attribute.
			 *     @type string $target Target attribute.
			 *     @type string $rel    The rel attribute.
			 *     @type string $href   The href attribute.
			 * }
			 * @param object $item  The current menu item.
			 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth Depth of menu item. Used for padding.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			if($icon != '') {
				$item_output .= '<i class="fa fa-' . $icon . '"></i>';
			}
			/** This filter is documented in wp-includes/post-template.php */
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= $label_out;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$styles = '';

			if( $depth == 0) {
				/**
				 * Add background image to dropdown
				 **/


				if( has_post_thumbnail( $item->ID ) ) {
					$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'full' );

					//ar($post_thumbnail);

					$styles .= '.menu-item-' . $item->ID . ' > .sub-menu-dropdown {';
						$styles .= 'background-image: url(' . $post_thumbnail[0] .'); ';
					$styles .= '}';
				}

				if( ! empty( $item->description ) && !in_array("menu-item-has-children", $item->classes) ) {
					$item_output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
					$item_output .= "\n$indent<div class=\"container\">\n";
						$item_output .= do_shortcode( $item->description );
					$item_output .= "\n$indent</div>\n";
					$item_output .= "\n$indent</div>\n";

					if( $this->color_scheme == 'light') $this->color_scheme = 'dark';
				}
			}

			if($design == 'sized' && !empty($height) && !empty($width)) {
				$styles .= '.menu-item-' . $item->ID . ' > .sub-menu-dropdown {';
					$styles .= 'min-height: ' . $height .'px; ';
					$styles .= 'width: ' . $width .'px; ';
				$styles .= '}';
			}


			if( $styles != '' ) {
				$item_output .= '<style type="text/css">';
				$item_output .= $styles;
				$item_output .= '</style>';
			}

			/**
			 * Filter a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 * @since 3.0.0
			 *
			 * @param string $item_output The menu item's starting HTML output.
			 * @param object $item        Menu item data object.
			 * @param int    $depth       Depth of menu item. Used for padding.
			 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}



// **********************************************************************// 
// ! // Deletes first gallery shortcode and returns content (http://stackoverflow.com/questions/17224100/wordpress-remove-shortcode-and-save-for-use-elsewhere)
// **********************************************************************// 

if( ! function_exists( 'basel_strip_shortcode_gallery' ) ) {
	function  basel_strip_shortcode_gallery( $content ) {
	    preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER );
	    if ( ! empty( $matches ) ) {
	        foreach ( $matches as $shortcode ) {
	            if ( 'gallery' === $shortcode[2] ) {
	                $pos = strpos( $content, $shortcode[0] );
	                if ($pos !== false)
	                    return substr_replace( $content, '', $pos, strlen($shortcode[0]) );
	            }
	        }
	    }
	    return $content;
	}
}


// **********************************************************************// 
// ! Get exceprt from post content
// **********************************************************************// 

if( ! function_exists( 'basel_excerpt_from_content' ) ) {
	function basel_excerpt_from_content($post_content, $limit, $shortcodes = '') {
        // Strip shortcodes and HTML tags
        if ( empty( $shortcodes )) {
	        $post_content = preg_replace("/\[caption(.*)\[\/caption\]/i", '', $post_content);
            $post_content = preg_replace('`\[[^\]]*\]`','',$post_content);
        }

        $post_content = stripslashes(wp_filter_nohtml_kses($post_content));

        $excerpt = explode(' ', $post_content, $limit);

        if ( count( $excerpt) >= $limit ) {
            array_pop( $excerpt );
            $excerpt = implode( " ", $excerpt ) . '...';
        } else {
            $excerpt = implode( " ", $excerpt );
        }

        $excerpt = strip_tags( $excerpt );

        if (trim($excerpt) == '...') {
            return '';
        }

        return $excerpt;
    }
}

// **********************************************************************// 
// ! Get portfolio taxonomies dropdown
// **********************************************************************// 

if( ! function_exists( 'basel_get_projects_cats_array') ) {
	function basel_get_projects_cats_array() {
		$return = array('All' => '');

		if( ! post_type_exists( 'portfolio' ) ) return array();

		$cats = get_terms( 'project-cat' );

		foreach ($cats as $key => $cat) {
			$return[$cat->name] = $cat->term_id;
		}

		return $return;
	}
}

// **********************************************************************// 
// ! Get menus dropdown
// **********************************************************************// 

if( ! function_exists( 'basel_get_menus_array') ) {
	function basel_get_menus_array() {
		$basel_menus = wp_get_nav_menus();
		$basel_menu_dropdown = array();
		
		foreach ( $basel_menus as $menu ) {

			$basel_menu_dropdown[$menu->term_id] = $menu->name;
			
		}

		return $basel_menu_dropdown;
	}
}


// **********************************************************************// 
// ! Get registered sidebars dropdown
// **********************************************************************// 

if(!function_exists('basel_get_sidebars_array')) {
    function basel_get_sidebars_array() {
        global $wp_registered_sidebars;
        $sidebars['none'] = 'none';
        foreach( $wp_registered_sidebars as $id=>$sidebar ) {
            $sidebars[ $id ] = $sidebar[ 'name' ];
        }
        return $sidebars;
    }
}


// **********************************************************************// 
// ! If page needs header
// **********************************************************************// 

if( ! function_exists( 'basel_needs_header' ) ) {
	function basel_needs_header() {
		return ( ! basel_maintenance_page() );
	}
}

// **********************************************************************// 
// ! If page needs footer
// **********************************************************************// 

if( ! function_exists( 'basel_needs_footer' ) ) {
	function basel_needs_footer() {
		return ( ! basel_maintenance_page() );
	}
}

// **********************************************************************// 
// ! Is maintenance page
// **********************************************************************// 

if( ! function_exists( 'basel_maintenance_page' ) ) {
	function basel_maintenance_page() {
		
        $pages_ids = basel_pages_ids_from_template( 'maintenance' );

        if( ! empty( $pages_ids ) && is_page( $pages_ids ) ) {
        	return true;
        }

		return false;
	}
}


// **********************************************************************// 
// ! Get page id by template name
// **********************************************************************// 

if( ! function_exists( 'basel_pages_ids_from_template' ) ) {
	function basel_pages_ids_from_template( $name ) {
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => $name . '.php'
		));

		$return = array();

		foreach($pages as $page){
		    $return[] = $page->ID;
		}

		return $return;
	}
}

// **********************************************************************// 
// ! Get content of the SVG icon located in images/svg folder
// **********************************************************************// 
if( ! function_exists( 'basel_get_svg_content' ) ) {
	function basel_get_svg_content($name) {
		$folder = BASEL_THEMEROOT . '/images/svg';
		$file = $folder . '/' . $name . '.svg';

		return (file_exists( $file )) ? basel_get_any_svg( $file ) : false;
	}
}

if( ! function_exists( 'basel_get_any_svg' ) ) {
	function basel_get_any_svg( $file, $id = false ) {
		$content = file_get_contents( $file );
		$start_tag = '<svg';
		if( $id ) {
			$pattern = "/id=\"(\w)+\"/";
			if( preg_match($pattern, $content) ) {
				$content = preg_replace($pattern, "id=\"" . $id . "\"", $content);
			} else {
				$content = preg_replace( "/<svg/", "<svg id=\"" . $id . "\"", $content);
			}
		}
		// Strip doctype
		$position = strpos($content, $start_tag);
		$content = substr($content, $position);
		return $content;
	}
}

// **********************************************************************// 
// ! Get config file
// **********************************************************************// 

if( ! function_exists( 'basel_get_config' ) ) {
	function basel_get_config( $name ) {
		// $allowed = array('selectors', 'versions', 'base-options', 'widgets-import', 'specific-options', 'product-hovers');
		$path = BASEL_CONFIGS . '/' . $name . '.php';
		if( file_exists( $path ) ) { // && in_array($name, $allowed) 
			return include $path;
		} else {
			return array();
		}
	}
}


// **********************************************************************// 
// ! Text to one-line string
// **********************************************************************// 

if( ! function_exists( 'basel_text2line')) {
	function basel_text2line( $str ) {
        return trim(preg_replace("/('|\"|\r?\n)/", '', $str)); 
	}
}


// **********************************************************************// 
// ! Get page ID by it's template name
// **********************************************************************// 
if( ! function_exists( 'basel_tpl2id' ) ) {
	function basel_tpl2id( $tpl = '' ) {
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => $tpl
		));
		foreach($pages as $page){
		    return $page->ID;
		}
	}
}

// **********************************************************************// 
// ! Function print array within a pre tags
// **********************************************************************// 
if( ! function_exists( 'ar' ) ) {
	function ar($array) {

		echo '<pre>';
			print_r($array);
		echo '</pre>';

	}
}


// **********************************************************************// 
// ! Get protocol (http or https)
// **********************************************************************// 
if( ! function_exists( 'basel_http' )) {
	function basel_http() {
		if( ! is_ssl() ) {
			return 'http';
		} else {
			return 'https';
		}
	}
}


// **********************************************************************// 
// ! It could be useful if you using nginx instead of apache 
// **********************************************************************// 

if (!function_exists('getallheaders')) { 
    function getallheaders() { 
		$headers = array(); 
		foreach ($_SERVER as $name => $value) { 
			if (substr($name, 0, 5) == 'HTTP_') { 
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
			} 
		} 
		return $headers; 
    } 
} 

// **********************************************************************// 
//  Function return vc_row with gradient.
// **********************************************************************// 
if( ! function_exists( 'basel_get_gradient_attr' ) ) {
	function basel_get_gradient_attr( $output, $obj, $attr ) {
		if ( ! empty( $attr['basel_gradient_switch'] ) ) {
			$gradient_css = basel_get_gradient_css( $attr['basel_color_gradient'] );
			$output = preg_replace_callback('/basel-row-gradient-enable.*?>/',
				function ( $matches ) use( $gradient_css ) {
				   return strtolower( $matches[0] . '<div class="basel-row-gradient" style="' . $gradient_css . '";></div>' );
				}, $output );
		}
		return $output;
	}
}
add_filter( 'vc_shortcode_output', 'basel_get_gradient_attr', 10, 3 );

// **********************************************************************// 
//  Function return gradient css.
// **********************************************************************// 
if( ! function_exists( 'basel_get_gradient_css' ) ) {
	function basel_get_gradient_css( $gradient_attr ) {
		$gradient_css = explode( '|', $gradient_attr );
		$result =  'background-image:-webkit-' . $gradient_css[1] . ';';
		$result .= 'background-image:-moz-' . $gradient_css[1] . ';';
		$result .= 'background-image:-o-' . $gradient_css[1] . ';';
		$result .= 'background-image:'.$gradient_css[1] . ';';
		$result .= 'background-image:-ms-' . $gradient_css[1] . ';';
		return $result;
	}
}
// **********************************************************************// 
// ! Append :hover to CSS selectors array
// **********************************************************************// 
if( ! function_exists( 'basel_append_hover_state' ) ) {
	function basel_append_hover_state( $selectors , $focus = false ) {
		// if( ! is_array( $selectors ) ) {
			$selectors = explode(',', $selectors[0]);
		// }

		$return = array();

		foreach ($selectors as $selector) {
			$return[] = $selector . ':hover';
			( $focus ) ? $return[] .= $selector . ':focus' : false ;
		}

		return implode(',', $return);
	}
}
// **********************************************************************// 
// Include gradient file
// **********************************************************************// 
if( ! function_exists( 'basel_register_redux_gradient' ) ) {
	function basel_register_redux_gradient( $field ) {
		return get_template_directory() . 'inc/classes/Gradient.php';
	}
}
add_filter( 'redux/basel_options/field/class/basel_gradient', 'basel_register_redux_gradient' ); 


// **********************************************************************// 
// Get gradient field
// **********************************************************************// 
if( ! function_exists( 'basel_get_gradient_field' ) ) {
	function basel_get_gradient_field( $param_name, $value, $is_VC = false ) {
		$classes = $param_name;
		$classes .= ( $is_VC ) ? ' wpb_vc_param_value' : '';
		$uniqid = uniqid();
		$output = '<div class="basel-grad-wrap">';
			$output .= '<div class="basel-grad-line" id="basel-grad-line' . $uniqid . '"></div>';
			$output .= '<div class="basel-grad-preview" id="basel-grad-preview' . $uniqid . '"></div>';
			$output .= '<input id="basel-grad-val' . $uniqid . '" class="' . $classes . '" name="' . $param_name . '"  style="display:none"  value="'.$value.'"/>';
		$output .= '</div>';

		$gradient_data = explode( '|', $value );
		$gradient_points_data = $gradient_data[0];
		$gradient_type_data = ( isset( $gradient_data[2] ) ) ? $gradient_data[2] : '';
		$gradient_direction_data = ( isset( $gradient_data[3] ) ) ? $gradient_data[3] : '';

		//Point result
		$result_point_value = '';
		if ( ! empty( $gradient_points_data ) ) {
			$points_value = explode( '/', $gradient_points_data );
			array_pop( $points_value );
			foreach ( $points_value as $key => $points_values ) {
				$points_values = explode( '-', $points_values );
				$result_point_value .= '{color:"' . $points_values[0] . '",position:' . $points_values[1] . '},';
			}
		}else{
			$result_point_value = '{color:"rgb(60, 27, 59)",position:0},{color:"rgb(90, 55, 105)",position: 33},{color:"rgb(46, 76, 130)",position:66},{color:"rgb(29, 28, 44)",position:100}';
		}

		//Type result
		$result_type_value = ( ! empty( $gradient_type_data ) ) ? $gradient_type_data : 'linear' ;

		//Direction result
		$result_direction_value = ( ! empty( $gradient_direction_data ) ) ? $gradient_direction_data : 'left' ;

		
		$output .= "<script>
		jQuery( document ).ready( function() {
			var gradient_line = '#basel-grad-line" . $uniqid . "',
				gradient_preview = '#basel-grad-preview" . $uniqid . "',
				grad_val = '#basel-grad-val" . $uniqid . "';

			gradX(gradient_line, {
				targets: [gradient_preview],
				change: function( points, styles, type, direction ) {
				   for( i = 0; i < styles.length; ++i )  {  
				       jQuery( gradient_preview ).css( 'background-image', styles[i] );
						var points_value = '';
						jQuery( points ).each( function( index , value ){
							points_value +=  value[0] + '-' + value[1] + '/';
						})
						jQuery( grad_val ).attr( 'value', points_value + '|' + styles[i] + '|' + type + '|' + direction );
				   }
				 }, 
				type: \"" . $result_type_value . "\",
				direction: \"" .  $result_direction_value . "\",
				sliders: [" . $result_point_value . "]
			});
		})
		</script>";
		return $output;
	}
}

// **********************************************************************// 
// Basel get theme info
// **********************************************************************// 
if( ! function_exists( 'basel_get_theme_info' ) ) {
	function basel_get_theme_info( $parameter ) {
		$theme_info = wp_get_theme();
		if ( is_child_theme() ){
			$theme_info = wp_get_theme( $theme_info->parent()->template );
		} 
		return $theme_info->get( $parameter ); 
	}
}

// **********************************************************************// 
// Basel page CSS
// **********************************************************************// 
if ( !function_exists('basel_settings_css') ) { 
	function basel_settings_css() { 
        $logo_container_width = basel_get_opt( 'logo_width' );
        $logo_img_width = basel_get_opt( 'logo_img_width' );
		$right_column_width   = basel_get_opt( 'right_column_width' );

        $header = basel_get_opt( 'header' );
        $header_height = basel_get_opt( 'header_height' );
        $sticky_header_height = basel_get_opt( 'sticky_header_height' );
        $mobile_header_height = basel_get_opt( 'mobile_header_height' );

        $right_column_width_percents = $menu_width = (int) (100 - $logo_container_width) / 2;

        $widgets_scroll = basel_get_opt( 'widgets_scroll' );
        $widgets_height = basel_get_opt( 'widget_heights' );

        $custom_css 		= basel_get_opt( 'custom_css' );
        $css_desktop 		= basel_get_opt( 'css_desktop' );
        $css_tablet 		= basel_get_opt( 'css_tablet' );
        $css_wide_mobile 	= basel_get_opt( 'css_wide_mobile' );
        $css_mobile         = basel_get_opt( 'css_mobile' );
        $custom_js          = basel_get_opt( 'custom_js' );
        $js_ready 		    = basel_get_opt( 'js_ready' );
		
		//Topbar
		$topbar_height = basel_get_opt( 'top_bar_height' );
		$topbar_height_mobile = basel_get_opt( 'top_bar_mobile_height' );
		
		//Header banner
		$header_banner_height = basel_get_opt( 'header_banner_height' );
		$header_banner_height_mobile = basel_get_opt( 'header_banner_mobile_height' );
		
		//Shop popup
		$shop_popup_width = basel_get_opt( 'popup_width' );

        $custom_product_background = get_post_meta( get_the_ID(),  '_basel_product-background', true );
		
		ob_start();

		?>	
			/* Shop popup */
			
			.basel-promo-popup {
			   max-width: <?php echo esc_html( $shop_popup_width ); ?>px;
			}
	
            .site-logo {
                width: <?php echo esc_html( $logo_container_width ); ?>%;
            }    

            .site-logo img {
                max-width: <?php echo esc_html( $logo_img_width ); ?>px;
                max-height: <?php echo esc_html( $header_height ); ?>px;
            }    

            <?php if( $header == 'shop'  ): ?>
                .widgetarea-head,
                .main-nav {
                    width: <?php echo esc_html( $menu_width ); ?>%;
                }  

                .right-column {
                    width: <?php echo esc_html( $right_column_width_percents ); ?>%;
                }  

            <?php elseif( $header == 'logo-center' ): ?>
                .widgetarea-head {
                    width: <?php echo esc_html( $menu_width ); ?>%;
                }  

                .right-column {
                    width: <?php echo esc_html( $right_column_width_percents ); ?>%;
                }  

                .sticky-header .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  

            <?php elseif( $header == 'split' ): ?>
                .left-column,
                .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  
            <?php else: ?>
                .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  
            <?php endif; ?>

            <?php if( $widgets_scroll ): ?>
                .basel-woocommerce-layered-nav .basel-scroll {
                    max-height: <?php echo ($widgets_height); ?>px;
                }
            <?php endif; ?>

			/* header Banner */
			.header-banner {
				height: <?php echo esc_html( $header_banner_height ); ?>px;
			}
	
			.header-banner-display .website-wrapper {
				margin-top:<?php echo esc_html( $header_banner_height ); ?>px;
			}	

            /* Topbar height configs */

			.topbar-menu ul > li {
				line-height: <?php echo esc_html( $topbar_height ); ?>px;
			}
			
			.topbar-wrapp,
			.topbar-content:before {
				height: <?php echo esc_html( $topbar_height ); ?>px;
			}
			
			.sticky-header-prepared.basel-top-bar-on .header-shop, 
			.sticky-header-prepared.basel-top-bar-on .header-split,
			.enable-sticky-header.basel-header-overlap.basel-top-bar-on .main-header {
				top: <?php echo esc_html( $topbar_height ); ?>px;
			}

            /* Header height configs */

            /* Limit logo image height for according to header height */
            .site-logo img {
                max-height: <?php echo esc_html( $header_height ); ?>px;
            } 

            /* And for sticky header logo also */
            .act-scroll .site-logo img,
            .header-clone .site-logo img {
                max-height: <?php echo esc_html( $sticky_header_height ); ?>px;
            }   

            /* Set sticky headers height for cloned headers based on menu links line height */
            .header-clone .main-nav .menu > li > a {
                height: <?php echo esc_html( $sticky_header_height ); ?>px;
                line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
            } 

            /* Height for switch logos */

            .sticky-header-real:not(.global-header-menu-top) .switch-logo-enable .basel-logo {
                height: <?php echo esc_html( $header_height ); ?>px;
            }

            .sticky-header-real:not(.global-header-menu-top) .act-scroll .switch-logo-enable .basel-logo {
                height: <?php echo esc_html( $sticky_header_height ); ?>px;
            }

            .sticky-header-real:not(.global-header-menu-top) .act-scroll .switch-logo-enable {
                transform: translateY(-<?php echo esc_html( $sticky_header_height ); ?>px);
                -webkit-transform: translateY(-<?php echo esc_html( $sticky_header_height ); ?>px);
            }

            <?php if( $header == 'base' || $header == 'logo-center' || $header == 'split' ): ?>
                /* Header height for layouts that don't have line height for menu links */
                .wrapp-header {
                    min-height: <?php echo esc_html( $header_height ); ?>px;
                } 
            <?php elseif( $header != 'vertical' ): ?>
                /* Header height for these layouts based on it's menu links line height */
                .main-nav .menu > li > a {
                    height: <?php echo esc_html( $header_height ); ?>px;
                    line-height: <?php echo esc_html( $header_height ); ?>px;
                }  
                /* The same for sticky header */
                .act-scroll .main-nav .menu > li > a {
                    height: <?php echo esc_html( $sticky_header_height ); ?>px;
                    line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }  
            <?php endif; ?>

            <?php if( $header == 'split'  ): ?>
                /* Sticky header height for split header layout */
                .act-scroll .wrapp-header {
                    min-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }   
            <?php endif; ?>

            <?php if( $header == 'shop'  ): ?>
                /* Set line height for header links for shop header layout. Based in the header height option */
                .header-shop .right-column .header-links {
                    height: <?php echo esc_html( $header_height ); ?>px;
                    line-height: <?php echo esc_html( $header_height ); ?>px;
                }  

                /* The same for sticky header */
                .header-shop.act-scroll .right-column .header-links {
                    height: <?php echo esc_html( $sticky_header_height ); ?>px;
                    line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }  
            <?php endif; ?>

            <?php if ( !empty( $custom_product_background ) ): ?>
				.single-product .site-content{
					background-color: <?php echo esc_html( $custom_product_background ); ?> !important;
				}
			<?php endif ?>

            /* Page headings settings for heading overlap. Calculate on the header height base */

            .basel-header-overlap .title-size-default,
            .basel-header-overlap .title-size-small,
            .basel-header-overlap .title-shop.without-title.title-size-default,
            .basel-header-overlap .title-shop.without-title.title-size-small {
                padding-top: <?php echo ($header_height + 40);  ?>px;
            }


            .basel-header-overlap .title-shop.without-title.title-size-large,
            .basel-header-overlap .title-size-large {
                padding-top: <?php echo ($header_height + 120);  ?>px;
            }

            @media (max-width: 991px) {

				/* header Banner */
				.header-banner {
					height: <?php echo esc_html( $header_banner_height_mobile ); ?>px;
				}
	
				.header-banner-display .website-wrapper {
					margin-top:<?php echo esc_html( $header_banner_height_mobile ); ?>px;
				}

	            /* Topbar height configs */
				.topbar-menu ul > li {
					line-height: <?php echo esc_html( $topbar_height_mobile ); ?>px;
				}
				
				.topbar-wrapp,
				.topbar-content:before {
					height: <?php echo esc_html( $topbar_height_mobile ); ?>px;
				}
				
				.sticky-header-prepared.basel-top-bar-on .header-shop, 
				.sticky-header-prepared.basel-top-bar-on .header-split,
				.enable-sticky-header.basel-header-overlap.basel-top-bar-on .main-header {
					top: <?php echo esc_html( $topbar_height_mobile ); ?>px;
				}

                /* Set header height for mobile devices */
                .main-header .wrapp-header {
                    min-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                } 

                /* Limit logo image height for mobile according to mobile header height */
                .site-logo img {
                    max-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }   

                /* Limit logo on sticky header. Both header real and header cloned */
                .act-scroll .site-logo img,
                .header-clone .site-logo img {
                    max-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }

                /* Height for switch logos */

                .main-header .switch-logo-enable .basel-logo {
                    height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }

                .sticky-header-real:not(.global-header-menu-top) .act-scroll .switch-logo-enable .basel-logo {
                    height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }

                .sticky-header-real:not(.global-header-menu-top) .act-scroll .switch-logo-enable {
                    transform: translateY(-<?php echo esc_html( $mobile_header_height ); ?>px);
                    -webkit-transform: translateY(-<?php echo esc_html( $mobile_header_height ); ?>px);
                }

                /* Page headings settings for heading overlap. Calculate on the MOBILE header height base */
                .basel-header-overlap .title-size-default,
                .basel-header-overlap .title-size-small,
                .basel-header-overlap .title-shop.without-title.title-size-default,
                .basel-header-overlap .title-shop.without-title.title-size-small {
                    padding-top: <?php echo ($mobile_header_height + 20);  ?>px;
                }

                .basel-header-overlap .title-shop.without-title.title-size-large,
                .basel-header-overlap .title-size-large {
                    padding-top: <?php echo ($mobile_header_height + 60);  ?>px;
                }
 
            }
     
            <?php 
            if( $custom_css != '' ) {
                echo ($custom_css);
            }
            if( $css_desktop != '' ) {
                echo '@media (min-width: 992px) { ' . ($css_desktop) . ' }'; 
            }
            if( $css_tablet != '' ) {
                echo '@media (min-width: 768px) and (max-width: 991px) {' . ($css_tablet) . ' }'; 
            }
            if( $css_wide_mobile != '' ) {
                echo '@media (min-width: 481px) and (max-width: 767px) { ' . ($css_wide_mobile) . ' }'; 
            }
            if( $css_mobile != '' ) {
                echo '@media (max-width: 480px) { ' . ($css_mobile) . ' }'; 
            }

			return ob_get_clean();
	} 
}

// **********************************************************************// 
// Get typekit fonts
// **********************************************************************// 
if ( ! function_exists( 'basel_get_typekit_fonts' ) ) {
	function basel_get_typekit_fonts() {
		global $basel_options;
		if ( !empty( $basel_options['typekit_fonts'] ) ) {
			$fonts_string = explode( ',', $basel_options['typekit_fonts'] );
			$fonts_array['Custom-Fonts'] = array_combine( $fonts_string, $fonts_string );
			return $fonts_array;
		}else{
			return array();
		}
	}
	add_filter( 'redux/basel_options/field/typography/custom_fonts', 'basel_get_typekit_fonts' );
}

// **********************************************************************// 
//  Function return vc_video with image mask.
// **********************************************************************// 
if( ! function_exists( 'basel_add_video_poster' ) ) {
	function basel_add_video_poster( $output, $obj, $attr ) {
		if ( ! empty( $attr['image_poster_switch'] ) ) {
			$image_id = $attr['poster_image'];
			$image_size = 'full';
			if ( isset( $attr['img_size'] ) ) $image_size = $attr['img_size'];
			$image = basel_get_image_src( $image_id, $image_size );
			$output = preg_replace_callback('/wpb_video_wrapper.*?>/',
				function ( $matches ) use( $image ) {
				   return strtolower( $matches[0] . '<div class="basel-video-poster-wrapper"><div class="basel-video-poster" style="background-image:url(' . $image . ')";></div><div class="button-play"></div></div>' );
				}, $output );
		}
		return $output;
	}
	add_filter( 'vc_shortcode_output', 'basel_add_video_poster', 10, 3 );
}

// **********************************************************************// 
//  Function return all images sizes
// **********************************************************************// 
if( ! function_exists( 'basel_get_all_image_sizes' ) ) {
	function basel_get_all_image_sizes() {
	    global $_wp_additional_image_sizes;

	    $default_image_sizes = array( 'thumbnail', 'medium', 'large', 'full' );

	    foreach ( $default_image_sizes as $size ) {
	        $image_sizes[ $size ][ 'width' ] = intval( get_option( "{$size}_size_w" ) );
	        $image_sizes[ $size ][ 'height' ] = intval( get_option( "{$size}_size_h" ) );
	        $image_sizes[ $size ][ 'crop' ] = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
	    }

	    if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
	        $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
	    }

	    return $image_sizes;
	}
}
if( ! function_exists( 'basel_get_image_size' ) ) {
	function basel_get_image_size( $thumb_size ) {
		if ( is_string( $thumb_size ) && in_array( $thumb_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ) ) ) {
			$images_sizes = basel_get_all_image_sizes();
			$image_size = $images_sizes[$thumb_size];
			if ( $thumb_size == 'full') {
				$image_size['width'] = 999999; 
				$image_size['height'] = 999999;
			}
			return array( $image_size['width'], $image_size['height'] );
		}elseif ( is_string( $thumb_size ) ) {
			preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$thumb_size = array();
				if ( count( $thumb_matches[0] ) > 1 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][1]; // height
				} elseif ( count( $thumb_matches[0] ) > 0 && count( $thumb_matches[0] ) < 2 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][0]; // height
				} else {
					$thumb_size = false;
				}
			}
			return $thumb_size;	
		}	
	}
}

if( ! function_exists( 'basel_get_image_src' ) ) {
	function basel_get_image_src( $thumb_id, $thumb_size ) {
		$thumb_size = basel_get_image_size( $thumb_size );
		$thumbnail = wpb_resize( $thumb_id, null, $thumb_size[0], $thumb_size[1], true );
		return $thumbnail['url'];
	}
}

// **********************************************************************// 
// Check is theme is activated with a purchase code
// **********************************************************************// 

if ( ! function_exists( 'basel_is_license_activated' ) ) {
	function basel_is_license_activated() {
	    return get_option( 'basel_is_activated', false );
	}
}
