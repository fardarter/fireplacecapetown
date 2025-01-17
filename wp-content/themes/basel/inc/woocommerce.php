<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Add theme support for WooCommerce
 * ------------------------------------------------------------------------------------------------
 */

add_theme_support( 'woocommerce' );
add_theme_support( 'wc-product-gallery-zoom' );

// **********************************************************************// 
// ! Items view select on the shop page
// **********************************************************************// 

if( ! function_exists( 'basel_products_view_select' ) ) {
	
	add_action( 'woocommerce_before_shop_loop', 'basel_products_view_select', 27 );

	function basel_products_view_select() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) return;

		$shop_view = basel_get_opt('shop_view');
		if( $shop_view == 'grid' || $shop_view == 'list' ) return;
		
		$current_view = basel_get_shop_view();
		?>
		<div class="basel-products-shop-view <?php echo esc_attr( 'products-view-' . $shop_view ); ?>">
			<a rel="nofollow" href="<?php echo add_query_arg('shop_view', 'list', basel_shop_page_link(true)); ?>" class="shop-view <?php echo ('list' == $current_view) ? 'current-view' : ''; ?>">
				<?php
					echo basel_get_svg_content('list-style');
				?>
			</a>
			<a rel="nofollow" href="<?php echo add_query_arg('shop_view', 'grid', basel_shop_page_link(true)); ?>" class="shop-view <?php echo ('grid' == $current_view) ? 'current-view' : ''; ?>">
				<?php
					echo basel_get_svg_content('grid-style');
				?>
			</a>
		</div>
		<?php
	}
}
if( ! function_exists( 'basel_shop_view_action' ) ) {

	add_action( 'init', 'basel_shop_view_action', 100 );

	function basel_shop_view_action() {
		if( ! class_exists('WC_Session_Handler')) return;
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) return;

		if ( isset( $_REQUEST['shop_view'] ) ) {
			$s->set( 'shop_view', $_REQUEST['shop_view'] );
		}
	}
}

