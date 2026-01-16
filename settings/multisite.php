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

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-head"><?php esc_html_e( 'Multisite — Dam Spam', 'dam-spam' ); ?></h1>
	<?php
	$now	  = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
	$ip 	  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$nonce	  = '';
	$muswitch = get_option( 'dam_spam_muswitch' );
	if ( empty( $muswitch ) ) {
		$muswitch = 'N';
	}
	if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
		$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
	}
	if ( wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
		if ( array_key_exists( 'action', $_POST ) ) {
			if ( array_key_exists( 'muswitch', $_POST ) ) {
				$muswitch = isset( $_POST['muswitch'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['muswitch'] ) ) ) : '';
			}
			if ( empty( $muswitch ) ) {
				$muswitch = 'N';
			}
			if ( $muswitch != 'Y' ) {
				$muswitch = 'N';
			}
			update_option( 'dam_spam_muswitch', $muswitch );
			esc_html_e( 'Options Updated', 'dam-spam' );
		}
	} else {
	}
	$nonce = wp_create_nonce( 'dam_spam_update' );
	?>
	<form method="post" action="">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="action" value="update mu settings">
		<?php esc_html_e( 'Network Blog Option', 'dam-spam' ); ?>
		<p><?php esc_html_e( 'Networked ON:', 'dam-spam' ); ?> <input name="muswitch" type="radio" value='Y' <?php if ( $muswitch == 'Y' ) { echo 'checked="true"'; } ?>>
		<br>
		<?php esc_html_e( 'Networked OFF:', 'dam-spam' ); ?> <input name="muswitch" type="radio" value='N' <?php if ( $muswitch != 'Y' ) { echo 'checked="true"'; } ?>>
		<br>
		<?php esc_html_e( 'If want to control settings for all sites from the main admin, select ON. If you want to control settings separately for each respective site, select OFF.', 'dam-spam' ); ?></p>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>