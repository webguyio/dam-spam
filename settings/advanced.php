<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// ============================================================================
// Admin UI
// ============================================================================

function dam_spam_admin_notice_success() {
	?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Options Updated', 'dam-spam' ); ?></p>
	</div>
	<?php
}

function dam_spam_advanced_menu() {
	$dam_spam_firewall_setting = '';
	if ( get_option( 'dam_spam_enable_firewall', '' ) === 'yes' ) {
		$dam_spam_firewall_setting = "checked='checked'";
	}
	$existing_login_pages = false;
	$login_page = get_page_by_path( 'login' );
	$register_page = get_page_by_path( 'register' );
	$forgot_page = get_page_by_path( 'forgot' );
	if ( ( $login_page && get_option( 'dam_spam_enable_custom_login', '' ) !== 'yes' ) ||
	     ( $register_page && get_option( 'dam_spam_enable_custom_login', '' ) !== 'yes' ) ||
	     ( $forgot_page && get_option( 'dam_spam_enable_custom_login', '' ) !== 'yes' ) ) {
		$existing_login_pages = true;
	}
	$dam_spam_login_setting = '';
	$dam_spam_login_disabled = '';
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		$dam_spam_login_setting = "checked='checked'";
	}
	if ( $existing_login_pages ) {
		$dam_spam_login_disabled = "disabled='disabled'";
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
	if ( get_option( 'dam_spam_honeypot_cf7' ) === 'yes' && is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		$dam_spam_honeypot_cf7 = "checked='checked'";
	}
	$dam_spam_honeypot_bbpress = '';
	if ( get_option( 'dam_spam_honeypot_bbpress' ) === 'yes' && is_plugin_active( 'bbpress/bbpress.php' ) ) {
		$dam_spam_honeypot_bbpress = "checked='checked'";
	}
	$dam_spam_honeypot_elementor = '';
	if ( get_option( 'dam_spam_honeypot_elementor' ) === 'yes' && is_plugin_active( 'elementor/elementor.php' ) ) {
		$dam_spam_honeypot_elementor = "checked='checked'";
	}
	$theme = wp_get_theme();
	$dam_spam_honeypot_divi = '';
	if ( get_option( 'dam_spam_honeypot_divi' ) === 'yes' && ( $theme->name === 'Divi' || $theme->parent_theme === 'Divi' ) ) {
		$dam_spam_honeypot_divi = "checked='checked'";
	}
	?>
	<div id="dam-spam" class="wrap">
		<h1 id="dam-spam-head"><?php esc_html_e( 'Advanced — Dam Spam', 'dam-spam' ); ?></h1>
		<div class="metabox-holder">
			<div class="postbox">
				<form method="post">
					<div class="inside">
						<h3><span><?php esc_html_e( 'Firewall Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_firewall_setting">
								<div class="notice notice-warning inline">
									<p><em><?php esc_html_e( 'For advanced users only: This option will modify your .htaccess file with extra security rules and in some small cases, conflict with your server settings. If you do not understand how to edit your .htaccess file to remove these rules in the event of an error, do not enable.', 'dam-spam' ); ?></em></p>
								</div>
								<input type="checkbox" name="dam_spam_firewall_setting" id="dam_spam_firewall_setting" value="yes" <?php echo esc_attr( $dam_spam_firewall_setting ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Server-side Security Rules', 'dam-spam' ); ?>
							</label>
						</div>
						<input type="hidden" name="dam_spam_firewall_setting_placeholder" value="dam_spam_firewall_setting">
					</div>
					<br>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Login Settings', 'dam-spam' ); ?></span></h3>
						<div class="checkbox switcher">
							<label for="dam_spam_login_attempts">
								<input type="checkbox" name="dam_spam_login_attempts" id="dam_spam_login_attempts" value="yes" <?php echo esc_attr( $dam_spam_login_attempts ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Limit Login Attempts:', 'dam-spam' ); ?>
								<?php
								// translators: Label before the threshold number input field
								esc_html_e( 'After', 'dam-spam' );
								?>
								<input type="number" name="dam_spam_login_attempts_threshold" id="dam_spam_login_attempts_threshold" class="dam-spam-small-box" min="20" value="<?php echo esc_attr( get_option( 'dam_spam_login_attempts_threshold', 20 ) ); ?>">
								<?php
								// translators: Label between threshold and duration fields
								esc_html_e( 'failed login attempts within', 'dam-spam' );
								?>
								<input type="number" name="dam_spam_login_attempts_duration" id="dam_spam_login_attempts_duration" class="dam-spam-small-box" min="1" max="1440" value="<?php echo esc_attr( get_option( 'dam_spam_login_attempts_duration', 60 ) ); ?>">
								<?php
								// translators: Label after the duration field, now hardcoded to minutes
								esc_html_e( 'minutes, ban the IP address.', 'dam-spam' );
								?>
							</label>
							<p class="description"><?php esc_html_e( 'Recommended: 20 attempts within 60 minutes', 'dam-spam' ); ?></p>
						</div>
						<input type="hidden" name="dam_spam_login_setting_placeholder" value="dam_spam_login_setting">
						<br>
						<?php if ( $existing_login_pages ): ?>
							<div class="notice inline">
								<p><strong><?php esc_html_e( 'Custom login pages detected. You may have added these manually or you are using a plugin that auto creates them.', 'dam-spam' ); ?></strong></p>
							</div>
						<?php endif; ?>
						<div class="checkbox switcher">
							<label for="dam_spam_login_setting">
								<input type="checkbox" name="dam_spam_login_setting" id="dam_spam_login_setting" value="yes" <?php echo esc_attr( $dam_spam_login_setting ); ?> <?php echo esc_attr( $dam_spam_login_disabled ); ?>>
								<span><small></small></span>
								<?php esc_html_e( 'Themed Login (disables wp-login.php)', 'dam-spam' ); ?>
							</label>
						</div>
						<h4><span><?php esc_html_e( 'How can users log in?', 'dam-spam' ); ?></span></h4>
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
						<input type="hidden" name="dam_spam_login_type_field" value="dam_spam_login_type">
					</div>
					<br>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Honeypot Settings', 'dam-spam' ); ?></span></h3>
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
						<input type="hidden" name="dam_spam_honeypot_placeholder" value="dam_spam_honeypot">
					</div>
					<br>
					<hr>
					<div class="inside">
						<h3><span><?php esc_html_e( 'Ban List Settings', 'dam-spam' ); ?></span></h3>
						<h4><span><?php esc_html_e( 'Manual Ban List', 'dam-spam' ); ?></span></h4>
						<p class="description"><?php esc_html_e( 'IPs you add to this list will be permanently banned unless manually removed. One IP per line.', 'dam-spam' ); ?></p>
						<textarea name="dam_spam_manual_bans" id="dam_spam_manual_bans" rows="10" class="large-text code"><?php echo esc_textarea( get_option( 'dam_spam_manual_bans', '' ) ); ?></textarea>
						<br><br>
						<h4><span><?php esc_html_e( 'Automatic Ban List', 'dam-spam' ); ?></span></h4>
						<p class="description">
							<?php
							$auto_bans = get_option( 'dam_spam_automatic_bans', array() );
							$auto_ban_count = is_array( $auto_bans ) ? count( $auto_bans ) : 0;
							/* translators: %d: Number of IPs in the automatic ban list */
							printf( esc_html__( 'IPs automatically banned by Limit Login Attempts and other protections. Auto-culls oldest entries at 100,000 IPs. Currently contains %d IPs.', 'dam-spam' ), $auto_ban_count );
							?>
						</p>
						<textarea id="dam_spam_automatic_bans_display" rows="10" class="large-text code" readonly="readonly" disabled="disabled"><?php
							if ( !empty( $auto_bans ) && is_array( $auto_bans ) ) {
								echo esc_textarea( implode( "\n", array_keys( $auto_bans ) ) );
							}
						?></textarea>
						<br><br>
						<button type="submit" name="dam_spam_clear_auto_bans_action" id="dam_spam_clear_auto_bans" class="button button-secondary" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to clear all automatic bans? This cannot be undone.', 'dam-spam' ) ); ?>');"><?php esc_html_e( 'Clear All Automatic Bans', 'dam-spam' ); ?></button>
						<input type="hidden" name="dam_spam_ban_list_placeholder" value="dam_spam_ban_list">
					</div>
					<br>
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
					<form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset all settings? This cannot be undone.', 'dam-spam' ) ); ?>');">
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

// ============================================================================
// Admin Settings
// ============================================================================

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
		$threshold = absint( $_POST['dam_spam_login_attempts_threshold'] );
		if ( $threshold < 20 ) {
			$threshold = 20;
		}
		update_option( 'dam_spam_login_attempts_threshold', $threshold );
	}
	if ( isset( $_POST['dam_spam_login_attempts_duration'] ) ) {
		$duration = absint( $_POST['dam_spam_login_attempts_duration'] );
		if ( $duration < 1 ) {
			$duration = 1;
		} elseif ( $duration > 1440 ) {
			$duration = 1440;
		}
		update_option( 'dam_spam_login_attempts_duration', $duration );
	}
}

