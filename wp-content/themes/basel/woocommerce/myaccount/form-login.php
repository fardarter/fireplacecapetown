<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$tabs = basel_get_opt( 'login_tabs' );
$reg_text = basel_get_opt( 'reg_text' );
$login_text = basel_get_opt( 'login_text' );

$class = 'basel-registration-page';

if( $tabs && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
	$class .= ' basel-register-tabs';
}

if( $tabs && get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) {
	$class .= ' basel-no-registration';
}

if( $login_text && $reg_text ) {
    $class .= ' with-login-reg-info';
}

?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="<?php echo esc_attr( $class ); ?>">

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="u-columns col2-set" id="customer_login">

	<div class="u-column1 col-1 col-login">

<?php endif; ?>

		<h2><?php _e( 'Login', 'woocommerce' ); ?></h2>

		<?php basel_login_form(); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="u-column2 col-2 col-register">

		<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

		<form method="post" class="register">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( $_POST['username'] ) : ''; ?>" />
				</p>

			<?php endif; ?>

			<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( $_POST['email'] ) : ''; ?>" />
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
				</p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<p class="woocommerce-FormRow form-row">
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<button type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

	<?php if ( $tabs ): ?>
		<div class="col-2 col-register-text">

			<span class="register-or"><?php _e('Or', 'basel') ?></span>

			<?php if ( $login_text ): ?>
				<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
			<?php else: ?>
				<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
			<?php endif ?>

			<?php if ( $login_text ): ?>
				<div class="login-info"><?php echo ( $login_text ); ?></div>
			<?php endif ?>

			<?php if ( $reg_text ): ?>
				<div class="registration-info"><?php echo ( $reg_text ); ?></div>
			<?php endif ?>

			<a href="#" class="btn btn-color-black basel-switch-to-register" data-login="<?php _e( 'Login', 'basel') ?>" data-register="<?php _e( 'Register', 'basel') ?>"><?php _e( 'Register', 'basel') ?></a>

		</div>
	<?php endif ?>
	
</div>
<?php endif; ?>

</div><!-- .basel-registration-page -->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
