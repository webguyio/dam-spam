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
$nonce   = '';

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	if ( array_key_exists( 'action', $_POST ) ) {
		if ( array_key_exists( 'apikey', $_POST ) ) {
			$apikey = isset( $_POST['apikey'] ) ? sanitize_text_field( wp_unslash( $_POST['apikey'] ) ) : '';
			$options['apikey'] = $apikey;
		}
		if ( array_key_exists( 'googleapi', $_POST ) ) {
			$googleapi = isset( $_POST['googleapi'] ) ? sanitize_text_field( wp_unslash( $_POST['googleapi'] ) ) : '';
			$options['googleapi'] = $googleapi;
		}
		if ( array_key_exists( 'honeyapi', $_POST ) ) {
			$honeyapi = isset( $_POST['honeyapi'] ) ? sanitize_text_field( wp_unslash( $_POST['honeyapi'] ) ) : '';
			$options['honeyapi'] = $honeyapi;
		}
		if ( array_key_exists( 'botscoutapi', $_POST ) ) {
			$botscoutapi = isset( $_POST['botscoutapi'] ) ? sanitize_text_field( wp_unslash( $_POST['botscoutapi'] ) ) : '';
			$options['botscoutapi'] = $botscoutapi;
		}
		if ( array_key_exists( 'sfsfreq', $_POST ) ) {
			$sfsfreq = isset( $_POST['sfsfreq'] ) ? sanitize_text_field( wp_unslash( $_POST['sfsfreq'] ) ) : '';
			$options['sfsfreq'] = $sfsfreq;
		}
		if ( array_key_exists( 'sfsage', $_POST ) ) {
			$sfsage = isset( $_POST['sfsage'] ) ? sanitize_text_field( wp_unslash( $_POST['sfsage'] ) ) : '';
			$options['sfsage'] = $sfsage;
		}
		if ( array_key_exists( 'hnyage', $_POST ) ) {
			$hnyage = isset( $_POST['hnyage'] ) ? sanitize_text_field( wp_unslash( $_POST['hnyage'] ) ) : '';
			$options['hnyage'] = $hnyage;
		}
		if ( array_key_exists( 'hnylevel', $_POST ) ) {
			$hnylevel = isset( $_POST['hnylevel'] ) ? sanitize_text_field( wp_unslash( $_POST['hnylevel'] ) ) : '';
			$options['hnylevel'] = $hnylevel;
		}
		if ( array_key_exists( 'botfreq', $_POST ) ) {
			$botfreq = isset( $_POST['botfreq'] ) ? sanitize_text_field( wp_unslash( $_POST['botfreq'] ) ) : '';
			$options['botfreq'] = $botfreq;
		}
		if ( array_key_exists( 'cf_email', $_POST ) ) {
			$cf_email = isset( $_POST['cf_email'] ) ? sanitize_email( wp_unslash( $_POST['cf_email'] ) ) : '';
			$options['cf_email'] = $cf_email;
		}
		if ( array_key_exists( 'cf_api_key', $_POST ) ) {
			$cf_api_key = isset( $_POST['cf_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_api_key'] ) ) : '';
			$options['cf_api_key'] = $cf_api_key;
		}
		if ( array_key_exists( 'cf_zone_id', $_POST ) ) {
			$cf_zone_id = isset( $_POST['cf_zone_id'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_zone_id'] ) ) : '';
			$options['cf_zone_id'] = $cf_zone_id;
		}
		$optionlist = array( 'check_sfs', 'check_dnsbl' );
		foreach ( $optionlist as $check ) {
			$v = 'N';
			if ( array_key_exists( $check, $_POST ) ) {
				$v = isset( $_POST[$check] ) ? sanitize_text_field( wp_unslash( $_POST[$check] ) ) : 'N';
				if ( $v != 'Y' ) {
					$v = 'N';
				}
			}
			$options[$check] = $v;
		}
		dam_spam_set_options( $options );
		extract( $options );
	}
	if ( array_key_exists( 'cf_clear_cache', $_POST ) ) {
		$result = function_exists( 'dam_spam_cloudflare_clear_cache' ) ? dam_spam_cloudflare_clear_cache() : array( 'success' => false, 'message' => 'Function not available' );
		if ( isset( $result['success'] ) && $result['success'] === true ) {
			$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Cloudflare cache cleared successfully.', 'dam-spam' ) . '</p></div>';
		} else {
			$error_msg = isset( $result['message'] ) ? $result['message'] : esc_html__( 'Unknown error', 'dam-spam' );
			$msg = '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Cloudflare cache clear failed: ', 'dam-spam' ) . esc_html( $error_msg ) . '</p></div>';
		}
	} else {
		$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
	}
}