add_action( 'admin_init', 'dam_spam_save_ban_lists' );
function dam_spam_save_ban_lists() {
	if ( empty( $_POST['dam_spam_ban_list_placeholder'] ) || 'dam_spam_ban_list' !== $_POST['dam_spam_ban_list_placeholder'] ) {
		return;
	}
	if ( !isset( $_POST['dam_spam_advanced_settings_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_advanced_settings_nonce'] ) ), 'dam_spam_advanced_settings' ) ) {
		return;
	}
	if ( !current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['dam_spam_clear_auto_bans_action'] ) ) {
		update_option( 'dam_spam_automatic_bans', array() );
		dam_spam_write_ban_file();
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
		return;
	}
	if ( isset( $_POST['dam_spam_manual_bans'] ) ) {
		$manual_bans = sanitize_textarea_field( wp_unslash( $_POST['dam_spam_manual_bans'] ) );
		update_option( 'dam_spam_manual_bans', $manual_bans );
		dam_spam_write_ban_file();
		add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
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
	$stats = get_option( 'dam_spam_stats', array() );
	$export_data = array(
		'options' => $options,
		'stats' => $stats,
		'advanced_settings' => array(
			'enable_firewall' => get_option( 'dam_spam_enable_firewall', '' ),
			'enable_custom_login' => get_option( 'dam_spam_enable_custom_login', '' ),
			'login_attempts' => get_option( 'dam_spam_login_attempts', '' ),
			'login_attempts_threshold' => get_option( 'dam_spam_login_attempts_threshold', 20 ),
			'login_attempts_duration' => get_option( 'dam_spam_login_attempts_duration', 1 ),
			'login_attempts_unit' => get_option( 'dam_spam_login_attempts_unit', 'hour' ),
			'login_type' => get_option( 'dam_spam_login_type', '' ),
			'honeypot_cf7' => get_option( 'dam_spam_honeypot_cf7', '' ),
			'honeypot_bbpress' => get_option( 'dam_spam_honeypot_bbpress', '' ),
			'honeypot_elementor' => get_option( 'dam_spam_honeypot_elementor', '' ),
			'honeypot_divi' => get_option( 'dam_spam_honeypot_divi', '' ),
		),
		'ban_lists' => array(
			'manual_bans' => get_option( 'dam_spam_manual_bans', '' ),
			'automatic_bans' => get_option( 'dam_spam_automatic_bans', array() ),
		),
		'multisite' => array(
			'muswitch' => get_option( 'dam_spam_muswitch', '' ),
		),
		'export_version' => DAM_SPAM_VERSION,
		'export_date' => gmdate( 'Y-m-d H:i:s' ),
	);
	ignore_user_abort( true );
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=dam-spam-settings-export-' . gmdate( 'm-d-Y-H-i-s' ) . '.json' );
	header( 'Expires: 0' );
	echo wp_json_encode( $export_data );
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
	$import_data = json_decode( $file_contents, true );
	if ( !is_array( $import_data ) || json_last_error() !== JSON_ERROR_NONE ) {
		wp_die( esc_html__( 'Invalid JSON file format', 'dam-spam' ) );
	}
	if ( isset( $import_data['options'] ) && is_array( $import_data['options'] ) ) {
		dam_spam_set_options( $import_data['options'] );
	} elseif ( !isset( $import_data['stats'] ) && !isset( $import_data['advanced_settings'] ) && !isset( $import_data['ban_lists'] ) ) {
		dam_spam_set_options( $import_data );
	}
	if ( isset( $import_data['stats'] ) && is_array( $import_data['stats'] ) ) {
		update_option( 'dam_spam_stats', $import_data['stats'] );
	}
	if ( isset( $import_data['advanced_settings'] ) && is_array( $import_data['advanced_settings'] ) ) {
		$adv = $import_data['advanced_settings'];
		if ( isset( $adv['enable_firewall'] ) ) {
			update_option( 'dam_spam_enable_firewall', sanitize_text_field( $adv['enable_firewall'] ) );
		}
		if ( isset( $adv['enable_custom_login'] ) ) {
			update_option( 'dam_spam_enable_custom_login', sanitize_text_field( $adv['enable_custom_login'] ) );
		}
		if ( isset( $adv['login_attempts'] ) ) {
			update_option( 'dam_spam_login_attempts', sanitize_text_field( $adv['login_attempts'] ) );
		}
		if ( isset( $adv['login_attempts_threshold'] ) ) {
			update_option( 'dam_spam_login_attempts_threshold', absint( $adv['login_attempts_threshold'] ) );
		}
		if ( isset( $adv['login_attempts_duration'] ) ) {
			update_option( 'dam_spam_login_attempts_duration', absint( $adv['login_attempts_duration'] ) );
		}
		if ( isset( $adv['login_attempts_unit'] ) ) {
			update_option( 'dam_spam_login_attempts_unit', sanitize_text_field( $adv['login_attempts_unit'] ) );
		}
		if ( isset( $adv['login_type'] ) ) {
			update_option( 'dam_spam_login_type', sanitize_text_field( $adv['login_type'] ) );
		}
		if ( isset( $adv['honeypot_cf7'] ) ) {
			update_option( 'dam_spam_honeypot_cf7', sanitize_text_field( $adv['honeypot_cf7'] ) );
		}
		if ( isset( $adv['honeypot_bbpress'] ) ) {
			update_option( 'dam_spam_honeypot_bbpress', sanitize_text_field( $adv['honeypot_bbpress'] ) );
		}
		if ( isset( $adv['honeypot_elementor'] ) ) {
			update_option( 'dam_spam_honeypot_elementor', sanitize_text_field( $adv['honeypot_elementor'] ) );
		}
		if ( isset( $adv['honeypot_divi'] ) ) {
			update_option( 'dam_spam_honeypot_divi', sanitize_text_field( $adv['honeypot_divi'] ) );
		}
	}
	if ( isset( $import_data['ban_lists'] ) && is_array( $import_data['ban_lists'] ) ) {
		if ( isset( $import_data['ban_lists']['manual_bans'] ) ) {
			update_option( 'dam_spam_manual_bans', sanitize_textarea_field( $import_data['ban_lists']['manual_bans'] ) );
		}
		if ( isset( $import_data['ban_lists']['automatic_bans'] ) && is_array( $import_data['ban_lists']['automatic_bans'] ) ) {
			update_option( 'dam_spam_automatic_bans', $import_data['ban_lists']['automatic_bans'] );
		}
		dam_spam_write_ban_file();
	}
	if ( isset( $import_data['multisite'] ) && is_array( $import_data['multisite'] ) ) {
		if ( isset( $import_data['multisite']['muswitch'] ) ) {
			update_option( 'dam_spam_muswitch', sanitize_text_field( $import_data['multisite']['muswitch'] ) );
		}
	}
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
	$url = DAM_SPAM_PATH . 'modules/config/default.json';
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
	if ( isset( $options['dam_spam_enable_firewall'] ) ) {
		update_option( 'dam_spam_enable_firewall', sanitize_text_field( $options['dam_spam_enable_firewall'] ) );
	}
	if ( isset( $options['dam_spam_enable_custom_login'] ) ) {
		update_option( 'dam_spam_enable_custom_login', sanitize_text_field( $options['dam_spam_enable_custom_login'] ) );
	}
	if ( isset( $options['dam_spam_login_attempts'] ) ) {
		update_option( 'dam_spam_login_attempts', sanitize_text_field( $options['dam_spam_login_attempts'] ) );
	}
	if ( isset( $options['dam_spam_login_attempts_threshold'] ) ) {
		update_option( 'dam_spam_login_attempts_threshold', absint( $options['dam_spam_login_attempts_threshold'] ) );
	}
	if ( isset( $options['dam_spam_login_attempts_duration'] ) ) {
		update_option( 'dam_spam_login_attempts_duration', absint( $options['dam_spam_login_attempts_duration'] ) );
	}
	if ( isset( $options['dam_spam_login_type'] ) ) {
		update_option( 'dam_spam_login_type', sanitize_text_field( $options['dam_spam_login_type'] ) );
	}
	if ( isset( $options['dam_spam_honeypot_cf7'] ) ) {
		update_option( 'dam_spam_honeypot_cf7', sanitize_text_field( $options['dam_spam_honeypot_cf7'] ) );
	}
	if ( isset( $options['dam_spam_honeypot_bbpress'] ) ) {
		update_option( 'dam_spam_honeypot_bbpress', sanitize_text_field( $options['dam_spam_honeypot_bbpress'] ) );
	}
	if ( isset( $options['dam_spam_honeypot_elementor'] ) ) {
		update_option( 'dam_spam_honeypot_elementor', sanitize_text_field( $options['dam_spam_honeypot_elementor'] ) );
	}
	if ( isset( $options['dam_spam_honeypot_divi'] ) ) {
		update_option( 'dam_spam_honeypot_divi', sanitize_text_field( $options['dam_spam_honeypot_divi'] ) );
	}
	if ( isset( $options['dam_spam_manual_bans'] ) ) {
		update_option( 'dam_spam_manual_bans', sanitize_textarea_field( $options['dam_spam_manual_bans'] ) );
	}
	if ( isset( $options['dam_spam_automatic_bans'] ) && is_array( $options['dam_spam_automatic_bans'] ) ) {
		update_option( 'dam_spam_automatic_bans', $options['dam_spam_automatic_bans'] );
	}
	dam_spam_write_ban_file();
	add_action( 'admin_notices', 'dam_spam_admin_notice_success' );
}

