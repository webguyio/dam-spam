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
$nonce   = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = $_POST['ds_control'];
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'action', $_POST ) ) {
		// other API keys
		if ( array_key_exists( 'apikey', $_POST ) ) {
			$apikey			   = stripslashes( sanitize_text_field( $_POST['apikey'] ) );
			$options['apikey'] = $apikey;
		}
		if ( array_key_exists( 'googleapi', $_POST ) ) {
			$googleapi			  = stripslashes( sanitize_text_field( $_POST['googleapi'] ) );
			$options['googleapi'] = $googleapi;
		}
		if ( array_key_exists( 'honeyapi', $_POST ) ) {
			$honeyapi			 = stripslashes( sanitize_text_field( $_POST['honeyapi'] ) );
			$options['honeyapi'] = $honeyapi;
		}
		if ( array_key_exists( 'botscoutapi', $_POST ) ) {
			$botscoutapi			= stripslashes( sanitize_text_field( $_POST['botscoutapi'] ) );
			$options['botscoutapi'] = $botscoutapi;
		}
		if ( array_key_exists( 'sfsfreq', $_POST ) ) {
			$sfsfreq			= stripslashes( sanitize_text_field( $_POST['sfsfreq'] ) );
			$options['sfsfreq'] = $sfsfreq;
		}
		if ( array_key_exists( 'sfsage', $_POST ) ) {
			$sfsage			   = stripslashes( sanitize_text_field( $_POST['sfsage'] ) );
			$options['sfsage'] = $sfsage;
		}
		if ( array_key_exists( 'hnyage', $_POST ) ) {
			$hnyage			   = stripslashes( sanitize_text_field( $_POST['hnyage'] ) );
			$options['hnyage'] = $hnyage;
		}
		if ( array_key_exists( 'hnylevel', $_POST ) ) {
			$hnylevel			 = stripslashes( sanitize_text_field( $_POST['hnylevel'] ) );
			$options['hnylevel'] = $hnylevel;
		}
		if ( array_key_exists( 'botfreq', $_POST ) ) {
			$botfreq			= stripslashes( sanitize_text_field( $_POST['botfreq'] ) );
			$options['botfreq'] = $botfreq;
		}
		$optionlist = array( 'chksfs', 'chkdnsbl' );
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
		ds_set_options( $options );
		extract( $options ); // extract again to get the new options
	}
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}
$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'APIs — Dam Spam', 'dam-spam' ); ?></h1>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<div class="ds-info-box">
		<p><?php esc_html_e( 'Below are several services that can be enabled to check for spam or protect your website against spammers.', 'dam-spam' ); ?></p>
	</div>
	<br>
	<form method="post" action="">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div class="checkbox switcher">
	  		<label class="ds-subhead" for="chkdnsbl">
				<input class="ds_toggle" type="checkbox" id="chkdnsbl" name="chkdnsbl" value="Y" <?php if ( $chkdnsbl == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important;"><?php esc_html_e( 'Check Against DNSBL Lists Such as Spamhaus.org', 'dam-spam' ); ?></span></small>
			</label>
		</div>	  
		<br>		
		<div class="checkbox switcher">
	  		<label class="ds-subhead" for="chksfs">
				<input class="ds_toggle" type="checkbox" id="chksfs" name="chksfs" value="Y" <?php if ( $chksfs == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important;"><?php esc_html_e( 'Enable Stop Forum Spam Lookups', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<label class="keyhead">
			<?php esc_html_e( 'StopForumSpam.com API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="apikey" type="text" value="<?php echo esc_attr( $apikey ); ?>">
		</label>
		<br>
		<table cellspacing="1" style="background-color:#ccc;font-size:0.9em">
			<tr bgcolor="white">
				<td valign="top">
				<?php printf(
					esc_html__( 'Block spammers found on Stop Forum Spam with more than', 'dam-spam' ) .
					'<input size="3" name="sfsfreq" type="text" class="small-text" value="' . esc_attr( $sfsfreq ) . '">'
					. esc_html__( 'incidents, and occurring less than', 'dam-spam' ) .
					'<input size="4" name="sfsage" type="text" class="small-text" value="' . esc_attr( $sfsage ) . '">'
				); ?>
				</td>
			</tr>
		</table>
		<br>
		<br>
		<label class="keyhead">
			<?php esc_html_e( 'Project Honeypot API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="honeyapi" type="text" value="<?php echo esc_attr( $honeyapi ); ?>">
		</label>
		<br>
		<table cellspacing="1" style="background-color:#ccc;font-size:0.9em">
			<tr bgcolor="white">
				<td valign="top">
				<?php printf(
					esc_html__( 'Block spammers found on Project HoneyPot with incidents less than', 'dam-spam' ) .
					'<input size="3" name="hnyage" type="text" class="small-text" value="' . esc_attr( $hnyage ) . '">'
					. esc_html__( 'days ago, and with more than', 'dam-spam' ) .
					'<input size="4" name="hnylevel" type="text" class="small-text" value="' . esc_attr( $hnylevel ) . '">'
					. esc_html__( 'threat level. (25 threat level is average, threat level 5 is fairly low.)', 'dam-spam' )
				); ?>
				</td>
			</tr>
		</table>
		<br>
		<br>
		<label class="keyhead">
			<?php esc_html_e( 'BotScout API Key', 'dam-spam' ); ?>
			<br>
			<input size="32" name="botscoutapi" type="text" value="<?php echo esc_attr( $botscoutapi ); ?>">
		</label>
		<br>
		<table cellspacing="1" style="background-color:#ccc;font-size:0.9em">
			<tr bgcolor="white">
				<td valign="top">
				<?php printf(
					esc_html__( 'Block spammers found on BotScout with more than', 'dam-spam' ) .
					'<input size="3" name="botfreq" type="text" class="small-text" value="' . esc_attr( $botfreq ) . '">'
					. esc_html__( 'incidents.', 'dam-spam' )
				); ?>
				</td>
			</tr>
		</table>
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