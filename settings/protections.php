<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

dam_spam_fix_post_vars();
$now	 = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = dam_spam_get_options();
extract( $options );
$cf_configured = function_exists( 'dam_spam_cloudflare_is_configured' ) && dam_spam_cloudflare_is_configured();

if ( !isset( $cf_blocked_countries ) || !is_array( $cf_blocked_countries ) ) {
	$cf_blocked_countries = array();
}

if ( !isset( $cf_block_countries ) ) {
	$cf_block_countries = 'N';
}

$nonce   = '';

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	$optionlist = array(
		'check_amazon',
		'add_to_allow_list',
		'check_admin',
		'check_accept',
		'check_bbcode',
		'check_periods',
		'check_hyphens',
		'check_referer',
		'check_disposable',
		'check_long',
		'check_short',
		'check_multi',
		'check_session',
		'check_404',
		'check_exploits',
		'check_admin_log',
		'check_hosting',
		'check_vpn',
		'check_tor',
		'check_akismet',
		'filter_registrations',
		'check_form',
		'check_credit_card',
		'check_woo_form',
		'check_gravity_form',
		'check_wp_form',
		'dam_spam_private_mode',
		'check_ubiquity',
		'check_urls',
		'enable_custom_password',
		'cf_block_countries'
	);
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
	if ( array_key_exists( 'sesstime', $_POST ) ) {
		$sesstime = isset( $_POST['sesstime'] ) ? sanitize_text_field( wp_unslash( $_POST['sesstime'] ) ) : '';
		$options['sesstime'] = $sesstime;
	}
	if ( array_key_exists( 'multitime', $_POST ) ) {
		$multitime = isset( $_POST['multitime'] ) ? sanitize_text_field( wp_unslash( $_POST['multitime'] ) ) : '';
		$options['multitime'] = $multitime;
	}
	if ( array_key_exists( 'multicount', $_POST ) ) {
		$multicount = isset( $_POST['multicount'] ) ? sanitize_text_field( wp_unslash( $_POST['multicount'] ) ) : '';
		$options['multicount'] = $multicount;
	}
	if ( array_key_exists( 'cf_blocked_countries', $_POST ) ) {
		$cf_blocked_countries = isset( $_POST['cf_blocked_countries'] ) && is_array( $_POST['cf_blocked_countries'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['cf_blocked_countries'] ) ) : array();
		$options['cf_blocked_countries'] = $cf_blocked_countries;
	}
	dam_spam_set_options( $options );
	extract( $options );
	if ( $cf_configured && isset( $cf_block_countries ) ) {
		dam_spam_sync_countries_to_cloudflare();
	}
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'dam_spam_update' );

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-header"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg> <?php esc_html_e( 'Protections — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<form method="post" action="" name="ss">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div id="form-checking" class="main-section"><?php esc_html_e( 'Form Checking', 'dam-spam' ); ?></div>
		<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			echo '<div class="notice inline"><p style="color:#c77dff">' . esc_html__( 'WooCommerce detected. If you experience any issues using WooCommerce and Dam Spam together, you may need to adjust these settings.', 'dam-spam' ) . '</p></div>';
		} ?>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_form">
				<input class="dam_spam_toggle" type="checkbox" id="check_form" name="check_form" value="Y" <?php if ( $check_form == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Only Check Native WordPress Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_credit_card">
				<input class="dam_spam_toggle" type="checkbox" id="check_credit_card" name="check_credit_card" value="Y" <?php if ( $check_credit_card == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Skip Payment Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_woo_form">
				<input class="dam_spam_toggle" type="checkbox" id="check_woo_form" name="check_woo_form" value="Y" <?php if ( $check_woo_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Skip WooCommerce Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
	 	<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_gravity_form">
				<input class="dam_spam_toggle" type="checkbox" id="check_gravity_form" name="check_gravity_form" value="Y" <?php if ( $check_gravity_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Skip Gravity Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_wp_form">
				<input class="dam_spam_toggle" type="checkbox" id="check_wp_form" name="check_wp_form" value="Y" <?php if ( $check_wp_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Skip WP Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="private-mode" class="main-section"><?php esc_html_e( 'Private Mode', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="dam_spam_private_mode">
				<input class="dam_spam_toggle" type="checkbox" id="dam_spam_private_mode" name="dam_spam_private_mode" value="Y" <?php if ( $dam_spam_private_mode == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Users Must Be Logged in to View Site', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="prevent-lockouts" class="main-section"><?php esc_html_e( 'Prevent Lockouts', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="add_to_allow_list">
				<input class="dam_spam_toggle" type="checkbox" id="add_to_allow_list" name="add_to_allow_list" value="Y" <?php if ( $add_to_allow_list == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Automatically Add Admins to Allow List', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_admin_log">
				<input class="dam_spam_toggle" type="checkbox" id="check_admin_log" name="check_admin_log" value="Y" <?php if ( $check_admin_log == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check Credentials on All Login Attempts', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="login-protection" class="main-section"><?php esc_html_e( 'Login Protection', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_admin">
				<input class="dam_spam_toggle" type="checkbox" id="check_admin" name="check_admin" value="Y" <?php if ( $check_admin == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for "admin" Username in Login Attempts', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="filter_registrations">
				<input class="dam_spam_toggle" type="checkbox" id="filter_registrations" name="filter_registrations" value="Y" <?php if ( $filter_registrations == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Filter Login Requests', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="validate-requests" class="main-section"><?php esc_html_e( 'Validate Requests', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_accept">
				<input class="dam_spam_toggle" type="checkbox" id="check_accept" name="check_accept" value="Y" <?php if ( $check_accept == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Missing HTTP_ACCEPT Header', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_referer">
				<input class="dam_spam_toggle" type="checkbox" id="check_referer" name="check_referer" value="Y" <?php if ( $check_referer == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Invalid HTTP_REFERER', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_session">
				<input class="dam_spam_toggle" type="checkbox" id="check_session" name="check_session" value="Y" onclick="dam_spam_show_quick()" <?php if ( $check_session == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Quick Responses', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<span id="dam_spam_show_quick" style="display:none">
			<p><?php esc_html_e( 'Response Timeout Value: ', 'dam-spam' ); ?>
			<input name="sesstime" type="text" value="<?php echo esc_attr( $sesstime ); ?>" size="2"><br></p>
		</span>
		<br>
		<div id="validate-input" class="main-section"><?php esc_html_e( 'Validate Input', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_disposable">
				<input class="dam_spam_toggle" type="checkbox" id="check_disposable" name="check_disposable" value="Y" <?php if ( $check_disposable == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Disposable Email Addresses', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_long">
				<input class="dam_spam_toggle" type="checkbox" id="check_long" name="check_long" value="Y" <?php if ( $check_long == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Long Emails, Usernames, and Passwords', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_short">
				<input class="dam_spam_toggle" type="checkbox" id="check_short" name="check_short" value="Y" <?php if ( $check_short == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Short Emails and Usernames', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_bbcode">
				<input class="dam_spam_toggle" type="checkbox" id="check_bbcode" name="check_bbcode" value="Y" <?php if ( $check_bbcode == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for BBCode', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_periods">
				<input class="dam_spam_toggle" type="checkbox" id="check_periods" name="check_periods" value="Y" <?php if ( $check_periods == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Periods', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_hyphens">
				<input class="dam_spam_toggle" type="checkbox" id="check_hyphens" name="check_hyphens" value="Y" <?php if ( $check_hyphens == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Hyphens', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_exploits">
				<input class="dam_spam_toggle" type="checkbox" id="check_exploits" name="check_exploits" value="Y" <?php if ( $check_exploits == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Exploits', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_urls">
				<input class="dam_spam_toggle" type="checkbox" id="check_urls" name="check_urls" value="Y" <?php if ( $check_urls == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for URLs', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="ip-reputation" class="main-section"><?php esc_html_e( 'IP Reputation', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_vpn">
				<input class="dam_spam_toggle" type="checkbox" id="check_vpn" name="check_vpn" value="Y" <?php if ( $check_vpn == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for VPNs', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_tor">
				<input class="dam_spam_toggle" type="checkbox" id="check_tor" name="check_tor" value="Y" <?php if ( $check_tor == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Tor', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_ubiquity">
				<input class="dam_spam_toggle" type="checkbox" id="check_ubiquity" name="check_ubiquity" value="Y" <?php if ( $check_ubiquity == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Ubiquity-Nobis and Other Blocklists', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_hosting">
				<input class="dam_spam_toggle" type="checkbox" id="check_hosting" name="check_hosting" value="Y" <?php if ( $check_hosting == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Major Hosting Companies and Cloud Services', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_amazon">
				<input class="dam_spam_toggle" type="checkbox" id="check_amazon" name="check_amazon" value="Y" <?php if ( $check_amazon == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Amazon Cloud', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_akismet">
				<input class="dam_spam_toggle" type="checkbox" id="check_akismet" name="check_akismet" value="Y" <?php if ( $check_akismet == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for IPs Detected by Akismet', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="behavior-detection" class="main-section"><?php esc_html_e( 'Behavior Detection', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_404">
				<input class="dam_spam_toggle" type="checkbox" id="check_404" name="check_404" value="Y" <?php if ( $check_404 == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for 404 Exploit Probing', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="check_multi">
				<input class="dam_spam_toggle" type="checkbox" id="check_multi" name="check_multi" value="Y" onclick="dam_spam_show_check_multi()" <?php if ( $check_multi == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Many Hits in a Short Time', 'dam-spam' ); ?></small>
			</label>
		</div>
		<span id="dam_spam_show_check_multi" style="display:none">
			<p><?php esc_html_e( 'Block access when there are', 'dam-spam' ); ?>
				<select name="multicount">
					<option val="4" <?php if ( $multicount <= 4 ) { echo 'selected="selected"'; } ?>>4</option>
					<option val="5" <?php if ( $multicount == 5 ) { echo 'selected="selected"'; } ?>>5</option>
					<option val="6" <?php if ( $multicount == 6 ) { echo 'selected="selected"'; } ?>>6</option>
					<option val="7" <?php if ( $multicount == 7 ) { echo 'selected="selected"'; } ?>>7</option>
					<option val="8" <?php if ( $multicount == 8 ) { echo 'selected="selected"'; } ?>>8</option>
					<option val="9" <?php if ( $multicount == 9 ) { echo 'selected="selected"'; } ?>>9</option>
					<option val="10" <?php if ( $multicount >= 10 ) { echo 'selected="selected"'; } ?>>10</option>
				</select>
				<?php esc_html_e( 'comments or logins in less than', 'dam-spam' ); ?>
				<select name="multitime">
					<option val="1" <?php if ( $multitime <= 1 ) { echo 'selected="selected"'; } ?>>1</option>
					<option val="2" <?php if ( $multitime == 2 ) { echo 'selected="selected"'; } ?>>2</option>
					<option val="3" <?php if ( $multitime == 3 ) { echo 'selected="selected"'; } ?>>3</option>
					<option val="4" <?php if ( $multitime == 4 ) { echo 'selected="selected"'; } ?>>4</option>
					<option val="5" <?php if ( $multitime == 5 ) { echo 'selected="selected"'; } ?>>5</option>
					<option val="6" <?php if ( $multitime == 6 ) { echo 'selected="selected"'; } ?>>6</option>
					<option val="7" <?php if ( $multitime == 7 ) { echo 'selected="selected"'; } ?>>7</option>
					<option val="8" <?php if ( $multitime == 8 ) { echo 'selected="selected"'; } ?>>8</option>
					<option val="9" <?php if ( $multitime == 9 ) { echo 'selected="selected"'; } ?>>9</option>
					<option val="10" <?php if ( $multitime >= 10 ) { echo 'selected="selected"'; } ?>>10</option>
				</select>
				<?php esc_html_e( 'minutes.', 'dam-spam' ); ?><br>
			</p>
		</span>
		<br>
		<div id="block-countries" class="main-section"><?php esc_html_e( 'Block Countries', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="dam-spam-sub-header" for="cf_block_countries">
				<input class="dam_spam_toggle" type="checkbox" id="cf_block_countries" name="cf_block_countries" value="Y" <?php if ( $cf_block_countries == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( !$cf_configured ) { echo 'disabled="disabled"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block Countries via Cloudflare', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<?php if ( !$cf_configured ) { ?>
			<p class="description">
				<?php esc_html_e( 'Configure Cloudflare on the APIs page to enable this feature.', 'dam-spam' ); ?>
			</p>
		<?php } ?>
		<br>
		<label>
			<?php esc_html_e( 'Select countries to block:', 'dam-spam' ); ?>
			<br>
			<select name="cf_blocked_countries[]" multiple="multiple" size="10" <?php if ( !$cf_configured ) { echo 'disabled="disabled"'; } ?>>
				<option value="AF" <?php if ( in_array( 'AF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Afghanistan</option>
				<option value="AX" <?php if ( in_array( 'AX', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Aland Islands</option>
				<option value="AL" <?php if ( in_array( 'AL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Albania</option>
				<option value="DZ" <?php if ( in_array( 'DZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Algeria</option>
				<option value="AS" <?php if ( in_array( 'AS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>American Samoa</option>
				<option value="AD" <?php if ( in_array( 'AD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Andorra</option>
				<option value="AO" <?php if ( in_array( 'AO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Angola</option>
				<option value="AI" <?php if ( in_array( 'AI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Anguilla</option>
				<option value="AQ" <?php if ( in_array( 'AQ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Antarctica</option>
				<option value="AG" <?php if ( in_array( 'AG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Antigua and Barbuda</option>
				<option value="AR" <?php if ( in_array( 'AR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Argentina</option>
				<option value="AM" <?php if ( in_array( 'AM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Armenia</option>
				<option value="AW" <?php if ( in_array( 'AW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Aruba</option>
				<option value="AU" <?php if ( in_array( 'AU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Australia</option>
				<option value="AT" <?php if ( in_array( 'AT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Austria</option>
				<option value="AZ" <?php if ( in_array( 'AZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Azerbaijan</option>
				<option value="BS" <?php if ( in_array( 'BS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bahamas</option>
				<option value="BH" <?php if ( in_array( 'BH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bahrain</option>
				<option value="BD" <?php if ( in_array( 'BD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bangladesh</option>
				<option value="BB" <?php if ( in_array( 'BB', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Barbados</option>
				<option value="BY" <?php if ( in_array( 'BY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Belarus</option>
				<option value="BE" <?php if ( in_array( 'BE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Belgium</option>
				<option value="BZ" <?php if ( in_array( 'BZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Belize</option>
				<option value="BJ" <?php if ( in_array( 'BJ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Benin</option>
				<option value="BM" <?php if ( in_array( 'BM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bermuda</option>
				<option value="BT" <?php if ( in_array( 'BT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bhutan</option>
				<option value="BO" <?php if ( in_array( 'BO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bolivia</option>
				<option value="BQ" <?php if ( in_array( 'BQ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bonaire</option>
				<option value="BA" <?php if ( in_array( 'BA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bosnia and Herzegovina</option>
				<option value="BW" <?php if ( in_array( 'BW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Botswana</option>
				<option value="BV" <?php if ( in_array( 'BV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bouvet Island</option>
				<option value="BR" <?php if ( in_array( 'BR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Brazil</option>
				<option value="IO" <?php if ( in_array( 'IO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>British Indian Ocean Territory</option>
				<option value="VG" <?php if ( in_array( 'VG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>British Virgin Islands</option>
				<option value="BN" <?php if ( in_array( 'BN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Brunei</option>
				<option value="BG" <?php if ( in_array( 'BG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Bulgaria</option>
				<option value="BF" <?php if ( in_array( 'BF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Burkina Faso</option>
				<option value="BI" <?php if ( in_array( 'BI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Burundi</option>
				<option value="KH" <?php if ( in_array( 'KH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cambodia</option>
				<option value="CM" <?php if ( in_array( 'CM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cameroon</option>
				<option value="CA" <?php if ( in_array( 'CA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Canada</option>
				<option value="CV" <?php if ( in_array( 'CV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cape Verde</option>
				<option value="KY" <?php if ( in_array( 'KY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cayman Islands</option>
				<option value="CF" <?php if ( in_array( 'CF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Central African Republic</option>
				<option value="TD" <?php if ( in_array( 'TD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Chad</option>
				<option value="CL" <?php if ( in_array( 'CL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Chile</option>
				<option value="CN" <?php if ( in_array( 'CN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>China</option>
				<option value="CX" <?php if ( in_array( 'CX', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Christmas Island</option>
				<option value="CC" <?php if ( in_array( 'CC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cocos Islands</option>
				<option value="CO" <?php if ( in_array( 'CO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Colombia</option>
				<option value="KM" <?php if ( in_array( 'KM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Comoros</option>
				<option value="CK" <?php if ( in_array( 'CK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cook Islands</option>
				<option value="CR" <?php if ( in_array( 'CR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Costa Rica</option>
				<option value="HR" <?php if ( in_array( 'HR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Croatia</option>
				<option value="CU" <?php if ( in_array( 'CU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cuba</option>
				<option value="CW" <?php if ( in_array( 'CW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Curacao</option>
				<option value="CY" <?php if ( in_array( 'CY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Cyprus</option>
				<option value="CZ" <?php if ( in_array( 'CZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Czech Republic</option>
				<option value="CD" <?php if ( in_array( 'CD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Democratic Republic of the Congo</option>
				<option value="DK" <?php if ( in_array( 'DK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Denmark</option>
				<option value="DJ" <?php if ( in_array( 'DJ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Djibouti</option>
				<option value="DM" <?php if ( in_array( 'DM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Dominica</option>
				<option value="DO" <?php if ( in_array( 'DO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Dominican Republic</option>
				<option value="EC" <?php if ( in_array( 'EC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ecuador</option>
				<option value="EG" <?php if ( in_array( 'EG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Egypt</option>
				<option value="SV" <?php if ( in_array( 'SV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>El Salvador</option>
				<option value="GQ" <?php if ( in_array( 'GQ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Equatorial Guinea</option>
				<option value="ER" <?php if ( in_array( 'ER', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Eritrea</option>
				<option value="EE" <?php if ( in_array( 'EE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Estonia</option>
				<option value="SZ" <?php if ( in_array( 'SZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Eswatini</option>
				<option value="ET" <?php if ( in_array( 'ET', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ethiopia</option>
				<option value="FK" <?php if ( in_array( 'FK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Falkland Islands</option>
				<option value="FO" <?php if ( in_array( 'FO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Faroe Islands</option>
				<option value="FJ" <?php if ( in_array( 'FJ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Fiji</option>
				<option value="FI" <?php if ( in_array( 'FI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Finland</option>
				<option value="FR" <?php if ( in_array( 'FR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>France</option>
				<option value="GF" <?php if ( in_array( 'GF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>French Guiana</option>
				<option value="PF" <?php if ( in_array( 'PF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>French Polynesia</option>
				<option value="TF" <?php if ( in_array( 'TF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>French Southern Territories</option>
				<option value="GA" <?php if ( in_array( 'GA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Gabon</option>
				<option value="GM" <?php if ( in_array( 'GM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Gambia</option>
				<option value="GE" <?php if ( in_array( 'GE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Georgia</option>
				<option value="DE" <?php if ( in_array( 'DE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Germany</option>
				<option value="GH" <?php if ( in_array( 'GH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ghana</option>
				<option value="GI" <?php if ( in_array( 'GI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Gibraltar</option>
				<option value="GR" <?php if ( in_array( 'GR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Greece</option>
				<option value="GL" <?php if ( in_array( 'GL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Greenland</option>
				<option value="GD" <?php if ( in_array( 'GD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Grenada</option>
				<option value="GP" <?php if ( in_array( 'GP', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guadeloupe</option>
				<option value="GU" <?php if ( in_array( 'GU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guam</option>
				<option value="GT" <?php if ( in_array( 'GT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guatemala</option>
				<option value="GG" <?php if ( in_array( 'GG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guernsey</option>
				<option value="GN" <?php if ( in_array( 'GN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guinea</option>
				<option value="GW" <?php if ( in_array( 'GW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guinea-Bissau</option>
				<option value="GY" <?php if ( in_array( 'GY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Guyana</option>
				<option value="HT" <?php if ( in_array( 'HT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Haiti</option>
				<option value="HM" <?php if ( in_array( 'HM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Heard Island</option>
				<option value="HN" <?php if ( in_array( 'HN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Honduras</option>
				<option value="HK" <?php if ( in_array( 'HK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Hong Kong</option>
				<option value="HU" <?php if ( in_array( 'HU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Hungary</option>
				<option value="IS" <?php if ( in_array( 'IS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Iceland</option>
				<option value="IN" <?php if ( in_array( 'IN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>India</option>
				<option value="ID" <?php if ( in_array( 'ID', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Indonesia</option>
				<option value="IR" <?php if ( in_array( 'IR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Iran</option>
				<option value="IQ" <?php if ( in_array( 'IQ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Iraq</option>
				<option value="IE" <?php if ( in_array( 'IE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ireland</option>
				<option value="IM" <?php if ( in_array( 'IM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Isle of Man</option>
				<option value="IL" <?php if ( in_array( 'IL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Israel</option>
				<option value="IT" <?php if ( in_array( 'IT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Italy</option>
				<option value="CI" <?php if ( in_array( 'CI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ivory Coast</option>
				<option value="JM" <?php if ( in_array( 'JM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Jamaica</option>
				<option value="JP" <?php if ( in_array( 'JP', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Japan</option>
				<option value="JE" <?php if ( in_array( 'JE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Jersey</option>
				<option value="JO" <?php if ( in_array( 'JO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Jordan</option>
				<option value="KZ" <?php if ( in_array( 'KZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Kazakhstan</option>
				<option value="KE" <?php if ( in_array( 'KE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Kenya</option>
				<option value="KI" <?php if ( in_array( 'KI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Kiribati</option>
				<option value="KW" <?php if ( in_array( 'KW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Kuwait</option>
				<option value="KG" <?php if ( in_array( 'KG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Kyrgyzstan</option>
				<option value="LA" <?php if ( in_array( 'LA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Laos</option>
				<option value="LV" <?php if ( in_array( 'LV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Latvia</option>
				<option value="LB" <?php if ( in_array( 'LB', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Lebanon</option>
				<option value="LS" <?php if ( in_array( 'LS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Lesotho</option>
				<option value="LR" <?php if ( in_array( 'LR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Liberia</option>
				<option value="LY" <?php if ( in_array( 'LY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Libya</option>
				<option value="LI" <?php if ( in_array( 'LI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Liechtenstein</option>
				<option value="LT" <?php if ( in_array( 'LT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Lithuania</option>
				<option value="LU" <?php if ( in_array( 'LU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Luxembourg</option>
				<option value="MO" <?php if ( in_array( 'MO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Macao</option>
				<option value="MG" <?php if ( in_array( 'MG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Madagascar</option>
				<option value="MW" <?php if ( in_array( 'MW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Malawi</option>
				<option value="MY" <?php if ( in_array( 'MY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Malaysia</option>
				<option value="MV" <?php if ( in_array( 'MV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Maldives</option>
				<option value="ML" <?php if ( in_array( 'ML', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mali</option>
				<option value="MT" <?php if ( in_array( 'MT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Malta</option>
				<option value="MH" <?php if ( in_array( 'MH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Marshall Islands</option>
				<option value="MQ" <?php if ( in_array( 'MQ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Martinique</option>
				<option value="MR" <?php if ( in_array( 'MR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mauritania</option>
				<option value="MU" <?php if ( in_array( 'MU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mauritius</option>
				<option value="YT" <?php if ( in_array( 'YT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mayotte</option>
				<option value="MX" <?php if ( in_array( 'MX', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mexico</option>
				<option value="FM" <?php if ( in_array( 'FM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Micronesia</option>
				<option value="MD" <?php if ( in_array( 'MD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Moldova</option>
				<option value="MC" <?php if ( in_array( 'MC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Monaco</option>
				<option value="MN" <?php if ( in_array( 'MN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mongolia</option>
				<option value="ME" <?php if ( in_array( 'ME', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Montenegro</option>
				<option value="MS" <?php if ( in_array( 'MS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Montserrat</option>
				<option value="MA" <?php if ( in_array( 'MA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Morocco</option>
				<option value="MZ" <?php if ( in_array( 'MZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Mozambique</option>
				<option value="MM" <?php if ( in_array( 'MM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Myanmar</option>
				<option value="NA" <?php if ( in_array( 'NA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Namibia</option>
				<option value="NR" <?php if ( in_array( 'NR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Nauru</option>
				<option value="NP" <?php if ( in_array( 'NP', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Nepal</option>
				<option value="NL" <?php if ( in_array( 'NL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Netherlands</option>
				<option value="NC" <?php if ( in_array( 'NC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>New Caledonia</option>
				<option value="NZ" <?php if ( in_array( 'NZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>New Zealand</option>
				<option value="NI" <?php if ( in_array( 'NI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Nicaragua</option>
				<option value="NE" <?php if ( in_array( 'NE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Niger</option>
				<option value="NG" <?php if ( in_array( 'NG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Nigeria</option>
				<option value="NU" <?php if ( in_array( 'NU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Niue</option>
				<option value="NF" <?php if ( in_array( 'NF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Norfolk Island</option>
				<option value="KP" <?php if ( in_array( 'KP', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>North Korea</option>
				<option value="MK" <?php if ( in_array( 'MK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>North Macedonia</option>
				<option value="MP" <?php if ( in_array( 'MP', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Northern Mariana Islands</option>
				<option value="NO" <?php if ( in_array( 'NO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Norway</option>
				<option value="OM" <?php if ( in_array( 'OM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Oman</option>
				<option value="PK" <?php if ( in_array( 'PK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Pakistan</option>
				<option value="PW" <?php if ( in_array( 'PW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Palau</option>
				<option value="PS" <?php if ( in_array( 'PS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Palestine</option>
				<option value="PA" <?php if ( in_array( 'PA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Panama</option>
				<option value="PG" <?php if ( in_array( 'PG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Papua New Guinea</option>
				<option value="PY" <?php if ( in_array( 'PY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Paraguay</option>
				<option value="PE" <?php if ( in_array( 'PE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Peru</option>
				<option value="PH" <?php if ( in_array( 'PH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Philippines</option>
				<option value="PN" <?php if ( in_array( 'PN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Pitcairn</option>
				<option value="PL" <?php if ( in_array( 'PL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Poland</option>
				<option value="PT" <?php if ( in_array( 'PT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Portugal</option>
				<option value="PR" <?php if ( in_array( 'PR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Puerto Rico</option>
				<option value="QA" <?php if ( in_array( 'QA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Qatar</option>
				<option value="CG" <?php if ( in_array( 'CG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Republic of the Congo</option>
				<option value="RE" <?php if ( in_array( 'RE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Reunion</option>
				<option value="RO" <?php if ( in_array( 'RO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Romania</option>
				<option value="RU" <?php if ( in_array( 'RU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Russia</option>
				<option value="RW" <?php if ( in_array( 'RW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Rwanda</option>
				<option value="BL" <?php if ( in_array( 'BL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Barthelemy</option>
				<option value="SH" <?php if ( in_array( 'SH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Helena</option>
				<option value="KN" <?php if ( in_array( 'KN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Kitts and Nevis</option>
				<option value="LC" <?php if ( in_array( 'LC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Lucia</option>
				<option value="MF" <?php if ( in_array( 'MF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Martin</option>
				<option value="PM" <?php if ( in_array( 'PM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Pierre and Miquelon</option>
				<option value="VC" <?php if ( in_array( 'VC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saint Vincent and the Grenadines</option>
				<option value="WS" <?php if ( in_array( 'WS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Samoa</option>
				<option value="SM" <?php if ( in_array( 'SM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>San Marino</option>
				<option value="ST" <?php if ( in_array( 'ST', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sao Tome and Principe</option>
				<option value="SA" <?php if ( in_array( 'SA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Saudi Arabia</option>
				<option value="SN" <?php if ( in_array( 'SN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Senegal</option>
				<option value="RS" <?php if ( in_array( 'RS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Serbia</option>
				<option value="SC" <?php if ( in_array( 'SC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Seychelles</option>
				<option value="SL" <?php if ( in_array( 'SL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sierra Leone</option>
				<option value="SG" <?php if ( in_array( 'SG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Singapore</option>
				<option value="SX" <?php if ( in_array( 'SX', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sint Maarten</option>
				<option value="SK" <?php if ( in_array( 'SK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Slovakia</option>
				<option value="SI" <?php if ( in_array( 'SI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Slovenia</option>
				<option value="SB" <?php if ( in_array( 'SB', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Solomon Islands</option>
				<option value="SO" <?php if ( in_array( 'SO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Somalia</option>
				<option value="ZA" <?php if ( in_array( 'ZA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>South Africa</option>
				<option value="GS" <?php if ( in_array( 'GS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>South Georgia</option>
				<option value="KR" <?php if ( in_array( 'KR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>South Korea</option>
				<option value="SS" <?php if ( in_array( 'SS', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>South Sudan</option>
				<option value="ES" <?php if ( in_array( 'ES', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Spain</option>
				<option value="LK" <?php if ( in_array( 'LK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sri Lanka</option>
				<option value="SD" <?php if ( in_array( 'SD', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sudan</option>
				<option value="SR" <?php if ( in_array( 'SR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Suriname</option>
				<option value="SJ" <?php if ( in_array( 'SJ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Svalbard and Jan Mayen</option>
				<option value="SE" <?php if ( in_array( 'SE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Sweden</option>
				<option value="CH" <?php if ( in_array( 'CH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Switzerland</option>
				<option value="SY" <?php if ( in_array( 'SY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Syria</option>
				<option value="TW" <?php if ( in_array( 'TW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Taiwan</option>
				<option value="TJ" <?php if ( in_array( 'TJ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tajikistan</option>
				<option value="TZ" <?php if ( in_array( 'TZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tanzania</option>
				<option value="TH" <?php if ( in_array( 'TH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Thailand</option>
				<option value="TL" <?php if ( in_array( 'TL', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Timor-Leste</option>
				<option value="TG" <?php if ( in_array( 'TG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Togo</option>
				<option value="TK" <?php if ( in_array( 'TK', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tokelau</option>
				<option value="TO" <?php if ( in_array( 'TO', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tonga</option>
				<option value="TT" <?php if ( in_array( 'TT', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Trinidad and Tobago</option>
				<option value="TN" <?php if ( in_array( 'TN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tunisia</option>
				<option value="TR" <?php if ( in_array( 'TR', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Turkey</option>
				<option value="TM" <?php if ( in_array( 'TM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Turkmenistan</option>
				<option value="TC" <?php if ( in_array( 'TC', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Turks and Caicos Islands</option>
				<option value="TV" <?php if ( in_array( 'TV', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Tuvalu</option>
				<option value="UG" <?php if ( in_array( 'UG', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Uganda</option>
				<option value="UA" <?php if ( in_array( 'UA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Ukraine</option>
				<option value="AE" <?php if ( in_array( 'AE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>United Arab Emirates</option>
				<option value="GB" <?php if ( in_array( 'GB', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>United Kingdom</option>
				<option value="US" <?php if ( in_array( 'US', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>United States</option>
				<option value="UM" <?php if ( in_array( 'UM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>United States Minor Outlying Islands</option>
				<option value="UY" <?php if ( in_array( 'UY', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Uruguay</option>
				<option value="VI" <?php if ( in_array( 'VI', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>US Virgin Islands</option>
				<option value="UZ" <?php if ( in_array( 'UZ', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Uzbekistan</option>
				<option value="VU" <?php if ( in_array( 'VU', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Vanuatu</option>
				<option value="VA" <?php if ( in_array( 'VA', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Vatican City</option>
				<option value="VE" <?php if ( in_array( 'VE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Venezuela</option>
				<option value="VN" <?php if ( in_array( 'VN', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Vietnam</option>
				<option value="WF" <?php if ( in_array( 'WF', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Wallis and Futuna</option>
				<option value="EH" <?php if ( in_array( 'EH', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Western Sahara</option>
				<option value="YE" <?php if ( in_array( 'YE', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Yemen</option>
				<option value="ZM" <?php if ( in_array( 'ZM', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Zambia</option>
				<option value="ZW" <?php if ( in_array( 'ZW', $cf_blocked_countries ) ) { echo 'selected="selected"'; } ?>>Zimbabwe</option>
			</select>
		</label>
		<br style="clear:both">
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>