// ============================================================================
// Honeypots
// ============================================================================

if ( get_option( 'dam_spam_honeypot_cf7' ) === 'yes' ) {
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

if ( get_option( 'dam_spam_honeypot_bbpress' ) === 'yes' ) {
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

if ( get_option( 'dam_spam_honeypot_elementor' ) === 'yes' ) {
	add_action( 'elementor/widget/render_content', 'dam_spam_elementor_add_honeypot', 10, 2 );
	add_action( 'elementor_pro/forms/validation', 'dam_spam_elementor_verify_honeypot', 10, 2 );
	function dam_spam_elementor_add_honeypot( $content, $widget ) {
		if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return $content;
		}
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
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Elementor honeypot verification hook
	function dam_spam_elementor_verify_honeypot( $record, $ajax_handler ) {
		$form_fields = isset( $_POST['form_fields'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['form_fields'] ) ) : array();
		$your_website = isset( $form_fields['your-website'] ) ? sanitize_url( $form_fields['your-website'] ) : '';
		if ( $your_website !== 'https://example.com/' ) {
			$ajax_handler->add_error( 'your-website', esc_html__( 'Something went wrong!', 'dam-spam' ) );
		}
	}
}

if ( get_option( 'dam_spam_honeypot_divi' ) === 'yes' ) {
	add_action( 'init', 'dam_spam_divi_contact_verify_honeypot_early', 1 );
	function dam_spam_divi_contact_verify_honeypot_early() {
		if ( isset( $_POST['your-website'] ) ) {
			$your_website = sanitize_url( wp_unslash( $_POST['your-website'] ) );
			if ( $your_website !== 'https://example.com/' ) {
				wp_die( esc_html__( 'Invalid submission. Please refresh the page and try again.', 'dam-spam' ) );
			}
			unset( $_POST['your-website'] );
			foreach ( $_POST as $key => $value ) {
				if ( strpos( $key, 'et_pb_contact_email_fields_' ) === 0 ) {
					$form_json = json_decode( str_replace( '\\', '', $value ), true );
					if ( is_array( $form_json ) ) {
						foreach ( $form_json as $index => $field ) {
							if ( isset( $field['field_id'] ) && $field['field_id'] === 'your-website' ) {
								unset( $form_json[$index] );
								$_POST[$key] = wp_json_encode( array_values( $form_json ) );
								break;
							}
						}
					}
				}
			}
		}
	}
	add_filter( 'et_module_shortcode_output', 'dam_spam_et_add_honeypot', 20, 3 );
	function dam_spam_et_add_honeypot( $output, $render_slug, $module ) {
		if ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) {
			return $output;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['et_fb_ajax_nonce'] ) ) {
			return $output;
		}
		$html = '';
		if ( $render_slug === 'et_pb_contact_form' ) {
			$html  .= '<input type="text" name="your-website" id="your-website" value="https://example.com/" autocomplete="off" style="position:absolute;opacity:0;pointer-events:none;left:-9999px" tabindex="-1" aria-hidden="true">';
			$output = str_replace( '</form>', $html . '</form>', $output );
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
	add_filter( 'et_contact_error_messages', 'dam_spam_divi_contact_verify_honeypot', 10, 1 );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Divi contact form honeypot verification hook
	function dam_spam_divi_contact_verify_honeypot( $error_messages ) {
		if ( isset( $_POST['et_pb_contact_your_website'] ) ) {
			$your_website = sanitize_url( wp_unslash( $_POST['et_pb_contact_your_website'] ) );
			if ( $your_website !== 'https://example.com/' ) {
				$error_messages[] = esc_html__( 'Invalid submission. Please refresh the page and try again.', 'dam-spam' );
			} else {
				unset( $_POST['et_pb_contact_your_website'] );
			}
		}
		return $error_messages;
	}
	add_action( 'et_pb_newsletter_fieldam_spam_before', 'dam_spam_divi_email_optin_verify_honeypot' );
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Divi honeypot verification hook
	function dam_spam_divi_email_optin_verify_honeypot() {
		if ( isset( $_POST['et_custom_fields']['your-website'] ) ) {
			$your_website = sanitize_url( wp_unslash( $_POST['et_custom_fields']['your-website'] ) );
			if ( $your_website !== 'https://example.com/' ) {
				echo '{"error":"' . esc_js( esc_html__( 'Subscription Error: An error occurred, please try later.', 'dam-spam' ) ) . '"}';
				exit;
			} else {
				unset( $_POST['et_custom_fields']['your-website'] );
			}
		}
	}
}

// ============================================================================
// Custom Login - Installation
// ============================================================================

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

// ============================================================================
// Custom Login - Helper Functions
// ============================================================================

function dam_spam_set_error( $error ) {
	$GLOBALS['dam_spam_error'] = $error;
}

function dam_spam_safe_redirect( $url ) {
	wp_safe_redirect( $url );
	exit;
}

function dam_spam_validate_honeypot() {
	return isset( $_POST['user_url'] ) && sanitize_url( wp_unslash( $_POST['user_url'] ) ) === 'https://example.com/';
}

// ============================================================================
// Custom Login - Core Functions
// ============================================================================

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

// phpcs:disable WordPress.Security.NonceVerification.Missing -- Public registration form with honeypot protection
function dam_spam_register() {
	if ( empty( $_POST ) ) {
		return;
	}
	if ( !get_option( 'users_can_register' ) ) {
		dam_spam_set_error( new WP_Error( 'registration_disabled', esc_html__( 'User registration is currently not allowed.', 'dam-spam' ) ) );
		return;
	}
	if ( !dam_spam_validate_honeypot() ) {
		return;
	}
	$user_login = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
	$user_email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$errors = new WP_Error();
	if ( empty( $user_login ) ) {
		$errors->add( 'empty_username', esc_html__( 'Please enter a username.', 'dam-spam' ) );
	}
	if ( empty( $user_email ) ) {
		$errors->add( 'empty_email', esc_html__( 'Please enter an email address.', 'dam-spam' ) );
	}
	if ( username_exists( $user_login ) ) {
		$errors->add( 'username_exists', esc_html__( 'This username is already registered.', 'dam-spam' ) );
	}
	if ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', esc_html__( 'This email address is already registered.', 'dam-spam' ) );
	}
	if ( $errors->has_errors() ) {
		dam_spam_set_error( $errors );
		return;
	}
	$user_id = wp_create_user( $user_login, wp_generate_password(), $user_email );
	if ( is_wp_error( $user_id ) ) {
		dam_spam_set_error( $user_id );
		return;
	}
	wp_new_user_notification( $user_id, null, 'user' );
	dam_spam_safe_redirect( home_url( 'login/?checkemail=registered' ) );
}

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
		dam_spam_set_error( $errors );
		return;
	}
	if ( !$user_data ) {
		dam_spam_set_error( new WP_Error( 'invalidcombo', esc_html__( 'ERROR: Invalid username or email.', 'dam-spam' ) ) );
		return;
	}
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );
	if ( is_wp_error( $key ) ) {
		dam_spam_set_error( $key );
		return;
	}
	$message  = esc_html__( 'Someone requested that the password be reset for the following account:', 'dam-spam' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	// translators: %s is the username
	$message .= sprintf( esc_html__( 'Username: %s', 'dam-spam' ), $user_login ) . "\r\n\r\n";
	$message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'dam-spam' ) . "\r\n\r\n";
	$message .= esc_html__( 'To reset your password, visit the following address:', 'dam-spam' ) . "\r\n\r\n";
	$message .= '<' . home_url( "forgot/?action=rp&key=$key&login=" . rawurlencode( $user_login ) ) . ">\r\n";
	$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	// translators: %s is the website name
	$title = sprintf( esc_html__( '[%s] Password Reset', 'dam-spam' ), $blogname );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
	$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core hook
	$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
	if ( $message && !wp_mail( $user_email, $title, $message ) ) {
		wp_die( esc_html__( 'The email could not be sent.', 'dam-spam' ) . "<br>\n" . esc_html__( 'Possible reason: your host may have disabled the mail() function...', 'dam-spam' ) );
	}
	dam_spam_safe_redirect( home_url( 'login/?checkemail=confirm' ) );
}

