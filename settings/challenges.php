<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$now	 = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = ds_get_options();
extract( $options );
$nonce   = '';
$msg	 = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = isset( $_POST['ds_control'] ) ? sanitize_text_field( wp_unslash( $_POST['ds_control'] ) ) : '';
}

if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'action', $_POST ) ) {
		$optionlist = array( 'redir', 'notify', 'email_request', 'allow_list_request' );
		foreach ( $optionlist as $check ) {
			$v = 'N';
			if ( array_key_exists( $check, $_POST ) ) {
				$v = isset( $_POST[$check] ) ? sanitize_text_field( wp_unslash( $_POST[$check] ) ) : '';
				if ( $v != 'Y' ) {
					$v = 'N';
				}
			}
			$options[$check] = $v;
		}
		if ( array_key_exists( 'redirect_url', $_POST ) ) {
			$redirect_url = isset( $_POST['redirect_url'] ) ? trim( esc_url( sanitize_text_field( wp_unslash( $_POST['redirect_url'] ) ) ) ) : '';
			$options['redirect_url'] = $redirect_url;
		}
		if ( array_key_exists( 'allow_list_request_email', $_POST ) ) {
			$allow_list_request_email = isset( $_POST['allow_list_request_email'] ) ? trim( sanitize_email( wp_unslash( $_POST['allow_list_request_email'] ) ) ) : '';
			$options['allow_list_request_email'] = $allow_list_request_email;
		}
		if ( array_key_exists( 'reject_message', $_POST ) ) {
			$reject_message = isset( $_POST['reject_message'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['reject_message'] ) ) ) : '';
			$options['reject_message'] = $reject_message;
		}
		if ( array_key_exists( 'check_captcha', $_POST ) ) {
			$check_captcha = isset( $_POST['check_captcha'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['check_captcha'] ) ) ) : '';
			$options['check_captcha'] = $check_captcha;
		}
		if ( array_key_exists( 'form_captcha_login', $_POST ) and ( $check_captcha == 'G' or $check_captcha == 'H' or $check_captcha == 'S' ) ) {
			$form_captcha_login = isset( $_POST['form_captcha_login'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['form_captcha_login'] ) ) ) : '';
			$options['form_captcha_login'] = $form_captcha_login;
		} else {
			$options['form_captcha_login'] = 'N';
		}
		if ( array_key_exists( 'form_captcha_registration', $_POST ) and ( $check_captcha == 'G' or $check_captcha == 'H' or $check_captcha == 'S' ) ) {
			$form_captcha_login = isset( $_POST['form_captcha_registration'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['form_captcha_registration'] ) ) ) : '';
			$options['form_captcha_registration'] = $form_captcha_login;
		} else {
			$options['form_captcha_registration'] = 'N';
		}
		if ( array_key_exists( 'form_captcha_comment', $_POST ) and ( $check_captcha == 'G' or $check_captcha == 'H' or $check_captcha == 'S' ) ) {
			$form_captcha_login = isset( $_POST['form_captcha_comment'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['form_captcha_comment'] ) ) ) : '';
			$options['form_captcha_comment'] = $form_captcha_login;
		} else {
			$options['form_captcha_comment'] = 'N';
		}
		if ( array_key_exists( 'recaptchaapisecret', $_POST ) ) {
			$recaptchaapisecret = isset( $_POST['recaptchaapisecret'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptchaapisecret'] ) ) : '';
			$options['recaptchaapisecret'] = $recaptchaapisecret;
		}
		if ( array_key_exists( 'recaptchaapisite', $_POST ) ) {
			$recaptchaapisite = isset( $_POST['recaptchaapisite'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptchaapisite'] ) ) : '';
			$options['recaptchaapisite'] = $recaptchaapisite;
		}
		if ( array_key_exists( 'hcaptchaapisecret', $_POST ) ) {
			$hcaptchaapisecret = isset( $_POST['hcaptchaapisecret'] ) ? sanitize_text_field( wp_unslash( $_POST['hcaptchaapisecret'] ) ) : '';
			$options['hcaptchaapisecret'] = $hcaptchaapisecret;
		}
		if ( array_key_exists( 'hcaptchaapisite', $_POST ) ) {
			$hcaptchaapisite = isset( $_POST['hcaptchaapisite'] ) ? sanitize_text_field( wp_unslash( $_POST['hcaptchaapisite'] ) ) : '';
			$options['hcaptchaapisite'] = $hcaptchaapisite;
		}
		if ( $check_captcha == 'G' && ( $recaptchaapisecret == '' || $recaptchaapisite == '' ) ) {
			$check_captcha = 'Y';
			$options['check_captcha'] = $check_captcha;
			$msg = esc_html__( 'You cannot use Google reCAPTCHA unless you have entered an API key.', 'dam-spam' );
		}
		if ( $check_captcha == 'H' && ( $hcaptchaapisecret == '' || $hcaptchaapisite == '' ) ) {
			$check_captcha = 'Y';
			$options['check_captcha'] = $check_captcha;
			$msg = esc_html__( 'You cannot use hCaptcha unless you have entered an API key.', 'dam-spam' );
		}
		ds_set_options( $options );
		extract( $options );
	}
	$update = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
 }

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Challenges — Dam Spam', 'dam-spam' ); ?></h1>
	<?php if ( !empty( $update ) ) {
		echo wp_kses_post( $update );
	} ?>
	<?php if ( !empty( $msg ) ) {
		echo '<span style="color:red;font-size:1.2em">' . esc_html( $msg ) . '</span>';
	} ?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="action" value="update challenge">
		<br>
		<div class="mainsection"><?php esc_html_e( 'Access Blocked Message', 'dam-spam' ); ?></div>
		<textarea id="reject_message" name="reject_message" cols="40" rows="5"><?php echo wp_kses_post( $reject_message ); ?></textarea>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Routing and Notifications', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="redir">
				<input class="ds_toggle" type="checkbox" id="redir" name="redir" value="Y" onclick="ds_show_option()" <?php if ( $redir == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Send Blocked Users to URL', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<span id="ds_show_option" style="display:none"><?php esc_html_e( 'URL:', 'dam-spam' ); ?>
		<input size="77" name="redirect_url" type="text" placeholder="https://example.com/" value="<?php echo esc_url( $redirect_url ); ?>"></span>
		<script>
		function ds_show_option() {
			var checkBox = document.getElementById("redir");
			var text = document.getElementById("ds_show_option");
			if (checkBox.checked == true) {
				text.style.display = "block";
			} else {
				text.style.display = "none";
			}
		}
		ds_show_option();
		</script>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="allow_list_request">
				<input class="ds_toggle" type="checkbox" id="allow_list_request" name="allow_list_request" value="Y" <?php if ( $allow_list_request == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Send Blocked Users to Allow Request Form', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="notify">
				<input class="ds_toggle" type="checkbox" id="notify" name="notify" value="Y" onclick="ds_show_notify()" <?php if ( $notify == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Email Admin for New Requests', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<span id="ds_show_notify" style="display:none"><?php esc_html_e( 'Email:', 'dam-spam' ); ?>
		<input id="ds-input" size="48" name="allow_list_request_email" type="text" value="<?php echo esc_html( $allow_list_request_email ); ?>"></span>
		<script>
		function ds_show_notify() {
			var checkBox = document.getElementById("notify");
			var text = document.getElementById("ds_show_notify");
			if (checkBox.checked == true){
				text.style.display = "block";
			} else {
				text.style.display = "none";
			}
		}
		ds_show_notify();
		</script>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="email_request">
				<input class="ds_toggle" type="checkbox" id="email_request" name="email_request" value="Y" <?php if ( $email_request == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Email Blocked Users when They\'re Allowed', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php esc_html_e( 'CAPTCHA', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'Second Chance Challenge for Blocked Users', 'dam-spam' ); ?></p>
		<p><?php esc_html_e( 'Google reCAPTCHA and hCaptcha require an API Key (entered below).', 'dam-spam' ); ?></p>
		<div>
			<?php
			if ( !empty( $msg ) ) {
				echo '<span style="color:red;font-size:1.2em">' . esc_html( $msg ) . '</span>';
			}
			?>
		</div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_captcha1">
				<input class="ds_toggle" type="radio" id="check_captcha1" name="check_captcha" value="N" <?php if ( $check_captcha == 'N' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'No CAPTCHA (default)', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_captcha2">
				<input class="ds_toggle" type="radio" id="check_captcha2" name="check_captcha" value="G" <?php if ( $check_captcha == 'G' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Google reCAPTCHA', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_captcha3">
				<input class="ds_toggle" type="radio" id="check_captcha3" name="check_captcha" value="H" <?php if ( $check_captcha == 'H' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'hCaptcha', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_captcha5">
				<input class="ds_toggle" type="radio" id="check_captcha5" name="check_captcha" value="A" <?php if ( $check_captcha == 'A' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Math Question', 'dam-spam' ); ?></small>
			</label>
		</div>
		<p><?php esc_html_e( 'Enable CAPTCHAs on common WordPress forms.', 'dam-spam' ); ?></p>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_login">
				<input class="ds_toggle" type="checkbox" id="form_captcha_login" name="form_captcha_login" value="Y" <?php if ( isset( $form_captcha_login ) and $form_captcha_login == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Login', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_registration">
				<input class="ds_toggle" type="checkbox" id="form_captcha_registration" name="form_captcha_registration" value="Y" <?php if ( isset( $form_captcha_registration ) and $form_captcha_registration == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Registration', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_comment">
				<input class="ds_toggle" type="checkbox" id="form_captcha_comment" name="form_captcha_comment" value="Y" <?php if ( isset( $form_captcha_comment ) and $form_captcha_comment == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Comment', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<br>
		<div>
			<?php esc_html_e( 'Google reCAPTCHA', 'dam-spam' ); ?><br>
			<input size="64" name="recaptchaapisite" type="text" placeholder="<?php esc_html_e( 'Site Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $recaptchaapisite ); ?>">
			<br>
			<input size="64" name="recaptchaapisecret" type="text" placeholder="<?php esc_html_e( 'Secret Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $recaptchaapisecret ); ?>">
			<br>
			<?php if ( !empty( $recaptchaapisite ) ) {
				wp_enqueue_script( 'ds-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1', array( 'strategy' => 'async', 'in_footer' => true ) );
			?>
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptchaapisite ); ?>"></div>
			<?php } ?>
			<br>
			<?php esc_html_e( 'hCaptcha', 'dam-spam' ); ?><br>
			<input size="64" name="hcaptchaapisite" type="text" placeholder="<?php esc_html_e( 'Site Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisite ); ?>">
			<br>
			<input size="64" name="hcaptchaapisecret" type="text" placeholder="<?php esc_html_e( 'Secret Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisecret ); ?>">
			<br>
			<?php if ( !empty( $hcaptchaapisite ) ) {
				wp_enqueue_script( 'ds-hcaptcha', 'https://hcaptcha.com/1/api.js', array(), '1', array( 'strategy' => 'async', 'in_footer' => true ) );
			?>
				<div class="h-captcha" data-sitekey="<?php echo esc_attr( $hcaptchaapisite ); ?>"></div>
			<?php } ?>
		</div>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
