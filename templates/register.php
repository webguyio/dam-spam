<?php
if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}
?>

<form name="registerform" id="registerform" action="<?php echo esc_url( home_url( '/register/' ) ); ?>" method="post" novalidate="novalidate">
	<?php ds_show_error(); ?>
	<p class="ds-input-wrapper">
		<label for="user_login"><?php esc_html_e( 'Username', 'dam-spam' ); ?></label>
		<input type="text" name="user_login" id="user_login" class="input" size="20" autocapitalize="off" value="<?php echo ( isset( $_POST['user_login'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '' ); ?>">
	</p>
	<p class="ds-input-wrapper">
		<label for="user_email"><?php esc_html_e( 'Email', 'dam-spam' ); ?></label>
		<input type="email" name="user_email" id="user_email" class="input" value="<?php echo ( isset( $_POST['user_email'] ) ? esc_attr( sanitize_email( wp_unslash( $_POST['user_email'] ) ) ) : '' ); ?>" size="25">
	</p>
	<p class="ds-input-wrapper url">
		<label for="user_url"><?php esc_html_e( 'Website', 'dam-spam' ); ?></label>
		<input type="url" name="user_url" id="user_url" class="input" value="https://example.com/" size="25">
	</p>
	<?php do_action( 'register_form' ); ?>
	<p id="reg_passmail"><?php esc_html_e( 'Registration confirmation will be emailed to you.', 'dam-spam' ); ?></p>
	<br class="clear">
	<input type="hidden" name="redirect_to" value="">
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_html_e( 'Register', 'dam-spam' ); ?>">
	</p>
	<p class="ds-link-wrapper">
		<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'Login', 'dam-spam' ); ?></a> | <a href="<?php echo esc_url( home_url( '/forgot/' ) ); ?>"><?php esc_html_e( 'Forgot Password?', 'dam-spam' ); ?></a>
	</p>
</form>

<style>
p.ds-input-wrapper label, #registerform label {
	display: block;
}
p.ds-input-wrapper .input, #registerform .input {
	padding: 15px;
	min-width: 50%;
}
p.ds-input-wrapper.url {
	display: none !important;
}
</style>