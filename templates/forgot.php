<?php
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Form value repopulation only, nonce field added on line 15
if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}
?>

<form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url( home_url( '/forgot/' ) ); ?>" method="post">
	<?php dam_spam_show_error(); ?>
	<p><?php esc_html_e( 'Please enter your username or email address. You will receive a link to create a new password via email.', 'dam-spam' ); ?></p>
	<p>
		<label for="user_login"><?php esc_html_e( 'Username or Email Address', 'dam-spam' ); ?></label>
		<input type="text" name="user_login" id="user_login" class="input" value="<?php echo ( isset( $_POST['user_login'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) ) : '' ); ?>" size="20" autocapitalize="off">
	</p>
	<?php wp_nonce_field( 'dam_spam_forgot_password', 'dam_spam_forgot_nonce' ); ?>
	<input type="hidden" name="redirect_to" value="">
	<?php do_action( 'lostpassword_form' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook ?>
	<p class="submit">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_html_e( 'Get New Password', 'dam-spam' ); ?>">
	</p>
</form>

<style>
#lostpasswordform label {
	display: block;
}
#lostpasswordform .input {
	padding: 15px;
	min-width: 50%;
}
</style>