if( ! function_exists( 'basel_get_shop_view' ) ) {
	function basel_get_shop_view() {
		if( ! class_exists('WC_Session_Handler') ) return;
		$s = WC()->session; // WC()->session
		if ( is_null( $s ) ) return basel_get_opt('shop_view');

		if ( isset( $_REQUEST['shop_view'] ) ) {
			return $_REQUEST['shop_view'];
		}elseif ( $s->__isset( 'shop_view' ) ) {
			return $s->__get( 'shop_view' );
		}else {
			$shop_view = basel_get_opt('shop_view');
			if ( $shop_view == 'grid_list' ) {
				return 'grid';
			}elseif( $shop_view == 'list_grid'){
				return 'list';
			}else{
				return $shop_view;
			}
		}
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Main loop
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_woocommerce_main_loop' ) ) {

	add_action( 'basel_woocommerce_main_loop', 'basel_woocommerce_main_loop' );

	function basel_woocommerce_main_loop( $fragments = false ) {
		global $paged, $wp_query;

        $max_page = $wp_query->max_num_pages;

		if ( $fragments ) ob_start();
		
		if ( $fragments && isset( $_GET['loop'] ) ) basel_set_loop_prop( 'woocommerce_loop', (int) $_GET['loop'] );
		
		if ( have_posts() ) : ?>
		
			<?php if( ! $fragments ) woocommerce_product_loop_start(); ?>
			
				<?php if ( wc_get_loop_prop( 'total' ) || $fragments ): ?>
					
					<?php while ( have_posts() ) : the_post(); ?>
	
						<?php wc_get_template_part( 'content', 'product' ); ?>
	
					<?php endwhile; // end of the loop. ?>
					
				<?php endif; ?>

			<?php if( ! $fragments ) woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				if( ! $fragments ) do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php else: ?>

			<?php 
				/**
				 * Hook: woocommerce_no_products_found.
				 *
				 * @hooked wc_no_products_found - 10
				 */
				do_action( 'woocommerce_no_products_found' );
			?>

		<?php endif;

		if ( $fragments ) $output = ob_get_clean();

	    if( $fragments ) {
	    	$output =  array(
	    		'items' => $output,
	    		'status' => ( $max_page > $paged ) ? 'have-posts' : 'no-more-posts',
	    		'nextPage' => str_replace( '&#038;', '&', next_posts( $max_page, false ) )
	    	);

	    	echo json_encode( $output );
	    }
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Change number of products displayed per page
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_shop_products_per_page' ) ) {
	function basel_shop_products_per_page() {
		$per_page = 12;
		$number = apply_filters('basel_shop_per_page', basel_get_opt( 'shop_per_page' ) );
		if( is_numeric( $number ) ) {
			$per_page = $number;
		}

		return $per_page;
	}

	add_filter( 'loop_shop_per_page', 'basel_shop_products_per_page', 20 );
}


/**
 * ------------------------------------------------------------------------------------------------
 * Set full width layouts for woocommerce pages on set up
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_woocommerce_install_actions' ) ) {
	function basel_woocommerce_install_actions() {
		if ( ! empty( $_GET['page'] ) && @$_GET['page'] == 'wc-setup' && ! empty( $_GET['step'] ) && @$_GET['step'] == 'next_steps') {
			$pages = apply_filters( 'woocommerce_create_pages', array(
				'cart' => array(
					'name'    => _x( 'cart', 'Page slug', 'woocommerce' ),
					'title'   => _x( 'Cart', 'Page title', 'woocommerce' ),
					'content' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']'
				),
				'checkout' => array(
					'name'    => _x( 'checkout', 'Page slug', 'woocommerce' ),
					'title'   => _x( 'Checkout', 'Page title', 'woocommerce' ),
					'content' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']'
				),
			) );

			foreach ( $pages as $key => $page ) {
				$option = 'woocommerce_' . $key . '_page_id';
				$page_id = get_option( $option );
				update_post_meta( $page_id, '_basel_main_layout', 'full-width' );
			}

			basel_woocommerce_image_dimensions();
		}
	}

	add_action( 'admin_init', 'basel_woocommerce_install_actions', 1000);
	add_action( 'admin_print_styles', 'basel_woocommerce_install_actions', 1000);
}


/**
 * Define image sizes
 */
if( ! function_exists( 'basel_woocommerce_image_dimensions' ) ) {
	function basel_woocommerce_image_dimensions() {
		global $pagenow;

		/*if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
			return;
		}*/
		
		// Image sizes
		update_option( 'woocommerce_single_image_width', '1200' ); 		// Single product image
		update_option( 'woocommerce_thumbnail_image_width', '600' ); 	// Gallery and catalog image
	}
	//add_action( 'after_switch_theme', 'basel_woocommerce_image_dimensions', 1 );
}


/**
 * ------------------------------------------------------------------------------------------------
 * Check if WooCommerce is active
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_woocommerce_installed' ) ) {
	function basel_woocommerce_installed() {
	    return class_exists( 'WooCommerce' );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Get base shop page link
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_shop_page_link' ) ) {
	function basel_shop_page_link( $keep_query = false ) {
		// Base Link decided by current page
		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id('shop') ) ) {
			$link = get_post_type_archive_link( 'product' );
		} elseif( is_product_category() ) {
			$link = get_term_link( get_query_var('product_cat'), 'product_cat' );
		} elseif( is_product_tag() ) {
			$link = get_term_link( get_query_var('product_tag'), 'product_tag' );
		} else {
			$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
		}

		if( $keep_query ) {
			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) {
				if ( 'orderby' === $key || 'submit' === $key ) {
					continue;
				}
				$link = add_query_arg( $key, $val, $link );

			}
		}

		return $link;
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * is ajax request
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'basel_is_woo_ajax' ) ) {
	function basel_is_woo_ajax() {
		
		$request_headers = getallheaders();

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}
		if( isset( $request_headers['x-pjax'] ) ) {
			return true;
		}
		if( isset( $request_headers['X-PJAX'] ) ) {
			return true;
		}
		if( isset( $_REQUEST['woo_ajax'] ) ) {
			return 'fragments';
		}
		if( basel_is_pjax() ) {
			return true;
		}
		return false;
	}
}

if( ! function_exists( 'basel_is_pjax' ) ) {
	function basel_is_pjax(){
		return isset( $_REQUEST['_pjax'] );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Get product design option
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'basel_product_design' ) ) {
	function basel_product_design() {
		$design = basel_get_opt( 'product_design' );
		if( is_singular( 'product' ) ) {
			$custom = get_post_meta( get_the_ID(), '_basel_product_design', true );
			if( ! empty( $custom ) && $custom != 'inherit' ) $design = $custom;
		}

		return $design;
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Custom function for product title
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
	function woocommerce_template_loop_product_title() {
		echo '<h3 class="product-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Register new image size two times larger than standard woocommerce one 
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_add_image_size' ) ) {
	add_action( 'after_setup_theme', 'basel_add_image_size' );

	function basel_add_image_size() {

		if( ! function_exists( 'wc_get_image_size' ) ) return;

		$shop_catalog = wc_get_image_size( 'woocommerce_thumbnail' );

		$width = (int) ( $shop_catalog['width'] * 2 );
		$height = ( !empty( $shop_catalog['height'] ) ) ? (int) ( $shop_catalog['height'] * 2 ) : '';

		add_image_size( 'shop_catalog_x2', $width, $height, $shop_catalog['crop'] );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Custom thumbnail function for slider
 * ------------------------------------------------------------------------------------------------
 */

if ( ! function_exists( 'basel_template_loop_product_thumbnail' ) ) {
	function basel_template_loop_product_thumbnail() {
		echo basel_get_product_thumbnail();
	}
}

if ( ! function_exists( 'basel_get_product_thumbnail' ) ) {
	function basel_get_product_thumbnail( $size = 'woocommerce_thumbnail', $attach_id = false ) {
		global $post;
		$custom_size = $size;
		$defined_sizes = array( 'woocommerce_thumbnail', 'shop_catalog_x2' );

		if( basel_loop_prop( 'double_size' ) ) {
			$size = 'shop_catalog_x2';
		}

		if ( has_post_thumbnail() ) {

			if( ! $attach_id ) $attach_id = get_post_thumbnail_id();

			$props = wc_get_product_attachment_props( $attach_id, $post );
			
			if( basel_loop_prop( 'img_size' ) ) {
				$custom_size = basel_loop_prop( 'img_size' );
			} 

			$custom_size = apply_filters( 'basel_custom_img_size', $custom_size ); 

			if( ! in_array( $custom_size, $defined_sizes ) && function_exists( 'wpb_getImageBySize' ) ) {

				$img = wpb_getImageBySize( array( 'attach_id' => $attach_id, 'thumb_size' => $custom_size, 'class' => 'content-product-image' ) );
				$img = $img['thumbnail'];

			} else {
				$img = wp_get_attachment_image( $attach_id, $size, array(
					'title'	 => $props['title'],
					'alt'    => $props['alt'],
				) );
			}

			return $img;

		} elseif ( wc_placeholder_img_src() ) {
			return wc_placeholder_img( $size );
		}
	}
}

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', 'basel_template_loop_product_thumbnail', 10 );


/**
 * ------------------------------------------------------------------------------------------------
 * Custom thumbnail for category (wide items)
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_category_thumb_double_size' ) ) {
	function basel_category_thumb_double_size( $category ) {
		$small_thumbnail_size  	= apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
		$dimensions    			= wc_get_image_size( $small_thumbnail_size );
		$thumbnail_id  			= get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true  );
        $attr_height 			= '';
		
		if( basel_loop_prop( 'double_size' ) ) {
			$small_thumbnail_size = 'shop_catalog_x2';
			$dimensions['width'] *= 2;
			if ( $dimensions['height'] ) {
				$dimensions['height'] *= 2;
				$attr_height = 'height="' . esc_attr( $dimensions['height'] ) . '"';
			}
		}

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size  );
			$image = $image[0];
		} else {
			$image = wc_placeholder_img_src();
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: https://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" ' . $attr_height . ' />';
		}
	}
}

remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
add_action( 'woocommerce_before_subcategory_title', 'basel_category_thumb_double_size', 10 );


/**
 * ------------------------------------------------------------------------------------------------
 * Quick View button
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_quick_view_btn' ) ) {
	function basel_quick_view_btn( $id = false ) {
		if( ! $id ) {
			$id = get_the_ID();
		}

		if ( basel_get_opt( 'quick_view') ): ?>
			<div class="quick-view">
				<a 
					href="<?php echo esc_url( get_the_permalink($id) ); ?>" 
					class="open-quick-view" 
					data-id="<?php echo esc_attr( $id ); ?>"><?php _e('Quick View', 'basel'); ?></a>
			</div>
		<?php endif;

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Quick shop button
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_quick_shop_btn' ) ) {
	function basel_quick_shop_btn() {
		global $product;
		if( $product->get_type() == 'variable' ) {
			?>
				<a href="#" class="btn-quick-shop" data-id="<?php echo esc_attr( $product->get_id() ); ?>"><span><?php _e('Quick shop', 'basel'); ?></span></a>
			<?php
		} else {
			do_action( 'woocommerce_after_shop_loop_item' );
		}
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Show attribute swatches list
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_swatches_list' ) ) {
	function basel_swatches_list( $attribute_name = false ) {
		global $product;

		$id = $product->get_id();

		if( empty( $id ) || ! $product->is_type( 'variable' ) ) return;
		
		if( ! $attribute_name ) {
			$attribute_name = basel_grid_swatches_attribute();
		}
		
		if( empty( $attribute_name ) ) return false;

		$available_variations = $product->get_available_variations();

		if( empty( $available_variations ) ) return;

		$swatches_to_show = basel_get_option_variations(  $attribute_name, $available_variations, false, $id );

		if( empty( $swatches_to_show ) ) return;

		echo '<div class="swatches-on-grid">';

		$swatch_size = basel_wc_get_attribute_term( $attribute_name, 'swatch_size' );

		if( apply_filters( 'basel_swatches_on_grid_right_order', true ) ) {
			$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'slugs' ) );

			$swatches_to_show_tmp = $swatches_to_show;

			$swatches_to_show = array();

			foreach ($terms as $id => $slug) {
				if( ! isset( $swatches_to_show_tmp[$slug] ) ) continue;
				$swatches_to_show[$slug] = $swatches_to_show_tmp[$slug];
			}
		}


		foreach ($swatches_to_show as $key => $swatch) {
			$style = $class = '';

			if( ! empty( $swatch['color'] )) {
				$style = 'background-color:' .  $swatch['color'];
			} else if( ! empty( $swatch['image'] ) ) {
				$style = 'background-image: url(' . $swatch['image'] . ')';
			} else if( ! empty( $swatch['not_dropdown'] ) ) {
				$class .= 'text-only ';
			}

			$style .= ';';

			$data = '';

			if( isset( $swatch['image_src'] ) ) {
				$class .= 'swatch-has-image';
				$data .= 'data-image-src="' . $swatch['image_src'] . '"';
				$data .= ' data-image-srcset="' . $swatch['image_srcset'] . '"';
				$data .= ' data-image-sizes="' . $swatch['image_sizes'] . '"';
				if( basel_get_opt( 'swatches_use_variation_images' ) ) {
					$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $swatch['variation_id'] ), 'woocommerce_thumbnail');
					if ( !empty( $thumb ) ) {
						$style = 'background-image: url(' . $thumb[0] . ')';
						$class .= ' variation-image-used';
					}
				}

				if( ! $swatch['is_in_stock'] ) {
					$class .= ' variation-out-of-stock';
				}
			}

			$class .= ' swatch-size-' . $swatch_size;

			$term = get_term_by( 'slug', $key, $attribute_name );

			echo '<div class="swatch-on-grid basel-tooltip ' . esc_attr( $class ) . '" style="' . esc_attr( $style ) .'" ' . $data . '>' . $term->name . '</div>';
		}

		echo '</div>';

	}
}


if( ! function_exists( 'basel_grid_swatches_attribute' ) ) {
	function basel_grid_swatches_attribute() {
		$custom = get_post_meta(get_the_ID(),  '_basel_swatches_attribute', true );
		return empty( $custom ) ? basel_get_opt( 'grid_swatches_attribute' ) : $custom;
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Product deal countdown
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_sale_countdown' ) ) {
    function basel_product_sale_countdown() {
        global $post;
		$sale_date = get_post_meta( $post->ID, '_sale_price_dates_to', true );
		$curent_date = strtotime( date( 'Y-m-d H:i:s' ) );

        if( $sale_date < $curent_date ) return;

        $timezone = 'GMT';

        if ( apply_filters( 'basel_wp_timezone_shop', false ) ) $timezone = wc_timezone_string();

        echo '<div class="basel-product-countdown basel-timer" data-end-date="' . esc_attr( date( 'Y-m-d H:i:s', $sale_date ) ) . '" data-timezone="' . $timezone . '"></div>';
    }
}

/**
 * ------------------------------------------------------------------------------------------------
 * Hover image
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_hover_image' ) ) {
	function basel_hover_image() {
		global $product;
	
		$attachment_ids = $product->get_gallery_image_ids();

		$hover_image = '';

		if ( ! empty( $attachment_ids[0] ) ) {
			$hover_image = basel_get_product_thumbnail( 'woocommerce_thumbnail', $attachment_ids[0] );
		}

		if( $hover_image != '' && basel_get_opt( 'hover_image' ) ): ?>
			<div class="hover-img">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php echo ( $hover_image ); ?>
				</a>
			</div>
		<?php endif;

	}
}


if( ! function_exists( 'basel_products_nav' ) ) {
	function basel_products_nav() {
	    $next = get_next_post();
	    $prev = get_previous_post();

	    $next = ( ! empty( $next ) ) ? wc_get_product( $next->ID ) : false;
	    $prev = ( ! empty( $prev ) ) ? wc_get_product( $prev->ID ) : false;
		?>
			<div class="basel-products-nav">
				<?php if ( ! empty( $prev ) ): ?>
				<div class="product-btn product-prev">
					<a href="<?php echo esc_url( $prev->get_permalink() ); ?>"><?php _e('Previous product', 'basel'); ?><span></span></a>
					<div class="wrapper-short">
						<div class="product-short">
							<a href="<?php echo esc_url( $prev->get_permalink() ); ?>" class="product-thumb">
								<?php echo $prev->get_image(); ?>
							</a>
							<a href="<?php echo esc_url( $prev->get_permalink() ); ?>" class="product-title">
								<?php echo $prev->get_title(); ?>
							</a>
							<span class="price">
								<?php echo $prev->get_price_html(); ?>
							</span>
						</div>
					</div>
				</div>
				<?php endif ?>

				<?php if ( ! empty( $next ) ): ?>
				<div class="product-btn product-next">
					<a href="<?php echo esc_url( $next->get_permalink() ); ?>"><?php _e('Next product', 'basel'); ?><span></span></a>
					<div class="wrapper-short">
						<div class="product-short">
							<a href="<?php echo esc_url( $next->get_permalink() ); ?>" class="product-thumb">
								<?php echo $next->get_image(); ?>
							</a>
							<a href="<?php echo esc_url( $next->get_permalink() ); ?>" class="product-title">
								<?php echo $next->get_title(); ?>
							</a>
							<span class="price">
								<?php echo $next->get_price_html(); ?>
							</span>
						</div>
					</div>
				</div>
				<?php endif ?>
			</div>
		<?php
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Compare button
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_configure_compare' ) ) {
	add_action( 'init', 'basel_configure_compare' );
	function basel_configure_compare() {
		global $yith_woocompare;
		if( ! class_exists( 'YITH_Woocompare' ) ) return;

		$compare = $yith_woocompare->obj;

		if ( get_option('yith_woocompare_compare_button_in_products_list') == 'yes' ) {
			remove_action( 'woocommerce_after_shop_loop_item', array( $compare, 'add_compare_link' ), 20 );
			#add_action( 'woocommerce_before_shop_loop_item', array( $compare, 'add_compare_link' ), 20 );
		}

        if ( get_option('yith_woocompare_compare_button_in_product_page') == 'yes' ) {
        	add_action( 'woocommerce_single_product_summary', 'basel_before_compare_button', 33 );
        	add_action( 'woocommerce_single_product_summary', 'basel_after_compare_button', 37 );
        }

	}
}

if( ! function_exists( 'basel_before_compare_button' ) ) {
	function basel_before_compare_button() {
		echo '<div class="compare-btn-wrapper">';
	}
}

if( ! function_exists( 'basel_after_compare_button' ) ) {
	function basel_after_compare_button() {
		echo '</div>';
	}
}

if( ! function_exists( 'basel_compare_btn' ) ) {
	function basel_compare_btn() {
		if( ! class_exists( 'YITH_Woocompare' ) ) return;

		if( get_option('yith_woocompare_compare_button_in_products_list') != 'yes' ) return;

		echo '<div class="product-compare-button">';
            global $product;
            $product_id = $product->get_id();

            // return if product doesn't exist
            if ( empty( $product_id ) || apply_filters( 'yith_woocompare_remove_compare_link_by_cat', false, $product_id ) )
	            return;

            $is_button = ! isset( $button_or_link ) || ! $button_or_link ? get_option( 'yith_woocompare_is_button' ) : $button_or_link;

            if ( ! isset( $button_text ) || $button_text == 'default' ) {
                $button_text = get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) );
                // yit_wpml_register_string( 'Plugins', 'plugin_yit_compare_button_text', $button_text );
                // $button_text = yit_wpml_string_translate( 'Plugins', 'plugin_yit_compare_button_text', $button_text );
            }

            printf( '<a href="%s" class="%s" data-product_id="%d" rel="nofollow">%s</a>', basel_compare_add_product_url( $product_id ), 'compare' . ( $is_button == 'button' ? ' button' : '' ), $product_id, $button_text );
        
		echo '</div>';
	}
}


if( ! function_exists( 'basel_compare_add_product_url' ) ) {
    function basel_compare_add_product_url( $product_id ) {
    	$action_add = 'yith-woocompare-add-product';
        $url_args = array(
            'action' => 'asd',
            'id' => $product_id
        );
        return apply_filters( 'yith_woocompare_add_product_url', esc_url_raw( add_query_arg( $url_args ) ), $action_add );
    }
}


if( ! function_exists( 'basel_compare_styles' ) ) {
	add_action( 'wp_print_styles', 'basel_compare_styles', 200 );
	function basel_compare_styles() {
		if( ! class_exists( 'YITH_Woocompare' ) ) return;
		$view_action = 'yith-woocompare-view-table';
		if ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != $view_action ) ) return;
		wp_enqueue_style( 'basel-style' );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * WishList button
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_wishlist_btn' ) ) {
	function basel_wishlist_btn() {
		if( class_exists('YITH_WCWL_Shortcode')) echo YITH_WCWL_Shortcode::add_to_wishlist(array());
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Get product page classes (columns) for product images and product information blocks
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_images_class' ) ) {
	function basel_product_images_class() {
		$size = basel_product_images_size();
		$class = 'col-sm-';
		return $class . $size;
	}

		function basel_product_images_size() {
			return apply_filters( 'basel_product_summary_size', 12 - basel_product_summary_size() );
		}
}

if( ! function_exists( 'basel_product_summary_class' ) ) {
	function basel_product_summary_class() {
		$size = basel_product_summary_size();
		$class = 'col-sm-';

		return $class . $size;
	}

		function basel_product_summary_size() {
			$page_layout = basel_get_opt( 'single_product_style' );

			if( basel_product_design() == 'sticky' ) $page_layout = 2; 

			$size = 6;
			switch ( $page_layout ) {
				case 1:
					$size = 8;
				break;
				case 2:
					$size = 6;
				break;
				case 3:
					$size = 4;
				break;
			}
			return apply_filters( 'basel_product_summary_size', $size );
		}
}

if( ! function_exists( 'basel_single_product_class' ) ) {
	function basel_single_product_class() {
		global $product;
		$classes = array();

		$attachment_ids = $product->get_gallery_image_ids();

		$classes[] = 'single-product-page';
		$classes[] = 'single-product-content';

		$classes[] = 'product-design-' . basel_product_design();


		if( $attachment_ids ) {
			$classes[] = 'product-with-attachments';
		}
		
		return $classes;

	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Configure product image gallery JS
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_product_gallery_settings' ) ) {
	function basel_get_product_gallery_settings() {
		return apply_filters( 'basel_product_gallery_settings', array(
			'images_slider' => (basel_get_opt('product_design') != 'sticky'),
			'thumbs_slider' => array(
				'enabled' => true,
				'position' => basel_get_opt('thums_position'),
				'items' => array(
					'desktop' => 4,
					'desktop_small' => 3,
					'tablet' => 4,
					'mobile' => 3,

					'vertical_items' => 3
				)
			)
		) );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Remove product content link
 * ------------------------------------------------------------------------------------------------
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

/**
 * ------------------------------------------------------------------------------------------------
 * WooCommerce enqueues 3 stylesheets by default. You can disable them all with the following snippet
 * ------------------------------------------------------------------------------------------------
 */

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * ------------------------------------------------------------------------------------------------
 * Disable photoswipe
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'wp_footer', 'woocommerce_photoswipe' );


/**
 * ------------------------------------------------------------------------------------------------
 * Change position of woocommerce notices
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
add_action( 'woocommerce_before_main_content', 'wc_print_notices', 50 );


/**
 * ------------------------------------------------------------------------------------------------
 * Unhook the WooCommerce wrappers
 * ------------------------------------------------------------------------------------------------
 */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);


