<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

function dam_spam_admin_notice_success() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Options Updated', 'dam-spam' ); ?></p>
	</div>
	<?php
}

if ( defined( 'DAM_SPAM_ENABLE_FIREWALL' ) ) {
	include __DIR__ . '/includes/firewall.php';
}

function dam_spam_advanced_menu() {
	$dam_spam_firewall_setting = '';
	if ( get_option( 'dam_spam_enable_firewall', '' ) === 'yes' ) {
		$dam_spam_firewall_setting = "checked='checked'";
	}
	$dam_spam_login_setting = '';
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		$dam_spam_login_setting = "checked='checked'";
	}
	$dam_spam_login_attempts = '';
	if ( get_option( 'dam_spam_login_attempts', '' ) === 'yes' ) {
		$dam_spam_login_attempts = "checked='checked'";
	}
	$dam_spam_login_type_default = '';
	$dam_spam_login_type_username = '';
	$dam_spam_login_type_email = '';
	$login_type = get_option( 'dam_spam_login_type', '' );
	if ( $login_type === 'username' ) {
		$dam_spam_login_type_username = "checked='checked'";
	} elseif ( $login_type === 'email' ) {
		$dam_spam_login_type_email = "checked='checked'";
	} else {
		$dam_spam_login_type_default = "checked='checked'";
	}
	$dam_spam_honeypot_cf7 = '';
	if ( get_option( 'dam_spam_honeypot_cf7', 'yes' ) === 'yes' && is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$dam_spam_honeypot_cf7 = "checked='checked'";
	}
	$dam_spam_honeypot_bbpress = '';
	if ( get_option( 'dam_spam_honeypot_bbpress', 'yes' ) === 'yes' && is_plugin_active( 'bbpress/bbpress.php' ) ) {
		$dam_spam_honeypot_bbpress = "checked='checked'";
	}
	$dam_spam_honeypot_elementor = '';
	if ( get_option( 'dam_spam_honeypot_elementor', 'yes' ) === 'yes' && is_plugin_active( 'elementor/elementor.php' ) ) {
		$dam_spam_honeypot_elementor = "checked='checked'";
	}
	$theme = wp_get_theme();
	$dam_spam_honeypot_divi = '';
	if ( get_option( 'dam_spam_honeypot_divi', 'yes' ) === 'yes' && ( $theme->name === 'Divi' || $theme->parent_theme === 'Divi' ) ) {
		$dam_spam_honeypot_divi = "checked='checked'";
	}
	$dam_spam_allow_vpn_setting = '';
	if ( get_option( 'dam_spam_allow_vpn', '' ) === 'yes' ) {
		$dam_spam_allow_vpn_setting = "checked='checked'";
	}
	?>
	<div id="dam-spam-plugin" class="wrap">
		<h1 id="dam-spam-head"><?php esc_html_e( 'Advanced — Dam Spam', 'dam-spam' ); ?></h1>
		<div class="metabox-holder">
			<div class="postbox">
				<form method="post">
					<div class="inside">
						<h3><span><?php esc_html_e( 'Firewall Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_firewall_setting">
								<?php if ( defined( 'DAM_SPAM_ENABLE_FIREWALL' ) ) { ?>
								<p><a href="edit.php?post_type=dam-spam-firewall" class="button-primary"><?php esc_html_e( 'Monitor Real-time Firewall', 'dam-spam' ); ?></a></p>
								<?php } else { ?>
								<p><em><?php esc_html_e( 'For advanced users only: If you\'d like to enable the real-time firewall beta feature, add define( \'DAM_SPAM_ENABLE_FIREWALL\', true ); to your wp-config.php file. This feature is resource-intensive, requiring a lot of memory and database space.', 'dam-spam' ); ?></em></p>
								<?php } ?>
								<input type="checkbox" name="dam_spam_firewall_setting" id="dam_spam_firewall_setting" value="yes" <?php echo esc_attr( $dam_spam_firewall_setting ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Enable Server-side Security Rules', 'dam-spam' ); ?>
								<p><em><?php esc_html_e( 'For advanced users only: This option will modify your .htaccess file with extra security rules and in some small cases, conflict with your server settings. If you do not understand how to edit your .htaccess file to remove these rules in the event of an error, do not enable.', 'dam-spam' ); ?></em></p>
							</label>
						</div>
						<p><input type="hidden" name="dam_spam_firewall_setting_placeholder" value="dam_spam_firewall_setting"></p>
					</div>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Login Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_login_setting">
								<input type="checkbox" name="dam_spam_login_setting" id="dam_spam_login_setting" value="yes" <?php echo esc_attr( $dam_spam_login_setting ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Enable themed registration and login pages (disables the default wp-login.php).', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam_spam_login_attempts">
								<input type="checkbox" name="dam_spam_login_attempts" id="dam_spam_login_attempts" value="yes" <?php echo esc_attr( $dam_spam_login_attempts ); ?>>
								<span><small></small></span>
								<strong><?php esc_html_e( 'Login Attempts:', 'dam-spam' ); ?></strong>
								<?php
								// translators: Label before the threshold number input field
								esc_html_e( 'After', 'dam-spam' );
								?>
								<input type="text" name="dam_spam_login_attempts_threshold" id="dam_spam_login_attempts_duration" class="dam-spam-small-box" value="<?php echo esc_attr( get_option( 'dam_spam_login_attempts_threshold', 5 ) ); ?>">
								<?php
								// translators: Label between threshold and duration fields
								esc_html_e( 'failed login attempts within', 'dam-spam' );
								?>
								<input type="text" name="dam_spam_login_attempts_duration" id="dam_spam_login_attempts_duration" class="dam-spam-small-box" value="<?php echo esc_attr( get_option( 'dam_spam_login_attempts_duration', 1 ) ); ?>">
								<select name="dam_spam_login_attempts_unit" id="dam_spam_login_attempts_unit" class="dam-spam-small-dropbox">
									<option value="minute" <?php selected( get_option( 'dam_spam_login_attempts_unit', 'hour' ), 'minute' ); ?>><?php esc_html_e( 'minute(s)', 'dam-spam' ); ?></option>
									<option value="hour" <?php selected( get_option( 'dam_spam_login_attempts_unit', 'hour' ), 'hour' ); ?>><?php esc_html_e( 'hour(s)', 'dam-spam' ); ?></option>
									<option value="day" <?php selected( get_option( 'dam_spam_login_attempts_unit', 'hour' ), 'day' ); ?>><?php esc_html_e( 'day(s)', 'dam-spam' ); ?></option>
								</select>,
								<?php
								// translators: Label before the lockout duration field
								esc_html_e( 'lockout the account for', 'dam-spam' );
								?>
								<input type="text" name="dam_spam_login_lockout_duration" id="dam_spam_login_lockout_duration" class="dam-spam-small-box" value="<?php echo esc_attr( get_option( 'dam_spam_login_lockout_duration', 24 ) ); ?>"> 
								<select name="dam_spam_login_lockout_unit" id="dam_spam_login_lockout_unit" class="dam-spam-small-dropbox">
									<option value="minute" <?php selected( get_option( 'dam_spam_login_lockout_unit', 'hour' ), 'minute' ); ?>><?php esc_html_e( 'minute(s)', 'dam-spam' ); ?></option>
									<option value="hour" <?php selected( get_option( 'dam_spam_login_lockout_unit', 'hour' ), 'hour' ); ?>><?php esc_html_e( 'hour(s)', 'dam-spam' ); ?></option>
									<option value="day" <?php selected( get_option( 'dam_spam_login_lockout_unit', 'hour' ), 'day' ); ?>><?php esc_html_e( 'day(s)', 'dam-spam' ); ?></option>
								</select>.
							</label>
						</div>
						<p><input type="hidden" name="dam_spam_login_setting_placeholder" value="dam_spam_login_setting"></p>
					</div>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Allow users to log in using their username or email address', 'dam-spam' ); ?></span></h3>
						<p><input type="hidden" name="dam_spam_login_type_field" value="dam_spam_login_type"></p>
						<div class="checkbox switcher">
							<label for="dam-spam-login-type-default">
								<input name="dam_spam_login_type" type="radio" id="dam-spam-login-type-default" value="default" <?php echo esc_attr( $dam_spam_login_type_default ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Username or Email', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam-spam-login-type-username">
								<input name="dam_spam_login_type" type="radio" id="dam-spam-login-type-username" value="username" <?php echo esc_attr( $dam_spam_login_type_username ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Username Only', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam-spam-login-type-email">
								<input name="dam_spam_login_type" type="radio" id="dam-spam-login-type-email" value="email" <?php echo esc_attr( $dam_spam_login_type_email ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Email Only', 'dam-spam' ); ?>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Honeypot', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_honeypot_cf7">
								<input type="checkbox" name="dam_spam_honeypot_cf7" id="dam_spam_honeypot_cf7" value="yes" <?php echo ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo esc_attr( $dam_spam_honeypot_cf7 ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Contact Form 7', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam_spam_honeypot_bbpress">
								<input type="checkbox" name="dam_spam_honeypot_bbpress" id="dam_spam_honeypot_bbpress" value="yes" <?php echo ( is_plugin_active( 'bbpress/bbpress.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo esc_attr( $dam_spam_honeypot_bbpress ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'bbPress', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam_spam_honeypot_elementor">
								<input type="checkbox" name="dam_spam_honeypot_elementor" id="dam_spam_honeypot_elementor" value="yes" <?php echo ( is_plugin_active( 'elementor/elementor.php' ) ? '' : 'disabled="disabled"' ); ?> <?php echo esc_attr( $dam_spam_honeypot_elementor ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Elementor Form', 'dam-spam' ); ?>
							</label>
						</div>
						<br>
						<div class="checkbox switcher">
							<label for="dam_spam_honeypot_divi">
								<input type="checkbox" name="dam_spam_honeypot_divi" id="dam_spam_honeypot_divi" value="yes" <?php echo ( ( $theme->name === 'Divi' || $theme->parent_theme === 'Divi' ) ? '' : 'disabled="disabled"' ); ?> <?php echo esc_attr( $dam_spam_honeypot_divi ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Divi Forms', 'dam-spam' ); ?>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Block VPNs', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_allow_vpn">
								<input type="checkbox" name="dam_spam_allow_vpn" id="dam_spam_allow_vpn" value="yes" <?php echo esc_attr( $dam_spam_allow_vpn_setting ); ?>>
								<span><small></small></span>
							</label>
						</div>
					</div>
					<hr>
					<div class="inside">			
						<p>
							<?php wp_nonce_field( 'dam_spam_advanced_settings', 'dam_spam_advanced_settings_nonce' ); ?>
							<?php submit_button( esc_html__( 'Save Changes', 'dam-spam' ), 'primary', 'submit', false ); ?>
						</p>
					</div>
				</form>
			</div>
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Shortcodes', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Contact Form: [dam-spam-contact-form]', 'dam-spam' ); ?></p>
					<p><?php esc_html_e( 'Login Form: [dam-spam-login]', 'dam-spam' ); ?></p>
					<p><?php esc_html_e( 'Logged-in User Display Name: [dam-spam-show-displayname-as]', 'dam-spam' ); ?></p>
					<p><?php esc_html_e( 'Logged-in User First/Last Name: [dam-spam-show-fullname-as]', 'dam-spam' ); ?></p>
					<p><?php esc_html_e( 'Logged-in User Email Address: [dam-spam-show-email-as]', 'dam-spam' ); ?></p>
				</div>
			</div>
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Export Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Export plugin settings as a .json file.', 'dam-spam' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="dam_spam_action" value="export_settings"></p>
						<p>
							<?php wp_nonce_field( 'dam_spam_export_nonce', 'dam_spam_export_nonce' ); ?>
							<?php submit_button( esc_html__( 'Export', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Import Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Import plugin settings from a .json file.', 'dam-spam' ); ?></p>
					<form method="post" enctype="multipart/form-data">
						<p><input type="file" name="import_file"></p>
						<p>
							<input type="hidden" name="dam_spam_action" value="import_settings">
							<?php wp_nonce_field( 'dam_spam_import_nonce', 'dam_spam_import_nonce' ); ?>
							<?php submit_button( esc_html__( 'Import', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>
			<div class="postbox">
				<h3><span><?php esc_html_e( 'Reset Settings', 'dam-spam' ); ?></span></h3>
				<div class="inside">
					<p><?php esc_html_e( 'Reset all plugin settings.', 'dam-spam' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="dam_spam_action" value="reset_settings"></p>
						<p>
							<?php wp_nonce_field( 'dam_spam_reset_nonce', 'dam_spam_reset_nonce' ); ?>
							<?php submit_button( esc_html__( 'Reset', 'dam-spam' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div>
			</div>			
		</div>
	</div>
	<?php
}

add_filter( 'widget_text', 'do_shortcode' );

add_shortcode( 'dam-spam-contact-form', 'dam_spam_contact_form_shortcode' );
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Public contact form shortcode with honeypot protection
function dam_spam_contact_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'email'    => '',
		'accent'   => '',
		'unstyled' => '',
	), $atts );
	ob_start();
	echo '
	<script>
	function nospam() {
		var message = document.forms["dam-spam-contact-form"]["message"].value;
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
	<form id="dam-spam-contact-form" name="dam-spam-contact-form" method="post" action="#send">
		<p id="name"><input type="text" name="sign" placeholder="' . esc_attr__( 'Name', 'dam-spam' ) . '" autocomplete="off" size="35" required></p>
		<p id="email"><input type="email" name="email" placeholder="' . esc_attr__( 'Email', 'dam-spam' ) . '" autocomplete="off" size="35" required></p>
		<p id="phone"><input type="tel" name="phone" placeholder="' . esc_attr__( 'Phone (optional)', 'dam-spam' ) . '" autocomplete="off" size="35"></p>
		<p id="url"><input type="url" name="url" placeholder="' . esc_attr__( 'URL', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" size="35" required></p>
		<p id="message"><textarea id="comment" name="message" placeholder="' . esc_attr__( 'Message', 'dam-spam' ) . '" rows="5" cols="100" onkeyup="nospam()"></textarea></p>
		<p id="submit"><input type="submit" value="' . esc_attr__( 'Submit', 'dam-spam' ) . '"></p>
	</form>
	';
	if ( $atts['unstyled'] === 'yes' ) {
		echo '
		<style>
		#dam-spam-contact-form #url{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}
		#send{text-align:center;padding:5%}
		#send.success{color:green}
		#send.fail{color:red}
		</style>
		';
	} else {
		$accent_color = !empty( $atts['accent'] ) ? esc_attr( $atts['accent'] ) : '#007acc';
		echo '
		<style>
		#dam-spam-contact-form, #dam-spam-contact-form *{box-sizing:border-box;transition:all 0.5s ease}
		#dam-spam-contact-form input, #dam-spam-contact-form textarea{width:100%;font-family:arial,sans-serif;font-size:14px;color:#767676;padding:15px;border:1px solid transparent;background:#f6f6f6}
		#dam-spam-contact-form input:focus, #dam-spam-contact-form textarea:focus{color:#000;border:1px solid ' . esc_attr( $accent_color ) . '}
		#dam-spam-contact-form #submit input{display:inline-block;font-size:18px;color:#fff;text-align:center;text-decoration:none;padding:15px 25px;background:' . esc_attr( $accent_color ) . ';cursor:pointer}
		#dam-spam-contact-form #submit input:hover, #submit input:focus{opacity:0.8}
		#dam-spam-contact-form #url{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}
		#send{text-align:center;padding:5%}
		#send.success{color:green}
		#send.fail{color:red}
		</style>
		';
	}
	$url = isset( $_POST['url'] ) ? sanitize_url( wp_unslash( $_POST['url'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	if ( ( $url === 'https://example.com/' ) && ( stripos( $message, 'http' ) === false ) ) {
		if ( !empty( $atts['email'] ) ) {
			$to = sanitize_email( $atts['email'] );
		} else {
			$to = sanitize_email( get_option( 'admin_email' ) );
		}
		// translators: %s is the website name
		$subject = sprintf( esc_html__( 'New Message from %s', 'dam-spam' ), esc_html( get_option( 'blogname' ) ) );
		$name = isset( $_POST['sign'] ) ? sanitize_text_field( wp_unslash( $_POST['sign'] ) ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$body  = '';
		$body .= esc_html__( 'Name: ', 'dam-spam' );
		$body .= $name;
		$body .= "\n";
		$body .= esc_html__( 'Email: ', 'dam-spam' );
		$body .= $email;
		if ( !empty( $phone ) ) {
			$body .= "\n";
			$body .= esc_html__( 'Phone: ', 'dam-spam' );
			$body .= $phone;
		}
		$body .= "\n\n";
		$body .= $message;
		$body .= "\n";
		// translators: %1$s is the sender's name, %2$s is the sender's email
		$headers = sprintf( 'From: %s <%s>', sanitize_text_field( $name ), sanitize_email( $email ) );
		$success = wp_mail( $to, $subject, $body, $headers );
		if ( $success ) {
			print '<p id="send" class="success">' . esc_html__( 'Message Sent Successfully', 'dam-spam' ) . '</p>';
		} else {
			print '<p id="send" class="fail">' . esc_html__( 'Message Failed', 'dam-spam' ) . '</p>';
			exit;
		}
	}
	$output = ob_get_clean();
	return $output;
}

if ( get_option( 'dam_spam_honeypot_cf7', 'yes' ) === 'yes' ) {
	add_filter( 'wpcf7_form_elements', 'dam_spam_cf7_add_honeypot', 10, 1 );
	function dam_spam_cf7_add_honeypot( $form ) {
		$html  = '';
		$html .= '<p class="dam-spam-user">';
		$html .= '<label>' . esc_html__( 'Your Website (required)', 'dam-spam' ) . '<br>';
		$html .= '<span class="wpcf7-form-control-wrap your-website">';
		$html .= '<input type="text" name="your-website" value="https://example.com/" autocomplete="off" tabindex="-1" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required>';
		$html .= '</span>';
		$html .= '<label>';
		$html .= '</p>';
		$html .= '<style>.dam-spam-user{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
		return $html . $form;
	}
	add_filter( 'wpcf7_spam', 'dam_spam_cf7_verify_honeypot', 10, 1 );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 honeypot verification hook
	function dam_spam_cf7_verify_honeypot( $spam ) {
		if ( $spam ) {
			return $spam;
		}
		$your_website = isset( $_POST['your-website'] ) ? sanitize_url( wp_unslash( $_POST['your-website'] ) ) : '';
		if ( $your_website !== 'https://example.com/' ) {
			return true;
		}
		return $spam;
	}
}

if ( get_option( 'dam_spam_honeypot_bbpress', 'yes' ) === 'yes' ) {
	add_action( 'bbp_theme_before_reply_form_submit_wrapper', 'dam_spam_bbp_add_honeypot' );
	add_action( 'bbp_theme_before_topic_form_submit_wrapper', 'dam_spam_bbp_add_honeypot' );
	function dam_spam_bbp_add_honeypot() {
		$html  = '';
		$html .= '<p class="dam-spam-user">';
		$html .= '<label for="bbp_your-website">' . esc_html__( 'Your Website:', 'dam-spam' ) . '</label><br>';
		$html .= '<input type="text" value="https://example.com/" autocomplete="off" tabindex="-1" size="40" name="bbp_your-website" id="bbp_your-website" required>';
		$html .= '</p>';
		$html .= '<style>.dam-spam-user{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
		echo wp_kses_post( $html );
	}
	add_action( 'bbp_new_reply_pre_extras', 'dam_spam_bbp_verify_honeypot' );
	add_action( 'bbp_new_topic_pre_extras', 'dam_spam_bbp_verify_honeypot' );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- bbPress honeypot verification hook
	function dam_spam_bbp_verify_honeypot() {
		$your_website = isset( $_POST['bbp_your-website'] ) ? sanitize_url( wp_unslash( $_POST['bbp_your-website'] ) ) : '';
		if ( $your_website !== 'https://example.com/' ) {
			bbp_add_error( 'bbp_throw_error', __( '<strong>ERROR</strong>: Something went wrong!', 'dam-spam' ) );
		}
	}
}

if ( get_option( 'dam_spam_honeypot_elementor', 'yes' ) === 'yes' ) {
	add_action( 'elementor/widget/render_content', 'dam_spam_elementor_add_honeypot', 10, 2 );
	function dam_spam_elementor_add_honeypot( $content, $widget ) {
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
	add_action( 'elementor_pro/forms/validation', 'dam_spam_elementor_verify_honeypot', 10, 2 );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Elementor honeypot verification hook
	function dam_spam_elementor_verify_honeypot( $record, $ajax_handler ) {
		$form_fields = isset( $_POST['form_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['form_fields'] ) ) : array();
		$your_website = isset( $form_fields['your-website'] ) ? sanitize_url( $form_fields['your-website'] ) : '';
		if ( $your_website !== 'https://example.com/' ) {
			$ajax_handler->add_error( 'your-website', esc_html__( 'Something went wrong!', 'dam-spam' ) );
		}
	}
}

if ( get_option( 'dam_spam_honeypot_divi', 'yes' ) === 'yes' ) {
	add_filter( 'et_module_shortcode_output', 'dam_spam_et_add_honeypot', 20, 3 );
	function dam_spam_et_add_honeypot( $output, $render_slug, $module ) {
		if ( isset( $_POST['et_pb_contact_your_website'] ) && sanitize_url( wp_unslash( $_POST['et_pb_contact_your_website'] ) ) === 'https://example.com/' ) {
			unset( $_POST['et_pb_contact_your_website'] );
			$post_keys = array_keys( $_POST );
			$post_last_key = !empty( $post_keys ) ? sanitize_key( end( $post_keys ) ) : '';
			if ( !empty( $post_last_key ) && isset( $_POST[ $post_last_key ] ) ) {
				$form_json = isset( $_POST[$post_last_key] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST[$post_last_key] ) ) ) : null;
				if ( is_array( $form_json ) ) {
					array_pop( $form_json );
					$_POST[ $post_last_key ] = wp_json_encode( $form_json );
				}
			}
		}
		$html = '';
		if ( $render_slug === 'et_pb_contact_form' ) {
			$html  .= '<p class="et_pb_contact_field et_pb_contact_your_website">';
			$html  .= '<label for="et_pb_contact_your_website" class="et_pb_contact_form_label">' . esc_html__( 'Your Website', 'dam-spam' ) . '</label>';
			$html  .= '<input type="text" name="et_pb_contact_your_website" id="et_pb_contact_your_website" placeholder="' . esc_attr__( 'Your Website', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" required>';
			$html  .= '</p>';
			$html  .= '<style>.et_pb_contact_your_website{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
			$html  .= '<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit';
			$output = str_replace( '<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit', $html, $output );
		} elseif ( $render_slug === 'et_pb_signup' ) {
			$html   = '';
			$html  .= '<p class="et_pb_signup_custom_field et_pb_signup_your_website et_pb_newsletter_field et_pb_contact_field_last et_pb_contact_field_last_tablet et_pb_contact_field_last_phone">';
			$html  .= '<label for="et_pb_signup_your_website" class="et_pb_contact_form_label">' . esc_html__( 'Your Website', 'dam-spam' ) . '</label>';
			$html  .= '<input type="text" class="input" id="et_pb_signup_your_website" placeholder="' . esc_attr__( 'Your Website', 'dam-spam' ) . '" value="https://example.com/" autocomplete="off" tabindex="-1" data-original_id="your-website" required>';
			$html  .= '</p>';
			$html  .= '<style>.et_pb_signup_your_website{position:absolute;top:0;left:0;width:0;height:0;opacity:0;z-index:-1}</style>';
			$html  .= '<p class="et_pb_newsletter_button_wrap">';
			$output = str_replace( '<p class="et_pb_newsletter_button_wrap">', $html, $output );
		}
		return $output;
	}
	add_action( 'et_pb_newsletter_fieldam_spam_before', 'dam_spam_divi_email_optin_verify_honeypot' );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Divi honeypot verification hook
	function dam_spam_divi_email_optin_verify_honeypot() {
		if ( isset( $_POST['et_custom_fields']['your-website'] ) ) {
			$your_website = sanitize_url( wp_unslash( $_POST['et_custom_fields']['your-website'] ) );
			if ( $your_website !== 'https://example.com/' ) {
				echo '{"error":"Subscription Error: An error occurred, please try later."}';
				exit;
			} else {
				unset( $_POST['et_custom_fields']['your-website'] );
			}
		}
	}
}

add_action( 'admin_init', 'dam_spam_enable_firewall' );
function dam_spam_enable_firewall() {
	if ( empty( $_POST['dam_spam_firewall_setting_placeholder'] ) || 'dam_spam_firewall_setting' !== $_POST['dam_spam_firewall_setting_placeholder'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_firewall_setting'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_firewall_setting'] ) ) === 'yes' ) {
		update_option( 'dam_spam_enable_firewall', 'yes' );
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
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
			'RewriteCond %{REQUEST_URI} (\.)(dam_spam_store|htaccess|htpasswd|init?|mysql-select-db)(/)?$ [NC,OR]',
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
		if ( function_exists( 'insert_with_markers' ) ) {
			return insert_with_markers( $htaccess, 'Dam Spam', (array) $insertion );
		}
	} else {
		update_option( 'dam_spam_enable_firewall', 'no' );
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
		$htaccess = ABSPATH . '.htaccess';
		return insert_with_markers( $htaccess, 'Dam Spam', '' );
	}
}

add_action( 'admin_init', 'dam_spam_enable_custom_login' );
function dam_spam_enable_custom_login() {
	if ( empty( $_POST['dam_spam_login_setting_placeholder'] ) || 'dam_spam_login_setting' !== $_POST['dam_spam_login_setting_placeholder'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_login_setting'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_login_setting'] ) ) === 'yes' ) {
		update_option( 'dam_spam_enable_custom_login', 'yes' );
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
		dam_spam_install_custom_login();
	} else {
		update_option( 'dam_spam_enable_custom_login', 'no' );
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
		dam_spam_uninstall_custom_login();
	}
}

add_action( 'admin_init', 'dam_spam_update_honeypot' );
function dam_spam_update_honeypot() {
	if ( empty( $_POST['dam_spam_honeypot_placeholder'] ) || 'dam_spam_honeypot' !== $_POST['dam_spam_honeypot_placeholder'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_honeypot_cf7'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_honeypot_cf7'] ) ) === 'yes' ) {
		update_option( 'dam_spam_honeypot_cf7', 'yes' );
	} else {
		update_option( 'dam_spam_honeypot_cf7', 'no' );
	}
	if ( isset( $_POST['dam_spam_honeypot_bbpress'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_honeypot_bbpress'] ) ) === 'yes' ) {
		update_option( 'dam_spam_honeypot_bbpress', 'yes' );
	} else {
		update_option( 'dam_spam_honeypot_bbpress', 'no' );
	}
	if ( isset( $_POST['dam_spam_honeypot_elementor'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_honeypot_elementor'] ) ) === 'yes' ) {
		update_option( 'dam_spam_honeypot_elementor', 'yes' );
	} else {
		update_option( 'dam_spam_honeypot_elementor', 'no' );
	}
	if ( isset( $_POST['dam_spam_honeypot_divi'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_honeypot_divi'] ) ) === 'yes' ) {
		update_option( 'dam_spam_honeypot_divi', 'yes' );
	} else {
		update_option( 'dam_spam_honeypot_divi', 'no' );
	}
	if ( isset( $_POST['dam_spam_allow_vpn'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_allow_vpn'] ) ) === 'yes' ) {
		update_option( 'dam_spam_allow_vpn', 'yes' );
	} else {
		update_option( 'dam_spam_allow_vpn', 'no' );
	}
}

add_action( 'admin_init', 'dam_spam_login_type_func' );
function dam_spam_login_type_func() {
	if ( empty( $_POST['dam_spam_login_type_field'] ) || 'dam_spam_login_type' !== $_POST['dam_spam_login_type_field'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_login_type'] ) ) {
		$login_type = sanitize_text_field( wp_unslash( $_POST['dam_spam_login_type'] ) );
		update_option( 'dam_spam_login_type', $login_type );
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
	}
}

function dam_spam_install_custom_login() {
	$pages = array(
		'login'    => esc_html__( 'Log In', 'dam-spam' ),
		'logout'   => esc_html__( 'Log Out', 'dam-spam' ),
		'register' => esc_html__( 'Register', 'dam-spam' ),
		'forgot'   => esc_html__( 'Forgot Password', 'dam-spam' ),
	);
	foreach ( $pages as $slug => $title ) {
		$page_id = dam_spam_get_page_id( $slug );
		if ( $page_id > 0 ) {
			wp_update_post( array(
				'ID'             => $page_id,
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_content'   => '[dam-spam-login]',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			) );
		} else {
			wp_insert_post( array(
				'post_title'     => $title,
				'post_name'      => $slug,
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_content'   => '[dam-spam-login]',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			) );
		}
	}
}

function dam_spam_uninstall_custom_login() {
	$pages = array(
		'login'    => esc_html__( 'Log In', 'dam-spam' ),
		'logout'   => esc_html__( 'Log Out', 'dam-spam' ),
		'register' => esc_html__( 'Register', 'dam-spam' ),
		'forgot'   => esc_html__( 'Forgot Password', 'dam-spam' ),
	);
	foreach ( $pages as $slug => $title ) {
		$page_id = dam_spam_get_page_id( $slug );
		wp_delete_post( $page_id, true );
	}
}

function dam_spam_get_page_id( $slug ) {
	$page = get_page_by_path( $slug );
	if ( !isset( $page->ID ) ) {
		return null;
	} else {
		return $page->ID;
	}
}

// phpcs:disable WordPress.Security.NonceVerification -- Template redirect for logout and login pages
add_action( 'template_redirect', function() {
	global $post;
	if ( !is_object( $post ) || !isset( $post->post_name ) ) {
		return;
	}
	if ( is_page( 'logout' ) ) {
		$user = wp_get_current_user();
		wp_logout();
		if ( !empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = sanitize_url( wp_unslash( $_REQUEST['redirect_to'] ) );
			$requested_redirect_to = $redirect_to;
		} else {
			$redirect_to = site_url( 'login/?loggedout=true' );
			$requested_redirect_to = '';
		}
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
		$redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );
		wp_safe_redirect( $redirect_to );
		exit;
	}
	if ( is_user_logged_in() && ( $post->post_name === 'login' || $post->post_name === 'register' || $post->post_name === 'forgot' ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
	if ( $post->post_name === 'login' ) {
		dam_spam_login();
	} elseif ( $post->post_name === 'register' ) {
		dam_spam_register();
	} elseif ( $post->post_name === 'forgot' ) {
		dam_spam_forgot_password();
	}
} );

function dam_spam_forgot_password() {
	global $wpdb, $wp_hasher;
	if ( empty( $_POST ) ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_forgot_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_forgot_nonce'] ) ), 'dam_spam_forgot_password' ) ) {
		return;
	}
	$errors = new WP_Error();
	if ( empty( $_POST['user_login'] ) ) {
		$errors->add( 'empty_username', esc_html__( 'ERROR: Enter a username or email address.', 'dam-spam' ) );
	} elseif ( isset( $_POST['user_login'] ) && strpos( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ), '@' ) ) {
		$user_data = get_user_by( 'email', trim( sanitize_email( wp_unslash( $_POST['user_login'] ) ) ) );
		if ( empty( $user_data ) ) {
			$errors->add( 'invalid_email', esc_html__( 'ERROR: There is no user registered with that email address.', 'dam-spam' ) );
		}
	} else {
		$login = trim( sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) );
		$user_data = get_user_by( 'login', $login );
	}
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
	do_action( 'lostpassword_post', $errors );
	if ( $errors->get_error_code() ) {
		$GLOBALS['dam_spam_error'] = $errors;
		return;
	}
	if ( !$user_data ) {
		$errors->add( 'invalidcombo', esc_html__( 'ERROR: Invalid username or email.', 'dam-spam' ) );
		$GLOBALS['dam_spam_error'] = $errors;
		return;
	}
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );
	if ( is_wp_error( $key ) ) {
		$GLOBALS['dam_spam_error'] = $key;
	}
	$message  = esc_html__( 'Someone requested that the password be reset for the following account:', 'dam-spam' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	// translators: %s is the username
	$message .= sprintf( esc_html__( 'Username: %s', 'dam-spam' ), $user_login ) . "\r\n\r\n";
	$message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'dam-spam' ) . "\r\n\r\n";
	$message .= esc_html__( 'To reset your password, visit the following address:', 'dam-spam' ) . "\r\n\r\n";
	$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	// translators: %s is the website name
	$title = sprintf( esc_html__( '[%s] Password Reset', 'dam-spam' ), $blogname );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	if ( $message && !wp_mail( $user_email, $title, $message ) ) {
		wp_die( esc_html__( 'The email could not be sent.', 'dam-spam' ) . "<br>\n" . esc_html__( 'Possible reason: your host may have disabled the mail() function...', 'dam-spam' ) );
		wp_safe_redirect( home_url( '/login/?rp=link-sent' ) );
		exit;
	}
}

add_shortcode( 'dam-spam-login', 'dam_spam_login_cb' );
function dam_spam_login_cb() {
	global $post;
	if ( !is_page() ) {
		return;
	}
	switch ( $post->post_name ) {
		case 'login':
			dam_spam_login_page();
			break;
		case 'register':
			dam_spam_register_page();
			break;
		case 'forgot':
			dam_spam_forgot_password_page();
			break;
		default:
			break;
	}
}

function dam_spam_login_page() {
	include DAM_SPAM_PLUGIN_FILE . '/templates/login.php';
}

function dam_spam_register_page() {
	include DAM_SPAM_PLUGIN_FILE . '/templates/register.php';
}

function dam_spam_forgot_password_page() {
	include DAM_SPAM_PLUGIN_FILE . '/templates/forgot.php';
}

function dam_spam_show_error() {
	global $dam_spam_error;
	if ( isset( $dam_spam_error->errors ) ) {
		foreach ( $dam_spam_error->errors as $errors ) {
			foreach ( $errors as $e ) {
				echo '<div style="color:#721c24;background-color:#f8d7da;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #f5c6cb">' . esc_html( $e ) . '</div>';
			}
		}
	}
}

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Public registration form with honeypot protection
function dam_spam_register() {
	if ( !get_option( 'users_can_register' ) ) {
		$redirect_to = site_url( 'wp-login.php?registration=disabled' );
		wp_safe_redirect( $redirect_to );
		exit;
	}
	$user_login = '';
	$user_email = '';
	if ( !empty( $_POST ) && ( isset( $_POST['user_url'] ) && sanitize_url( wp_unslash( $_POST['user_url'] ) ) === 'https://example.com/' ) ) {
		$user_login = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
		$user_email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
		$register_error = register_new_user( $user_login, $user_email );
		if ( !is_wp_error( $register_error ) ) {
			$redirect_to = !empty( $_POST['redirect_to'] ) ? sanitize_url( wp_unslash( $_POST['redirect_to'] ) ) : site_url( 'wp-login.php?checkemail=registered' );
			wp_safe_redirect( $redirect_to );
			exit;
		}
		$GLOBALS['dam_spam_error'] = $register_error;
	}
}

// phpcs:disable WordPress.Security.NonceVerification -- WordPress core login handler, uses wp_signon() authentication
function dam_spam_login() {
	$secure_cookie = '';
	$interim_login = isset( $_REQUEST['interim-login'] );
	if ( !empty( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = sanitize_url( wp_unslash( $_REQUEST['redirect_to'] ) );
		if ( $secure_cookie && false !== strpos( $redirect_to, 'wp-admin' ) ) {
			$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
		}
	} else {
		$redirect_to = admin_url();
	}
	$reauth = empty( $_REQUEST['reauth'] ) ? false : true;
	if ( isset( $_POST['log'] ) || isset( $_GET['testcookie'] ) ) {
		$user = wp_signon( array(), $secure_cookie );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
		$redirect_to = apply_filters( 'login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? sanitize_url( wp_unslash( $_REQUEST['redirect_to'] ) ) : '', $user );
		if ( !is_wp_error( $user ) && !$reauth ) {
			if ( ( empty( $redirect_to ) || $redirect_to === 'wp-admin/' || $redirect_to === admin_url() ) ) {
				if ( is_multisite() && !get_active_blog_for_user( $user->ID ) && !is_super_admin( $user->ID ) ) {
					$redirect_to = user_admin_url();
				} elseif ( is_multisite() && !$user->has_cap( 'read' ) ) {
					$redirect_to = get_dashboard_url( $user->ID );
				} elseif ( !$user->has_cap( 'edit_posts' ) ) {
					$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
					wp_safe_redirect( $redirect_to );
					exit;
				}
			}
			wp_safe_redirect( $redirect_to );
			exit;
		}
		$GLOBALS['dam_spam_error'] = $user;
	}
}

add_filter( 'login_url', 'dam_spam_login_url', 10, 2 );
function dam_spam_login_url( $url ) {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' && !is_user_logged_in() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		include get_query_template( '404' );
		exit;
	}
	return $url;
}

add_filter( 'logout_url', 'dam_spam_logout_url', 10, 2 );
function dam_spam_logout_url( $url, $redirect ) {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		$url = home_url( 'logout' );
	}
	return $url;
}

add_action( 'init', 'dam_spam_custom_login_module' );
function dam_spam_custom_login_module() {
	$login_type = get_option( 'dam_spam_login_type', '' );
	if ( $login_type === 'username' ) {
		remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
	} elseif ( $login_type === 'email' ) {
		remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
	}
}

function dam_spam_login_text( $translating ) {
	$login_type = get_option( 'dam_spam_login_type', '' );
	if ( $login_type === 'username' ) {
		return str_ireplace( 'Username or Email Address', 'Username', $translating );
	} elseif ( $login_type === 'email' ) {
		return str_ireplace( 'Username or Email Address', 'Email Address', $translating );
	} else {
		return $translating;
	}
}

add_action( 'admin_head-nav-menus.php', 'dam_spam_add_nav_menu_metabox' );
function dam_spam_add_nav_menu_metabox() {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		add_meta_box( 'dam_spam_menu_option', 'Dam Spam', 'dam_spam_nav_menu_metabox', 'nav-menus', 'side', 'default' );
	}
}

function dam_spam_nav_menu_metabox( $object ) {
	global $nav_menu_selected_id;
	$elems = array(
		'#dam-spam-nav-login'    => esc_html__( 'Log In', 'dam-spam' ),
		'#dam-spam-nav-logout'   => esc_html__( 'Log Out', 'dam-spam' ),
		'#dam-spam-nav-register' => esc_html__( 'Register', 'dam-spam' ),
		'#dam-spam-nav-loginout' => esc_html__( 'Log In', 'dam-spam' ) . '/' . esc_html__( 'Log Out', 'dam-spam' ),
	);
	$dam_spam_items = array();
	$i = 0;
	foreach ( $elems as $k => $v ) {
		$dam_spam_items[ $i ] = (object) array(
			'ID'               => 1,
			'url'              => esc_attr( $k ),
			'title'            => esc_attr( $v ),
			'object_id'        => esc_attr( $k ),
			'type_label'       => 'Dynamic Link',
			'type'             => 'custom',
			'object'           => 'dam-spam-slug',
			'db_id'            => 0,
			'menu_item_parent' => 0,
			'post_parent'      => 0,
			'target'           => '',
			'attr_title'       => '',
			'description'      => '',
			'classes'          => array(),
			'xfn'              => '',
		);
		$i++;
	}
	$walker = new Walker_Nav_Menu_Checklist( array() );
	?>
	<div id="dam-spam-div">
		<div id="tabs-panel-dam-spam-all" class="tabs-panel tabs-panel-active">
			<ul id="dam-spam-checklist-pop" class="categorychecklist form-no-clear" >
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $dam_spam_items ), 0, (object) array( 'walker' => $walker ) ); ?>
			</ul>
			<p class="button-controls">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'dam-spam' ); ?>" name="dam-spam-menu-item" id="submit-dam-spam-div">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
	</div>
	<?php
}

add_filter( 'wp_setup_nav_menu_item', 'dam_spam_nav_menu_type_label' );
function dam_spam_nav_menu_type_label( $menu_item ) {
	$elems = array( '#dam-spam-nav-login', '#dam-spam-nav-logout', '#dam-spam-nav-register', '#dam-spam-nav-loginout' );
	if ( isset( $menu_item->object, $menu_item->url ) && 'custom' === $menu_item->object && in_array( $menu_item->url, $elems ) ) {
		$menu_item->type_label = 'Dynamic Link';
	}
	return $menu_item;
}

function dam_spam_loginout_title( $title ) {
	$titles = explode( '/', $title );
	if ( !is_user_logged_in() ) {
		return isset( $titles[0] ) ? esc_html( $titles[0] ) : esc_html__( 'Log In', 'dam-spam' );
	} else {
		return isset( $titles[1] ) ? esc_html( $titles[1] ) : esc_html__( 'Log Out', 'dam-spam' );
	}
}

add_filter( 'wp_setup_nav_menu_item', 'dam_spam_setup_nav_menu_item' );
function dam_spam_setup_nav_menu_item( $item ) {
	global $pagenow;
	if ( $pagenow !== 'nav-menus.php' && !defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#dam-spam-nav' ) && get_option( 'dam_spam_enable_custom_login', '' ) !== 'yes' ) {
		$item->_invalid = true;
	} elseif ( $pagenow !== 'nav-menus.php' && !defined( 'DOING_AJAX' ) && isset( $item->url ) && strstr( $item->url, '#dam-spam-nav' ) !== false ) {
		$login_url = get_permalink( get_page_by_path( 'login' ) );
		$logout_url = get_permalink( get_page_by_path( 'logout' ) );
		switch ( $item->url ) {
			case '#dam-spam-nav-login':
				$item->url = get_permalink( get_page_by_path( 'login' ) );
				$item->_invalid = ( is_user_logged_in() ) ? true : false;
				break;
			case '#dam-spam-nav-logout':
				$item->url = get_permalink( get_page_by_path( 'logout' ) );
				$item->_invalid = ( !is_user_logged_in() ) ? true : false;
				break;
			case '#dam-spam-nav-register':
				$item->url = get_permalink( get_page_by_path( 'register' ) );
				$item->_invalid = ( is_user_logged_in() ) ? true : false;
				break;
			default:
				$item->url = ( is_user_logged_in() ) ? $logout_url : $login_url;
				$item->title = dam_spam_loginout_title( $item->title );
		}
	}
	return $item;
}

add_action( 'admin_init', 'dam_spam_limit_login_attempts' );
function dam_spam_limit_login_attempts() {
	if ( empty( $_POST['dam_spam_login_setting_placeholder'] ) || 'dam_spam_login_setting' !== $_POST['dam_spam_login_setting_placeholder'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_login_attempts'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_login_attempts'] ) ) === 'yes' ) {
		update_option( 'dam_spam_login_attempts', 'yes' );
	} else {
		update_option( 'dam_spam_login_attempts', 'no' );
	}
	if ( isset( $_POST['dam_spam_login_attempts_threshold'] ) ) {
		update_option( 'dam_spam_login_attempts_threshold', absint( $_POST['dam_spam_login_attempts_threshold'] ) );
	}
	if ( isset( $_POST['dam_spam_login_attempts_duration'] ) ) {
		update_option( 'dam_spam_login_attempts_duration', absint( $_POST['dam_spam_login_attempts_duration'] ) );
	}
	if ( isset( $_POST['dam_spam_login_attempts_unit'] ) ) {
		update_option( 'dam_spam_login_attempts_unit', sanitize_text_field( wp_unslash( $_POST['dam_spam_login_attempts_unit'] ) ) );
	}
	if ( isset( $_POST['dam_spam_login_lockout_duration'] ) ) {
		update_option( 'dam_spam_login_lockout_duration', absint( $_POST['dam_spam_login_lockout_duration'] ) );
	}
	if ( isset( $_POST['dam_spam_login_lockout_unit'] ) ) {
		update_option( 'dam_spam_login_lockout_unit', sanitize_text_field( wp_unslash( $_POST['dam_spam_login_lockout_unit'] ) ) );
	}
}

add_action( 'authenticate', 'dam_spam_authenticate', 100, 3 );
function dam_spam_authenticate( $user, $username, $password ) {
	$field = is_email( $username ) ? 'email' : 'login';
	$time = time();
	$userdata = get_user_by( $field, $username );
	if ( !$userdata ) {
		return $user;
	}
	if ( dam_spam_is_user_locked( $userdata->ID ) && get_option( 'dam_spam_login_attempts', 'no' ) === 'yes' ) {
		$expiration = dam_spam_get_user_lock_expiration( $userdata->ID );
		if ( $expiration ) {
			// translators: %s is the time remaining until the account is unlocked
			return new WP_Error( 'locked_account', sprintf( '<strong>%s</strong>: %s', esc_html__( 'ERROR', 'dam-spam' ), sprintf( esc_html__( 'This account has been locked because of too many failed login attempts. You may try again in %s.', 'dam-spam' ), human_time_diff( $time, $expiration ) ) ) );
		} else {
			// translators: %s is the time remaining until the account is unlocked
			return new WP_Error( 'locked_account', sprintf( '<strong>%s</strong>: %s', esc_html__( 'ERROR', 'dam-spam' ), esc_html__( 'This account has been locked.', 'dam-spam' ) ) );
		}
	} elseif ( is_wp_error( $user ) && 'incorrect_password' === $user->get_error_code() && get_option( 'dam_spam_login_attempts', 'no' ) === 'yes' ) {
		dam_spam_add_failed_login_attempt( $userdata->ID );
		$attempts = get_user_meta( $userdata->ID, 'dam_spam_failed_login_attempts', true );
		if ( count( $attempts ) >= ( get_option( 'dam_spam_login_attempts_threshold', 5 ) * 2 ) ) {
			$lockout_expiry = '+' . get_option( 'dam_spam_login_lockout_duration', 24 ) . ' ' . get_option( 'dam_spam_login_lockout_unit', 'hour' );
			$expiration = strtotime( $lockout_expiry );
			dam_spam_lock_user( $userdata->ID, $expiration );
			// translators: %s is the time remaining until the account is unlocked
			return new WP_Error( 'locked_account', sprintf( '<strong>%s</strong>: %s', esc_html__( 'ERROR', 'dam-spam' ), sprintf( esc_html__( 'This account has been locked because of too many failed login attempts. You may try again in %s.', 'dam-spam' ), human_time_diff( $time, $expiration ) ) ) );
		}
	}
	return $user;
}

function dam_spam_add_failed_login_attempt( $user_id ) {
	$new_attempts = array();
	$threshold = '-' . get_option( 'dam_spam_login_attempts_duration', 5 ) . ' ' . get_option( 'dam_spam_login_attempts_unit', 'hour' );
	$threshold_date_time = strtotime( $threshold );
	$attempts = get_user_meta( $user_id, 'dam_spam_failed_login_attempts', true );
	if ( !is_array( $attempts ) ) {
		$attempts = array();
	}
	$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$attempts[] = array(
		'time' => time(),
		'ip'   => $remote_addr,
	);
	foreach ( $attempts as $a ) {
		if ( $threshold_date_time < $a['time'] ) {
			$new_attempts[] = $a;
		}
	}
	update_user_meta( $user_id, 'dam_spam_failed_login_attempts', array() );
	update_user_meta( $user_id, 'dam_spam_failed_login_attempts', $new_attempts );
}

function dam_spam_is_user_locked( $user_id ) {
	if ( get_user_meta( $user_id, 'dam_spam_is_locked', true ) === false ) {
		return false;
	}
	$expires = dam_spam_get_user_lock_expiration( $user_id );
	if ( !$expires ) {
		return true;
	}
	$time = time();
	if ( $time > $expires ) {
		dam_spam_unlock_user( $user_id );
		return false;
	}
	return true;
}

function dam_spam_get_user_lock_expiration( $user_id ) {
	return get_user_meta( $user_id, 'dam_spam_lock_expiration', true );
}

function dam_spam_lock_user( $user_id, $expiration ) {
	update_user_meta( $user_id, 'dam_spam_is_locked', true );
	update_user_meta( $user_id, 'dam_spam_lock_expiration', $expiration );
	update_user_meta( $user_id, 'dam_spam_failed_login_attempts', array() );
}

function dam_spam_unlock_user( $user_id ) {
	update_user_meta( $user_id, 'dam_spam_is_locked', false );
	update_user_meta( $user_id, 'dam_spam_lock_expiration', '' );
	update_user_meta( $user_id, 'dam_spam_failed_login_attempts', array() );
}

add_action( 'admin_init', 'dam_spam_process_settings_export' );
function dam_spam_process_settings_export() {
	if ( empty( $_POST['dam_spam_action'] ) || 'export_settings' !== $_POST['dam_spam_action'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_export_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_export_nonce'] ) ), 'dam_spam_export_nonce' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	$options = dam_spam_get_options();
	ignore_user_abort( true );
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=dam-spam-settings-export-' . gmdate( 'm-d-Y-H-i-s' ) . '.json' );
	header( 'Expires: 0' );
	echo wp_json_encode( $options );
	exit;
}

add_action( 'admin_init', 'dam_spam_process_settings_import' );
function dam_spam_process_settings_import() {
	if ( empty( $_POST['dam_spam_action'] ) || 'import_settings' !== $_POST['dam_spam_action'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_import_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_import_nonce'] ) ), 'dam_spam_import_nonce' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( !isset( $_FILES['import_file'] ) || !isset( $_FILES['import_file']['type'] ) ) {
		wp_die( esc_html__( 'Please upload a file to import', 'dam-spam' ) );
	}
	$extension = sanitize_text_field( wp_unslash( $_FILES['import_file']['type'] ) );
	if ( $extension !== 'application/json' ) {
		wp_die( esc_html__( 'Please upload a valid .json file', 'dam-spam' ) );
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- tmp_name validated with is_uploaded_file() on next line
	$import_file = isset( $_FILES['import_file']['tmp_name'] ) ? $_FILES['import_file']['tmp_name'] : '';
	if ( empty( $import_file ) || !is_uploaded_file( $import_file ) ) {
		wp_die( esc_html__( 'Invalid file upload', 'dam-spam' ) );
	}
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}
	$file_contents = $wp_filesystem->get_contents( $import_file );
	if ( false === $file_contents ) {
		wp_die( esc_html__( 'Error reading import file', 'dam-spam' ) );
	}
	$options = json_decode( $file_contents, true );
	if ( !is_array( $options ) || json_last_error() !== JSON_ERROR_NONE ) {
		wp_die( esc_html__( 'Invalid JSON file format', 'dam-spam' ) );
	}
	dam_spam_set_options( $options );
	add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
}

add_action( 'admin_init', 'dam_spam_process_settings_reset' );
function dam_spam_process_settings_reset() {
	if ( empty( $_POST['dam_spam_action'] ) || 'reset_settings' !== $_POST['dam_spam_action'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_reset_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_reset_nonce'] ) ), 'dam_spam_reset_nonce' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	$url = DAM_SPAM_PLUGIN_FILE . '/modules/config/default.json';
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}
	$file_contents = $wp_filesystem->get_contents( $url );
	if ( false === $file_contents ) {
		wp_die( esc_html__( 'Error reading default settings file', 'dam-spam' ) );
	}
	$options = json_decode( $file_contents, true );
	if ( !is_array( $options ) || json_last_error() !== JSON_ERROR_NONE ) {
		wp_die( esc_html__( 'Error reading default settings file', 'dam-spam' ) );
	}
	dam_spam_set_options( $options );
	add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
}

add_shortcode( 'dam-spam-show-displayname-as', 'dam_spam_show_loggedin_function' );
function dam_spam_show_loggedin_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) {
		return $current_user->display_name;
	}
}

add_shortcode( 'dam-spam-show-fullname-as', 'dam_spam_show_fullname_function' );
function dam_spam_show_fullname_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) {
		return $current_user->user_firstname . ' ' . $current_user->user_lastname;
	}
}

add_shortcode( 'dam-spam-show-id-as', 'dam_spam_show_id_function' );
function dam_spam_show_id_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) {
		return $current_user->ID;
	}
}

add_shortcode( 'dam-spam-show-level-as', 'dam_spam_show_level_function' );
function dam_spam_show_level_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) {
		return $current_user->user_level;
	}
}

add_shortcode( 'dam-spam-show-email-as', 'dam_spam_show_email_function' );
function dam_spam_show_email_function( $atts ) {
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter( 'widget_text', 'do_shortcode' );
	if ( $user_login ) {
		return $current_user->user_email;
	}
}

function dam_spam_get_remote_ip_address() {
	if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
	} elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
	}
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

function dam_spam_check_proxy() {
	$timeout = 5;
	$ban_on_probability = 0.99;
	$ip = dam_spam_get_remote_ip_address();
	$contact_email = defined( 'DAM_SPAM_MAIL' ) ? DAM_SPAM_MAIL : get_option( 'admin_email' );
	$url = add_query_arg(
		array(
			'ip'      => $ip,
			'contact' => $contact_email,
		),
		'https://check.getipintel.net/check.php'
	);
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => $timeout,
		)
	);
	if ( is_wp_error( $response ) ) {
		return false;
	}
	$body = wp_remote_retrieve_body( $response );
	if ( $body > $ban_on_probability ) {
		return true;
	}
	return false;
}

add_action( 'init', 'dam_spam_disable_activities' );
function dam_spam_disable_activities() {
	if ( get_option( 'dam_spam_allow_vpn' ) === 'no' ) {
		return;
	}
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$login_url = wp_login_url();
	if ( substr_count( $request_uri, 'wp-login' ) || get_permalink() === $login_url || substr_count( $request_uri, 'checkout' ) || substr_count( $request_uri, 'wp-comments-post' ) ) {
		$is_vpn = dam_spam_check_proxy();
		if ( $is_vpn === true ) {
			status_header( 403 );
			wp_die( esc_html__( 'You cannot access this page.', 'dam-spam' ) );
		}
	}
}