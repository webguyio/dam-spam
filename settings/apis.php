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

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = isset( $_POST['ds_control'] ) ? sanitize_text_field( wp_unslash( $_POST['ds_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
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
		ds_set_options( $options );
		extract( $options );
	}
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'APIs — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<form method="post" action="">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div id="formchecking" class="mainsection"><?php esc_html_e( 'Blacklist Checking', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
	  		<label class="ds-subhead" for="check_dnsbl">
				<input class="ds_toggle" type="checkbox" id="check_dnsbl" name="check_dnsbl" value="Y" <?php if ( $check_dnsbl == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check DNSBLs (like Spamhaus.org)', 'dam-spam' ); ?></small>
			</label>
		</div>	  
		<br>		
		<div class="checkbox switcher">
	  		<label class="ds-subhead" for="check_sfs">
				<input class="ds_toggle" type="checkbox" id="check_sfs" name="check_sfs" value="Y" <?php if ( $check_sfs == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check Stop Forum Spam', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<label class="keyhead">
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
		<label class="keyhead">
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
		<label class="keyhead">
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
		<label class="keyhead">
			<?php esc_html_e( 'Google Safe Browsing API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="googleapi" type="text" value="<?php echo esc_attr( $googleapi ); ?>">
		</label>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>