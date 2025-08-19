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
	if ( array_key_exists( 'blist', $_POST ) ) {
		$blist = sanitize_textarea_field( $_POST['blist'] );
		if ( empty( $blist ) ) {
			$blist = array();
		} else {
			$blist = explode( "\n", $blist );
		}
		$tblist = array();
		foreach ( $blist as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblist[] = $bl;
			}
		}
		$options['blist'] = $tblist;
		$blist			  = $tblist;
	}
	if ( array_key_exists( 'spamwords', $_POST ) ) {
		$spamwords = sanitize_textarea_field( $_POST['spamwords'] );
		if ( empty( $spamwords ) ) {
			$spamwords = array();
		} else {
			$spamwords = explode( "\n", $spamwords );
		}
		$tblist = array();
		foreach ( $spamwords as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblist[] = $bl;
			}
		}
		$options['spamwords'] = $tblist;
		$spamwords			  = $tblist;
	}
	if ( array_key_exists( 'blockurlshortners', $_POST ) ) {
		$blockurlshortners = sanitize_textarea_field( $_POST['blockurlshortners'] );
		if ( empty( $blockurlshortners ) ) {
			$blockurlshortners = array();
		} else {
			$blockurlshortners = explode( "\n", $blockurlshortners );
		}
		$tblist = array();
		foreach ( $blockurlshortners as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblist[] = $bl;
			}
		}
		$options['blockurlshortners'] = $tblist;
		$blockurlshortners			  = $tblist;
	}
	if ( array_key_exists( 'badTLDs', $_POST ) ) {
		$badTLDs = sanitize_textarea_field( $_POST['badTLDs'] );
		if ( empty( $badTLDs ) ) {
			$badTLDs = array();
		} else {
			$badTLDs = explode( "\n", $badTLDs );
		}
		$tblist = array();
		foreach ( $badTLDs as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblist[] = $bl;
			}
		}
		$options['badTLDs'] = $tblist;
		$badTLDs			= $tblist;
	}
	if ( array_key_exists( 'badagents', $_POST ) ) {
		$badagents = sanitize_textarea_field( $_POST['badagents'] );
		if ( empty( $badagents ) ) {
			$badagents = array();
		} else {
			$badagents = explode( "\n", $badagents );
		}
		$tblist = array();
		foreach ( $badagents as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblist[] = $bl;
			}
		}
		$options['badagents'] = $tblist;
		$badagents			  = $tblist;
	}
	// check box setting
	$optionlist = array(
		'chkspamwords',
		'chkbluserid',
		'chkagent',
		'chkipsync',
		'chkurls'
	);
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
	extract( $options );
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Blocked — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<br>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<form method="post" action="">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div class="mainsection"><?php esc_html_e( 'Personalized Block List', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'Add IP addresses or emails here that you want blocked.', 'dam-spam' ); ?></p>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkbluserid">
				<input class="ds_toggle" type="checkbox" id="chkbluserid" name="chkbluserid" value="Y" <?php if ( $chkbluserid == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php esc_html_e( 'Enable Block by Username', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<textarea name="blist" cols="40" rows="8"><?php
			foreach ( $blist as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Spam Words List', 'dam-spam' ); ?></div>				
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkspamwords">
				<input class="ds_toggle" type="checkbox" id="chkspamwords" name="chkspamwords" value="Y" <?php if ( $chkspamwords == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><span style="font-size:16px!important"><?php esc_html_e( 'Check Spam Words', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<textarea name="spamwords" cols="40" rows="8"><?php
			foreach ( $spamwords as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<div class="mainsection"><?php esc_html_e( 'URL Shortening Services List', 'dam-spam' ); ?></div>			
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkurlshort">
				<input class="ds_toggle" type="checkbox" id="chkurlshort" name="chkurlshort" value="Y" <?php if ( $chkurlshort == 'Y' ) { echo 'checked="checked"'; } ?>>
				<span><small></small></span>
				<small><span style="font-size:16px!important"><?php esc_html_e( 'Check URL Shorteners', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<textarea name="blockurlshortners" cols="40" rows="8"><?php
			foreach ( $blockurlshortners as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<div class="mainsection"><?php esc_html_e( 'Check for URLs', 'dam-spam' ); ?></div>	
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkurls">
				<input class="ds_toggle" type="checkbox" id="chkurls" name="chkurls" value="Y" <?php if ( $chkurls == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php esc_html_e( 'Check for any URL', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Bad User Agents List', 'dam-spam' ); ?></div>	
		<div class="checkbox switcher">
			<label class="ds-subhead" for="chkagent">
				<input class="ds_toggle" type="checkbox" id="chkagent" name="chkagent" value="Y" <?php if ( $chkagent == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><span style="font-size:16px!important"><?php esc_html_e( 'Check Agents', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<textarea name="badagents" cols="40" rows="8"><?php
			foreach ( $badagents as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Blocked TLDs', 'dam-spam' ); ?></div>					
		<p><?php esc_html_e( 'Enter the TLD name including the period (for example .xxx). A TLD is the last part of a domain like .com or .net.', 'dam-spam' ); ?></p>
		<textarea name="badTLDs" cols="40" rows="8"><?php
			foreach ( $badTLDs as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