function dam_spam_reset_password() {
	$rp_key = isset( $_REQUEST['key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ) : '';
	$rp_login = isset( $_REQUEST['login'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['login'] ) ) : '';
	$user = check_password_reset_key( $rp_key, $rp_login );
	if ( is_wp_error( $user ) ) {
		dam_spam_set_error( $user );
		return;
	}
	if ( isset( $_POST['pass1'] ) && isset( $_POST['pass2'] ) && $_POST['pass1'] !== $_POST['pass2'] ) {
		dam_spam_set_error( new WP_Error( 'password_reset_mismatch', esc_html__( 'The passwords do not match.', 'dam-spam' ) ) );
		return;
	}
	if ( isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
		reset_password( $user, sanitize_text_field( wp_unslash( $_POST['pass1'] ) ) );
		dam_spam_safe_redirect( home_url( 'login/?password=changed' ) );
	}
	$GLOBALS['dam_spam_reset_user'] = $user;
	$GLOBALS['dam_spam_reset_key'] = $rp_key;
}

// ============================================================================
// Custom Login - Templates
// ============================================================================

function dam_spam_login_page() {
	include DAM_SPAM_PATH . 'templates/login.php';
}

function dam_spam_register_page() {
	include DAM_SPAM_PATH . 'templates/register.php';
}

function dam_spam_forgot_password_page() {
	include DAM_SPAM_PATH . 'templates/forgot.php';
}

function dam_spam_show_error() {
	global $dam_spam_error;
	if ( isset( $_GET['checkemail'] ) && $_GET['checkemail'] === 'confirm' ) {
		echo '<div style="color:#155724;background-color:#d4edda;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #c3e6cb">' . esc_html__( 'Check your email for the confirmation link.', 'dam-spam' ) . '</div>';
	}
	if ( isset( $_GET['checkemail'] ) && $_GET['checkemail'] === 'registered' ) {
		echo '<div style="color:#155724;background-color:#d4edda;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #c3e6cb">' . esc_html__( 'Registration complete. Please check your email.', 'dam-spam' ) . '</div>';
	}
	if ( isset( $_GET['password'] ) && $_GET['password'] === 'changed' ) {
		echo '<div style="color:#155724;background-color:#d4edda;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #c3e6cb">' . esc_html__( 'Your password has been reset.', 'dam-spam' ) . '</div>';
	}
	if ( isset( $dam_spam_error->errors ) ) {
		foreach ( $dam_spam_error->errors as $errors ) {
			foreach ( $errors as $e ) {
				echo '<div style="color:#721c24;background-color:#f8d7da;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid #f5c6cb">' . esc_html( $e ) . '</div>';
			}
		}
	}
}

// ============================================================================
// Custom Login - Navigation Menu
// ============================================================================

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

// ============================================================================
// Login Security
// ============================================================================

function dam_spam_authenticate( $user, $username, $password ) {
	$field = is_email( $username ) ? 'email' : 'login';
	$userdata = get_user_by( $field, $username );
	if ( !$userdata ) {
		return $user;
	}
	if ( dam_spam_is_user_locked( $userdata->ID ) ) {
		return new WP_Error( 'locked_account', sprintf( '<strong>%s</strong>: %s', esc_html__( 'ERROR', 'dam-spam' ), esc_html__( 'This account is locked.', 'dam-spam' ) ) );
	}
	if ( is_wp_error( $user ) && 'incorrect_password' === $user->get_error_code() && get_option( 'dam_spam_login_attempts', 'no' ) === 'yes' ) {
		dam_spam_track_failed_login_by_ip();
	}
	return $user;
}

function dam_spam_track_failed_login_by_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	if ( empty( $ip ) ) {
		return;
	}
	$attempts = get_option( 'dam_spam_login_attempts_by_ip', array() );
	if ( !is_array( $attempts ) ) {
		$attempts = array();
	}
	$threshold = get_option( 'dam_spam_login_attempts_threshold', 20 );
	$duration = get_option( 'dam_spam_login_attempts_duration', 60 );
	$time_limit = strtotime( '-' . $duration . ' minute' );
	$current_time = time();
	if ( !isset( $attempts[$ip] ) ) {
		$attempts[$ip] = array();
	}
	$attempts[$ip][] = $current_time;
	$attempts[$ip] = array_filter( $attempts[$ip], function( $timestamp ) use ( $time_limit ) {
		return $timestamp > $time_limit;
	});
	if ( count( $attempts[$ip] ) >= ( $threshold * 2 ) ) {
		$automatic_bans = get_option( 'dam_spam_automatic_bans', array() );
		if ( !is_array( $automatic_bans ) ) {
			$automatic_bans = array();
		}
		$automatic_bans[$ip] = $current_time;
		if ( count( $automatic_bans ) > 100000 ) {
			asort( $automatic_bans );
			$automatic_bans = array_slice( $automatic_bans, -100000, null, true );
		}
		update_option( 'dam_spam_automatic_bans', $automatic_bans );
		dam_spam_write_ban_file();
		unset( $attempts[$ip] );
	}
	update_option( 'dam_spam_login_attempts_by_ip', $attempts );
}

function dam_spam_is_user_locked( $user_id ) {
	$is_locked = get_user_meta( $user_id, 'dam_spam_is_locked', true );
	return !empty( $is_locked );
}

function dam_spam_lock_user( $user_id ) {
	update_user_meta( $user_id, 'dam_spam_is_locked', true );
}

function dam_spam_unlock_user( $user_id ) {
	delete_user_meta( $user_id, 'dam_spam_is_locked' );
}

add_filter( 'user_row_actions', 'dam_spam_user_row_actions', 10, 2 );
function dam_spam_user_row_actions( $actions, $user_object ) {
	if ( !current_user_can( 'edit_users' ) ) {
		return $actions;
	}
	if ( get_current_user_id() === $user_object->ID ) {
		return $actions;
	}
	$is_locked = dam_spam_is_user_locked( $user_object->ID );
	if ( $is_locked ) {
		$actions['dam_spam_unlock'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'dam_spam_unlock_user', 'user_id' => $user_object->ID ), admin_url( 'users.php' ) ), 'dam_spam_unlock_user_' . $user_object->ID ) ),
			esc_html__( 'Unlock account', 'dam-spam' )
		);
	} else {
		$actions['dam_spam_lock'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'dam_spam_lock_user', 'user_id' => $user_object->ID ), admin_url( 'users.php' ) ), 'dam_spam_lock_user_' . $user_object->ID ) ),
			esc_html__( 'Lock account', 'dam-spam' )
		);
	}
	return $actions;
}

