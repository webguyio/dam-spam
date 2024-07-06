<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( __( 'Access Blocked', 'dam-spam' ) );
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
		if ( array_key_exists( 'new_user_notification_to_admin', $_POST ) ) {	 
			$new_user_notification_to_admin			   = sanitize_text_field( trim( $_POST['new_user_notification_to_admin'] ) );
			$options['new_user_notification_to_admin'] = $new_user_notification_to_admin;
		}
		else {
			$options['new_user_notification_to_admin'] = 'N';
		}
		if ( array_key_exists( 'ds_new_user_notification_to_user', $_POST ) ) {
			$ds_new_user_notification_to_user			 = sanitize_text_field( trim( $_POST['ds_new_user_notification_to_user'] ) );
			$options['ds_new_user_notification_to_user'] = $ds_new_user_notification_to_user;
		}
		else {
			$options['ds_new_user_notification_to_user'] = 'N';
		}
        if ( array_key_exists( 'ds_send_email_change_email', $_POST ) ) {
			$ds_send_email_change_email	= sanitize_text_field( trim( $_POST['ds_send_email_change_email'] ) );
			$options['ds_send_email_change_email'] = $ds_send_email_change_email;
		}
		else {
			$options['ds_send_email_change_email'] = 'N';
		}
		if ( array_key_exists( 'ds_send_password_forgotten_email', $_POST ) ) {	 
			$ds_send_password_forgotten_email			 = sanitize_text_field( trim( $_POST['ds_send_password_forgotten_email'] ) );
			$options['ds_send_password_forgotten_email'] = $ds_send_password_forgotten_email;
		}
		else {
			$options['ds_send_password_forgotten_email'] = 'N';
		}
		if ( array_key_exists( 'ds_wp_notify_moderator', $_POST ) ) { 
			$ds_wp_notify_moderator			   = sanitize_text_field( trim( $_POST['ds_wp_notify_moderator'] ) );
			$options['ds_wp_notify_moderator'] = $ds_wp_notify_moderator;
		}
		else {
			$options['ds_wp_notify_moderator'] = 'N';
		}
        if ( array_key_exists( 'ds_wp_notify_post_author', $_POST ) ) {
			$ds_wp_notify_post_author			 = sanitize_text_field( trim( $_POST['ds_wp_notify_post_author'] ) );
			$options['ds_wp_notify_post_author'] = $ds_wp_notify_post_author;
		}
		else {
			$options['ds_wp_notify_post_author'] = 'N';
		}
		if ( array_key_exists( 'ds_password_change_notification_to_admin', $_POST ) ) {
			$ds_password_change_notification_to_admin			 = sanitize_text_field( trim( $_POST['ds_password_change_notification_to_admin'] ) );
			$options['ds_password_change_notification_to_admin'] = $ds_password_change_notification_to_admin;
		}
		else {
			$options['ds_password_change_notification_to_admin'] = 'N';
		}
		if ( array_key_exists( 'ds_password_change_notification_to_user', $_POST ) ) {
			$ds_password_change_notification_to_user			= sanitize_text_field( trim( $_POST['ds_password_change_notification_to_user'] ) );
			$options['ds_password_change_notification_to_user'] = $ds_password_change_notification_to_user;
		}
		else {
			$options['ds_password_change_notification_to_user'] = 'N';
		}
		if ( array_key_exists( 'ds_auto_core_update_send_email', $_POST ) ) {
			$ds_auto_core_update_send_email			   = sanitize_text_field( trim( $_POST['ds_auto_core_update_send_email'] ) );
			$options['ds_auto_core_update_send_email'] = $ds_auto_core_update_send_email;
		}
		else {
			$options['ds_auto_core_update_send_email'] = 'N';
		}
		if ( array_key_exists( 'ds_auto_plugin_update_send_email', $_POST ) ) {
			$ds_auto_plugin_update_send_email			 = sanitize_text_field( trim( $_POST['ds_auto_plugin_update_send_email'] ) );
			$options['ds_auto_plugin_update_send_email'] = $ds_auto_plugin_update_send_email;
		}
		else {
			$options['ds_auto_plugin_update_send_email'] = 'N';
		}
        if ( array_key_exists( 'ds_auto_theme_update_send_email', $_POST ) ) {
			$ds_auto_theme_update_send_email			= sanitize_text_field( trim( $_POST['ds_auto_core_update_send_email'] ) );
			$options['ds_auto_theme_update_send_email'] = $ds_auto_theme_update_send_email;
		}
		else {
			$options['ds_auto_theme_update_send_email'] = 'N';
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
			$msg				   = __( 'You cannot use Google reCAPTCHA unless you have entered an API key.', 'dam-spam' );
		}
		if ( $chkcaptcha == 'H' && ( $hcaptchaapisecret == '' || $hcaptchaapisite == '' ) ) {
			$chkcaptcha			   = 'Y';
			$options['chkcaptcha'] = $chkcaptcha;
			$msg				   = __( 'You cannot use hCaptcha unless you have entered an API key.', 'dam-spam' );
		}
		if ( $chkcaptcha == 'S' && ( $solvmediaapivchallenge == '' || $solvmediaapiverify == '' ) ) {
			$chkcaptcha			   = 'Y';
			$options['chkcaptcha'] = $chkcaptcha;
			$msg				   = __( 'You cannot use Solve Media CAPTCHA unless you have entered an API key.', 'dam-spam' );
		}
		ds_set_options( $options );
		extract( $options ); // extract again to get the new options
	}
	$update = '<div class="notice notice-success is-dismissible"><p>' . __( 'Options Updated', 'dam-spam' ) . '</p></div>';
 }

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head">Dam Spam — <?php _e( 'Challenge & Block', 'dam-spam' ); ?></h1>
	<?php if ( !empty( $update ) ) {
		echo "$update";
	} ?>
	<?php if ( !empty( $msg ) ) {
		echo '<span style="color:red;font-size:1.2em">' . $msg . '</span>';
	} ?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
		<input type="hidden" name="action" value="update challenge">
		<br>
		<div class="mainsection"><?php _e( 'Access Blocked Message', 'dam-spam' ); ?></div>
		<textarea id="rejectmessage" name="rejectmessage" cols="40" rows="5"><?php echo $rejectmessage; ?></textarea>
		<br>
		<div class="mainsection"><?php _e( 'Routing and Notifications', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="redir">
				<input class="ds_toggle" type="checkbox" id="redir" name="redir" value="Y" onclick="ds_show_option()" <?php if ( $redir == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Send Visitor to Another Web Page', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<span id="ds_show_option" style="margin-bottom:15px;display:none"><?php _e( 'Redirect URL:', 'dam-spam' ); ?>
		<input size="77" name="redirurl" type="text" placeholder="e.g. https://example.com/privacy-policy/" value="<?php echo $redirurl; ?>"></span>
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
				<small><span style="font-size:16px!important"><?php _e( 'Blocked users see the Allow Request form', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="notify">
				<input class="ds_toggle" type="checkbox" id="notify" name="notify" value="Y" onclick="ds_show_notify()" <?php if ( $notify == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important;"><?php _e( 'Notify Admin when a user requests to be added to the Allow List', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<span id="ds_show_notify" style="margin-bottom:15px;display:none"><?php _e( '(Optional) Specify where email requests are sent:', 'dam-spam' ); ?>
		<input id="dsinput" size="48" name="wlreqmail" type="text" value="<?php echo $wlreqmail; ?>"></span>
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
				<small><span style="font-size:16px!important"><?php _e( 'Notify Requester when an Admin has approved their request to be added to the Allow List', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div id="autoemails" class="mainsection"><?php _e( 'Options for emails to admin', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="new_user_notification_to_admin">
				<input class="ds_toggle" type="checkbox" id="new_user_notification_to_admin" name="new_user_notification_to_admin" value="Y" <?php if ( isset( $new_user_notification_to_admin ) and $new_user_notification_to_admin == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'New user notification to user', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_password_change_notification_to_admin">
				<input class="ds_toggle" type="checkbox" id="ds_password_change_notification_to_admin" name="ds_password_change_notification_to_admin" value="Y" <?php if ( isset( $ds_password_change_notification_to_admin ) and $ds_password_change_notification_to_admin == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Password change notification to admin', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_auto_core_update_send_email">
				<input class="ds_toggle" type="checkbox" id="ds_auto_core_update_send_email" name="ds_auto_core_update_send_email" value="Y" <?php if ( isset( $ds_auto_core_update_send_email ) and $ds_auto_core_update_send_email == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Automatic WordPress core update email', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_auto_plugin_update_send_email">
				<input class="ds_toggle" type="checkbox" id="ds_auto_plugin_update_send_email" name="ds_auto_plugin_update_send_email" value="Y" <?php if ( isset( $ds_auto_plugin_update_send_email ) and $ds_auto_plugin_update_send_email == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Automatic WordPress plugin update email', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_auto_theme_update_send_email">
				<input class="ds_toggle" type="checkbox" id="ds_auto_theme_update_send_email" name="ds_auto_theme_update_send_email" value="Y" <?php if ( isset( $ds_auto_theme_update_send_email ) and $ds_auto_theme_update_send_email == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Automatic WordPress theme update email', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php _e( 'Options for emails to users', 'dam-spam' ); ?></div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_new_user_notification_to_user">
				<input class="ds_toggle" type="checkbox" id="ds_new_user_notification_to_user" name="ds_new_user_notification_to_user" value="Y" <?php if ( isset( $ds_new_user_notification_to_user ) and $ds_new_user_notification_to_user == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'New user notification to user', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_wp_notify_post_author">
				<input class="ds_toggle" type="checkbox" id="ds_wp_notify_post_author" name="ds_wp_notify_post_author" value="Y" <?php if ( isset( $ds_wp_notify_post_author ) and $ds_wp_notify_post_author == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Notify Postauthor', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_wp_notify_moderator">
				<input class="ds_toggle" type="checkbox" id="ds_wp_notify_moderator" name="ds_wp_notify_moderator" value="Y" <?php if ( isset( $ds_wp_notify_moderator ) and $ds_wp_notify_moderator == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Notify Moderator', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_password_change_notification_to_user">
				<input class="ds_toggle" type="checkbox" id="ds_password_change_notification_to_user" name="ds_password_change_notification_to_user" value="Y" <?php if ( isset( $ds_password_change_notification_to_user ) and $ds_password_change_notification_to_user == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Password change notification to user', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
        <div class="checkbox switcher">
			<label class="ds-subhead" for="ds_send_email_change_email">
				<input class="ds_toggle" type="checkbox" id="ds_send_email_change_email" name="ds_send_email_change_email" value="Y" <?php if ( isset( $ds_send_email_change_email ) and $ds_send_email_change_email == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Email address change notification to user', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_send_password_forgotten_email">
				<input class="ds_toggle" type="checkbox" id="ds_send_password_forgotten_email" name="ds_send_password_forgotten_email" value="Y" <?php if ( isset( $ds_send_password_forgotten_email ) and $ds_send_password_forgotten_email == 'Y' ) { echo 'checked="checked"'; }?> 
				/><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Password forgotten email to user', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php _e( 'CAPTCHA', 'dam-spam' ); ?></div>
		<p><?php _e( 'Second Chance CAPTCHA Challenge', 'dam-spam' ); ?></p>
		<div>
			<?php
			if ( !empty( $msg ) ) {
				echo '<span style="color:red;font-size:1.2em">' . $msg . '</span>';
			}
			?>
		</div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha1">
				<input class="ds_toggle" type="radio" id="chkcaptcha1" name="chkcaptcha" value="N" <?php if ( $chkcaptcha == 'N' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'No CAPTCHA (default)', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha2">
				<input class="ds_toggle" type="radio" id="chkcaptcha2" name="chkcaptcha" value="G" <?php if ( $chkcaptcha == 'G' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Google reCAPTCHA', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha3">
				<input class="ds_toggle" type="radio" id="chkcaptcha3" name="chkcaptcha" value="H" <?php if ( $chkcaptcha == 'H' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'hCaptcha', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha4">
				<input class="ds_toggle" type="radio" id="chkcaptcha4" name="chkcaptcha" value="S" <?php if ( $chkcaptcha == 'S' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Solve Media CAPTCHA', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkcaptcha5">
				<input class="ds_toggle" type="radio" id="chkcaptcha5" name="chkcaptcha" value="A" <?php if ( $chkcaptcha == 'A' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Arithmetic Question', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<div>
			<p><?php _e( 'To use either the Solve Media, Google reCAPTCHA, or hCaptcha, you will need an API key.', 'dam-spam' ); ?></p>
		</div>
		<p><?php _e( 'CAPTCHA for Forms (works with reCAPTCHA, hCaptcha, and Solve Media CAPTCHA)', 'dam-spam' ); ?></p>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_login">
				<input class="ds_toggle" type="checkbox" id="form_captcha_login" name="form_captcha_login" value="Y" <?php if ( isset( $form_captcha_login ) and $form_captcha_login == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Login', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_registration">
				<input class="ds_toggle" type="checkbox" id="form_captcha_registration" name="form_captcha_registration" value="Y" <?php if ( isset( $form_captcha_registration ) and $form_captcha_registration == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Registration', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="form_captcha_comment">
				<input class="ds_toggle" type="checkbox" id="form_captcha_comment" name="form_captcha_comment" value="Y" <?php if ( isset( $form_captcha_comment ) and $form_captcha_comment == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php _e( 'Comment', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<br>
		<div>
			<small><span style="font-size:16px!important;"><?php _e( 'Google reCAPTCHA v2 API Key', 'dam-spam' ); ?></span></small><br>
			<input size="64" name="recaptchaapisite" type="text" placeholder="<?php _e( 'Site Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $recaptchaapisite ); ?>">
			<br>
			<input size="64" name="recaptchaapisecret" type="text" placeholder="<?php _e( 'Secret Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $recaptchaapisecret ); ?>">
			<br>
			<?php if ( !empty( $recaptchaapisite ) ) { ?>
				<script src="https://www.google.com/recaptcha/api.js" async defer></script>
				<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptchaapisite ); ?>"></div>
			<?php } ?>
			<br>
			<small><span style="font-size:16px!important;"><?php _e( 'hCaptcha API Key', 'dam-spam' ); ?></span></small><br>
			<input size="64" name="hcaptchaapisite" type="text" placeholder="<?php _e( 'Site Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisite ); ?>">
			<br>
			<input size="64" name="hcaptchaapisecret" type="text" placeholder="<?php _e( 'Secret Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $hcaptchaapisecret ); ?>">
			<br>
			<?php if ( !empty( $hcaptchaapisite ) ) { ?>
				<script src="https://hcaptcha.com/1/api.js" async defer></script>
				<div class="h-captcha" data-sitekey="<?php echo esc_attr( $hcaptchaapisite ); ?>"></div>
			<?php } ?>
			<br>
			<small><span style="font-size:16px!important"><?php _e( 'Solve Media CAPTCHA API Key', 'dam-spam' ); ?></span></small><br>
			<input size="64" name="solvmediaapivchallenge" type="text" placeholder="<?php _e( 'Challenge Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $solvmediaapivchallenge ); ?>">
			<br>
			<input size="64" name="solvmediaapiverify" type="text" placeholder="<?php _e( 'Verification Key', 'dam-spam' ); ?>" value="<?php echo esc_attr( $solvmediaapiverify ); ?>">
			<br>
			<?php if ( !empty( $solvmediaapivchallenge ) ) { ?>
				<script src="https://api-secure.solvemedia.com/papi/challenge.script?k=<?php echo esc_attr( $solvmediaapivchallenge ); ?>"></script>
			<?php } ?>
		</div>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php _e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
