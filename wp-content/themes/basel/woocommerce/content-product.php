<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;

$is_slider 		   = basel_loop_prop( 'is_slider' );
$is_shortcode 	   = basel_loop_prop( 'is_shortcode' );
$different_sizes   = basel_loop_prop( 'products_different_sizes' );
$hover 			   = basel_loop_prop( 'product_hover' );
$current_view      = basel_loop_prop( 'products_view' );
$shop_view 		   = basel_get_opt( 'shop_view' );

// Ensure visibility
if ( ! $product || ( ! $is_slider && ! $product->is_visible() ) ) return;

// Increase loop count
basel_set_loop_prop( 'woocommerce_loop', basel_loop_prop( 'woocommerce_loop' ) + 1 );
$woocommerce_loop = basel_loop_prop( 'woocommerce_loop' );

// Extra post classes
$classes = array( 'product-grid-item' );

//Grid or list style
if ( $shop_view == 'grid' || $shop_view == 'list' )	$current_view = $shop_view;

if ( $is_slider ) $current_view = 'grid';

if ( $is_shortcode ) $current_view = basel_loop_prop( 'products_view' );

if( $current_view == 'list' ){
	$hover = 'list';
	basel_set_loop_prop( 'products_columns', 1 );
	$classes[] = 'product-list-item'; 
}else{
	$classes[] = 'basel-hover-' . $hover; 
}

$classes[] = 'product'; 

$products_columns = basel_loop_prop( 'products_columns' );

if( $different_sizes && in_array( $woocommerce_loop, basel_get_wide_items_array( $different_sizes ) ) ){
	basel_set_loop_prop( 'double_size', true );
} 

if( ! $is_slider ){
	$classes[] = basel_get_grid_el_class( $woocommerce_loop , $products_columns, $different_sizes );
}else{
	$classes[] = 'product-in-carousel';
}

?>
	<div <?php post_class( $classes ); ?> data-loop="<?php echo esc_attr( $woocommerce_loop ); ?>" data-id="<?php echo esc_attr( $product->get_id() ); ?>">

		<?php wc_get_template_part( 'content', 'product-' . $hover ); ?>

	</div>
<?php 

if( ! $is_slider && ! basel_loop_prop( 'products_masonry' ) && $current_view != 'list' ){
	echo basel_get_grid_clear( $woocommerce_loop, $products_columns );
}