add_action( 'admin_init', 'dam_spam_handle_user_lock_actions' );
function dam_spam_handle_user_lock_actions() {
	if ( !current_user_can( 'edit_users' ) ) {
		return;
	}
	if ( !isset( $_GET['action'] ) || !isset( $_GET['user_id'] ) ) {
		return;
	}
	$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
	$user_id = absint( $_GET['user_id'] );
	if ( $action === 'dam_spam_lock_user' ) {
		if ( !isset( $_GET['_wpnonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dam_spam_lock_user_' . $user_id ) ) {
			return;
		}
		dam_spam_lock_user( $user_id );
		wp_safe_redirect( add_query_arg( array( 'dam_spam_locked' => '1' ), admin_url( 'users.php' ) ) );
		exit;
	} elseif ( $action === 'dam_spam_unlock_user' ) {
		if ( !isset( $_GET['_wpnonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dam_spam_unlock_user_' . $user_id ) ) {
			return;
		}
		dam_spam_unlock_user( $user_id );
		wp_safe_redirect( add_query_arg( array( 'dam_spam_unlocked' => '1' ), admin_url( 'users.php' ) ) );
		exit;
	}
}

add_action( 'admin_notices', 'dam_spam_user_lock_notices' );
function dam_spam_user_lock_notices() {
	if ( isset( $_GET['dam_spam_locked'] ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'User account locked.', 'dam-spam' ); ?></p>
		</div>
		<?php
	}
	if ( isset( $_GET['dam_spam_unlocked'] ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'User account unlocked.', 'dam-spam' ); ?></p>
		</div>
		<?php
	}
}

// ============================================================================
// Shortcodes
// ============================================================================

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

// ============================================================================
// Hooks
// ============================================================================

add_filter( 'widget_text', 'do_shortcode' );

add_action( 'template_redirect', function() {
	global $post;
	if ( !is_object( $post ) || !isset( $post->post_name ) ) {
		return;
	}
	if ( is_page( 'logout' ) ) {
		if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'dam_spam_logout' ) ) {
			wp_die( esc_html__( 'Security check failed', 'dam-spam' ), 403 );
		}
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
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'rp' && isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {
			dam_spam_reset_password();
		} else {
			dam_spam_forgot_password();
		}
	}
} );

