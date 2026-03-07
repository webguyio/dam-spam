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
	if ( array_key_exists( 'cf_blocked_countries_flag', $_POST ) ) {
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
			<br>
		<?php } ?>
		<fieldset class="dam-spam-country-list" <?php if ( !$cf_configured ) { echo 'disabled'; } ?> data-admin-country="<?php echo isset( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) ) : ''; ?>" data-own-country="<?php esc_attr_e( 'Your own country cannot be blocked.', 'dam-spam' ); ?>">
			<input type="hidden" name="cf_blocked_countries_flag" value="1">
			<input type="text" id="dam-spam-country-filter" placeholder="<?php esc_attr_e( 'Filter countries...', 'dam-spam' ); ?>" class="dam-spam-country-filter">
			<label class="dam-spam-check-all"><input type="checkbox" id="dam-spam-check-all"> <?php esc_html_e( 'Check All', 'dam-spam' ); ?></label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AF" <?php if ( in_array( 'AF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Afghanistan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AX" <?php if ( in_array( 'AX', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Aland Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AL" <?php if ( in_array( 'AL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Albania</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DZ" <?php if ( in_array( 'DZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Algeria</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AS" <?php if ( in_array( 'AS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> American Samoa</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AD" <?php if ( in_array( 'AD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Andorra</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AO" <?php if ( in_array( 'AO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Angola</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AI" <?php if ( in_array( 'AI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Anguilla</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AQ" <?php if ( in_array( 'AQ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Antarctica</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AG" <?php if ( in_array( 'AG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Antigua and Barbuda</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AR" <?php if ( in_array( 'AR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Argentina</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AM" <?php if ( in_array( 'AM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Armenia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AW" <?php if ( in_array( 'AW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Aruba</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AU" <?php if ( in_array( 'AU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Australia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AT" <?php if ( in_array( 'AT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Austria</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AZ" <?php if ( in_array( 'AZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Azerbaijan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BS" <?php if ( in_array( 'BS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bahamas</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BH" <?php if ( in_array( 'BH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bahrain</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BD" <?php if ( in_array( 'BD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bangladesh</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BB" <?php if ( in_array( 'BB', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Barbados</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BY" <?php if ( in_array( 'BY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Belarus</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BE" <?php if ( in_array( 'BE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Belgium</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BZ" <?php if ( in_array( 'BZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Belize</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BJ" <?php if ( in_array( 'BJ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Benin</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BM" <?php if ( in_array( 'BM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bermuda</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BT" <?php if ( in_array( 'BT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bhutan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BO" <?php if ( in_array( 'BO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bolivia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BQ" <?php if ( in_array( 'BQ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bonaire</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BA" <?php if ( in_array( 'BA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bosnia and Herzegovina</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BW" <?php if ( in_array( 'BW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Botswana</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BV" <?php if ( in_array( 'BV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bouvet Island</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BR" <?php if ( in_array( 'BR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Brazil</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IO" <?php if ( in_array( 'IO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> British Indian Ocean Territory</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VG" <?php if ( in_array( 'VG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> British Virgin Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BN" <?php if ( in_array( 'BN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Brunei</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BG" <?php if ( in_array( 'BG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Bulgaria</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BF" <?php if ( in_array( 'BF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Burkina Faso</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BI" <?php if ( in_array( 'BI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Burundi</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KH" <?php if ( in_array( 'KH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cambodia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CM" <?php if ( in_array( 'CM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cameroon</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CA" <?php if ( in_array( 'CA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Canada</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CV" <?php if ( in_array( 'CV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cape Verde</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KY" <?php if ( in_array( 'KY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cayman Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CF" <?php if ( in_array( 'CF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Central African Republic</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TD" <?php if ( in_array( 'TD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Chad</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CL" <?php if ( in_array( 'CL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Chile</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CN" <?php if ( in_array( 'CN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> China</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CX" <?php if ( in_array( 'CX', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Christmas Island</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CC" <?php if ( in_array( 'CC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cocos Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CO" <?php if ( in_array( 'CO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Colombia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KM" <?php if ( in_array( 'KM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Comoros</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CK" <?php if ( in_array( 'CK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cook Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CR" <?php if ( in_array( 'CR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Costa Rica</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HR" <?php if ( in_array( 'HR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Croatia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CU" <?php if ( in_array( 'CU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cuba</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CW" <?php if ( in_array( 'CW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Curacao</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CY" <?php if ( in_array( 'CY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Cyprus</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CZ" <?php if ( in_array( 'CZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Czech Republic</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CD" <?php if ( in_array( 'CD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Democratic Republic of the Congo</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DK" <?php if ( in_array( 'DK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Denmark</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DJ" <?php if ( in_array( 'DJ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Djibouti</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DM" <?php if ( in_array( 'DM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Dominica</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DO" <?php if ( in_array( 'DO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Dominican Republic</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="EC" <?php if ( in_array( 'EC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ecuador</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="EG" <?php if ( in_array( 'EG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Egypt</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SV" <?php if ( in_array( 'SV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> El Salvador</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GQ" <?php if ( in_array( 'GQ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Equatorial Guinea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ER" <?php if ( in_array( 'ER', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Eritrea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="EE" <?php if ( in_array( 'EE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Estonia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SZ" <?php if ( in_array( 'SZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Eswatini</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ET" <?php if ( in_array( 'ET', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ethiopia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FK" <?php if ( in_array( 'FK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Falkland Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FO" <?php if ( in_array( 'FO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Faroe Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FJ" <?php if ( in_array( 'FJ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Fiji</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FI" <?php if ( in_array( 'FI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Finland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FR" <?php if ( in_array( 'FR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> France</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GF" <?php if ( in_array( 'GF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> French Guiana</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PF" <?php if ( in_array( 'PF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> French Polynesia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TF" <?php if ( in_array( 'TF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> French Southern Territories</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GA" <?php if ( in_array( 'GA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Gabon</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GM" <?php if ( in_array( 'GM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Gambia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GE" <?php if ( in_array( 'GE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Georgia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="DE" <?php if ( in_array( 'DE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Germany</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GH" <?php if ( in_array( 'GH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ghana</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GI" <?php if ( in_array( 'GI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Gibraltar</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GR" <?php if ( in_array( 'GR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Greece</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GL" <?php if ( in_array( 'GL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Greenland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GD" <?php if ( in_array( 'GD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Grenada</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GP" <?php if ( in_array( 'GP', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guadeloupe</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GU" <?php if ( in_array( 'GU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guam</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GT" <?php if ( in_array( 'GT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guatemala</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GG" <?php if ( in_array( 'GG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guernsey</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GN" <?php if ( in_array( 'GN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guinea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GW" <?php if ( in_array( 'GW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guinea-Bissau</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GY" <?php if ( in_array( 'GY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Guyana</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HT" <?php if ( in_array( 'HT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Haiti</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HM" <?php if ( in_array( 'HM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Heard Island</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HN" <?php if ( in_array( 'HN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Honduras</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HK" <?php if ( in_array( 'HK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Hong Kong</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="HU" <?php if ( in_array( 'HU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Hungary</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IS" <?php if ( in_array( 'IS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Iceland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IN" <?php if ( in_array( 'IN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> India</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ID" <?php if ( in_array( 'ID', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Indonesia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IR" <?php if ( in_array( 'IR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Iran</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IQ" <?php if ( in_array( 'IQ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Iraq</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IE" <?php if ( in_array( 'IE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ireland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IM" <?php if ( in_array( 'IM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Isle of Man</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IL" <?php if ( in_array( 'IL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Israel</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="IT" <?php if ( in_array( 'IT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Italy</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CI" <?php if ( in_array( 'CI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ivory Coast</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="JM" <?php if ( in_array( 'JM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Jamaica</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="JP" <?php if ( in_array( 'JP', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Japan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="JE" <?php if ( in_array( 'JE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Jersey</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="JO" <?php if ( in_array( 'JO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Jordan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KZ" <?php if ( in_array( 'KZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Kazakhstan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KE" <?php if ( in_array( 'KE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Kenya</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KI" <?php if ( in_array( 'KI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Kiribati</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KW" <?php if ( in_array( 'KW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Kuwait</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KG" <?php if ( in_array( 'KG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Kyrgyzstan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LA" <?php if ( in_array( 'LA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Laos</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LV" <?php if ( in_array( 'LV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Latvia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LB" <?php if ( in_array( 'LB', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Lebanon</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LS" <?php if ( in_array( 'LS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Lesotho</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LR" <?php if ( in_array( 'LR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Liberia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LY" <?php if ( in_array( 'LY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Libya</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LI" <?php if ( in_array( 'LI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Liechtenstein</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LT" <?php if ( in_array( 'LT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Lithuania</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LU" <?php if ( in_array( 'LU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Luxembourg</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MO" <?php if ( in_array( 'MO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Macao</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MG" <?php if ( in_array( 'MG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Madagascar</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MW" <?php if ( in_array( 'MW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Malawi</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MY" <?php if ( in_array( 'MY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Malaysia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MV" <?php if ( in_array( 'MV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Maldives</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ML" <?php if ( in_array( 'ML', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mali</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MT" <?php if ( in_array( 'MT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Malta</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MH" <?php if ( in_array( 'MH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Marshall Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MQ" <?php if ( in_array( 'MQ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Martinique</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MR" <?php if ( in_array( 'MR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mauritania</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MU" <?php if ( in_array( 'MU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mauritius</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="YT" <?php if ( in_array( 'YT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mayotte</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MX" <?php if ( in_array( 'MX', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mexico</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="FM" <?php if ( in_array( 'FM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Micronesia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MD" <?php if ( in_array( 'MD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Moldova</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MC" <?php if ( in_array( 'MC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Monaco</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MN" <?php if ( in_array( 'MN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mongolia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ME" <?php if ( in_array( 'ME', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Montenegro</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MS" <?php if ( in_array( 'MS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Montserrat</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MA" <?php if ( in_array( 'MA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Morocco</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MZ" <?php if ( in_array( 'MZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Mozambique</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MM" <?php if ( in_array( 'MM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Myanmar</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NA" <?php if ( in_array( 'NA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Namibia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NR" <?php if ( in_array( 'NR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Nauru</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NP" <?php if ( in_array( 'NP', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Nepal</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NL" <?php if ( in_array( 'NL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Netherlands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NC" <?php if ( in_array( 'NC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> New Caledonia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NZ" <?php if ( in_array( 'NZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> New Zealand</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NI" <?php if ( in_array( 'NI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Nicaragua</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NE" <?php if ( in_array( 'NE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Niger</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NG" <?php if ( in_array( 'NG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Nigeria</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NU" <?php if ( in_array( 'NU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Niue</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NF" <?php if ( in_array( 'NF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Norfolk Island</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KP" <?php if ( in_array( 'KP', $cf_blocked_countries ) ) { echo 'checked'; } ?>> North Korea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MK" <?php if ( in_array( 'MK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> North Macedonia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MP" <?php if ( in_array( 'MP', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Northern Mariana Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="NO" <?php if ( in_array( 'NO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Norway</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="OM" <?php if ( in_array( 'OM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Oman</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PK" <?php if ( in_array( 'PK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Pakistan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PW" <?php if ( in_array( 'PW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Palau</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PS" <?php if ( in_array( 'PS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Palestine</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PA" <?php if ( in_array( 'PA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Panama</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PG" <?php if ( in_array( 'PG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Papua New Guinea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PY" <?php if ( in_array( 'PY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Paraguay</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PE" <?php if ( in_array( 'PE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Peru</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PH" <?php if ( in_array( 'PH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Philippines</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PN" <?php if ( in_array( 'PN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Pitcairn</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PL" <?php if ( in_array( 'PL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Poland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PT" <?php if ( in_array( 'PT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Portugal</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PR" <?php if ( in_array( 'PR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Puerto Rico</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="QA" <?php if ( in_array( 'QA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Qatar</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CG" <?php if ( in_array( 'CG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Republic of the Congo</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="RE" <?php if ( in_array( 'RE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Reunion</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="RO" <?php if ( in_array( 'RO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Romania</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="RU" <?php if ( in_array( 'RU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Russia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="RW" <?php if ( in_array( 'RW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Rwanda</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="BL" <?php if ( in_array( 'BL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Barthelemy</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SH" <?php if ( in_array( 'SH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Helena</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KN" <?php if ( in_array( 'KN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Kitts and Nevis</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LC" <?php if ( in_array( 'LC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Lucia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="MF" <?php if ( in_array( 'MF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Martin</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="PM" <?php if ( in_array( 'PM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Pierre and Miquelon</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VC" <?php if ( in_array( 'VC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saint Vincent and the Grenadines</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="WS" <?php if ( in_array( 'WS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Samoa</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SM" <?php if ( in_array( 'SM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> San Marino</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ST" <?php if ( in_array( 'ST', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sao Tome and Principe</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SA" <?php if ( in_array( 'SA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Saudi Arabia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SN" <?php if ( in_array( 'SN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Senegal</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="RS" <?php if ( in_array( 'RS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Serbia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SC" <?php if ( in_array( 'SC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Seychelles</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SL" <?php if ( in_array( 'SL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sierra Leone</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SG" <?php if ( in_array( 'SG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Singapore</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SX" <?php if ( in_array( 'SX', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sint Maarten</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SK" <?php if ( in_array( 'SK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Slovakia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SI" <?php if ( in_array( 'SI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Slovenia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SB" <?php if ( in_array( 'SB', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Solomon Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SO" <?php if ( in_array( 'SO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Somalia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ZA" <?php if ( in_array( 'ZA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> South Africa</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GS" <?php if ( in_array( 'GS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> South Georgia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="KR" <?php if ( in_array( 'KR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> South Korea</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SS" <?php if ( in_array( 'SS', $cf_blocked_countries ) ) { echo 'checked'; } ?>> South Sudan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ES" <?php if ( in_array( 'ES', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Spain</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="LK" <?php if ( in_array( 'LK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sri Lanka</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SD" <?php if ( in_array( 'SD', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sudan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SR" <?php if ( in_array( 'SR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Suriname</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SJ" <?php if ( in_array( 'SJ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Svalbard and Jan Mayen</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SE" <?php if ( in_array( 'SE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Sweden</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="CH" <?php if ( in_array( 'CH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Switzerland</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="SY" <?php if ( in_array( 'SY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Syria</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TW" <?php if ( in_array( 'TW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Taiwan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TJ" <?php if ( in_array( 'TJ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tajikistan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TZ" <?php if ( in_array( 'TZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tanzania</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TH" <?php if ( in_array( 'TH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Thailand</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TL" <?php if ( in_array( 'TL', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Timor-Leste</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TG" <?php if ( in_array( 'TG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Togo</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TK" <?php if ( in_array( 'TK', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tokelau</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TO" <?php if ( in_array( 'TO', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tonga</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TT" <?php if ( in_array( 'TT', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Trinidad and Tobago</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TN" <?php if ( in_array( 'TN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tunisia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TR" <?php if ( in_array( 'TR', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Turkey</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TM" <?php if ( in_array( 'TM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Turkmenistan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TC" <?php if ( in_array( 'TC', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Turks and Caicos Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="TV" <?php if ( in_array( 'TV', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Tuvalu</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="UG" <?php if ( in_array( 'UG', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Uganda</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="UA" <?php if ( in_array( 'UA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Ukraine</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="AE" <?php if ( in_array( 'AE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> United Arab Emirates</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="GB" <?php if ( in_array( 'GB', $cf_blocked_countries ) ) { echo 'checked'; } ?>> United Kingdom</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="US" <?php if ( in_array( 'US', $cf_blocked_countries ) ) { echo 'checked'; } ?>> United States</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="UM" <?php if ( in_array( 'UM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> United States Minor Outlying Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="UY" <?php if ( in_array( 'UY', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Uruguay</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VI" <?php if ( in_array( 'VI', $cf_blocked_countries ) ) { echo 'checked'; } ?>> US Virgin Islands</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="UZ" <?php if ( in_array( 'UZ', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Uzbekistan</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VU" <?php if ( in_array( 'VU', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Vanuatu</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VA" <?php if ( in_array( 'VA', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Vatican City</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VE" <?php if ( in_array( 'VE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Venezuela</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="VN" <?php if ( in_array( 'VN', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Vietnam</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="WF" <?php if ( in_array( 'WF', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Wallis and Futuna</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="EH" <?php if ( in_array( 'EH', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Western Sahara</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="YE" <?php if ( in_array( 'YE', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Yemen</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ZM" <?php if ( in_array( 'ZM', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Zambia</label>
			<label><input type="checkbox" name="cf_blocked_countries[]" value="ZW" <?php if ( in_array( 'ZW', $cf_blocked_countries ) ) { echo 'checked'; } ?>> Zimbabwe</label>
		</fieldset>
		<br style="clear:both">
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>