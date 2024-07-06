<form name="loginform" action="<?php echo home_url( '/login/' ); ?>" method="post">
	<?php ds_show_error(); ?>
	<p class="ds-input-wrapper">
		<?php if ( get_option( 'ds_login_type', '' ) == 'email' ): ?>
			<label for="user_login"><?php _e( 'Email Address', 'dam-spam' ); ?></label>
		<?php elseif ( get_option( 'ds_login_type', '' ) == 'username' ): ?>
			<label for="user_login"><?php _e( 'Username', 'dam-spam' ); ?></label>
		<?php else: ?>
			<label for="user_login"><?php _e( 'Username or Email Address', 'dam-spam' ); ?></label>
		<?php endif; ?>
		<input type="text" name="log" id="user_login" class="input" value="" size="20">
	</p>
	<p class="ds-input-wrapper">
		<label for="user_pass"><?php _e( 'Password', 'dam-spam' ); ?></label>
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
	</p>
	<?php do_action( 'login_form' ); ?>
	<p class="ds-input-wrapper">
		<label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> <?php _e( 'Remember Me', 'dam-spam' ); ?></label>
	</p>
	<p class="ds-submit-wrapper">
		<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php esc_attr_e( 'Log In' ); ?>">
		<?php if ( isset( $_GET['redirect_to'] ) ): ?>
			<input type="hidden" name="redirect_to" value="<?php echo $_GET['redirect_to']; ?>">
		<?php else: ?>
			<input type="hidden" name="redirect_to" value="<?php echo admin_url();?>">
		<?php endif; ?>
	</p>
	<p class="ds-link-wrapper">
		<a href="<?php echo home_url( '/register/' ); ?>"><?php _e( 'Register', 'dam-spam' ); ?></a> | <a href="<?php echo home_url( '/forgot-password/' ); ?>">Forgot password?</a>
	</p>
</form>
	
<style>
p.ds-input-wrapper label {
    display: block;
}
p.ds-input-wrapper .input {
    padding: 15px;
    min-width: 50%;
}
</style>