add_filter( 'login_url', 'dam_spam_login_url', 10, 2 );
function dam_spam_login_url( $url, $redirect ) {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		return home_url( 'login/' . ( $redirect ? '?redirect_to=' . urlencode( $redirect ) : '' ) );
	}
	return $url;
}

add_filter( 'logout_url', 'dam_spam_logout_url', 10, 2 );
function dam_spam_logout_url( $url, $redirect ) {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		$url = wp_nonce_url( home_url( 'logout' ), 'dam_spam_logout' );
	}
	return $url;
}

add_filter( 'wp_new_user_notification_email', 'dam_spam_filter_new_user_email', 10, 3 );
function dam_spam_filter_new_user_email( $wp_new_user_notification_email, $user, $blogname ) {
	if ( get_option( 'dam_spam_enable_custom_login', '' ) === 'yes' ) {
		$message = $wp_new_user_notification_email['message'];
		$message = str_replace( network_site_url( 'wp-login.php?action=rp', 'login' ), home_url( 'forgot/?action=rp' ), $message );
		$message = preg_replace( '#' . preg_quote( site_url( 'wp-login.php' ), '#' ) . '([^\s]*)#', home_url( 'forgot/$1' ), $message );
		$wp_new_user_notification_email['message'] = $message;
	}
	return $wp_new_user_notification_email;
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

add_filter( 'wp_setup_nav_menu_item', 'dam_spam_nav_menu_type_label' );

add_filter( 'wp_setup_nav_menu_item', 'dam_spam_setup_nav_menu_item' );

add_action( 'authenticate', 'dam_spam_authenticate', 100, 3 );