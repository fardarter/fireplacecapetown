<?php
/**
 * The Footer Sidebar
 */

if ( ! is_active_sidebar( 'footer-1' ) ) {
	return;
}

$footer_layout = basel_get_opt( 'footer-layout' );

$footer_config = basel_get_footer_config( $footer_layout );

if( count( $footer_config['cols'] ) > 0 ) {
	?>
	<div class="container main-footer">
		<aside class="footer-sidebar widget-area row" role="complementary">
			<?php
				foreach ( $footer_config['cols'] as $key => $columns ) {
					$index = $key + 1;
					?>
						<div class="footer-column footer-column-<?php echo esc_attr( $index ); ?> <?php echo esc_attr( $columns ); ?>">
							<?php dynamic_sidebar( 'footer-' . $index ); ?>
						</div>
						<?php if ( isset( $footer_config['clears'][$index] ) ): ?>
							<div class="clearfix visible-<?php echo esc_attr( $footer_config['clears'][$index] ); ?>-block"></div>
						<?php endif ?>
					<?php
				}
			?>
		</aside><!-- .footer-sidebar -->
	</div>
	<?php
}

?>

