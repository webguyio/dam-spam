<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$now	 = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = ds_get_options();
extract( $options );
// $ip = ds_get_ip();
$nonce   = '';
$msg	 = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = $_POST['ds_control'];
}

if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'action', $_POST ) ) {
		$optionlist = array( 'redir', 'notify', 'emailrequest', 'wlreq' );
		foreach ( $optionlist as $check ) {
			$v = 'N';
			if ( array_key_exists( $check, $_POST ) ) {
				$v = $_POST[$check];
				if ( $v != 'Y' ) {
					$v = 'N';
				}
			}
			$options[$check] = $v;
		}
		// other options
		if ( array_key_exists( 'redirurl', $_POST ) ) {
			$redirurl			 = esc_url( trim( $_POST['redirurl'] ) );
			$options['redirurl'] = $redirurl;
		}
		if ( array_key_exists( 'wlreqmail', $_POST ) ) {
			$wlreqmail			  = sanitize_email( trim( $_POST['wlreqmail'] ) );
			$options['wlreqmail'] = $wlreqmail;
		}
		if ( array_key_exists( 'rejectmessage', $_POST ) ) {
			$rejectmessage			  = sanitize_textarea_field( trim( $_POST['rejectmessage'] ) );
			$options['rejectmessage'] = $rejectmessage;
		}
		if ( array_key_exists( 'chkcaptcha', $_POST ) ) {
			$chkcaptcha			   = sanitize_text_field( trim( $_POST['chkcaptcha'] ) );
			$options['chkcaptcha'] = $chkcaptcha;
		}
		if ( array_key_exists( 'form_captcha_login', $_POST ) and ( $chkcaptcha == 'G' or $chkcaptcha == 'H' or $chkcaptcha == 'S' ) ) {
			$form_captcha_login			   = sanitize_text_field( trim( $_POST['form_captcha_login'] ) );
			$options['form_captcha_login'] = $form_captcha_login;
		} else {
			$options['form_captcha_login'] = 'N';
		}
		if ( array_key_exists( 'form_captcha_registration', $_POST ) and ( $chkcaptcha == 'G' or $chkcaptcha == 'H' or $chkcaptcha == 'S' ) ) {
			$form_captcha_login					  = sanitize_text_field( trim( $_POST['form_captcha_registration'] ) );
			$options['form_captcha_registration'] = $form_captcha_login;
		} else {
			$options['form_captcha_registration'] = 'N';
		}
		if ( array_key_exists( 'form_captcha_comment', $_POST ) and ( $chkcaptcha == 'G' or $chkcaptcha == 'H' or $chkcaptcha == 'S' ) ) {
			$form_captcha_login				 = sanitize_text_field( trim( $_POST['form_captcha_comment'] ) );
			$options['form_captcha_comment'] = $form_captcha_login;
		} else {
			$options['form_captcha_comment'] = 'N';
		}
		// added the API key stiff for Captchas
		if ( array_key_exists( 'recaptchaapisecret', $_POST ) ) {
			$recaptchaapisecret			   = sanitize_text_field( $_POST['recaptchaapisecret'] );
			$options['recaptchaapisecret'] = $recaptchaapisecret;
		}
		if ( array_key_exists( 'recaptchaapisite', $_POST ) ) {
			$recaptchaapisite			 = sanitize_text_field( $_POST['recaptchaapisite'] );
			$options['recaptchaapisite'] = $recaptchaapisite;
		}
		if ( array_key_exists( 'hcaptchaapisecret', $_POST ) ) {
			$hcaptchaapisecret			  = sanitize_text_field( $_POST['hcaptchaapisecret'] );
			$options['hcaptchaapisecret'] = $hcaptchaapisecret;
		}
		if ( array_key_exists( 'hcaptchaapisite', $_POST ) ) {
			$hcaptchaapisite			= sanitize_text_field( $_POST['hcaptchaapisite'] );
			$options['hcaptchaapisite'] = $hcaptchaapisite;
		}
		if ( array_key_exists( 'solvmediaapivchallenge', $_POST ) ) {
			$solvmediaapivchallenge			   = sanitize_text_field( $_POST['solvmediaapivchallenge'] );
			$options['solvmediaapivchallenge'] = $solvmediaapivchallenge;
		}
		if ( array_key_exists( 'solvmediaapiverify', $_POST ) ) {
			$solvmediaapiverify			   = sanitize_text_field( $_POST['solvmediaapiverify'] );
			$options['solvmediaapiverify'] = $solvmediaapiverify;
		}
		// validate the chkcaptcha variable
		if ( $chkcaptcha == 'G' && ( $recaptchaapisecret == '' || $recaptchaapisite == '' ) ) {
			$chkcaptcha			   = 'Y';
			$options['chkcaptcha'] = $chkcaptcha;
			$msg				   = esc_html__( 'You cannot use Google reCAPTCHA unless you have entered an API key.', 'dam-spam' );
		}
		if ( $chkcaptcha == 'H' && ( $hcaptchaapisecret == '' || $hcaptchaapisite == '' ) ) {
			$chkcaptcha			   = 'Y';
			$options['chkcaptcha'] = $chkcaptcha;
			$msg				   = esc_html__( 'You cannot use hCaptcha unless you have entered an API key.', 'dam-spam' );
		}
		if ( $chkcaptcha == 'S' && ( $solvmediaapivchallenge == '' || $solvmediaapiverify == '' ) ) {
			$chkcaptcha			   = 'Y';
			$options['chkcaptcha'] = $chkcaptcha;
			$msg				   = esc_html__( 'You cannot use Solve Media CAPTCHA unless you have entered an API key.', 'dam-spam' );
		}
		ds_set_options( $options );
		extract( $options ); // extract again to get the new options
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
		<textarea id="rejectmessage" name="rejectmessage" cols="40" rows="5"><?php echo wp_kses_post( $rejectmessage ); ?></textarea>
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
		<input size="77" name="redirurl" type="text" placeholder="https://example.com/" value="<?php echo esc_url( $redirurl ); ?>"></span>
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
			<label class="ds-subhead" for="wlreq">
				<input class="ds_toggle" type="checkbox" id="wlreq" name="wlreq" value="Y" <?php if ( $wlreq == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
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
		<input id="dsinput" size="48" name="wlreqmail" type="text" value="<?php echo esc_html( $wlreqmail ); ?>"></span>
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
			<label class="ds-subhead" for="emailrequest">
				<input class="ds_toggle" type="checkbox" id="emailrequest" name="emailrequest" value="Y" <?php if ( $emailrequest == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Email Blocked Users when They\'re Allowed', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php esc_html_e( 'CAPTCHA', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'Second Chance Challenge for Blocked Users', 'dam-spam' ); ?></p>
		<p><?php esc_html_e( 'Google reCAPTCHA, hCaptcha, and Solve Media CAPTCHA Require an API Key (entered below).', 'dam-spam' ); ?></p>
		<div>
			<?php
			if ( !empty( $msg ) ) {
				echo '<span style="color:red;font-size:1.2em">' . esc_html( $msg ) . '</span>';
			}
			?>
		</div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha1">
				<input class="ds_toggle" type="radio" id="chkcaptcha1" name="chkcaptcha" value="N" <?php if ( $chkcaptcha == 'N' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'No CAPTCHA (default)', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha2">
				<input class="ds_toggle" type="radio" id="chkcaptcha2" name="chkcaptcha" value="G" <?php if ( $chkcaptcha == 'G' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Google reCAPTCHA', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha3">
				<input class="ds_toggle" type="radio" id="chkcaptcha3" name="chkcaptcha" value="H" <?php if ( $chkcaptcha == 'H' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'hCaptcha', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha4">
				<input class="ds_toggle" type="radio" id="chkcaptcha4" name="chkcaptcha" value="S" <?php if ( $chkcaptcha == 'S' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Solve Media CAPTCHA', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha5">
				<input class="ds_toggle" type="radio" id="chkcaptcha5" name="chkcaptcha" value="A" <?php if ( $chkcaptcha == 'A' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
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
			<?php if ( !empty( $recaptchaapisite ) ) { ?>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptchaapisite ); ?>"></div>
			<?php } ?>
			<br>
			<?php esc_html_e( 'hCaptcha', 'dam-spam' ); ?><br>
			<input size="64" name="hcaptchaapisite" type="text" placeholder="<?php esc_html_e( 'Site Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisite ); ?>">
			<br>
			<input size="64" name="hcaptchaapisecret" type="text" placeholder="<?php esc_html_e( 'Secret Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisecret ); ?>">
			<br>
			<?php if ( !empty( $hcaptchaapisite ) ) { ?>
				<script src="https://hcaptcha.com/1/api.js" async defer></script>
				<div class="h-captcha" data-sitekey="<?php echo esc_attr( $hcaptchaapisite ); ?>"></div>
			<?php } ?>
			<br>
			<?php esc_html_e( 'Solve Media CAPTCHA', 'dam-spam' ); ?><br>
			<input size="64" name="solvmediaapivchallenge" type="text" placeholder="<?php esc_html_e( 'Challenge Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $solvmediaapivchallenge ); ?>">
			<br>
			<input size="64" name="solvmediaapiverify" type="text" placeholder="<?php esc_html_e( 'Verification Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $solvmediaapiverify ); ?>">
			<br>
			<?php if ( !empty( $solvmediaapivchallenge ) ) { ?>
				<script src="https://api-secure.solvemedia.com/papi/challenge.script?k=<?php echo esc_attr( $solvmediaapivchallenge ); ?>"></script>
			<?php } ?>
		</div>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
