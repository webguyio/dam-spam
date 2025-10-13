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
	if ( array_key_exists( 'block_list', $_POST ) ) {
		$block_list = isset( $_POST['block_list'] ) ? sanitize_textarea_field( wp_unslash( $_POST['block_list'] ) ) : '';
		if ( empty( $block_list ) ) {
			$block_list = array();
		} else {
			$block_list = explode( "\n", $block_list );
		}
		$tblock_list = array();
		foreach ( $block_list as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['block_list'] = $tblock_list;
		$block_list = $tblock_list;
	}
	if ( array_key_exists( 'spam_words', $_POST ) ) {
		$spam_words = isset( $_POST['spam_words'] ) ? sanitize_textarea_field( wp_unslash( $_POST['spam_words'] ) ) : '';
		if ( empty( $spam_words ) ) {
			$spam_words = array();
		} else {
			$spam_words = explode( "\n", $spam_words );
		}
		$tblock_list = array();
		foreach ( $spam_words as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['spam_words'] = $tblock_list;
		$spam_words = $tblock_list;
	}
	if ( array_key_exists( 'block_url_shortners', $_POST ) ) {
		$block_url_shortners = isset( $_POST['block_url_shortners'] ) ? sanitize_textarea_field( wp_unslash( $_POST['block_url_shortners'] ) ) : '';
		if ( empty( $block_url_shortners ) ) {
			$block_url_shortners = array();
		} else {
			$block_url_shortners = explode( "\n", $block_url_shortners );
		}
		$tblock_list = array();
		foreach ( $block_url_shortners as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['block_url_shortners'] = $tblock_list;
		$block_url_shortners = $tblock_list;
	}
	if ( array_key_exists( 'bad_tlds', $_POST ) ) {
		$bad_tlds = isset( $_POST['bad_tlds'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bad_tlds'] ) ) : '';
		if ( empty( $bad_tlds ) ) {
			$bad_tlds = array();
		} else {
			$bad_tlds = explode( "\n", $bad_tlds );
		}
		$tblock_list = array();
		foreach ( $bad_tlds as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['bad_tlds'] = $tblock_list;
		$bad_tlds = $tblock_list;
	}
	if ( array_key_exists( 'bad_agents', $_POST ) ) {
		$bad_agents = isset( $_POST['bad_agents'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bad_agents'] ) ) : '';
		if ( empty( $bad_agents ) ) {
			$bad_agents = array();
		} else {
			$bad_agents = explode( "\n", $bad_agents );
		}
		$tblock_list = array();
		foreach ( $bad_agents as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['bad_agents'] = $tblock_list;
		$bad_agents = $tblock_list;
	}
	$optionlist = array(
		'check_spam_words',
		'check_blocked_user_id',
		'check_agent',
		'check_ipsync',
		'check_urls'
	);
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
		<div class="mainsection"><?php esc_html_e( 'Block List', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'One email or IP per line. You can use wild cards here for emails.', 'dam-spam' ); ?></p>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_blocked_user_id">
				<input class="ds_toggle" type="checkbox" id="check_blocked_user_id" name="check_blocked_user_id" value="Y" <?php if ( $check_blocked_user_id == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Allow Usernames', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<textarea name="block_list" cols="40" rows="8"><?php
			foreach ( $block_list as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Spam Words List', 'dam-spam' ); ?></div>				
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_spam_words">
				<input class="ds_toggle" type="checkbox" id="check_spam_words" name="check_spam_words" value="Y" <?php if ( $check_spam_words == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check Spam Words', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<textarea name="spam_words" cols="40" rows="8"><?php
			foreach ( $spam_words as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<div class="mainsection"><?php esc_html_e( 'URL Shortening Services List', 'dam-spam' ); ?></div>			
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_url_short">
				<input class="ds_toggle" type="checkbox" id="check_url_short" name="check_url_short" value="Y" <?php if ( $check_url_short == 'Y' ) { echo 'checked="checked"'; } ?>>
				<span><small></small></span>
				<small><?php esc_html_e( 'Check URL Shorteners', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<textarea name="block_url_shortners" cols="40" rows="8"><?php
			foreach ( $block_url_shortners as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<div class="mainsection"><?php esc_html_e( 'Check for URLs', 'dam-spam' ); ?></div>	
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_urls">
				<input class="ds_toggle" type="checkbox" id="check_urls" name="check_urls" value="Y" <?php if ( $check_urls == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check for any URL', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Bad User Agents List', 'dam-spam' ); ?></div>	
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_agent">
				<input class="ds_toggle" type="checkbox" id="check_agent" name="check_agent" value="Y" <?php if ( $check_agent == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Check Agents', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<textarea name="bad_agents" cols="40" rows="8"><?php
			foreach ( $bad_agents as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Blocked TLDs', 'dam-spam' ); ?></div>					
		<p><?php esc_html_e( 'One TLD per line. Example: .com', 'dam-spam' ); ?></p>
		<textarea name="bad_tlds" cols="40" rows="8"><?php
			foreach ( $bad_tlds as $p ) {
				echo esc_html( $p ) . "\r\n";
			}
		?></textarea>
		<br>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