/**
 * ------------------------------------------------------------------------------------------------
 * hook in your own functions to display the wrappers your theme requires
 * ------------------------------------------------------------------------------------------------
 */


/**
 * ------------------------------------------------------------------------------------------------
 * Get CSS class for widget in shop area. Based on the number of widgets
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_widget_column_class' ) ) {
	function basel_get_widget_column_class( $sidebar_id = 'filters-area' ) {
		global $_wp_sidebars_widgets;
		if ( empty( $_wp_sidebars_widgets ) ) :
			$_wp_sidebars_widgets = get_option( 'sidebars_widgets', array() );
		endif;
		
		$sidebars_widgets_count = $_wp_sidebars_widgets;

		if ( isset( $sidebars_widgets_count[ $sidebar_id ] ) || $sidebar_id == 'filters-area' ) {
			$count = ( isset( $sidebars_widgets_count[ $sidebar_id ] ) ) ? count( $sidebars_widgets_count[ $sidebar_id ] ) : 0;
			$widget_count = apply_filters( 'widgets_count_' . $sidebar_id, $count );
			$widget_classes = 'widget-count-' . $widget_count;
			$widget_classes .= basel_get_grid_el_class( 0, ( ($widget_count > 4) ? 4 : $widget_count ), false, 12, 6, 6 );
			return apply_filters( 'widget_class_' . $sidebar_id, $widget_classes);
		}
	}
}

 

add_action('woocommerce_before_main_content', 'basel_woo_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'basel_woo_wrapper_end', 10);

if(!function_exists( 'basel_woo_wrapper_start' )) {
	function basel_woo_wrapper_start() {
		$content_class = basel_get_content_class();
		if( is_singular('product') ) $content_class = 'col-sm-12';
		if ( have_posts() ) {
			$content_class .= ' content-with-products';
        }else{
			$content_class .= ' content-without-products';
		}
		echo '<div class="site-content shop-content-area ' . $content_class . '" role="main">';
	}
}


if(!function_exists( 'basel_woo_wrapper_end' )) {
	function basel_woo_wrapper_end() {
		echo '</div>';
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * My account sidebar
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_before_my_account_navigation' ) ) {
	function basel_before_my_account_navigation() {
		echo '<div class="basel-my-account-sidebar">';
		the_title( '<h3 class="woocommerce-MyAccount-title entry-title">', '</h3>' );
	}

	add_action( 'woocommerce_account_navigation', 'basel_before_my_account_navigation', 5 );
}

if( ! function_exists( 'basel_after_my_account_navigation' ) ) {
	function basel_after_my_account_navigation() {
		$sidebar_name = 'sidebar-my-account';
		if ( is_active_sidebar( $sidebar_name ) ) : ?>
			<aside class="sidebar-container" role="complementary">
				<div class="sidebar-inner">
					<div class="widget-area">
						<?php dynamic_sidebar( $sidebar_name ); ?>
					</div><!-- .widget-area -->
				</div><!-- .sidebar-inner -->
			</aside><!-- .sidebar-container -->
		<?php endif;
		echo '</div><!-- .basel-my-account-sidebar -->';
	}

	add_action( 'woocommerce_account_navigation', 'basel_after_my_account_navigation', 30 );
}



/**
 * ------------------------------------------------------------------------------------------------
 * Play with woocommerce hooks
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_woocommerce_hooks' ) ) {
	function basel_woocommerce_hooks() {
        global $basel_prefix;
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

		add_action( 'basel_woocommerce_after_sidebar', 'woocommerce_upsell_display', 10 );

		// Disable related products option
		if( basel_get_opt('related_products') && ! get_post_meta(get_the_ID(),  '_basel_related_off', true ) ) {
			add_action( 'basel_woocommerce_after_sidebar', 'woocommerce_output_related_products', 20 );
		}

		// Disable product tabs title option
		if( basel_get_opt('hide_tabs_titles') || get_post_meta(get_the_ID(),  '_basel_hide_tabs_titles', true ) ) {
			add_filter( 'woocommerce_product_description_heading', '__return_false', 20 );
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false', 20 );
		}

		if( basel_get_opt('shop_filters') ) {
 			
 			// Use our own order widget list?
			if( apply_filters( 'basel_use_custom_order_widget', true ) ) {
				if( ! is_active_widget( false, false, 'basel-woocommerce-sort-by', true ) ) {
					add_action( 'basel_before_filters_widgets', 'basel_sorting_widget', 10 );
				}
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}

			// Use our custom price filter widget list?
			if( apply_filters( 'basel_use_custom_price_widget', true )  && ! is_active_widget( false, false, 'basel-price-filter', true ) ) {
				add_action( 'basel_before_filters_widgets', 'basel_price_widget', 20 );
			}

			// Add 'filters button'
			add_action( 'woocommerce_before_shop_loop', 'basel_filter_buttons', 40 );
		}

		add_action( 'woocommerce_cart_is_empty', 'basel_empty_cart_text', 20 );

		// Timer on the single product page
		add_action( 'woocommerce_single_product_summary', function() {
			$timer = basel_get_opt( 'product_countdown' );
			if( basel_loop_prop( 'timer' ) ) basel_product_sale_countdown();
		}, 25 );


		// Compact product type
		$product_design = basel_product_design();
		if( $product_design == 'compact' ) {
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 39);
		}

		// Product video and 360 view
		$video_url = get_post_meta(get_the_ID(),  '_basel_product_video', true );
		$images_360_gallery = basel_get_360_gallery_attachment_ids();

		if( ! empty( $video_url ) || ! empty( $images_360_gallery ) ) {
			add_action( 'woocommerce_before_single_product_summary', 'basel_additional_galleries_open', 25 );
			add_action( 'woocommerce_before_single_product_summary', 'basel_additional_galleries_close', 100 );
		}
		
		if( ! empty( $video_url ) ) {
			add_action( 'woocommerce_before_single_product_summary', 'basel_product_video_button', 30 );
		}

		if( ! empty( $images_360_gallery ) ) {
			add_action( 'woocommerce_before_single_product_summary', 'basel_product_360_view', 40 );
		}

		// Custom extra content
		$extra_block = get_post_meta(get_the_ID(),  '_basel_extra_content', true );

		if( ! empty( $extra_block ) && $extra_block != 0 ) {
			$extra_position = get_post_meta(get_the_ID(),  '_basel_extra_position', true );
			if( $extra_position == 'before' ) {
				add_action( 'woocommerce_before_single_product', 'basel_product_extra_content', 20 );
			} else if( $extra_position == 'prefooter' ) {
				add_action( 'basel_woocommerce_after_sidebar', 'basel_product_extra_content', 30 );
			} else {
				add_action( 'basel_after_product_content', 'basel_product_extra_content', 20 );
				
			}
		}


		// Custom tab 
		add_filter( 'woocommerce_product_tabs', 'basel_custom_product_tabs' );

		// Instagram by hashbat for product
		add_action( 'basel_woocommerce_after_sidebar', 'basel_product_instagram', 80 );
		
		// Brand tab for single product
		if( basel_get_opt( 'brand_tab' ) ) {
			add_filter( 'woocommerce_product_tabs', 'basel_product_brand_tab' );
		}

		// Poduct brand
		if( basel_get_opt( 'product_brand_location' ) == 'about_title' && is_singular( 'product' ) ) {
			add_action( 'woocommerce_single_product_summary', 'basel_product_brand', 3);
		} elseif( is_singular( 'product' ) ) {
			add_action( 'basel_before_sidebar_area', 'basel_product_brand', 10 );
		}

	}

	add_action( 'wp', 'basel_woocommerce_hooks', 1000 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Extra content block
 * ------------------------------------------------------------------------------------------------
 */


