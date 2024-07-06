<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

// settings successfully updated message
function ds_admin_notice_success() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php _e( 'Options Updated', 'dam-spam' ); ?></p>
	</div>
	<?php
}

if ( defined( 'DS_ENABLE_FIREWALL' ) ) {
	include __DIR__ . '/includes/firewall.php';
}

function ds_advanced_menu() {
	$ds_firewall_setting = '';
	if ( get_option( 'ds_enable_firewall', '' ) === 'yes' ) {
		$ds_firewall_setting = "checked='checked'";
	}
	$ds_hide_admin_notices = '';
	if ( get_option( 'ds_hide_admin_notices', '' ) === 'yes' ) {
		$ds_hide_admin_notices = "checked='checked'";
	}
	$ds_disable_admin_emails = '';
	if ( get_option( 'ds_disable_admin_emails', '' ) === 'yes' ) {
		$ds_disable_admin_emails = "checked='checked'";
	}
	$ds_disable_admin_emails_update = '';
	if ( get_option( 'ds_disable_admin_emails_update', '' ) === 'yes' ) {
		$ds_disable_admin_emails_update = "checked='checked'";
	}
	$ds_disable_admin_emails_comment = '';
	if ( get_option( 'ds_disable_admin_emails_comment', '' ) === 'yes' ) {
		$ds_disable_admin_emails_comment = "checked='checked'";
	}
	$ds_disable_admin_emails_password_reset = '';
	if ( get_option( 'ds_disable_admin_emails_password_reset', '' ) === 'yes' ) {
		$ds_disable_admin_emails_password_reset = "checked='checked'";
	}
	$ds_disable_admin_emails_new_user = '';
	if ( get_option( 'ds_disable_admin_emails_new_user', '' ) === 'yes' ) {
		$ds_disable_admin_emails_new_user = "checked='checked'";
	}
	$ds_disable_core_nudge = '';
	if ( get_option( 'ds_disable_core_nudge', '' ) === 'yes' ) {
		$ds_disable_core_nudge = "checked='checked'";
	}
	$ds_disable_theme_nudge = '';
	if ( get_option( 'ds_disable_theme_nudge', '' ) === 'yes' ) {
		$ds_disable_theme_nudge = "checked='checked'";
	}
	$ds_disable_plugin_nudge = '';
	if ( get_option( 'ds_disable_plugin_nudge', '' ) === 'yes' ) {
		$ds_disable_plugin_nudge = "checked='checked'";
	}
	$ds_login_setting = '';
	if ( get_option( 'ds_enable_custom_login', '' ) === 'yes' ) {
		$ds_login_setting = "checked='checked'";
	}
	$ds_login_attempts = '';
	if ( get_option( 'ds_login_attempts', '' ) === 'yes' ) {
		$ds_login_attempts = "checked='checked'";
	}
	$ds_login_type_default = '';
	$ds_login_type_username = '';
	$ds_login_type_email = '';
	if ( get_option( 'ds_login_type', '' ) == 'username' ) {
		$ds_login_type_username = "checked='checked'";
	} else if ( get_option( 'ds_login_type', '' ) == 'email'  ) {
		$ds_login_type_email = "checked='checked'";
	} else {
		$ds_login_type_default = "checked='checked'";
	}
	$ds_honeypot_cf7 = '';
	if ( get_option( 'ds_honeypot_cf7', 'yes' ) == 'yes' and is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$ds_honeypot_cf7 = "checked='checked'";
	}
	$ds_honeypot_bbpress = '';
	if ( get_option( 'ds_honeypot_bbpress', 'yes' ) == 'yes' and is_plugin_active( 'bbpress/bbpress.php' ) ) {
		$ds_honeypot_bbpress = "checked='checked'";
	}
	$ds_honeypot_elementor = '';
	if ( get_option( 'ds_honeypot_elementor', 'yes' ) == 'yes' and is_plugin_active( 'elementor/elementor.php' ) ) {
		$ds_honeypot_elementor = "checked='checked'";
	}
	$theme = wp_get_theme();
	$ds_honeypot_divi = '';
	if ( get_option( 'ds_honeypot_divi', 'yes' ) == 'yes' and ( $theme->name == 'Divi' || $theme->parent_theme == 'Divi' ) ) {
		$ds_honeypot_divi = "checked='checked'";
	}
    $ds_allow_vpn_setting = '';
	if ( get_option( 'ds_allow_vpn', '' ) === 'yes' ) {
		$ds_allow_vpn_setting = "checked='checked'";
	}
	?>
	<div id="ds-plugin" class="wrap">
		<h1 id="ds-head"><?php _e( 'Dam Spam — Advanced', 'dam-spam' ); ?></h1>
		<div class="metabox-holder">
			<div class="postbox">
				<form method="post">
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Firewall Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="ds_firewall_setting">
								<?php if ( defined( 'DS_ENABLE_FIREWALL' ) ) { ?>
								<p><a href="edit.php?post_type=ds-firewall" class="button-primary"><?php _e( 'Monitor Real-time Firewall', 'dam-spam' ); ?></a></p>
								<?php } else { ?>
								<p><em><small><?php _e( 'For advanced users only: If you\'d like to enable the real-time firewall beta feature, add <strong>define( \'DS_ENABLE_FIREWALL\', true );</strong> to your wp-config.php file. This feature is resource-intensive, requiring a lot of memory and database space.', 'dam-spam' ); ?></small></em></p>
								<?php } ?>
								<input type="checkbox" name="ds_firewall_setting" id="ds_firewall_setting" value="yes" <?php echo $ds_firewall_setting; ?>>
								<span><small></small></span>
								<?php _e( 'Enable Server-side Security Rules', 'dam-spam' ); ?>
								<p><em><small><?php _e( 'For advanced users only: This option will modify your .htaccess file with extra security rules and in some small cases, conflict with your server settings. If you do not understand how to edit your .htaccess file to remove these rules in the event of an error, do not enable.', 'dam-spam' ); ?></small></em></p>
							</label>
						</div>
						<p><input type="hidden" name="ds_firewall_setting_placeholder" value="ds_firewall_setting"></p>
					</div>
					<hr>
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Notification Control', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="ds_hide_admin_notices">
								<input type="checkbox" name="ds_hide_admin_notices" id="ds-hide-admin-notices" value="yes" <?php echo $ds_hide_admin_notices; ?>>
								<span><small></small></span>
								<?php _e( 'Hide All Admin Notices', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_disable_admin_emails">
								<input type="checkbox" name="ds_disable_admin_emails" id="ds-disable-admin-emails" value="yes" <?php echo $ds_disable_admin_emails; ?>>
								<span><small></small></span>
								<?php _e( 'Disable Admin Emails', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="ds-disable-admin-emails-wraps"<?php echo ( get_option( 'ds_disable_admin_emails', 'no' ) == 'yes' ? '': ' style="display:none"' ); ?>>
							<div class="checkbox switcher">
								<label for="ds_disable_admin_emails_update">
									<input type="checkbox" name="ds_disable_admin_emails_update" id="ds_disable_admin_emails_update" value="yes" <?php echo $ds_disable_admin_emails_update; ?>>
									<span><small></small></span>
									<?php _e( 'Disable Update Emails', 'dam-spam' ); ?>
								</label>
							</div>
							<br>
							<div class="checkbox switcher">
								<label for="ds_disable_admin_emails_comment">
									<input type="checkbox" name="ds_disable_admin_emails_comment" id="ds_disable_admin_emails_comment" value="yes" <?php echo $ds_disable_admin_emails_comment; ?>>
									<span><small></small></span>
									<?php _e( 'Disable Comment Emails', 'dam-spam' ); ?>
								</label>
							</div>
							<br>
							<div class="checkbox switcher">
								<label for="ds_disable_admin_emails_password_reset">
									<input type="checkbox" name="ds_disable_admin_emails_password_reset" id="ds_disable_admin_emails_password_reset" value="yes" <?php echo $ds_disable_admin_emails_password_reset; ?>>
									<span><small></small></span>
									<?php _e( 'Disable Password Reset Emails', 'dam-spam' ); ?>
								</label>
							</div>
							<br>
							<div class="checkbox switcher">
								<label for="ds_disable_admin_emails_new_user">
									<input type="checkbox" name="ds_disable_admin_emails_new_user" id="ds_disable_admin_emails_new_user" value="yes" <?php echo $ds_disable_admin_emails_new_user; ?>>
									<span><small></small></span>
									<?php _e( 'Disable New User Emails', 'dam-spam' ); ?>
								</label>
							</div>
							<br>
						</div>
						<div class="checkbox switcher">
							<label for="ds_disable_core_nudge">
								<input type="checkbox" name="ds_disable_core_nudge" id="ds_disable_core_nudge" value="yes" <?php echo $ds_disable_core_nudge; ?>>
								<span><small></small></span>
								<?php _e( 'Disable Core Updates Nudge', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_disable_theme_nudge">
								<input type="checkbox" name="ds_disable_theme_nudge" id="ds_disable_theme_nudge" value="yes" <?php echo $ds_disable_theme_nudge; ?>>
								<span><small></small></span>
								<?php _e( 'Disable Theme Updates Nudge', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_disable_plugin_nudge">
								<input type="checkbox" name="ds_disable_plugin_nudge" id="ds_disable_plugin_nudge" value="yes" <?php echo $ds_disable_plugin_nudge; ?>>
								<span><small></small></span>
								<?php _e( 'Disable Plugin Updates Nudge', 'dam-spam' ); ?>
							</label>
						</div>
						<p><input type="hidden" name="ds_notification_control_setting" value="ds_notification_control_update"></p>
					</div>
					<div class="inside ds_reset_hidden_notice_wrap"<?php echo ( get_option( 'ds_hide_admin_notices', 'no' ) != 'yes' ? '': ' style="display:none"' ); ?>>
						<h3 style="font-size:16px!important"><span><?php _e( 'Reset hidden notices for ', 'dam-spam' ); ?></span></h3>	
						<p><input type="radio" name="ds_reset_hidden_notice" value="current" checked="checked"> Current User</p>
						<p><input type="radio" name="ds_reset_hidden_notice" value="all"> All Users</p>
						<?php submit_button( __( 'Reset', 'dam-spam' ), 'secondary', 'submit', false ); ?>
					</div>
					<hr>
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Login Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="ds_login_setting">
								<input type="checkbox" name="ds_login_setting" id="ds_login_setting" value="yes" <?php echo $ds_login_setting; ?>>
								<span><small></small></span>
								<?php _e( 'Enable themed registration and login pages (disables the default wp-login.php).', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_login_attempts">
								<input type="checkbox" name="ds_login_attempts" id="ds_login_attempts" value="yes" <?php echo $ds_login_attempts; ?>>
								<span><small></small></span>
								<strong><?php _e( 'Login Attempts:', 'dam-spam' ); ?></strong>
								<?php _e( 'After', 'dam-spam' ); ?>
								<input type="text" name="ds_login_attempts_threshold" id="ds_login_attempts_duration" class="ds-small-box" value="<?php echo get_option( 'ds_login_attempts_threshold', 5 ); ?>">
								<?php _e( 'failed login attempts within', 'dam-spam' ); ?>
								<input type="text" name="ds_login_attempts_duration" id="ds_login_attempts_duration" class="ds-small-box" value="<?php echo get_option( 'ds_login_attempts_duration', 1 ); ?>">
								<select name="ds_login_attempts_unit" id="ds_login_attempts_unit" class="ds-small-dropbox">
									<option value="minute" <?php if ( get_option( 'ds_login_attempts_unit', 'hour' ) == 'minute' ) { echo 'selected="selected"'; } ?>><?php _e( 'minute(s)', 'dam-spam' ); ?></option>
									<option value="hour" <?php if ( get_option( 'ds_login_attempts_unit', 'hour' ) == 'hour' ) { echo 'selected="selected"'; } ?>><?php _e( 'hour(s)', 'dam-spam' ); ?></option>
									<option value="day" <?php if ( get_option( 'ds_login_attempts_unit', 'hour' ) == 'day' ) { echo 'selected="selected"'; } ?>><?php _e( 'day(s)', 'dam-spam' ); ?></option>
								</select>,
								<?php _e( 'lockout the account for', 'dam-spam' ); ?>
								<input type="text" name="ds_login_lockout_duration" id="ds_login_lockout_duration" class="ds-small-box" value="<?php echo get_option( 'ds_login_lockout_duration', 24 ); ?>"> 
								<select name="ds_login_lockout_unit" id="ds_login_lockout_unit" class="ds-small-dropbox">
									<option value="minute" <?php if ( get_option( 'ds_login_lockout_unit', 'hour' ) == 'minute' ) { echo 'selected="selected"'; } ?>><?php _e( 'minute(s)', 'dam-spam' ); ?></option>
									<option value="hour" <?php if ( get_option( 'ds_login_lockout_unit', 'hour' ) == 'hour' ) { echo 'selected="selected"'; } ?>><?php _e( 'hour(s)', 'dam-spam' ); ?></option>
									<option value="day" <?php if ( get_option( 'ds_login_lockout_unit', 'hour' ) == 'day' ) { echo 'selected="selected"'; } ?>><?php _e( 'day(s)', 'dam-spam' ); ?></option>
								</select>.
							</label>
						</div>
						<p><input type="hidden" name="ds_login_setting_placeholder" value="ds_login_setting"></p>
					</div>
					<hr>
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Allow users to log in using their username and/or email address', 'dam-spam' ); ?></span></h3>
						<p><input type="hidden" name="ds_login_type_field" value="ds_login_type"></p>
						<div class="checkbox switcher">
							<label for="ds-login-type-default">
								<input name="ds_login_type" type="radio" id="ds-login-type-default" value="default" <?php echo $ds_login_type_default; ?>>
								<span><small></small></span>
								<?php _e( 'Username or Email', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds-login-type-username">
								<input name="ds_login_type" type="radio" id="ds-login-type-username" value="username" <?php echo $ds_login_type_username; ?>>
								<span><small></small></span>
								<?php _e( 'Username Only', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds-login-type-email">
								<input name="ds_login_type" type="radio" id="ds-login-type-email" value="email" <?php echo $ds_login_type_email; ?>>
								<span><small></small></span>
								<?php _e( 'Email Only', 'dam-spam' ); ?>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Honeypot', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="ds_honeypot_cf7">
								<input type="checkbox" name="ds_honeypot_cf7" id="ds_honeypot_cf7" value="yes" <?php echo ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo $ds_honeypot_cf7; ?>>
								<span><small></small></span>
								<?php _e( 'Contact Form 7', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_honeypot_bbpress">
								<input type="checkbox" name="ds_honeypot_bbpress" id="ds_honeypot_bbpress" value="yes" <?php echo ( is_plugin_active( 'bbpress/bbpress.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo $ds_honeypot_bbpress; ?>>
								<span><small></small></span>
								<?php _e( 'bbPress', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_honeypot_elementor">
								<input type="checkbox" name="ds_honeypot_elementor" id="ds_honeypot_elementor" value="yes" <?php echo ( is_plugin_active( 'elementor/elementor.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo $ds_honeypot_elementor; ?>>
								<span><small></small></span>
								<?php _e( 'Elementor Form', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="ds_honeypot_divi">
								<input type="checkbox" name="ds_honeypot_divi" id="ds_honeypot_divi" value="yes" <?php echo ( ( $theme->name == 'Divi' || $theme->parent_theme == 'Divi' ) ? '' : 'disabled="disabled"' ); ?> <?php echo $ds_honeypot_divi; ?>>
								<span><small></small></span>
								<?php _e( 'Divi Forms', 'dam-spam' ); ?>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">
						<h3 style="font-size:16px!important"><span><?php _e( 'Block users from registering, commenting, and purchasing while on a VPN', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="ds_allow_vpn">
								<input type="checkbox" name="ds_allow_vpn" id="ds_allow_vpn" value="yes" <?php echo $ds_allow_vpn_setting;?>>
								<span><small></small></span>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">			
						<p>
							<?php wp_nonce_field( 'ds_login_type_nonce', 'ds_login_type_nonce' ); ?>
							<?php submit_button( __( 'Save Changes', 'dam-spam' ), 'primary', 'submit', false ); ?>
						</p>
					</div>
				</form>
			</div>
			<div class="postbox">
				<h3 style="font-size:18px"><span><?php _e( 'Shortcodes', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Contact Form: <strong>[ds-contact-form]</strong>', 'dam-spam' ); ?></p>
					<p><?php _e( 'Login Form: <strong>[ds-login]</strong>', 'dam-spam' ); ?></p>
					<p><?php _e( 'Logged-in User Display Name: <strong>[show-displayname-as]</strong>', 'dam-spam' ); ?></p>
					<p><?php _e( 'Logged-in User First/Last Name: <strong>[show-fullname-as]</strong>', 'dam-spam' ); ?></p>
					<p><?php _e( 'Logged-in User Email Address: <strong>[show-email-as]</strong>', 'dam-spam' ); ?></p>
				</div>
			</div>
			<div class="postbox">
				<h3 style="font-size:16px!important"><span><?php _e( 'Export Log Settings (not working)', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export the Log records to an Excel file.', 'dam-spam' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="export_log" value="export_log_data"></p>
						<p>
							<?php wp_nonce_field( 'ds_export_action', 'ds_export_action' ); ?>
							<?php submit_button( __( 'Export', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3 style="font-size:16px!important"><span><?php _e( 'Export Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'dam-spam' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="ds_action" value="export_settings"></p>
						<p>
							<?php wp_nonce_field( 'ds_export_nonce', 'ds_export_nonce' ); ?>
							<?php submit_button( __( 'Export', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
			<div class="postbox">
				<h3 style="font-size:16px!important"><span><?php _e( 'Import Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'dam-spam' ); ?></p>
					<form method="post" enctype="multipart/form-data">
						<p><input type="file" name="import_file"></p>
						<p>
							<input type="hidden" name="ds_action" value="import_settings">
							<?php wp_nonce_field( 'ds_import_nonce', 'ds_import_nonce' ); ?>
							<?php submit_button( __( 'Import', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
			<div class="postbox">
				<h3 style="font-size:16px!important"><span><?php _e( 'Reset Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Reset the plugin settings for this site. This allows you to easily reset the configuration.', 'dam-spam' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="ds_action" value="reset_settings"></p>
						<p>
							<?php wp_nonce_field( 'ds_reset_nonce', 'ds_reset_nonce' ); ?>
							<?php submit_button( __( 'Reset', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->			
		</div><!-- .metabox-holder -->
	</div>
	<?php
}

// add contact form shortcode
function ds_contact_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'email'    => '',
		'accent'   => '',
		'unstyled' => '',
	), $atts );
	ob_start();
	echo '
	<script>
	function nospam() {
		var message = document.forms["ds-contact-form"]["message"].value;
		var comment = document.getElementById("comment");
		var link = message.indexOf("http");
		if (link > -1) {
			comment.setCustomValidity("' . esc_html__( 'Links are welcome, but please remove the https:// portion of them.', 'dam-spam' ) . '");
			comment.reportValidity();
		} else {
			comment.setCustomValidity("");
			comment.reportValidity();
		}
	}
	</script>
	<form id="ds-contact-form" name="ds-contact-form" method="post" action="#send">
		<p id="name"><input type="text" name="sign" placeholder="' . esc_html__( 'Name', 'dam-spam' ) . '" autocomplete="off" size="35" required></p>
		<p id="email"><input type="email" name="email" placeholder="' . esc_html__( 'Email', 'dam-spam' ) . '" autocomplete="off" size="35" required></p>
		<p id="phone"><input type="tel" name="phone" placeholder="' . esc_html__( 'Phone (optional)', 'dam-spam' ) . '" autocomplete="off" size="35"></p>
		<p id="url"><input type="url" name="url" placeholder="' . esc_html__( 'URL', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" size="35" required></p>
		<p id="message"><textarea id="comment" name="message" placeholder="' . esc_html__( 'Message', 'dam-spam' ) . '" rows="5" cols="100" onkeyup="nospam()"></textarea></p>
		<p id="submit"><input type="submit" value="' . esc_html__( 'Submit', 'dam-spam' ) . '"></p>
	</form>
	';
	if ( 'yes' == $atts['unstyled'] ) {
		echo '
		<style>
		#ds-contact-form #url{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}
		#send{text-align:center;padding:5%}
		#send.success{color:green}
		#send.fail{color:red}
		</style>
		';
	} else {
		echo '
		<style>
		#ds-contact-form, #ds-contact-form *{box-sizing:border-box;transition:all 0.5s ease}
		#ds-contact-form input, #ds-contact-form textarea{width:100%;font-family:arial,sans-serif;font-size:14px;color:#767676;padding:15px;border:1px solid transparent;background:#f6f6f6}
		#ds-contact-form input:focus, #ds-contact-form textarea:focus{color:#000;border:1px solid ' . ( esc_attr( $atts['accent'] ) ? esc_attr( $atts['accent'] ) : '#007acc' ) . '}
		#ds-contact-form #submit input{display:inline-block;font-size:18px;color:#fff;text-align:center;text-decoration:none;padding:15px 25px;background:' . ( esc_attr( $atts['accent'] ) ? esc_attr( $atts['accent'] ) : '#007acc' ) . ';cursor:pointer}
		#ds-contact-form #submit input:hover, #submit input:focus{opacity:0.8}
		#ds-contact-form #url{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}
		#send{text-align:center;padding:5%}
		#send.success{color:green}
		#send.fail{color:red}
		</style>
		';
	}
	$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
	$message = isset( $_POST['message'] ) ? $_POST['message'] : '';
	if ( ( esc_url( $url ) == 'https://example.com/' ) && ( stripos( $message, 'http' ) === false ) ) {
		if ( $atts['email'] ) {
			$to = sanitize_email( $atts['email'] );
		} else {
			$to = sanitize_email( get_option( 'admin_email' ) );
		}
		$subject = esc_html__( 'New Message from ', 'dam-spam' ) . esc_html( get_option( 'blogname' ) );
		$name    = sanitize_text_field( $_POST['sign'] );
		$email   = sanitize_email( $_POST['email'] );
		$phone   = sanitize_text_field( $_POST['phone'] );
		$message = sanitize_textarea_field( $_POST['message'] );
		$validated = true;
		if ( !$validated ) {
			print '<p id="send" class="fail">' . esc_html__( 'Message Failed', 'dam-spam' ) . '</p>';
			exit;
		}
		$body  = '';
		$body .= esc_html__( 'Name: ', 'dam-spam' );
		$body .= wp_unslash( $name );
		$body .= "\n";
		$body .= esc_html__( 'Email: ', 'dam-spam' );
		$body .= $email;
		if ( $_POST['phone'] ) {
			$body .= "\n";
			$body .= esc_html__( 'Phone: ', 'dam-spam' );
			$body .= $phone;
		}
		$body .= "\n\n";
		$body .= wp_unslash( $message );
		$body .= "\n";
		$success = wp_mail( $to, $subject, $body, esc_html__( 'From: ', 'dam-spam' ) . "$name <$email>" );
		if ( $success ) {
			print '<p id="send" class="success">' . esc_html__( 'Message Sent Successfully', 'dam-spam' ) . '</p>';
		} else {
			print '<p id="send" class="fail">' . esc_html__( 'Message Failed', 'dam-spam' ) . '</p>';
		}
	}
	$output = ob_get_clean();
	return $output;
}
add_shortcode( 'ds-contact-form', 'ds_contact_form_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );

if ( get_option( 'ds_honeypot_cf7', 'yes' ) == 'yes' ) {
	// add honeypot to Contact Form 7
	function ds_cf7_add_honeypot( $form ) {
		$html  = '';
		$html .= '<p class="ds-user">';
		$html .= '<label>' . __( 'Your Website (required)', 'dam-spam' ) . '<br>';
		$html .= '<span class="wpcf7-form-control-wrap your-website">';
		$html .= '<input type="text" name="your-website" value="https://example.com/" autocomplete="off" tabindex="-1" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required>';
		$html .= '</span>';
		$html .= '<label>';
		$html .= '</p>';
		$html .= '<style>.ds-user{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
		return $html.$form;
	}
	add_filter( 'wpcf7_form_elements', 'ds_cf7_add_honeypot', 10, 1 );
	function ds_cf7_verify_honeypot( $spam ) {
		if ( $spam ) {
			return $spam;
		}
		if ( $_POST['your-website'] != 'https://example.com/' ) {
			return true;
		}
		return $spam;
	}
	add_filter( 'wpcf7_spam', 'ds_cf7_verify_honeypot', 10, 1 );
}

if ( get_option( 'ds_honeypot_bbpress', 'yes' ) == 'yes' ) {
	// add honeypot to bbPress
	function ds_bbp_add_honeypot() {
		$html  = '';
		$html .= '<p class="ds-user">';
		$html .= '<label for="bbp_your-website">' . __( 'Your Website:', 'dam-spam' ) . '</label><br>';
		$html .= '<input type="text" value="https://example.com/" autocomplete="off" tabindex="-1" size="40" name="bbp_your-website" id="bbp_your-website" required>';
		$html .= '</p>';
		$html .= '<style>.ds-user{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
		echo $html;
	}
	add_action( 'bbp_theme_before_reply_form_submit_wrapper', 'ds_bbp_add_honeypot' );
	add_action( 'bbp_theme_before_topic_form_submit_wrapper', 'ds_bbp_add_honeypot' );
	function ds_bbp_verify_honeypot() {
		if ( $_POST['bbp_your-website'] != 'https://example.com/' ) {
			bbp_add_error( 'bbp_throw_error', __( "<strong>ERROR</strong>: Something went wrong!", 'dam-spam' ) );
		}
	}
	add_action( 'bbp_new_reply_pre_extras', 'ds_bbp_verify_honeypot' );
	add_action( 'bbp_new_topic_pre_extras', 'ds_bbp_verify_honeypot' );
}

if ( get_option( 'ds_honeypot_elementor', 'yes' ) == 'yes' ) {
	// add honeypot to Elementor form
	function ds_elementor_add_honeypot( $content, $widget ) {
		if ( 'form' === $widget->get_name() ) {
			$html    = '';
			$html   .= '<div class="elementor-field-type-text">';
			$html   .= '<input size="40" type="text" value="https://example.com/" autocomplete="off" tabindex="-1" name="form_fields[your-website]" id="form-field-your-website" class="elementor-field elementor-size-sm">';
			$html   .= '</div>';
			$html   .= '<style>#form-field-your-website{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
			$content = str_replace( '<div class="elementor-field-group', $html . '<div class="elementor-field-group', $content );
			return $content;
		}
		return $content;
	}
	add_action( 'elementor/widget/render_content', 'ds_elementor_add_honeypot', 10, 2 );
	function ds_elementor_verify_honeypot( $record, $ajax_handler ) {
		if ( $_POST['form_fields']['your-website'] != 'https://example.com/' ) {
			$ajax_handler->add_error( 'your-website', __( 'Something went wrong!', 'dam-spam' ) );
		}
	}
	add_action( 'elementor_pro/forms/validation', 'ds_elementor_verify_honeypot', 10, 2 );
}

if ( get_option( 'ds_honeypot_divi', 'yes' ) == 'yes' ) {
	// add honeypot to Divi contact form and opt-in
	function ds_et_add_honeypot( $output, $render_slug, $module ) {
		if ( isset( $_POST['et_pb_contact_your_website'] ) and $_POST['et_pb_contact_your_website'] == 'https://example.com/' ) {
			unset( $_POST['et_pb_contact_your_website'] );
			$post_last_key = array_key_last( $_POST );
			$form_json	   = json_decode( stripslashes( $_POST[$post_last_key] ) );
			array_pop( $form_json );
			$_POST[$post_last_key] = json_encode( $form_json );
		}
		$html = '';
		if ( $render_slug == 'et_pb_contact_form' ) {
			$html  .= '<p class="et_pb_contact_field et_pb_contact_your_website">';
			$html  .= '<label for="et_pb_contact_your_website" class="et_pb_contact_form_label">' . __( 'Your Website', 'dam-spam' ) . '</label>';
			$html  .= '<input type="text" name="et_pb_contact_your_website" id="et_pb_contact_your_website" placeholder="' . __( 'Your Website', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" required>';
			$html  .= '</p>';
			$html  .= '<style>.et_pb_contact_your_website{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
			$html  .= '<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit';
			$output = str_replace( '<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit', $html, $output );
		} else if ( $render_slug == 'et_pb_signup' ) {
			$html   = '';
			$html  .= '<p class="et_pb_signup_custom_field et_pb_signup_your_website et_pb_newsletter_field et_pb_contact_field_last et_pb_contact_field_last_tablet et_pb_contact_field_last_phone">';
			$html  .= '<label for="et_pb_signup_your_website" class="et_pb_contact_form_label">' . __( 'Your Website', 'dam-spam' ) . '</label>';
			$html  .= '<input type="text" class="input" id="et_pb_signup_your_website" placeholder="' . __( 'Your Website', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" data-original_id="your-website" required>';
			$html  .= '</p>';
			$html  .= '<style>.et_pb_signup_your_website{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
			$html  .= '<p class="et_pb_newsletter_button_wrap">';
			$output = str_replace( '<p class="et_pb_newsletter_button_wrap">', $html, $output );
		}
		return $output;
	}
	add_filter( 'et_module_shortcode_output', 'ds_et_add_honeypot', 20, 3 );
	function ds_divi_email_optin_verify_honeypot() {
		if ( isset( $_POST['et_custom_fields']['your-website'] ) and $_POST['et_custom_fields']['your-website'] != 'https://example.com/' ) { 
			echo '{"error":"Subscription Error: An error occurred, please try later."}';
			exit;
		} else if ( isset( $_POST['et_custom_fields']['your-website'] ) and $_POST['et_custom_fields']['your-website'] == 'https://example.com/' ) { 
			unset( $_POST['et_custom_fields']['your-website'] );
		}
	}
	add_action( 'admin_init', 'ds_divi_email_optin_verify_honeypot' );
}

// enable security rules firewall
function ds_enable_firewall() {
	if ( empty( $_POST['ds_firewall_setting_placeholder'] ) || 'ds_firewall_setting' != $_POST['ds_firewall_setting_placeholder'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	if ( isset( $_POST['ds_firewall_setting'] ) and $_POST['ds_firewall_setting'] == 'yes' ) {
		update_option( 'ds_enable_firewall', 'yes' );
		add_action( 'admin_notices', 'ds_admin_notice_success' );
		$insertion = array(
			'<IfModule mod_headers.c>',
			'Header set X-XSS-Protection "1; mode=block"',
			'Header always append X-Frame-Options SAMEORIGIN',
			'Header set X-Content-Type-Options nosniff',
			'Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"',
			'</IfModule>',
			'ServerSignature Off',
			'Options -Indexes',
			'RewriteEngine On',
			'RewriteBase /',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{QUERY_STRING} ([a-z0-9]{2000,}) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (/|%2f)(:|%3a)(/|%2f) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (order(\s|%20)by(\s|%20)1--) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (/|%2f)(\*|%2a)(\*|%2a)(/|%2f) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (`|<|>|\^|\|\\\|0x00|%00|%0d%0a) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (ckfinder|fck|fckeditor|fullclick) [NC,OR]',
			'RewriteCond %{QUERY_STRING} ((.*)header:|(.*)set-cookie:(.*)=) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (cmd|command)(=|%3d)(chdir|mkdir)(.*)(x20) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (globals|mosconfig([a-z_]{1,22})|request)(=|\[) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (/|%2f)((wp-)?config)((\.|%2e)inc)?((\.|%2e)php) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (thumbs?(_editor|open)?|tim(thumbs?)?)((\.|%2e)php) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (absolute_|base|root_)(dir|path)(=|%3d)(ftp|https?) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (localhost|loopback|127(\.|%2e)0(\.|%2e)0(\.|%2e)1) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (s)?(ftp|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215)) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\.|20)(get|the)(_|%5f)(permalink|posts_page_url)(\(|%28) [NC,OR]',
			'RewriteCond %{QUERY_STRING} ((boot|win)((\.|%2e)ini)|etc(/|%2f)passwd|self(/|%2f)environ) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (((/|%2f){3,3})|((\.|%2e){3,3})|((\.|%2e){2,2})(/|%2f|%u2215)) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (benchmark|char|exec|fopen|function|html)(.*)(\(|%28)(.*)(\)|%29) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (php)([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (e|%65|%45)(v|%76|%56)(a|%61|%31)(l|%6c|%4c)(.*)(\(|%28)(.*)(\)|%29) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (/|%2f)(=|%3d|$&|_mm|cgi(\.|-)|inurl(:|%3a)(/|%2f)|(mod|path)(=|%3d)(\.|%2e)) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (<|%3c)(.*)(e|%65|%45)(m|%6d|%4d)(b|%62|%42)(e|%65|%45)(d|%64|%44)(.*)(>|%3e) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (<|%3c)(.*)(i|%69|%49)(f|%66|%46)(r|%72|%52)(a|%61|%41)(m|%6d|%4d)(e|%65|%45)(.*)(>|%3e) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (<|%3c)(.*)(o|%4f|%6f)(b|%62|%42)(j|%4a|%6a)(e|%65|%45)(c|%63|%43)(t|%74|%54)(.*)(>|%3e) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (<|%3c)(.*)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(.*)(>|%3e) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\+|%2b|%20)(d|%64|%44)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(t|%74|%54)(e|%65|%45)(\+|%2b|%20) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\+|%2b|%20)(i|%69|%49)(n|%6e|%4e)(s|%73|%53)(e|%65|%45)(r|%72|%52)(t|%74|%54)(\+|%2b|%20) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\+|%2b|%20)(s|%73|%53)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(c|%63|%43)(t|%74|%54)(\+|%2b|%20) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\+|%2b|%20)(u|%75|%55)(p|%70|%50)(d|%64|%44)(a|%61|%41)(t|%74|%54)(e|%65|%45)(\+|%2b|%20) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (\\\x00|(\"|%22|\\\'|%27)?0(\"|%22|\\\'|%27)?(=|%3d)(\"|%22|\\\'|%27)?0|cast(\(|%28)0x|or%201(=|%3d)1) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (g|%67|%47)(l|%6c|%4c)(o|%6f|%4f)(b|%62|%42)(a|%61|%41)(l|%6c|%4c)(s|%73|%53)(=|\[|%[0-9A-Z]{0,2}) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (_|%5f)(r|%72|%52)(e|%65|%45)(q|%71|%51)(u|%75|%55)(e|%65|%45)(s|%73|%53)(t|%74|%54)(=|\[|%[0-9A-Z]{2,}) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (j|%6a|%4a)(a|%61|%41)(v|%76|%56)(a|%61|%31)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(:|%3a)(.*)(;|%3b|\)|%29) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (b|%62|%42)(a|%61|%41)(s|%73|%53)(e|%65|%45)(6|%36)(4|%34)(_|%5f)(e|%65|%45|d|%64|%44)(e|%65|%45|n|%6e|%4e)(c|%63|%43)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(.*)(\()(.*)(\)) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (@copy|\$_(files|get|post)|allow_url_(fopen|include)|auto_prepend_file|blexbot|browsersploit|(c99|php)shell|curl(_exec|test)|disable_functions?|document_root|elastix|encodeuricom|exploit|fclose|fgets|file_put_contents|fputs|fsbuff|fsockopen|gethostbyname|grablogin|hmei7|input_file|null|open_basedir|outfile|passthru|phpinfo|popen|proc_open|quickbrute|remoteview|root_path|safe_mode|shell_exec|site((.){0,2})copier|sux0r|trojan|user_func_array|wget|xertive) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (;|<|>|\\\'|\"|\)|%0a|%0d|%22|%27|%3c|%3e|%00)(.*)(/\*|alter|base64|benchmark|cast|concat|convert|create|encode|declare|delete|drop|insert|md5|request|script|select|set|union|update) [NC,OR]',
			'RewriteCond %{QUERY_STRING} ((\+|%2b)(concat|delete|get|select|union)(\+|%2b)) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (union)(.*)(select)(.*)(\(|%28) [NC,OR]',
			'RewriteCond %{QUERY_STRING} (concat|eval)(.*)(\(|%28) [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{REQUEST_URI} (\^|`|<|>|\\\|\|) [NC,OR]',
			'RewriteCond %{REQUEST_URI} ([a-z0-9]{2000,}) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (=?\\\(\\\'|%27)/?)(\.) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(\*|\"|\\\'|\.|,|&|&amp;?)/?$ [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\.)(php)(\()?([0-9]+)(\))?(/)?$ [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(vbulletin|boards|vbforum)(/)? [NC,OR]',
			'RewriteCond %{REQUEST_URI} /((.*)header:|(.*)set-cookie:(.*)=) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(ckfinder|fck|fckeditor|fullclick) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\.(s?ftp-?)config|(s?ftp-?)config\.) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\{0\}|\"?0\"?=\"?0|\(/\(|\.\.\.|\+\+\+|\\\\\\") [NC,OR]',
			'RewriteCond %{REQUEST_URI} (thumbs?(_editor|open)?|tim(thumbs?)?)(\.php) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\.|20)(get|the)(_)(permalink|posts_page_url)(\() [NC,OR]',
			'RewriteCond %{REQUEST_URI} (///|\?\?|/&&|/\*(.*)\*/|/:/|\\\\\\\\|0x00|%00|%0d%0a) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/%7e)(root|ftp|bin|nobody|named|guest|logs|sshd)(/) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(etc|var)(/)(hidden|secret|shadow|ninja|passwd|tmp)(/)?$ [NC,OR]',
			'RewriteCond %{REQUEST_URI} (s)?(ftp|http|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215)) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(=|\$&?|&?(pws|rk)=0|_mm|_vti_|cgi(\.|-)?|(=|/|;|,)nt\.) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\.)(ds_store|htaccess|htpasswd|init?|mysql-select-db)(/)?$ [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(bin)(/)(cc|chmod|chsh|cpp|echo|id|kill|mail|nasm|perl|ping|ps|python|tclsh)(/)?$ [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(::[0-9999]|%3a%3a[0-9999]|127\.0\.0\.1|localhost|loopback|makefile|pingserver|wwwroot)(/)? [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\(null\)|\{\$itemURL\}|cAsT\(0x|echo(.*)kae|etc/passwd|eval\(|self/environ|\+union\+all\+select) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)?j((\s)+)?a((\s)+)?v((\s)+)?a((\s)+)?s((\s)+)?c((\s)+)?r((\s)+)?i((\s)+)?p((\s)+)?t((\s)+)?(%3a|:) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(awstats|(c99|php|web)shell|document_root|error_log|listinfo|muieblack|remoteview|site((.){0,2})copier|sqlpatch|sux0r) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)((php|web)?shell|crossdomain|fileditor|locus7|nstview|php(get|remoteview|writer)|r57|remview|sshphp|storm7|webadmin)(.*)(\.|\() [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(author-panel|bitrix|class|database|(db|mysql)-?admin|filemanager|htdocs|httpdocs|https?|mailman|mailto|msoffice|mysql|_?php-my-admin(.*)|tmp|undefined|usage|var|vhosts|webmaster|www)(/) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (base64_(en|de)code|benchmark|child_terminate|curl_exec|e?chr|eval|function|fwrite|(f|p)open|html|leak|passthru|p?fsockopen|phpinfo|posix_(kill|mkfifo|setpgid|setsid|setuid)|proc_(close|get_status|nice|open|terminate)|(shell_)?exec|system)(.*)(\()(.*)(\)) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (/)(^$|00.temp00|0day|3index|3xp|70bex?|admin_events|bkht|(php|web)?shell|c99|config(\.)?bak|curltest|db|dompdf|filenetworks|hmei7|index\.php/index\.php/index|jahat|kcrew|keywordspy|libsoft|marg|mobiquo|mysql|nessus|php-?info|racrew|sql|vuln|(web-?|wp-)?(conf\b|config(uration)?)|xertive)(\.php) [NC,OR]',
			'RewriteCond %{REQUEST_URI} (\.)(7z|ab4|ace|afm|ashx|aspx?|bash|ba?k?|bin|bz2|cfg|cfml?|cgi|conf\b|config|ctl|dat|db|dist|dll|eml|engine|env|et2|exe|fec|fla|git|hg|inc|ini|inv|jsp|log|lqd|make|mbf|mdb|mmw|mny|module|old|one|orig|out|passwd|pdb|phtml|pl|profile|psd|pst|ptdb|pwd|py|qbb|qdf|rar|rdf|save|sdb|sql|sh|soa|svn|swf|swl|swo|swp|stx|tar|tax|tgz|theme|tls|tmd|wow|xtmpl|ya?ml|zlib)$ [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{HTTP_USER_AGENT} ([a-z0-9]{2000,}) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} (&lt;|%0a|%0d|%27|%3c|%3e|%00|0x00) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} (ahrefs|alexibot|majestic|mj12bot|rogerbot) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} ((c99|php|web)shell|remoteview|site((.){0,2})copier) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} (econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} (base64_decode|bin/bash|disconnect|eval|lwp-download|unserialize|\\\\\x22) [NC,OR]',
			'RewriteCond %{HTTP_USER_AGENT} (acapbot|acoonbot|asterias|attackbot|backdorbot|becomebot|binlar|blackwidow|blekkobot|blexbot|blowfish|bullseye|bunnys|butterfly|careerbot|casper|checkpriv|cheesebot|cherrypick|chinaclaw|choppy|clshttp|cmsworld|copernic|copyrightcheck|cosmos|crescent|cy_cho|datacha|demon|diavol|discobot|dittospyder|dotbot|dotnetdotcom|dumbot|emailcollector|emailsiphon|emailwolf|extract|eyenetie|feedfinder|flaming|flashget|flicky|foobot|g00g1e|getright|gigabot|go-ahead-got|gozilla|grabnet|grafula|harvest|heritrix|httrack|icarus6j|jetbot|jetcar|jikespider|kmccrew|leechftp|libweb|linkextractor|linkscan|linkwalker|loader|masscan|miner|mechanize|morfeus|moveoverbot|netmechanic|netspider|nicerspro|nikto|ninja|nutch|octopus|pagegrabber|petalbot|planetwork|postrank|proximic|purebot|pycurl|python|queryn|queryseeker|radian6|radiation|realdownload|scooter|seekerspider|semalt|siclab|sindice|sistrix|sitebot|siteexplorer|sitesnagger|skygrid|smartdownload|snoopy|sosospider|spankbot|spbot|sqlmap|stackrambler|stripper|sucker|surftbot|sux0r|suzukacz|suzuran|takeout|teleport|telesoft|true_robots|turingos|turnit|vampire|vikspider|voideye|webleacher|webreaper|webstripper|webvac|webviewer|webwhacker|winhttp|wwwoffle|woxbot|xaldon|xxxyy|yamanalab|yioopbot|youda|zeus|zmeu|zune|zyborg) [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{REMOTE_HOST} (163data|amazonaws|colocrossing|crimea|g00g1e|justhost|kanagawa|loopia|masterhost|onlinehome|poneytel|sprintdatacenter|reverse.softlayer|safenet|ttnet|woodpecker|wowrack) [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{HTTP_REFERER} (semalt.com|todaperfeita) [NC,OR]',
			'RewriteCond %{HTTP_REFERER} (order(\s|%20)by(\s|%20)1--) [NC,OR]',
			'RewriteCond %{HTTP_REFERER} (blue\spill|cocaine|ejaculat|erectile|erections|hoodia|huronriveracres|impotence|levitra|libido|lipitor|phentermin|pro[sz]ac|sandyauer|tramadol|troyhamby|ultram|unicauca|valium|viagra|vicodin|xanax|ypxaieo) [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
			'<IfModule mod_rewrite.c>',
			'RewriteCond %{REQUEST_METHOD} ^(connect|debug|move|trace|track) [NC]',
			'RewriteRule .* - [F,L]',
			'</IfModule>',
		);
		$htaccess = ABSPATH . '.htaccess';
		if ( function_exists( 'insert_with_markers') ) {
			return insert_with_markers( $htaccess, 'Dam Spam', ( array ) $insertion );
		}
	}
	else {
		update_option( 'ds_enable_firewall', 'no' );
		add_action( 'admin_notices', 'ds_admin_notice_success' );
		$htaccess = ABSPATH . '.htaccess';
		return insert_with_markers( $htaccess, 'Dam Spam', '' );
	}
}
add_action( 'admin_init', 'ds_enable_firewall' );

// Notification Control settings update
function ds_update_notification_control() {
	if ( empty( $_POST['ds_notification_control_setting'] ) || 'ds_notification_control_update' != $_POST['ds_notification_control_setting'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	if ( $_POST['submit'] == "Reset" ) {
		if ( $_POST['ds_reset_hidden_notice'] == 'all') {
			global $wpdb;
			delete_user_meta( get_current_user_id(), 'ds_notice_preference' );
			$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 'ds_notice_preference'" );
		} else {
			delete_user_meta( get_current_user_id(), 'ds_notice_preference' );
		}
		return;
	}
	if ( isset( $_POST['ds_hide_admin_notices'] ) and $_POST['ds_hide_admin_notices'] == 'yes' )
		update_option( 'ds_hide_admin_notices', 'yes' );
	else
		update_option( 'ds_hide_admin_notices', 'no' );
	if ( !isset( $_POST['ds_disable_admin_emails'] ) ) {
		update_option( 'ds_disable_admin_emails', 'no' );
		update_option( 'ds_disable_admin_emails_update', 'no' );
		update_option( 'ds_disable_admin_emails_comment', 'no' );
		update_option( 'ds_disable_admin_emails_password_reset', 'no' );
		update_option( 'ds_disable_admin_emails_new_user', 'no' );
	} else {
		if ( isset( $_POST['ds_disable_admin_emails_update'] ) and $_POST['ds_disable_admin_emails_update'] == 'yes' )
			update_option( 'ds_disable_admin_emails_update', 'yes' );
		else
			update_option( 'ds_disable_admin_emails_update', 'no' );
		if ( isset( $_POST['ds_disable_admin_emails_comment'] ) and $_POST['ds_disable_admin_emails_comment'] == 'yes' )
			update_option( 'ds_disable_admin_emails_comment', 'yes' );
		else
			update_option( 'ds_disable_admin_emails_comment', 'no' );
		if ( isset( $_POST['ds_disable_admin_emails_password_reset'] ) and $_POST['ds_disable_admin_emails_password_reset'] == 'yes' )
			update_option( 'ds_disable_admin_emails_password_reset', 'yes' );
		else
			update_option( 'ds_disable_admin_emails_password_reset', 'no' );
		if ( isset( $_POST['ds_disable_admin_emails_new_user'] ) and $_POST['ds_disable_admin_emails_new_user'] == 'yes' )
			update_option( 'ds_disable_admin_emails_new_user', 'yes' );
		else
			update_option( 'ds_disable_admin_emails_new_user', 'no' );
		if ( ( isset( $_POST['ds_disable_admin_emails_update'] ) and $_POST['ds_disable_admin_emails_update'] == 'yes' )
			|| ( isset( $_POST['ds_disable_admin_emails_comment'] ) and $_POST['ds_disable_admin_emails_comment'] == 'yes' )
			|| ( isset( $_POST['ds_disable_admin_emails_password_reset'] ) and $_POST['ds_disable_admin_emails_password_reset'] == 'yes' )
			|| ( isset( $_POST['ds_disable_admin_emails_new_user'] ) and $_POST['ds_disable_admin_emails_new_user'] == 'yes' )
		)
			update_option( 'ds_disable_admin_emails', 'yes' );
		else
			update_option( 'ds_disable_admin_emails', 'no' );
	}
	if ( isset( $_POST['ds_disable_core_nudge'] ) and $_POST['ds_disable_core_nudge'] == 'yes' )
		update_option( 'ds_disable_core_nudge', 'yes' );
	else
		update_option( 'ds_disable_core_nudge', 'no' );
	if ( isset( $_POST['ds_disable_theme_nudge'] ) and $_POST['ds_disable_theme_nudge'] == 'yes' )
		update_option( 'ds_disable_theme_nudge', 'yes' );
	else
		update_option( 'ds_disable_theme_nudge', 'no' );
	if ( isset( $_POST['ds_disable_plugin_nudge'] ) and $_POST['ds_disable_plugin_nudge'] == 'yes' )
		update_option( 'ds_disable_plugin_nudge', 'yes' );
	else
		update_option( 'ds_disable_plugin_nudge', 'no' );
}
add_action( 'admin_init', 'ds_update_notification_control' );

// Notification Control: for core/plugin/theme updates
if ( get_option( 'ds_disable_admin_emails_update', 'no' ) === 'yes' ) {
	add_filter( 'auto_core_update_send_email', '__return_false' );
	add_filter( 'auto_theme_update_send_email', '__return_false' );
	add_filter( 'auto_plugin_update_send_email', '__return_false' );
}

// Notification Control: for comments
if ( get_option( 'ds_disable_admin_emails_comment', 'no' ) === 'yes' ) {
	function wp_notify_postauthor( $comment_id, $deprecated = null ) {}
	function wp_notify_moderator( $comment_id ) {}
}

// Notification Control: for reset password
if ( get_option( 'ds_disable_admin_emails_password_reset', 'no' ) === 'yes' ) {
	function wp_password_change_notification( $user ) {}
}

// Notification Control: for new user registration
if ( get_option( 'ds_disable_admin_emails_new_user', 'no' ) === 'yes' ) {
	remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
	add_action( 'register_new_user', function( $user_id, $notify = 'user' ) {
		wp_send_new_user_notifications( $user_id, $notify );
	} );
}

// Notification Control: for native nudges
function ds_remove_core_updates() {
	global $wp_version;
	return( object ) array( 'last_checked' => time(), 'version_checked' => $wp_version );
}
if ( get_option( 'ds_disable_core_nudge', 'no' ) === 'yes' ) {
	add_filter( 'pre_site_transient_update_core', 'ds_remove_core_updates' );
}
if ( get_option( 'ds_disable_theme_nudge', 'no' ) === 'yes' ) {
	add_filter( 'pre_site_transient_update_themes', 'ds_remove_core_updates' );
}
if ( get_option( 'ds_disable_plugin_nudge', 'no' ) === 'yes' ) {
	add_filter( 'pre_site_transient_update_plugins', 'ds_remove_core_updates' );
}

// enable custom login
function ds_enable_custom_login() {
	if ( empty( $_POST['ds_login_setting_placeholder'] ) || 'ds_login_setting' != $_POST['ds_login_setting_placeholder'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	if ( isset( $_POST['ds_login_setting'] ) and $_POST['ds_login_setting'] == 'yes' ) {
		update_option( 'ds_enable_custom_login', 'yes' );
		add_action( 'admin_notices', 'ds_admin_notice_success' );
		ds_install_custom_login();
	} else {
		update_option( 'ds_enable_custom_login', 'no' );
		add_action( 'admin_notices', 'ds_admin_notice_success' );
		ds_uninstall_custom_login();
	}
}
add_action( 'admin_init', 'ds_enable_custom_login' );

// manage honeypot settings
function ds_update_honeypot() {
	if ( empty( $_POST['ds_notification_control_setting'] ) || 'ds_notification_control_update' != $_POST['ds_notification_control_setting'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	if ( isset( $_POST['ds_honeypot_cf7'] ) and $_POST['ds_honeypot_cf7'] == 'yes' )
		update_option( 'ds_honeypot_cf7', 'yes' );
	else
		update_option( 'ds_honeypot_cf7', 'no' );
	if ( isset( $_POST['ds_honeypot_bbpress'] ) and $_POST['ds_honeypot_bbpress'] == 'yes' )
		update_option( 'ds_honeypot_bbpress', 'yes' );
	else
		update_option( 'ds_honeypot_bbpress', 'no' );
	if ( isset( $_POST['ds_honeypot_elementor'] ) and $_POST['ds_honeypot_elementor'] == 'yes' )
		update_option( 'ds_honeypot_elementor', 'yes' );
	else
		update_option( 'ds_honeypot_elementor', 'no' );
	if ( isset( $_POST['ds_honeypot_divi'] ) and $_POST['ds_honeypot_divi'] == 'yes' )
		update_option( 'ds_honeypot_divi', 'yes' );
	else
		update_option( 'ds_honeypot_divi', 'no' );
	if ( isset( $_POST['ds_allow_vpn'] ) and $_POST['ds_allow_vpn'] == 'yes' )
		update_option( 'ds_allow_vpn', 'yes' );
	else
		update_option( 'ds_allow_vpn', 'no' );
}
add_action( 'admin_init', 'ds_update_honeypot' );

// process to setup login type
function ds_login_type_func() {
	if ( empty( $_POST['ds_login_type_field'] ) || 'ds_login_type' != $_POST['ds_login_type_field'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	if ( isset( $_POST['ds_login_type'] ) ) {
		update_option( 'ds_login_type', $_POST['ds_login_type'] );
		add_action( 'admin_notices', 'ds_admin_notice_success' );
	}
}
add_action( 'admin_init', 'ds_login_type_func' ); 

// install default pages for custom login
function ds_install_custom_login() {
	$pages =  array(
		'login'           => __( 'Log In', 'dam-spam' ),
		'logout'          => __( 'Log Out', 'dam-spam' ),
		'register'        => __( 'Register', 'dam-spam' ),
		'forgot-password' => __( 'Forgot Password', 'dam-spam' ),
	);
	foreach( $pages as $slug => $title ) {
		$page_id = ds_get_page_id( $slug );
		if ( $page_id > 0 ) {
			wp_update_post( array(
				'ID'			 => $page_id,
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_content'   => '[ds-login]',
				'comment_status' => 'closed',
				'ping_status'    => 'closed'
			) );
		} else {
			wp_insert_post( array(
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_content'   => '[ds-login]',
				'comment_status' => 'closed',
				'ping_status'    => 'closed'
			) );
		}
	}
}

// uninstall default pages for custom login
function ds_uninstall_custom_login() {
	$pages = array(
		'login'           => __( 'Log In', 'dam-spam' ),
		'logout'          => __( 'Log Out', 'dam-spam' ),
		'register'        => __( 'Register', 'dam-spam' ),
		'forgot-password' => __( 'Forgot Password', 'dam-spam' ),
	);
	foreach( $pages as $slug => $title ) {
		$page_id = ds_get_page_id( $slug );
		wp_delete_post( $page_id, true );
	}	
}

function ds_get_page_id( $slug ) {
	$page = get_page_by_path( $slug );
	if ( !isset( $page->ID ) )
		return null;
	else 
		return $page->ID;
}

add_action( 'template_redirect', function() {
	global $post;
	if ( is_page( 'logout' ) ) {
		$user = wp_get_current_user();
		wp_logout();
		if ( !empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
		} else {
			$redirect_to = site_url( 'login/?loggedout=true' );
			$requested_redirect_to = '';
		}
		$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );
		wp_safe_redirect( $redirect_to );
		exit;
	}
	if ( is_user_logged_in() && ( $post->post_name == 'login' or $post->post_name == 'register' or $post->post_name == 'forgot-password' ) ) {
		wp_redirect( admin_url() );
		exit;
	}
	if ( $post->post_name == 'login' )
		ds_login();
	elseif ( $post->post_name == 'register' )
		ds_register();
	elseif ( $post->post_name == 'forgot-password' )
		ds_forgot_password();
} );

function ds_forgot_password() {
	global $wpdb, $wp_hasher;
	if ( empty( $_POST ) )
		return;
	$errors = new WP_Error();
	if ( empty( $_POST['user_login'] ) ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.', 'dam-spam' ) );
	} else if ( strpos( $_POST['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( wp_unslash( $_POST['user_login'] ) ) );
		if ( empty( $user_data ) )
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.', 'dam-spam' ) );
	} else {
		$login = trim( $_POST['user_login'] );
		$user_data = get_user_by( 'login', $login );
	}
	do_action( 'lostpassword_post', $errors );
	if ( $errors->get_error_code() ){
		$GLOBALS['ds_error'] = $errors;
		return;
	}
	if ( !$user_data ) {
		$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: Invalid username or email.', 'dam-spam' ) );
		$GLOBALS['ds_error'] = $errors;
		return;
	}
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );
	if ( is_wp_error( $key ) ) {
		$GLOBALS['ds_error'] = $key;
	}
	$message  = __( 'Someone requested that the password be reset for the following account:', 'dam-spam' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf( __( 'Username: %s', 'dam-spam' ), $user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'dam-spam' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:', 'dam-spam' ) . "\r\n\r\n";
	$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	$title    = sprintf( __( '[%s] Password Reset', 'dam-spam' ), $blogname );
	$title    = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
	$message  = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	if ( $message && !wp_mail( $user_email, $title, $message ) ) {
		wp_die( __( 'The email could not be sent.', 'dam-spam' ) . "<br>\n" . __( 'Possible reason: your host may have disabled the mail() function...', 'dam-spam' ) );
		wp_redirect( home_url( '/login/?rp=link(target, link)-sent' ) );
		exit;
	}
}
add_shortcode( 'ds-login', 'ds_login_cb' );

function ds_login_cb() {
	global $post;
	if ( !is_page() )
		return;
	switch ( $post->post_name ) {
		case 'login':
			ds_login_page();
			break;
		case 'register':
			ds_register_page();
			break;
		case 'forgot-password':
			ds_forgot_password_page();
			break;
		default:
			break;
	}
}

function ds_login_page() {
	include __DIR__ . '/templates/login.php';
}

function ds_register_page() {
	include __DIR__ . '/templates/register.php';
}

function ds_forgot_password_page() {
	include __DIR__ . '/templates/forgot-password.php';
}

function ds_show_error() {
	global $ds_error;
	if ( isset( $ds_error->errors ) ) {
		foreach( $ds_error->errors as $errors ) {
			foreach( $errors as $e ) {
				echo "<div style='color:#721c24;background-color:#f8d7da;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #f5c6cb'>$e</div>";
			}
		}
	}
}

function ds_register() {
	if ( !get_option( 'users_can_register' ) ) {
		$redirect_to = site_url( 'wp-login.php?registration=disabled' );
		wp_redirect( $redirect_to );
		exit;
	}	
	$user_login = '';
	$user_email = '';
	if ( !empty( $_POST ) && ( $_POST['user_url'] == 'https://example.com/' ) ) {
		$user_login = isset( $_POST['user_login'] ) ? $_POST['user_login'] : '';
		$user_email = isset( $_POST['user_email'] ) ? $_POST['user_email'] : '';
		$register_error = register_new_user( $user_login, $user_email );
		if ( !is_wp_error( $register_error ) ) {
			$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : site_url( 'wp-login.php?checkemail=registered' );
			wp_safe_redirect( $redirect_to );
			exit;
		}
		$GLOBALS['ds_error'] = $register_error;
	}
}

function ds_login() {
	$secure_cookie = '';
	$interim_login = isset( $_REQUEST['interim-login'] );
	if ( !empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// redirect to https if user wants SSL
		if ( $secure_cookie && false !== strpos( $redirect_to, 'wp-admin' ) )
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
	} else {
		$redirect_to = admin_url();
	}
	$reauth = empty( $_REQUEST['reauth'] ) ? false : true;
	if ( isset( $_POST['log'] ) || isset( $_GET['testcookie'] ) ) {
		$user = wp_signon( array(), $secure_cookie );
		$redirect_to = apply_filters( 'login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user );
		// $user = wp_get_current_user();
		if ( !is_wp_error( $user ) && !$reauth ) {
			if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
				// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
				if ( is_multisite() && !get_active_blog_for_user( $user->ID ) && !is_super_admin( $user->ID ) )
					$redirect_to = user_admin_url();
				elseif ( is_multisite() && !$user->has_cap( 'read' ) )
					$redirect_to = get_dashboard_url( $user->ID );
				elseif ( !$user->has_cap( 'edit_posts' ) )
					$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
					wp_redirect( $redirect_to );
				exit;
			}
			wp_safe_redirect( $redirect_to );
			exit;
		}
		$GLOBALS['ds_error'] = $user;
	}
}

function ds_login_url( $url ) {
	if ( get_option( 'ds_enable_custom_login', '' ) == 'yes' and !is_user_logged_in() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		include( get_query_template( '404' ) );
		exit;
	}
	return $url;
}
add_filter( 'login_url', 'ds_login_url', 10, 2 );

function ds_logout_url( $url, $redirect ) {
	if ( get_option( 'ds_enable_custom_login', '' ) == 'yes' )
		$url = home_url( 'logout' );
	return $url;
}
add_filter( 'logout_url', 'ds_logout_url', 10, 2 );

// enable custom login module 
function ds_custom_login_module() {
	if ( get_option( 'ds_login_type', '' ) == "username" ) {
		remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
	} else if ( get_option( 'ds_login_type', '' ) == "email" ) {
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
	}
}
add_action( 'init', 'ds_custom_login_module' );

// add_filter( 'gettext', 'ds_login_text' );
function ds_login_text( $translating ) {
	if ( get_option( 'ds_login_type', '' ) == "username" ) {	
		return str_ireplace( 'Username or Email Address', 'Username', $translating );
	} else if ( get_option( 'ds_login_type', '' ) == "email" ) {
		return str_ireplace( 'Username or Email Address', 'Email Address', $translating );
	} else {
		return $translating;
	}
}

// add menu option for login/logout links
function ds_add_nav_menu_metabox() {
	if ( get_option( 'ds_enable_custom_login', '' ) == 'yes' ) {
		add_meta_box( 'ds_menu_option', 'Dam Spam', 'ds_nav_menu_metabox', 'nav-menus', 'side', 'default' );
	}
}
add_action( 'admin_head-nav-menus.php', 'ds_add_nav_menu_metabox' );

function ds_nav_menu_metabox( $object ) {
	global $nav_menu_selected_id;
	$elems = array(
		'#ds-nav-login'    => __( 'Log In', 'dam-spam' ),
		'#ds-nav-logout'   => __( 'Log Out', 'dam-spam' ),
		'#ds-nav-register' => __( 'Register', 'dam-spam' ),
		'#ds-nav-loginout' => __( 'Log In', 'dam-spam' ) . '/' . __( 'Log Out', 'dam-spam' )
	);
	$temp = ( object ) array(
				'ID'			   => 1,
				'object_id'		   => 1,
				'type_label'	   => '',
				'title'			   => '',
				'url'			   => '',
				'type'			   => 'custom',
				'object'		   => 'ds-slug',
				'db_id'			   => 0,
				'menu_item_parent' => 0,
				'post_parent'	   => 0,
				'target'		   => '',
				'attr_title'	   => '',
				'description'	   => '',
				'classes'		   => array(),
				'xfn'			   => '',
			);
	// create an array of objects that imitate post objects
	$ds_items = array();
	$i = 0;
	foreach ( $elems as $k => $v ) {
		$ds_items[$i] = ( object ) array();
		$ds_items[$i]->ID				 = 1;
		$ds_items[$i]->url 			 = esc_attr( $k );
		$ds_items[$i]->title 			 = esc_attr( $v );
		$ds_items[$i]->object_id		 = esc_attr( $k );
		$ds_items[$i]->type_label 		 = "Dynamic Link";
		$ds_items[$i]->type 			 = 'custom';
		$ds_items[$i]->object 			 = 'ds-slug';
		$ds_items[$i]->db_id 			 = 0;
		$ds_items[$i]->menu_item_parent = 0;
		$ds_items[$i]->post_parent 	 = 0;
		$ds_items[$i]->target 			 = '';
		$ds_items[$i]->attr_title 		 = '';
		$ds_items[$i]->description 	 = '';
		$ds_items[$i]->classes 		 = array();
		$ds_items[$i]->xfn 			 = '';
		$i++;
	}
	$walker = new Walker_Nav_Menu_Checklist( array() );
	?>
	<div id="ds-div">
		<div id="tabs-panel-ds-all" class="tabs-panel tabs-panel-active">
			<ul id="ds-checklist-pop" class="categorychecklist form-no-clear" >
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $ds_items ), 0, ( object ) array( 'walker' => $walker ) ); ?>
			</ul>
			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'dam-spam' ); ?>" name="ds-menu-item" id="submit-ds-div">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
	</div>
	<?php
}

function ds_nav_menu_type_label( $menu_item ) {
	$elems = array( '#ds-nav-login', '#ds-nav-logout', '#ds-nav-register', '#ds-nav-loginout' );
	if ( isset( $menu_item->object, $menu_item->url ) && 'custom' == $menu_item->object && in_array( $menu_item->url, $elems ) ) {
		$menu_item->type_label = 'Dynamic Link';
	}
	return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'ds_nav_menu_type_label' );

function ds_loginout_title( $title ) {
	$titles = explode( '/', $title );
	if ( !is_user_logged_in() ) {
		return esc_html( isset( $titles[0] ) ? $titles[0]: __( 'Log In', 'dam-spam' ) );
	} else {
		return esc_html( isset($titles[1] ) ? $titles[1] : __( 'Log Out', 'dam-spam' ) );
	}
}

function ds_setup_nav_menu_item( $item ) {
	global $pagenow;
	if ( $pagenow != 'nav-menus.php' && !defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#ds-nav' ) and get_option( 'ds_enable_custom_login', '' ) != 'yes' ) {
		$item->_invalid = true;	
	} else if ( $pagenow != 'nav-menus.php' && !defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#ds-nav' ) != '' ) {	
		$login_url 	= get_permalink( get_page_by_path( 'login' ) );
		$logout_url = get_permalink( get_page_by_path( 'logout' ) );
		switch( $item->url ) {
			case '#ds-nav-login':
				$item->url = get_permalink( get_page_by_path( 'login' ) );
				$item->_invalid = ( is_user_logged_in() ) ?  true : false;
				break;
			case '#ds-nav-logout':
				$item->url = get_permalink( get_page_by_path( 'logout' ) );
				$item->_invalid = ( !is_user_logged_in() ) ?  true : false;
				break;
			case '#ds-nav-register':
				$item->url = get_permalink( get_page_by_path( 'register' ) );
				$item->_invalid = ( is_user_logged_in() ) ?  true : false;
			break;
			default: 
			$item->url = ( is_user_logged_in() ) ? $logout_url : $login_url;
			$item->title = ds_loginout_title( $item->title );
		}
	}
	return $item;
}
add_filter( 'wp_setup_nav_menu_item', 'ds_setup_nav_menu_item' );

// enable limit login attempts
function ds_limit_login_attempts() {
	if ( empty( $_POST['ds_login_setting_placeholder'] ) || 'ds_login_setting' != $_POST['ds_login_setting_placeholder'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_login_type_nonce'], 'ds_login_type_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;

	if ( isset( $_POST['ds_login_attempts'] ) and $_POST['ds_login_attempts'] == 'yes' ) {
		update_option( 'ds_login_attempts', 'yes' );
	} else {
		update_option( 'ds_login_attempts', 'no' );
	}
	update_option( 'ds_login_attempts_threshold', $_POST['ds_login_attempts_threshold'] );
	update_option( 'ds_login_attempts_duration', $_POST['ds_login_attempts_duration'] );
	update_option( 'ds_login_attempts_unit', $_POST['ds_login_attempts_unit'] );
	update_option( 'ds_login_lockout_duration', $_POST['ds_login_lockout_duration'] );
	update_option( 'ds_login_lockout_unit', $_POST['ds_login_lockout_unit'] );
}
add_action( 'admin_init', 'ds_limit_login_attempts' );

function ds_authenticate( $user, $username, $password ) {
	$field = is_email( $username ) ? 'email' : 'login';
	$time  = time();
	if ( !$userdata = get_user_by( $field, $username ) )
		return $user;
	if ( ds_is_user_locked( $userdata->ID ) && get_option( 'ds_login_attempts', 'no' ) === 'yes'  ) {
		if ( $expiration = ds_get_user_lock_expiration( $userdata->ID ) ) {
			return new WP_Error( 'locked_account', sprintf( __( '<strong>ERROR</strong>: This account has been locked because of too many failed login attempts. You may try again in %s.', 'dam-spam' ), human_time_diff( $time, $expiration ) ) );
		} else {
			return new WP_Error( 'locked_account', __( '<strong>ERROR</strong>: This account has been locked.', 'dam-spam' ) );
		}
	} elseif ( is_wp_error( $user ) && 'incorrect_password' == $user->get_error_code() && get_option( 'ds_login_attempts', 'no') === 'yes' ) {
		ds_add_failed_login_attempt( $userdata->ID );
		$attempts = get_user_meta( $userdata->ID, 'ds_failed_login_attempts', true);
		if ( count( $attempts ) >= ( get_option( 'ds_login_attempts_threshold', 5 ) * 2 ) ) {
			$lockout_expiry = '+' . get_option( 'ds_login_lockout_duration', 24 ) . ' ' . get_option( 'ds_login_lockout_unit', 'hour' );
			$expiration = strtotime( $lockout_expiry );
			ds_lock_user( $userdata->ID, $expiration );
			return new WP_Error( 'locked_account', sprintf( __( '<strong>ERROR</strong>: This account has been locked because of too many failed login attempts. You may try again in %s.', 'dam-spam' ), human_time_diff( $time, $expiration ) ) );
		}
	}
	return $user;
}
add_action( 'authenticate', 'ds_authenticate', 100, 3 );

function ds_add_failed_login_attempt( $user_id ) {
	$new_attempts = array();
	$threshold = '-'. get_option( 'ds_login_attempts_duration', 5 ) . ' ' . get_option( 'ds_login_attempts_unit', 'hour' );
	$threshold_date_time = strtotime( $threshold );
	$attempts = get_user_meta( $user_id, 'ds_failed_login_attempts', true );
	if ( !is_array( $attempts ) )
		$attempts = array();
	$attempts[] = array( 'time' => time(), 'ip' => $_SERVER['REMOTE_ADDR'] );
	foreach( $attempts as $a ) {
		if ( $threshold_date_time < $a['time'] )
			$new_attempts[] = $a;
	}
	update_user_meta( $user_id, 'ds_failed_login_attempts', array() );
	update_user_meta( $user_id, 'ds_failed_login_attempts', $new_attempts );
}

function ds_is_user_locked( $user_id ) {
	if ( get_user_meta( $user_id, 'ds_is_locked', true ) == false )
		return false;
	if ( !$expires = ds_get_user_lock_expiration( $user_id ) )
		return true;
	$time = time();
	if ( $time > $expires ) {
		ds_unlock_user( $user_id );
		return false;
	}
	return true;
}

function ds_get_user_lock_expiration( $user_id ) {
	return get_user_meta( $user_id, 'ds_lock_expiration', true );
}

function ds_lock_user ( $user_id, $expiration ) {	
	update_user_meta( $user_id, 'ds_is_locked', true );
	update_user_meta( $user_id, 'ds_lock_expiration', $expiration );
	update_user_meta( $user_id, 'ds_failed_login_attempts', array() );
}

function ds_unlock_user( $user_id ) {
	update_user_meta( $user_id, 'ds_is_locked', false );
	update_user_meta( $user_id, 'ds_lock_expiration', '' );
	update_user_meta( $user_id, 'ds_failed_login_attempts', array() );
}

// export that generates a .json file of the shop settings
function ds_proceds_settings_export() {
	if ( empty( $_POST['ds_action'] ) || 'export_settings' != $_POST['ds_action'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_export_nonce'], 'ds_export_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	$settings = get_option( 'ds_settings' );
	$options  = ds_get_options();
	ignore_user_abort( true );
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=ds-settings-export-' . date( 'm-d-Y H:i:s' ) . '.json' );
	header( "Expires: 0" );
	echo json_encode( $options );
	exit;
}
add_action( 'admin_init', 'ds_proceds_settings_export' );

function ds_export_excel() {
	if ( empty( $_POST['export_log'] ) || 'export_log_data' != $_POST['export_log'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_export_action'], 'ds_export_action' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue( 'A1', 'Date/Time' );
		$sheet->setCellValue( 'B1', 'Email' );
		$sheet->setCellValue( 'C1', 'IP' );
		$sheet->setCellValue( 'D1', 'Author, User/Pwd' );
		$sheet->setCellValue( 'E1', 'Script' );
		$sheet->setCellValue( 'F1', 'Reason' );
		$stats = ds_get_stats();
		extract( $stats );
		$index = 2;
		foreach ( $stats['hist'] as $key => $value ) {
		$sheet->setCellValue( 'A'.$index, $key );
		$sheet->setCellValue( 'B'.$index, $value[1] );
		$sheet->setCellValue( 'C'.$index, $value[0] );
		$sheet->setCellValue( 'D'.$index, $value[2] );
		$sheet->setCellValue( 'E'.$index, $value[3] );
		$sheet->setCellValue( 'F'.$index, $value[4] );
		$index++;
		}
		// redirect output to a client's web browser (xlsx)
		header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		header( 'Content-Disposition: attachment;filename="ds_log_' . time() . '.xlsx"' );
		header( 'Cache-Control: max-age=0' );
		// if you're serving to IE 9, then the following may be needed
		header( 'Cache-Control: max-age=1' );
		$writer = IOFactory::createWriter( $spreadsheet, 'Xlsx' );
		$writer->save( 'php://output' );
		exit;
}
add_action( 'admin_init', 'ds_export_excel' );

// settings import from a json file
function ds_proceds_settings_import() {
	if ( empty( $_POST['ds_action'] ) || 'import_settings' != $_POST['ds_action'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_import_nonce'], 'ds_import_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	// $extension = end( explode( '.', $_FILES['import_file']['name'] ) );
	$extension = $_FILES['import_file']['type'] ;
	// if ( $extension != 'json' ) {
	if ( $extension != 'application/json' ) {
		wp_die( __( 'Please upload a valid .json file', 'dam-spam' ) );
	}
	$import_file = $_FILES['import_file']['tmp_name'];
	if ( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import', 'dam-spam' ) );
	}
	// retrieve the settings from the file and convert the json object to an array
	$options = ( array ) json_decode( file_get_contents( $import_file ) );	
	ds_set_options( $options );
	add_action( 'admin_notices', 'ds_admin_notice_success' );
	// wp_safe_redirect( admin_url( 'admin.php?page=ds-advanced' ) ); 
	// add_action( 'admin_notices', 'ds_admin_notice_success' );
	// exit;
}
add_action( 'admin_init', 'ds_proceds_settings_import' );

// settings reset from a json file
function ds_proceds_settings_reset() {
	if ( empty( $_POST['ds_action'] ) || 'reset_settings' != $_POST['ds_action'] )
		return;
	if ( !wp_verify_nonce( $_POST['ds_reset_nonce'], 'ds_reset_nonce' ) )
		return;
	if ( !current_user_can( 'manage_options' ) )
		return;
	$url	 = plugin_dir_path( __FILE__ ) . '/modules/default.json'; 
	$options = ( array ) json_decode( file_get_contents( $url ) );
	ds_set_options( $options );
	add_action( 'admin_notices', 'ds_admin_notice_success' );
}
add_action( 'admin_init', 'ds_proceds_settings_reset' );

// shortcodes to print the username, name, and email
function show_loggedin_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) 
		return $current_user->display_name;
}
add_shortcode( 'show-displayname-as', 'show_loggedin_function' );

function show_fullname_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) 
		return $current_user->user_firstname . ' ' . $current_user->user_lastname;
}
add_shortcode( 'show-fullname-as', 'show_fullname_function' );

function show_id_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) 
		return $current_user->ID;
}
add_shortcode( 'show-id-as', 'show_id_function' );

function show_level_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) 
		return $current_user->user_level;	
}
add_shortcode( 'show-level-as', 'show_level_function' );

function show_email_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) 
		return $current_user->user_email;
}
add_shortcode( 'show-email-as', 'show_email_function' );

function ds_getRemote_ip_address() {
	if ( !empty($_SERVER['HTTP_CLIENT_IP'] ) ) {
		return $_SERVER['HTTP_CLIENT_IP'];
	} else if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	return $_SERVER['REMOTE_ADDR'];
}

function ds_check_proxy() {
	$timeout = 5;
	$banOnProbability = 0.99;
	$ip = ds_getRemote_ip_address();
    $ch = curl_init();
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
	curl_setopt( $ch, CURLOPT_URL, "https://check.getipintel.net/check.php?ip=$ip&contact=$DS_MAIL" );
	$response = curl_exec( $ch );
	if ( $response > $banOnProbability ) {
		$is_vpn = true;
	} else {
		if ( $response < 0 || strcmp( $response, "" ) == 0 ) {
		}
		$is_vpn = false;		
	}
	return $is_vpn;
}

function ds_disable_activities() {
	if ( get_option( 'ds_allow_vpn' ) == 'no' ) {
		return;
	}
    // disable login, checkout, comments
	if ( ( substr_count( $_SERVER['REQUEST_URI'], 'wp-login' ) || get_permalink() === wp_login_url() || substr_count( $_SERVER['REQUEST_URI'], 'checkout' ) ) || substr_count( $_SERVER['REQUEST_URI'], 'wp-comments-post' )) {
		$is_vpn = ds_check_proxy();
		if ( $is_vpn == true ) {
			status_header( 403 );
			wp_die( esc_html__( 'You cannot access this page.', 'dam-spam' ) );
		}
	}
}
add_action( 'init', 'ds_disable_activities' );
