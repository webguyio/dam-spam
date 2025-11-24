<?php
// phpcs:disable WordPress.Security.NonceVerification -- WordPress core login form, authentication handled by wp_signon()
if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}
?>

<form name="loginform" action="<?php echo esc_url( home_url( '/login/' ) ); ?>" method="post">
	<?php dam_spam_show_error(); ?>
	<p class="dam-spam-input-wrapper">
		<?php if ( get_option( 'dam_spam_login_type', '' ) === 'email' ): ?>
			<label for="user_login"><?php esc_html_e( 'Email Address', 'dam-spam' ); ?></label>
		<?php elseif ( get_option( 'dam_spam_login_type', '' ) === 'username' ): ?>
			<label for="user_login"><?php esc_html_e( 'Username', 'dam-spam' ); ?></label>
		<?php else: ?>
			<label for="user_login"><?php esc_html_e( 'Username or Email Address', 'dam-spam' ); ?></label>
		<?php endif; ?>
		<input type="text" name="log" id="user_login" class="input" value="" size="20">
	</p>
	<p class="dam-spam-input-wrapper">
		<label for="user_pass"><?php esc_html_e( 'Password', 'dam-spam' ); ?></label>
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
	</p>
	<?php do_action( 'login_form' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook ?>
	<p class="dam-spam-input-wrapper">
		<label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> <?php esc_html_e( 'Remember Me', 'dam-spam' ); ?></label>
	</p>
	<p class="dam-spam-submit-wrapper">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php esc_attr_e( 'Log In', 'dam-spam' ); ?>">
		<?php if ( isset( $_GET['redirect_to'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'redirect_nonce' ) ): ?>
			<input type="hidden" name="redirect_to" value="<?php echo isset( $_GET['redirect_to'] ) ? esc_url( sanitize_text_field( wp_unslash( $_GET['redirect_to'] ) ) ) : ''; ?>">
		<?php else: ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( admin_url() ); ?>">
		<?php endif; ?>
	</p>
	<p class="dam-spam-link-wrapper">
		<a href="<?php echo esc_url( home_url( '/register/' ) ); ?>"><?php esc_html_e( 'Register', 'dam-spam' ); ?></a> | <a href="<?php echo esc_url( home_url( '/forgot/' ) ); ?>"><?php esc_html_e( 'Forgot Password?', 'dam-spam' ); ?></a>
	</p>
</form>

<style>
p.dam-spam-input-wrapper label {
	display: block;
}
p.dam-spam-input-wrapper .input {
	padding: 15px;
	min-width: 50%;
}
</style>