if( ! function_exists( 'basel_product_extra_content' ) ) {
	function basel_product_extra_content( $tabs ) { 
		$extra_block = get_post_meta(get_the_ID(),  '_basel_extra_content', true );
		echo basel_html_block_shortcode( array( 'id' => $extra_block ) );
	}
}
		

/**
 * ------------------------------------------------------------------------------------------------
 * Additional tab
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_custom_product_tabs' ) ) {
	function basel_custom_product_tabs( $tabs ) {
		$additional_tab_title = basel_get_opt( 'additional_tab_title' );
		$custom_tab_title = get_post_meta( get_the_ID(),  '_basel_product_custom_tab_title', true );
		
		if ( $additional_tab_title ) {
			$tabs['basel_additional_tab'] = array(
				'title' 	=> $additional_tab_title,
				'priority' 	=> 50,
				'callback' 	=> 'basel_additional_product_tab_content'
			);
		}
		
		if ( $custom_tab_title ) {
			$tabs['basel_custom_tab'] = array(
				'title' 	=> $custom_tab_title,
				'priority' 	=> 60,
				'callback' 	=> 'basel_custom_product_tab_content'
			);
		}
		
		return $tabs;
	}
}

if( ! function_exists( 'basel_additional_product_tab_content' ) ) {
	function basel_additional_product_tab_content() {
		// The new tab content
		$tab_content = basel_get_opt( 'additional_tab_text' );
		echo do_shortcode( $tab_content );
		
	}
}

if( ! function_exists( 'basel_custom_product_tab_content' ) ) {
	function basel_custom_product_tab_content() {
		// The new tab content
		$tab_content = get_post_meta( get_the_ID(),  '_basel_product_custom_tab_content', true );
		echo do_shortcode( $tab_content );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Product video and 360 view buttons
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_video_button' ) ) {
	function basel_product_video_button() {
		$video_url = get_post_meta(get_the_ID(),  '_basel_product_video', true );
		?>
			<div class="product-video-button">
				<a href="<?php echo esc_url( $video_url ); ?>"><span><?php _e('Watch video', 'basel'); ?></span></a>
			</div>
		<?php
	}
}

if( ! function_exists( 'basel_additional_galleries_open' ) ) {
	function basel_additional_galleries_open() {
		?>
			<div class="product-additional-galleries">
		<?php
	}
}

if( ! function_exists( 'basel_additional_galleries_close' ) ) {
	function basel_additional_galleries_close() {
		?>
			</div>
		<?php
	}
}


if( ! function_exists( 'basel_product_360_view' ) ) {
	function basel_product_360_view() {
		$images = basel_get_360_gallery_attachment_ids();
		if( empty( $images ) ) return;

		$id = rand(100,999);

		$title = '';

		$frames_count = count( $images );

		$images_js_string = '';

		?>
			<div class="product-360-button">
				<a href="#product-360-view"><span><?php _e('360 product view', 'basel'); ?></span></a>
			</div>
			<div id="product-360-view" class="product-360-view-wrapper mfp-hide">
				<div class="basel-threed-view threed-id-<?php echo esc_attr( $id ); ?>">
					<?php if ( ! empty( $title ) ): ?>
						<h3 class="threed-title"><span><?php echo ($title); ?></span></h3>
					<?php endif ?>
					<ul class="threed-view-images">
						<?php if ( count($images) > 0 ): ?>
							<?php $i=0; foreach ($images as $img_id): $i++; ?>
								<?php 
									$img = wp_get_attachment_image_src( $img_id, 'full' );
									$width = $img[1];
									$height = $img[2];
									$images_js_string .= "'" . $img[0] . "'"; 
									if( $i < $frames_count ) {
										$images_js_string .= ","; 
									}
								?>
							<?php endforeach ?>
						<?php endif ?>
					</ul>
				    <div class="spinner">
				        <span>0%</span>
				    </div>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function( $ ) {
					    $('.threed-id-<?php echo esc_attr( $id ); ?>').ThreeSixty({
					        totalFrames: <?php echo $frames_count; ?>,
					        endFrame: <?php echo $frames_count; ?>, 
					        currentFrame: 1, 
					        imgList: '.threed-view-images', 
					        progress: '.spinner',
					        imgArray: [<?php echo $images_js_string; ?>],
					        height: <?php echo $height ?>,
					        width: <?php echo $width ?>,
					        responsive: true,
					        navigation: true
					    });
					});
				</script>
			</div>
		<?php
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Instagram by hashtag for products
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_instagram' ) ) {
	function basel_product_instagram() {
		$hashtag = get_post_meta(get_the_ID(),  '_basel_product_hashtag', true );
		if( empty( $hashtag ) ) return;
		?>
			<div class="basel-product-instagram">
				<p class="product-instagram-intro"><?php printf( wp_kses( __('Tag your photos with <span>%s</span> on Instagram.', 'basel') , array('span' => array())), $hashtag ); ?></p>
				<?php echo basel_shortcode_instagram( array(
					'username' => esc_html( $hashtag ),
					'number' => 8,
					'size' => 'large',
					'target' => '_self',
					'design' => '',
					'spacing' => 0,
					'rounded' => 0,
					'per_row' => 4
				) ); ?>
			</div>
		<?php
	}
}
/**
 * ------------------------------------------------------------------------------------------------
 * Filters buttons
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_filter_buttons' ) ) {
	function basel_filter_buttons() {
		if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) return;
		?>
			<div class="basel-filter-buttons">
				<a href="#" class="open-filters"><?php _e('Filters', 'basel'); ?></a>
			</div>
		<?php
	}
}

if( ! function_exists( 'basel_sorting_widget' ) ) {
	function basel_sorting_widget() {
		$filter_widget_class = basel_get_widget_column_class( 'filters-area' );
		the_widget( 'BASEL_Widget_Sorting', array( 'title' => __('Sort by', 'basel') ), array(							
			'before_widget' => '<div id="BASEL_Widget_Sorting" class="filter-widget ' . esc_attr( $filter_widget_class ) . '">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>') 
		);
	}
}

if( ! function_exists( 'basel_price_widget' ) ) {
	function basel_price_widget() {
		$filter_widget_class = basel_get_widget_column_class( 'filters-area' );
		the_widget( 'BASEL_Widget_Price_Filter', array( 'title' => __('Price filter', 'basel') ), array(							
			'before_widget' => '<div id="BASEL_Widget_Price_Filter" class="filter-widget ' . esc_attr( $filter_widget_class ) . '">',
			'after_widget'  => '</div>',
			'before_title'  => '<h5 class="widget-title">',
			'after_title'   => '</h5>') 
		);
	}
}

if( ! function_exists( 'basel_filter_widgts_classes' ) ) {
	function basel_filter_widgts_classes( $count ) {

		if( apply_filters( 'basel_use_custom_order_widget', true )  && ! is_active_widget( false, false, 'basel-woocommerce-sort-by', true ) ) {
			$count++;
		}

		if( apply_filters( 'basel_use_custom_price_widget', true )  && ! is_active_widget( false, false, 'basel-price-filter', true ) ) {
			$count++;
		}

		return $count;
	}

	add_filter('widgets_count_filters-area', 'basel_filter_widgts_classes');
}



/**
 * ------------------------------------------------------------------------------------------------
 * Force BASEL Swatche layered nav and price widget to work
 * ------------------------------------------------------------------------------------------------
 */