$nonce = wp_create_nonce( 'dam_spam_update' );

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-header"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"/><circle cx="16.5" cy="7.5" r=".5" fill="currentColor"/></svg> <?php esc_html_e( 'APIs — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<form method="post" action="">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div id="cloudflare" class="main-section"><?php esc_html_e( 'Cloudflare Integration', 'dam-spam' ); ?></div>
		<p class="description">
			<?php
			printf(
				/* translators: %s: URL to documentation */
				esc_html__( 'Need help finding these? See the %s.', 'dam-spam' ),
				'<a href="https://github.com/webguyio/dam-spam/wiki#cloudflare-integration" target="_blank">' . esc_html__( 'setup guide', 'dam-spam' ) . '</a>'
			);
			?>
		</p>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Cloudflare Email', 'dam-spam' ); ?>
			<br>
			<input size="32" name="cf_email" type="email" value="<?php echo esc_attr( $cf_email ); ?>">
		</label>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Cloudflare Global API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="cf_api_key" type="password" value="<?php echo esc_attr( $cf_api_key ); ?>">
		</label>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Cloudflare Zone ID', 'dam-spam' ); ?>
			<br>
			<input size="32" name="cf_zone_id" type="text" value="<?php echo esc_attr( $cf_zone_id ); ?>">
		</label>
		<br>
		<button type="submit" name="cf_clear_cache" class="button-secondary" <?php if ( !$cf_configured ) { echo 'disabled="disabled"'; } ?>>
			<?php esc_html_e( 'Clear Cloudflare Cache', 'dam-spam' ); ?>
		</button>
		<br>
		<br>
		<div id="blocklist-checking" class="main-section"><?php esc_html_e( 'Blocklist Checking', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
	  		<label class="dam-spam-sub-header" for="check_dnsbl">
				<input class="dam_spam_toggle" type="checkbox" id="check_dnsbl" name="check_dnsbl" value="Y" <?php if ( $check_dnsbl == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check DNSBLs (like Spamhaus.org)', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-sub-header" for="check_sfs">
				<input class="dam_spam_toggle" type="checkbox" id="check_sfs" name="check_sfs" value="Y" <?php if ( $check_sfs == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check Stop Forum Spam', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'StopForumSpam.com API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="apikey" type="text" value="<?php echo esc_attr( $apikey ); ?>">
		</label>
		<?php printf(
			esc_html__( 'Block spammers found with more than ', 'dam-spam' ) .
			'<input size="3" name="sfsfreq" type="text" class="small-text" value="' . esc_attr( $sfsfreq ) . '">'
			. esc_html__( ' incidents, and occurring less than ', 'dam-spam' ) .
			'<input size="4" name="sfsage" type="text" class="small-text" value="' . esc_attr( $sfsage ) . '">'
			. esc_html__( ' days ago.', 'dam-spam' )
		); ?>
		<br>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Project Honeypot API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="honeyapi" type="text" value="<?php echo esc_attr( $honeyapi ); ?>">
		</label>
		<?php printf(
			esc_html__( 'Block spammers found with more than ', 'dam-spam' ) .
			'<input size="4" name="hnylevel" type="text" class="small-text" value="' . esc_attr( $hnylevel ) . '">'
			. esc_html__( ' threat level (25 is average, 5 is low), and occurring less than ', 'dam-spam' ) .
			'<input size="3" name="hnyage" type="text" class="small-text" value="' . esc_attr( $hnyage ) . '">'
			. esc_html__( ' days ago.', 'dam-spam' )
		); ?>
		<br>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'BotScout API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="botscoutapi" type="text" value="<?php echo esc_attr( $botscoutapi ); ?>">
		</label>
		<?php printf(
			esc_html__( 'Block spammers found with more than ', 'dam-spam' ) .
			'<input size="3" name="botfreq" type="text" class="small-text" value="' . esc_attr( $botfreq ) . '">'
			. esc_html__( ' incidents.', 'dam-spam' )
		); ?>
		<br>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Google Safe Browsing API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="googleapi" type="text" value="<?php echo esc_attr( $googleapi ); ?>">
		</label>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>