<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

$spacing = basel_get_opt( 'products_spacing' );
$class   = '';

if( basel_loop_prop( 'products_masonry' ) ) {
	$class .= ' grid-masonry';
}

if ( basel_get_shop_view() == 'list' ) {
	$class .= ' elements-list';
}else{
	$class .= ' basel-spacing-' . $spacing;
	$class .= ' products-spacing-' . $spacing;
}

$class .= ' pagination-' . basel_get_opt( 'shop_pagination' );

// fix for price filter ajax
$min_price = isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : '';
$max_price = isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : '';

?>

<div class="products elements-grid basel-products-holder <?php echo esc_attr( $class ); ?> row grid-columns-<?php echo esc_attr( basel_loop_prop( 'products_columns' ) ); ?>" data-min_price="<?php echo esc_attr( $min_price ); ?>" data-max_price="<?php echo esc_attr( $max_price ); ?>" data-source="main_loop">