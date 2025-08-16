<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Multisite — Dam Spam', 'dam-spam' ); ?></h1>
	<?php
	$now	  = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
	// $ip = ds_get_ip();
	$ip	      = $_SERVER['REMOTE_ADDR'];
	$nonce	  = '';
	$muswitch = get_option( 'ds_muswitch' );
	if ( empty( $muswitch ) ) {
		$muswitch = 'N';
	}
	if ( array_key_exists( 'ds_control', $_POST ) ) {
		$nonce = $_POST['ds_control'];
	}
	if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
		if ( array_key_exists( 'action', $_POST ) ) {
			if ( array_key_exists( 'muswitch', $_POST ) ) {
				$muswitch = trim( stripslashes( sanitize_text_field( $_POST['muswitch'] ) ) );
			}
			if ( empty( $muswitch ) ) {
				$muswitch = 'N';
			}
			if ( $muswitch != 'Y' ) {
				$muswitch = 'N';
			}
			update_option( 'ds_muswitch', $muswitch );
			esc_html_e( 'Options Updated', 'dam-spam' );
		}
	} else {
	// echo "no nonce<br>";
	}
	$nonce = wp_create_nonce( 'ds_update' );
	?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
		<input type="hidden" name="action" value="update mu settings">
		<span style="font-weight:bold;font-size:1.2em"><?php esc_html_e( 'Network Blog Option', 'dam-spam' ); ?></span>
		<p><?php esc_html_e( 'Networked ON:', 'dam-spam' ); ?> <input name="muswitch" type="radio" value='Y' <?php if ( $muswitch == 'Y' ) { echo 'checked="true"'; } ?>>
		<br>
		<?php esc_html_e( 'Networked OFF:', 'dam-spam' ); ?> <input name="muswitch" type="radio" value='N' <?php if ( $muswitch != 'Y' ) { echo 'checked="true"'; } ?>>
		<br>
		<?php esc_html_e( 'If you are running WPMU and want to control options and history through the main login admin panel, select ON. If you select OFF, each blog will have to configure the plugin separately, and each blog will have a separte history.', 'dam-spam' ); ?></p>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>