//add_filter( 'woocommerce_is_layered_nav_active', '__return_true' );
add_filter( 'woocommerce_is_layered_nav_active', 'basel_is_layered_nav_active' );
if( ! function_exists( 'basel_is_layered_nav_active' ) ) {
	function basel_is_layered_nav_active() {
		return is_active_widget( false, false, 'basel-woocommerce-layered-nav', true );
	}
}

add_filter( 'woocommerce_is_price_filter_active', 'basel_is_layered_price_active' );

if( ! function_exists( 'basel_is_layered_price_active' ) ) {
	function basel_is_layered_price_active() {
		$result = is_active_widget( false, false, 'basel-price-filter', true );
		if( ! $result ) {
			$result = apply_filters( 'basel_use_custom_price_widget', true );
		}
		return $result;
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Empty cart text
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_empty_cart_text' ) ) {
	add_action( 'woocommerce_cart_is_empty', 'basel_empty_cart_text', 20 );

	function basel_empty_cart_text() {
		$empty_cart_text = basel_get_opt( 'empty_cart_text' );

		if( ! empty( $empty_cart_text ) ) {
			?>
				<div class="basel-empty-cart-text"><?php echo wp_kses( $empty_cart_text, array('p' => array(), 'h1' => array(), 'h2' => array(), 'h3' => array(), 'strong' => array(), 'em' => array(), 'span' => array(), 'div' => array() , 'br' => array()) ); ?></div>
			<?php
		}
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Change the position of woocommerce breadcrumbs
 * ------------------------------------------------------------------------------------------------
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * ------------------------------------------------------------------------------------------------
 * Show woocommerce breadcrumbs above the product name on single
 * ------------------------------------------------------------------------------------------------
 */
//add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 1 );

/**
 * ------------------------------------------------------------------------------------------------
 * Add photoswipe template to body
 * ------------------------------------------------------------------------------------------------
 */
add_action( 'basel_after_footer', 'basel_photoswipe_template', 1000 );
if( ! function_exists( 'basel_photoswipe_template' ) ) {
	function basel_photoswipe_template() {
		get_template_part('woocommerce/single-product/photo-swipe-template');
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Display categories menu
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_categories_nav' ) ) {
	function basel_product_categories_nav() {
		global $wp_query, $post;

		$show_subcategories = basel_get_opt( 'shop_categories_ancestors' );
		$show_categories_neighbors = basel_get_opt( 'show_categories_neighbors' );

		$list_args = array(  
			'taxonomy' => 'product_cat', 
			'hide_empty' => false 
		);

		// Menu Order
		$list_args['menu_order'] = false;
		$list_args['menu_order'] = 'asc';

		// Setup Current Category
		$current_cat   = false;
		$cat_ancestors = array();

		if ( is_tax( 'product_cat' ) ) {
			$current_cat   = $wp_query->queried_object;
			$cat_ancestors = get_ancestors( $current_cat->term_id, 'product_cat' );
		}

		$list_args['depth']              = 5;
		$list_args['child_of']           = 0;
		$list_args['title_li']           = '';
		$list_args['hierarchical']       = 1;
		$list_args['use_desc_for_title'] = false;
		$list_args['walker'] 			 = new BASEL_Walker_Category();
		
		$shop_link = get_post_type_archive_link( 'product' );

		include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php' );
		
		if( is_object( $current_cat ) && !get_term_children( $current_cat->term_id, 'product_cat' ) && $show_subcategories && !$show_categories_neighbors ) return;

		echo '<a href="#" class="basel-show-categories">' . __('Categories', 'basel') . '</a>';

		echo '<ul class="basel-product-categories">';
		
		echo '<li class="cat-link shop-all-link"><a href="' . esc_url( $shop_link ) . '">' . __('All', 'basel') . '</a></li>';

		if( $show_subcategories ) {
			basel_show_category_ancestors();
		} else {
			wp_list_categories( $list_args );
		}

		echo '</ul>';
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Display ancestors of current category
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_show_category_ancestors' )) {
	function basel_show_category_ancestors() {
		global $wp_query, $post;

		$current_cat   = false;
		$list_args = array();

		$show_categories_neighbors = basel_get_opt( 'show_categories_neighbors' );

		if ( is_tax('product_cat') ) {
			$current_cat   = $wp_query->queried_object;
		}

		$list_args = array( 'taxonomy' => 'product_cat', 'hide_empty' => true );

		// Show Siblings and Children Only
		if ( $current_cat ) {

			// Direct children are wanted
			$include = get_terms(
				'product_cat',
				array(
					'fields'       => 'ids',
					'parent'       => $current_cat->term_id,
					'hierarchical' => true,
					'hide_empty'   => false
				)
			);

			$list_args['include']     = implode( ',', $include );

			if ( empty( $include ) && !$show_categories_neighbors ) {
				return;
			}

			if ( $show_categories_neighbors ) {
				if ( get_term_children( $current_cat->term_id, 'product_cat' ) ) {
					$list_args['child_of'] = $current_cat->term_id;
				}elseif( $current_cat->parent != 0 ){
					$list_args['child_of'] = $current_cat->parent;
				}
			}

		}

		//include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php' );

		$list_args['depth']                      = 1;
		$list_args['hierarchical']               = 1;
		$list_args['title_li']                   = '';
		$list_args['pad_counts']                 = 1;
		$list_args['show_option_none']           = __('No product categories exist.', 'basel' );
		$list_args['current_category']           = ( $current_cat ) ? $current_cat->term_id : '';
		$list_args['use_desc_for_title']		 = false;

		//echo '<ul>';

			wp_list_categories( $list_args );

		//echo '</ul>';

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Show product categories
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_categories' ) ) {
	function basel_product_categories() {
		global $post, $product;
		?>
            <div class="basel-product-cats">
                <?php
                    echo wc_get_product_category_list( $product->get_id(), ', ' );
                ?>
            </div>
		<?php
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Function returns quick shop of the product by ID. Variations form HTML
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_quick_shop' ) ) {
	function basel_quick_shop($id = false) {
		if( isset($_GET['id']) ) {
			$id = (int) $_GET['id'];
		}
		if( ! $id || ! basel_woocommerce_installed() ) {
			return;
		}

		global $post;

		$args = array( 'post__in' => array($id), 'post_type' => 'product' );

		$quick_posts = get_posts( $args );

		$quick_view_variable = basel_get_opt( 'quick_view_variable' );

		foreach( $quick_posts as $post ) :
			setup_postdata($post);
        	woocommerce_template_single_add_to_cart();
		endforeach; 

		wp_reset_postdata(); 

		die();
	}

	add_action( 'wp_ajax_basel_quick_shop', 'basel_quick_shop' );
	add_action( 'wp_ajax_nopriv_basel_quick_shop', 'basel_quick_shop' );

}

/**
 * ------------------------------------------------------------------------------------------------
 * Function returns quick view of the product by ID
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_quick_view' ) ) {
	function basel_quick_view($id = false) {
		if( isset($_GET['id']) ) {
			$id = (int) $_GET['id'];
		}
		if( ! $id || ! basel_woocommerce_installed() ) {
			return;
		}

		global $post;

		$args = array( 'post__in' => array($id), 'post_type' => 'product' );

		$quick_posts = get_posts( $args );

		$quick_view_variable = basel_get_opt( 'quick_view_variable' );

		foreach( $quick_posts as $post ) :
			setup_postdata($post);
        	remove_action( 'woocommerce_single_product_summary', 'basel_before_compare_button', 33 );
        	remove_action( 'woocommerce_single_product_summary', 'basel_after_compare_button', 37 );
        	remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
			
			//Remove before and after add to cart button text
			remove_action( 'woocommerce_single_product_summary', 'basel_before_add_to_cart_area', 25 );
			remove_action( 'woocommerce_single_product_summary', 'basel_after_add_to_cart_area', 31 );
			
			// Add brand image
        	add_action( 'woocommerce_single_product_summary', 'basel_product_brand', 8 );

        	// Disable add to cart button for catalog mode
			if( basel_get_opt( 'catalog_mode' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			} elseif( ! $quick_view_variable ) {
				// If no needs to show variations
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30 );
			}

			if( basel_get_opt( 'product_share' ) ) add_action( 'woocommerce_single_product_summary', 'basel_product_share', 45 );
			get_template_part('woocommerce/content', 'quick-view');
		endforeach; 

		wp_reset_postdata(); 

		die();
	}

	add_action( 'wp_ajax_basel_quick_view', 'basel_quick_view' );
	add_action( 'wp_ajax_nopriv_basel_quick_view', 'basel_quick_view' );

}

if( ! function_exists( 'basel_product_images_slider' ) ) {
	function basel_product_images_slider() {
		wc_get_template( 'quick-view/product-images.php' );
	}
}

if( ! function_exists( 'basel_view_product_button' ) ) {
	function basel_view_product_button() {
		echo '<a href="' . get_permalink() . '" class="view-details-btn">' . __('View details', 'basel') . '</a>';
	}
}

if( ! function_exists( 'basel_product_share' ) ) {
	function basel_product_share() {
		echo '<span class="share-title">' . __('Share', 'basel'). '</span>';
		echo basel_shortcode_social( array( 'type' => 'share', 'align' => 'left', 'size' => 'small' ) );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Function returns numbers of items in the cart. Filter woocommerce fragments
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_cart_data' ) ) {
	add_filter('woocommerce_add_to_cart_fragments', 'basel_cart_data', 30);
	function basel_cart_data( $array ) {
		ob_start();
		basel_cart_count();
		$count = ob_get_clean();
		
		ob_start();
		basel_cart_subtotal();
		$subtotal = ob_get_clean();
		
		$array['span.basel-cart-number'] = $count;
		$array['span.basel-cart-subtotal'] = $subtotal;
		
		return $array;
	}
}

if( ! function_exists( 'basel_cart_count' ) ) {
	function basel_cart_count() {
		?>
			<span class="basel-cart-number"><?php echo WC()->cart->cart_contents_count; ?></span>
		<?php
	}
}

if( ! function_exists( 'basel_cart_subtotal' ) ) {
	function basel_cart_subtotal() {
		?>
			<span class="basel-cart-subtotal"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		<?php
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Set wishlist cookie
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_maybe_set_wishlist_cookies' ) ) {
	function basel_maybe_set_wishlist_cookies() {
		if( ! class_exists( 'YITH_WCWL' ) ) return;
		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {
			if ( YITH_WCWL()->count_products() > 0 ) {
				basel_set_wishlist_cookies( true );
			} elseif ( isset( $_COOKIE['basel_items_in_wishlist'] ) ) {
				basel_set_wishlist_cookies( false );
			}
		}
	}
	add_action( 'wp', 'basel_maybe_set_wishlist_cookies', 100 ); // Set cookies
	add_action( 'shutdown', 'basel_maybe_set_wishlist_cookies', 0 ); // Set cookies before shutdown and ob flushing
}


if( ! function_exists( 'basel_set_wishlist_cookies' ) ) {
	function basel_set_wishlist_cookies( $set = true ) {
		if( ! class_exists( 'YITH_WCWL' ) || ! function_exists( 'wc_setcookie' ) ) return;
		if ( $set ) {
			wc_setcookie( 'basel_items_in_wishlist', 1 );
			wc_setcookie( 'basel_wishlist_hash', YITH_WCWL()->count_products() );
		} elseif ( isset( $_COOKIE['basel_items_in_wishlist'] ) ) {
			wc_setcookie( 'basel_items_in_wishlist', 0, time() - HOUR_IN_SECONDS );
			wc_setcookie( 'basel_wishlist_hash', '', time() - HOUR_IN_SECONDS );
		}
		do_action( 'basel_set_wishlist_cookies', $set );
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Function returns numbers of items in the wishlist
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_wishlist_number' ) ) {
	function basel_wishlist_number() {
		if( ! class_exists( 'YITH_WCWL' ) ) die();
		echo YITH_WCWL()->count_products();
		die();
	}

	add_action( 'wp_ajax_basel_wishlist_number', 'basel_wishlist_number' );
	add_action( 'wp_ajax_nopriv_basel_wishlist_number', 'basel_wishlist_number' );

}


/**
 * ------------------------------------------------------------------------------------------------
 * Function that removes item from cart and returns fragments
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_remove_from_cart' ) ) {
	function basel_remove_from_cart() {
		if( ! class_exists( 'WC_AJAX' ) ) die(-1);

		if ( ! empty( $_GET['cart_item'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'woocommerce-cart' ) ) {
			// Remove from cart
			$cart_item_key = sanitize_text_field( $_GET['cart_item'] );

			if ( $cart_item = WC()->cart->get_cart_item( $cart_item_key ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}


		WC_AJAX::get_refreshed_fragments();
	}

	add_action( 'wp_ajax_basel_remove_from_cart', 'basel_remove_from_cart' );
	add_action( 'wp_ajax_nopriv_basel_remove_from_cart', 'basel_remove_from_cart' );
}
		
/**
 * ------------------------------------------------------------------------------------------------
 * Determine is it product attribute archieve page
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_is_product_attribute_archieve' ) ) {
	function basel_is_product_attribute_archieve() {
	    $queried_object = get_queried_object();
	    if( $queried_object && property_exists( $queried_object, 'taxonomy' ) ) {
	        $taxonomy = $queried_object->taxonomy;
	        return substr($taxonomy, 0, 3) == 'pa_';
	    }
	    return false;
	}
} 
		
/**
 * ------------------------------------------------------------------------------------------------
 * Function to prepare classes for grid element (column)
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_grid_el_class' ) ) {
	function basel_get_grid_el_class($loop = 0, $columns = 4, $different_sizes = false, $xs_size = false, $sm_size = 4, $md_size = 3) {
		$classes = '';

		$items_wide = basel_get_wide_items_array( $different_sizes );

		if( ! in_array( $columns, array(1,2,3,4,6,12) ) ) {
			$columns = 4;
		}

		if( ! $xs_size ) {
			$xs_size = apply_filters('basel_grid_xs_default', 6);
		}

		if( $columns < 3) {
			$xs_size = 12;
			if($columns == 1)
				$sm_size = 12;
			else
				$sm_size = 6;
		}		


		$col = ' col-xs-' . $xs_size . ' col-sm-' . $sm_size . ' col-md-';

		$md_size = 12/$columns;

		// every third element make 2 times larger (for isotope grid)
		if( $different_sizes && ( in_array( $loop, $items_wide ) ) ) { // ( $loop + 1 ) % 4  == 0 || 
			$md_size *= 2;
		}

		$classes .= $col . $md_size;

		if($loop > 0) {
			if ( 0 == ( $loop - 1 ) % $columns || 1 == $columns )
				$classes .= ' first ';
			if ( 0 == $loop % $columns )
				$classes .= ' last ';
		}

		return $classes;
	}
}

if( ! function_exists( 'basel_get_wide_items_array' ) ) {
	function basel_get_wide_items_array( $different_sizes = false ){
		$items_wide = apply_filters( 'basel_wide_items', array( 5, 6, 7, 8, 13, 14 ) );

		if( is_array( $different_sizes ) ) {
			$items_wide = apply_filters( 'basel_wide_items', $different_sizes );
		}

		return $items_wide;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Function to generate clear elements <div class="clear"></div>
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_grid_clear' )) {
	function basel_get_grid_clear($loop = 0, $columns = 4) {
		$output = '';

		if( ! in_array( $columns, array(1,2,3,4,6,12) ) ) {
			$columns = 4;
		}

		if( $columns < 3) {
			if( 0 == $loop % 1 ) {
				$output .= '<div class="clearfix visible-xs-block"></div>';
			}

			if( 0 == $loop % 2 && $columns != 1) {
				$output .= '<div class="clearfix visible-sm-block"></div>';
			}
		} else {
			if( 0 == $loop % 2 ) {
				$output .= '<div class="clearfix visible-xs-block"></div>';
			}

			if( 0 == $loop % 3 ) {
				$output .= '<div class="clearfix visible-sm-block"></div>';
			}
		}

		if( 0 == $loop % $columns ) {
			$output .= '<div class="clearfix visible-md-block visible-lg-block"></div>';
		}

		return $output;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Do we need to use new version of terms meta data
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_new_meta' ) ) {
	function basel_new_meta() {
		return apply_filters( 'basel_new_meta', get_option( 'basel_new_meta', false ) );
	}
}


if( ! function_exists( 'basel_get_current_term_id' ) ) {
	/**
	 * FIX CMB2 bug
	 */
	function basel_get_current_term_id() {
		return isset( $_REQUEST['tag_ID'] ) ? $_REQUEST['tag_ID'] : 0;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Basel Related product count
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_related_count' ) ) {
	add_filter( 'woocommerce_output_related_products_args', 'basel_related_count' );
	  function basel_related_count() {
		$args['posts_per_page'] = 8;
		if  ( basel_get_opt( 'related_product_count' ) ) $args['posts_per_page'] = basel_get_opt( 'related_product_count' );
		return $args;
	}
}
/**
 * ------------------------------------------------------------------------------------------------
 * Basel product label
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_product_label' ) ) {
	function basel_product_label() {
		global $product;

		$output = array();
		
		$product_attributes = basel_get_product_attributes_label();
		$percentage_label = basel_get_opt( 'percentage_label' );

		if ( $product->is_on_sale() ) {

			$percentage = '';

			if ( $product->get_type() == 'variable' && $percentage_label ) {

				$available_variations = $product->get_variation_prices();
				$max_percentage = 0;

				foreach( $available_variations['regular_price'] as $key => $regular_price ) {
					$sale_price = $available_variations['sale_price'][$key];

					if ( $sale_price < $regular_price ) {
						$percentage = round( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

						if ( $percentage > $max_percentage ) {
							$max_percentage = $percentage;
						}
					}
				}

				$percentage = $max_percentage;
			} elseif ( $product->get_type() == 'simple' || $product->get_type() == 'external' && $percentage_label ) {
				$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
			}

			if ( $percentage ) {
				$output[] = '<span class="onsale product-label">-' . $percentage . '%' . '</span>';
			}else{
				$output[] = '<span class="onsale product-label">' . esc_html__( 'Sale', 'basel' ) . '</span>';
			}
		}
		
		if( !$product->is_in_stock() && !is_product() ){
			$output[] = '<span class="out-of-stock product-label">' . esc_html__( 'Sold out', 'basel' ) . '</span>';
		}

		if ( $product->is_featured() && basel_get_opt( 'hot_label' ) ) {
			$output[] = '<span class="featured product-label">' . esc_html__( 'Hot', 'basel' ) . '</span>';
		}
		
		if ( get_post_meta( get_the_ID(), '_basel_new_label', true ) && basel_get_opt( 'new_label' ) ) {
			$output[] = '<span class="new product-label">' . esc_html__( 'New', 'basel' ) . '</span>';
		}
		
		if ( $product_attributes ) {
			foreach ( $product_attributes as $attribute ) {
				$output[] = $attribute;
			}
		}
		
		if ( $output ) {
			echo '<div class="product-labels labels-' . basel_get_opt( 'label_shape' ) . '">' . implode( '', $output ) . '</div>';
		}
	}
}
add_filter( 'woocommerce_sale_flash', 'basel_product_label', 10 );

/**
 * ------------------------------------------------------------------------------------------------
 * AJAX add to cart for all product types
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_ajax_add_to_cart' ) ) {
	function basel_ajax_add_to_cart() {

		// Get messages
		ob_start();

		wc_print_notices();

		$notices = ob_get_clean();


		// Get mini cart
		ob_start();

		woocommerce_mini_cart();

		$mini_cart = ob_get_clean();

		// Fragments and mini cart are returned
		$data = array(
			'notices' => $notices,
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
		);

		wp_send_json( $data );

		die();
	}
}

add_action( 'wp_ajax_basel_ajax_add_to_cart', 'basel_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_basel_ajax_add_to_cart', 'basel_ajax_add_to_cart' );

/**
 * ------------------------------------------------------------------------------------------------
 * Basel my account links
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_my_account_links' ) ) {
	function basel_my_account_links() {
		if ( !basel_get_opt( 'my_account_links' ) ) return;
		?>
		<div class="basel-my-account-links">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<div class="<?php echo $endpoint; ?>-link">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
				</div>
			<?php endforeach; ?>
			<?php if ( class_exists( 'YITH_WCWL' ) && basel_get_opt( 'my_account_wishlist' )  ): ?>
				<?php $wishlist_page_id = yith_wcwl_object_id( get_option( 'yith_wcwl_wishlist_page_id' ) ); ?>
				<div class="wishlist-link">
					<a href="<?php echo YITH_WCWL()->get_wishlist_url(); ?>"><?php echo get_the_title( $wishlist_page_id ); ?></a></li>
				</div>
			<?php endif; ?>
				<div class="logout-link">
					<a href="<?php echo wc_get_account_endpoint_url( 'customer-logout' ); ?>"><?php echo esc_html__( 'Logout', 'basel' ); ?></a>
				</div>
		</div>
		<?php
	}
	add_action( 'woocommerce_account_dashboard', 'basel_my_account_links', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Basel my account remove logout link
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_remove_my_account_logout' ) ) {
	function basel_remove_my_account_logout( $items ) {
		unset( $items['customer-logout'] );
		return $items;
	}
	add_filter( 'woocommerce_account_menu_items', 'basel_remove_my_account_logout', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Basel open wrapper in wishlist template
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_my_account_wishlist_start' ) ) {
	function basel_my_account_wishlist_start(){
		?>
		<?php if ( is_user_logged_in() || !basel_get_opt( 'my_account_wishlist' ) ): ?>
			<div class="woocommerce">
				<div class="woocommerce-my-account-wrapper">
		<?php else: ?>
			<div class="wishlist-wrapper">
		<?php endif; ?>
		
		<?php
	}
	add_action( 'yith_wcwl_before_wishlist_form', 'basel_my_account_wishlist_start', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Basel added my account navigation
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_my_account_wishlist_add_nav' ) ) {
	function basel_my_account_wishlist_add_nav(){
		if ( !is_user_logged_in() || !basel_get_opt( 'my_account_wishlist' ) ) return;
		$sidebar_name = 'sidebar-my-account';
		?>
			<div class="basel-my-account-sidebar">
				<?php if ( !function_exists( 'basel_my_account_title' ) ): ?>
					<h3 class="woocommerce-MyAccount-title entry-title"><?php echo esc_html__( 'My account', 'basel' ); ?></h3>
				<?php endif; ?>
				<?php wc_get_template('myaccount/navigation.php'); ?>
				<?php if ( is_active_sidebar( $sidebar_name ) ): ?>
					<aside class="sidebar-container" role="complementary">
						<div class="sidebar-inner">
							<div class="widget-area">
								<?php dynamic_sidebar( $sidebar_name ); ?>
							</div><!-- .widget-area -->
						</div><!-- .sidebar-inner -->
					</aside><!-- .sidebar-container -->
				<?php endif; ?>
			</div><!-- .basel-my-account-sidebar" -->
			
			<div class="woocommerce-MyAccount-content">
		<?php
	}
	add_action( 'yith_wcwl_before_wishlist_form', 'basel_my_account_wishlist_add_nav', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Basel end wrapper in wishlist template
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_my_account_wishlist_end' ) ) {
	function basel_my_account_wishlist_end(){
		?>
		<?php if ( is_user_logged_in() || !basel_get_opt( 'my_account_wishlist' ) ): ?>
					</div><!-- .woocommerce-MyAccount-content -->
				</div><!-- .woocommerce-my-account-wrapper -->
			</div><!-- .woocommerce -->
		<?php else: ?>
			</div><!-- .wishlist-wrapper -->
		<?php endif; ?>

		<?php
	}
	add_action( 'yith_wcwl_after_wishlist_form', 'basel_my_account_wishlist_end', 10 );	
}

/**
 * ------------------------------------------------------------------------------------------------
 * My account wrapper
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_my_account_wrapp_start' ) ) {
	function basel_my_account_wrapp_start(){
		echo '<div class="woocommerce-my-account-wrapper">';
	}
	add_action( 'woocommerce_account_navigation', 'basel_my_account_wrapp_start', 1 );
}

if( ! function_exists( 'basel_my_account_wrapp_end' ) ) {
	function basel_my_account_wrapp_end(){
		echo '</div><!-- .woocommerce-my-account-wrapper -->';
	}
	add_action( 'woocommerce_account_content', 'basel_my_account_wrapp_end', 10000 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Mini cart buttons
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_mini_cart_view_cart_btn' ) ) {
	function basel_mini_cart_view_cart_btn(){
		echo '<a href="' . esc_url( wc_get_cart_url() ) . '" class="button btn-cart wc-forward">' . esc_html__( 'View cart', 'woocommerce' ) . '</a>';
	}
	remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
	add_action( 'woocommerce_widget_shopping_cart_buttons', 'basel_mini_cart_view_cart_btn', 10 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Dokan compatibility
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_dokan_edit_product_wrap_start' ) ) {
	function basel_dokan_edit_product_wrap_start(){
		echo '<div class="site-content col-sm-12" role="main">';
	}
	add_action( 'dokan_dashboard_wrap_before', 'basel_dokan_edit_product_wrap_start', 10 );
}

if( ! function_exists( 'basel_dokan_edit_product_wrap_end' ) ) {
	function basel_dokan_edit_product_wrap_end(){
		echo '</div>';
	}
	add_action( 'dokan_dashboard_wrap_after', 'basel_dokan_edit_product_wrap_end', 10 );
}
/**
 * ------------------------------------------------------------------------------------------------
 * Add to Quote Plugin (YITH)
 * ------------------------------------------------------------------------------------------------
 */
if ( function_exists( 'YITH_YWRAQ_Frontend' ) ) {
	remove_action( 'woocommerce_before_single_product', array( YITH_YWRAQ_Frontend(), 'show_button_single_page' ) );

	if( ! function_exists( 'basel_show_YWRAQ_button_single_page' ) ) {
		function basel_show_YWRAQ_button_single_page(){
			global $product;

		    if( ! $product ){
			    global  $post;
			    if (  ! $post || ! is_object( $post ) || ! is_singular() ) {
				    return;
			    }
			    $product = wc_get_product( $post->ID);
		    }

		    if( get_option('ywraq_show_button_near_add_to_cart','no') == 'yes' && $product->is_in_stock() && $product->get_price() !== '' ){
			    if( $product->product_type == 'variable'  ){
				    add_action( 'woocommerce_after_single_variation', array(  YITH_YWRAQ_Frontend(), 'add_button_single_page' ),30 );
			    }else{
				    add_action( 'woocommerce_after_add_to_cart_button', array(  YITH_YWRAQ_Frontend(), 'add_button_single_page' ),15 );
			    }
		    }else{
			    add_action( 'woocommerce_single_product_summary', array( YITH_YWRAQ_Frontend(), 'add_button_single_page' ), 30 );
		    }

		}

		add_action( 'woocommerce_before_single_product', 'basel_show_YWRAQ_button_single_page', 35 );
	}
}


/**
 * ------------------------------------------------------------------------------------------------
 * Before add to cart text area
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_before_add_to_cart_area' ) ) {
	function basel_before_add_to_cart_area(){
		$content = basel_get_opt( 'content_before_add_to_cart' );
		if ( empty( $content ) ) return;
		echo '<div class="basel-before-add-to-cart">' . do_shortcode( $content ) . '</div>';
	}
	add_action( 'woocommerce_single_product_summary', 'basel_before_add_to_cart_area', 25 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * After add to cart text area
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_after_add_to_cart_area' ) ) {
	function basel_after_add_to_cart_area(){
		$content = basel_get_opt( 'content_after_add_to_cart' );
		if ( empty( $content ) ) return;
		echo '<div class="basel-after-add-to-cart">' . do_shortcode( $content ) . '</div>';
	}
	add_action( 'woocommerce_single_product_summary', 'basel_after_add_to_cart_area', 31 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Attribute on product element
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_get_product_attributes_label' ) ) {
	function basel_get_product_attributes_label(){
		global $product;
		$attributes = $product->get_attributes();
		$output = array();
		foreach ( $attributes as $attribute ) {
			if ( !isset( $attribute['name'] ) ) continue;
		    $show_attr_on_product = basel_wc_get_attribute_term( $attribute['name'], 'show_on_product' );
			if ( $show_attr_on_product == 'on' ) {
				$terms = wc_get_product_terms( $product->get_id(), $attribute['name'], array( 'fields' => 'all' ) );
				foreach ( $terms as $term ) {
					$content = esc_attr( $term->name );
					$classes = 'label-term-' . $term->slug;
					$classes .= ' label-attribute-' . $attribute['name'];
					
					$image = basel_tax_data( $term->taxonomy, $term->term_id, 'image' );
					if ( $image ) {
						$classes .= ' label-with-img';
						$content = '<img src="' . esc_url( $image ) . '" title="' . esc_attr( $term->slug ) . '" alt="' . esc_attr( $term->slug ) . '" />';
					}
					
					$output[] = '<span class="attribute-label product-label ' . esc_attr( $classes ) . '">'. $content .'</span>';
				}
			}
		}
		return $output;
	}
}
/**
 * ------------------------------------------------------------------------------------------------
 * Clear all filters button
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_clear_filters_btn' ) ) {
	function basel_clear_filters_btn() {
		$url = $_SERVER['REQUEST_URI'];
		$filters = array( 'filter_', 'min_price', 'max_price', 'product_visibility', 'stock', 'onsales' );
		$need_clear = false;
			
		foreach ( $filters as $filter )
			if ( strpos( $url, $filter ) ) $need_clear = true;	
			
		if ( $need_clear ) {
			$reset_url = strtok( $url, '?' );
			if ( isset( $_GET['post_type'] ) ) $reset_url = add_query_arg( 'post_type', wc_clean( wp_unslash( $_GET['post_type'] ) ), $reset_url );
			?>
				<div class="basel-clear-filters-wrapp">
					<a class="basel-clear-filters" href="<?php echo esc_url( $reset_url ); ?>"><?php echo esc_html__( 'Clear filters', 'basel' ); ?></a>
				</div>
			<?php
		}
	}
	add_action( 'basel_before_active_filters_widgets', 'basel_clear_filters_btn' );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Off canvas sidebar open btn
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_show_sidebar_btn' ) ) {
	
	add_action( 'woocommerce_before_shop_loop', 'basel_show_sidebar_btn', 25 );

	function basel_show_sidebar_btn() {
		if ( wc_get_loop_prop( 'is_shortcode' ) || ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) return;
		
		?>
			<div class="basel-show-sidebar-btn">
				<span class="basel-side-bar-icon"></span>
				<span><?php esc_html_e( 'Show sidebar', 'basel' ); ?></span>
			</div>
		<?php

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Print shop page css from vc elements
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_shop_vc_css' ) ) {
	function basel_shop_vc_css() {
		if ( ! function_exists( 'wc_get_page_id' ) ) return;
		$shop_custom_css = get_post_meta( wc_get_page_id( 'shop' ), '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $shop_custom_css ) ) {
			?>
				<style type="text/css" data-type="vc_shortcodes-custom-css"><?php echo $shop_custom_css; ?></style>
			<?php
		}
	}
	add_action( 'wp_head', 'basel_shop_vc_css', 1000 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Brand image
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_brand' ) ) {
	function basel_product_brand() {
		global $product;
		$attr = basel_get_opt( 'brands_attribute' );
		if( ! $attr || ! basel_get_opt( 'product_page_brand' ) ) return;

		$attributes = $product->get_attributes();

		if( ! isset( $attributes[ $attr ] ) || empty( $attributes[ $attr ] ) ) return;

		$brands = wc_get_product_terms( $product->get_id(), $attr, array( 'fields' => 'all' ) );

		if( empty( $brands ) ) return;

		if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
			$link = home_url();
		} else {
			$link = get_post_type_archive_link( 'product' );
		}

		echo '<div class="basel-product-brands">';

		foreach ($brands as $brand) {
			$image = basel_tax_data( $brand->taxonomy, $brand->term_id, 'image' );
			$filter_name    = 'filter_' . sanitize_title( str_replace( 'pa_', '', $attr ) );

			$attr_link = add_query_arg( $filter_name, $brand->slug, $link );

			if( ! empty( $image ) ) {
				echo '<div class="basel-product-brand">';
					echo '<a href="' . esc_url( $attr_link ) . '"><img src="' . esc_url( $image ) . '" title="' . esc_attr( $brand->slug ) . '" alt="' . esc_attr( $brand->slug ) . '" /></a>';
				echo '</div>';
			}

		}

		echo '</div>';

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Add product brand tab to the single product page
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_brand_tab' ) ) {
	function basel_product_brand_tab( $tabs ) {
		global $product;
		
		$show_tab = false;
		$brand_title = 'brand';
		$brand_info = wc_get_product_terms( $product->get_id(), basel_get_opt( 'brands_attribute' ), array( 'fields' => 'all' ) );
		if ( !isset( $brand_info[0] ) ) return $tabs;
		
		if ( $brand_info[0]->description ) $show_tab = true;
		if ( basel_get_opt( 'brand_tab_name' ) ) $brand_title = $brand_info[0]->name;
		
		if ( $show_tab ) {
			$tabs['brand_tab'] = array(
				'title' 	=> sprintf( esc_html__( 'About %s', 'basel' ), $brand_title ),
				'priority' 	=> 50,
				'callback' 	=> 'basel_product_brand_tab_content'
			);
		}

		return $tabs;
	}
}

if( ! function_exists( 'basel_product_brand_tab_content' ) ) {
	function basel_product_brand_tab_content() {
		global $product;
		$attr = basel_get_opt( 'brands_attribute' );
		if( ! $attr || ! basel_get_opt( 'product_page_brand' ) ) return;

		$attributes = $product->get_attributes();

		if( ! isset( $attributes[ $attr ] ) || empty( $attributes[ $attr ] ) ) return;

		$brands = wc_get_product_terms( $product->get_id(), $attr, array( 'fields' => 'slugs' ) );

		if( empty( $brands ) ) return;

		foreach ($brands as $id => $slug) {
			echo '<div class="basel-product-brand-description">';
				$brand = get_term_by('slug', $slug, $attr);
				echo do_shortcode( $brand->description );
			echo '</div>';
		}

	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Show product brand
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_product_brands_links' ) ) {
	function basel_product_brands_links() {
		global $product;
		$brand_option = basel_get_opt( 'brands_attribute' );
		$brands = wc_get_product_terms( $product->get_id(), $brand_option, array( 'fields' => 'all' ) );

		if( ! basel_get_opt( 'brands_under_title' ) || empty( $brands ) ) return;

		$link = ( defined( 'SHOP_IS_ON_FRONT' ) ) ? home_url() : get_post_type_archive_link( 'product' );

		echo '<div class="basel-product-brands-links">';

		foreach ( $brands as $brand ) {
			$filter_name = 'filter_' . sanitize_title( str_replace( 'pa_', '', $brand_option ) );
			$attr_link = add_query_arg( $filter_name, $brand->slug, $link );

			$sep = ', ';
			if ( end( $brands ) == $brand ) $sep = '';

			echo '<a href="' . esc_url( $attr_link ) . '">' . $brand->name . '</a>' . $sep;
		}

		echo '</div>';
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Reset loop
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_reset_loop' ) ) {
	function basel_reset_loop() {
		unset( $GLOBALS['basel_loop'] );
		basel_setup_loop();
	}
	add_action( 'woocommerce_after_shop_loop', 'basel_reset_loop', 1000 );
	add_action( 'loop_end', 'basel_reset_loop', 1000 );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Get loop prop
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_loop_prop' ) ) {
	function basel_loop_prop( $prop, $default = '' ) {
		basel_setup_loop();
		
		return isset( $GLOBALS['basel_loop'], $GLOBALS['basel_loop'][ $prop ] ) ? $GLOBALS['basel_loop'][ $prop ] : $default;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Set loop prop
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_set_loop_prop' ) ) {
	function basel_set_loop_prop( $prop, $value = '' ) {
		if ( ! isset( $GLOBALS['basel_loop'] ) ) wc_setup_loop();

		$GLOBALS['basel_loop'][ $prop ] = $value;
	}
}

/**
 * ------------------------------------------------------------------------------------------------
 * Setup loop
 * ------------------------------------------------------------------------------------------------
 */
 
if( ! function_exists( 'basel_setup_loop' ) ) {
	function basel_setup_loop( $args = array() ) {
		if ( isset( $GLOBALS['basel_loop'] ) ) return; // If the loop has already been setup, bail.
		
		$default_args = array(
			'products_different_sizes' 	=> basel_get_opt( 'products_different_sizes' ),
			'product_categories_design' => basel_get_opt( 'categories_design' ),
			'products_columns' 		    => ( basel_get_opt( 'per_row_columns_selector' ) ) ? apply_filters( 'loop_shop_columns', basel_get_products_columns_per_row() ) : basel_get_opt( 'products_columns' ),
			'product_categories_style'  => false,		
			'product_hover' 			=> basel_get_opt( 'products_hover' ),
			'products_view' 			=> basel_get_shop_view(),
			'products_masonry' 			=> basel_get_opt( 'products_masonry' ),
			
			'timer' 					=> basel_get_opt( 'shop_countdown' ),
			'swatches'					=> false,
			
			'is_slider' 				=> false,
			'is_shortcode' 				=> false,
			'is_quick_view' 			=> false,
			
			'woocommerce_loop' 			=> 0,
			'basel_loop' 				=> 0,
			
			'parts_media' 				=> true,
			'parts_title' 				=> true,
			'parts_meta' 				=> true,
			'parts_text' 				=> true,
			'parts_btn' 				=> true,
			
			'blog_design' 				=> basel_get_opt( 'blog_design' ),
			'blog_type' 				=> false,
			'blog_columns' 				=> basel_get_opt( 'blog_columns' ),
			'img_size' 					=> false,
			'double_size' 				=> false,
		);
		
		$GLOBALS['basel_loop'] = wp_parse_args( $args, $default_args );
	}
	add_action( 'woocommerce_before_shop_loop', 'basel_setup_loop' );
	add_action( 'wp', 'basel_setup_loop' );
	add_action( 'loop_start', 'basel_setup_loop' );
}
