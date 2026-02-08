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
$stats   = dam_spam_get_stats();
extract( $stats );
$now	 = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = dam_spam_get_options();
extract( $options );
$nonce   = '';
$ajaxurl = admin_url( 'admin-ajax.php' );

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	if ( array_key_exists( 'update_options', $_POST ) ) {
		if ( array_key_exists( 'dam_spam_cache', $_POST ) ) {
			$dam_spam_cache = isset( $_POST['dam_spam_cache'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_cache'] ) ) : '';
			$options['dam_spam_cache'] = $dam_spam_cache;
		}
		if ( array_key_exists( 'dam_spam_good', $_POST ) ) {
			$dam_spam_good = isset( $_POST['dam_spam_good'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_good'] ) ) : '';
			$options['dam_spam_good'] = $dam_spam_good;
		}
		dam_spam_set_options( $options );
	}
}

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
}

if ( wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	if ( array_key_exists( 'dam_spam_clear_cache', $_POST ) ) {
		$badips		      = array();
		$goodips		  = array();
		$stats['badips']  = $badips;
		$stats['goodips'] = $goodips;
		dam_spam_set_stats( $stats );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Cache Cleared', 'dam-spam' ) . '</p></div>';
	}
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'dam_spam_update' );

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-header"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 21 19V5"/><path d="M3 12A9 3 0 0 0 21 12"/></svg> <?php esc_html_e( 'Cache — Dam Spam', 'dam-spam' ); ?></h1>
	<?php
	if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<p><?php esc_html_e( 'If allowed, their IP is added to the Good Cache, and if blocked, it\'s added to the Bad Cache.', 'dam-spam' ); ?></p>
	<form method="post" action="">
		<input type="hidden" name="update_options" value="update">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<label class="key-header">
			<?php esc_html_e( 'Bad Cache Size', 'dam-spam' ); ?>
			<br>
			<select name="dam_spam_cache">
				<option value="0" <?php if ( $dam_spam_cache == '0' ) { echo 'selected="true"'; } ?>>0</option>
				<option value="10" <?php if ( $dam_spam_cache == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $dam_spam_cache == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $dam_spam_cache == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $dam_spam_cache == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $dam_spam_cache == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="200" <?php if ( $dam_spam_cache == '200' ) { echo 'selected="true"'; } ?>>200</option>
				<option value="500" <?php if ( $dam_spam_cache == '500' ) { echo 'selected="true"'; } ?>>500</option>
				<option value="1000" <?php if ( $dam_spam_cache == '1000' ) { echo 'selected="true"'; } ?>>1000</option>
			</select>
		</label>
		<br>
		<br>
		<label class="key-header">
			<?php esc_html_e( 'Good Cache Size', 'dam-spam' ); ?>
			<br>
			<select name="dam_spam_good">
				<option value="1" <?php if ( $dam_spam_good == '1' ) { echo 'selected="true"'; } ?>>1</option>
				<option value="2" <?php if ( $dam_spam_good == '2' ) { echo 'selected="true"'; } ?>>2</option>
				<option value="3" <?php if ( $dam_spam_good == '3' ) { echo 'selected="true"'; } ?>>3</option>
				<option value="4" <?php if ( $dam_spam_good == '4' ) { echo 'selected="true"'; } ?>>4</option>
				<option value="10" <?php if ( $dam_spam_good == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $dam_spam_good == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $dam_spam_good == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $dam_spam_good == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $dam_spam_good == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="200" <?php if ( $dam_spam_good == '200' ) { echo 'selected="true"'; } ?>>200</option>
				<option value="500" <?php if ( $dam_spam_good == '500' ) { echo 'selected="true"'; } ?>>500</option>
				<option value="1000" <?php if ( $dam_spam_good == '1000' ) { echo 'selected="true"'; } ?>>1000</option>
			</select>
		</label>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<?php if ( count( $badips ) == 0 && count( $goodips ) == 0 ) {
		esc_html_e( 'Nothing in the cache.', 'dam-spam' );
	} else { ?>
		<h2><?php esc_html_e( 'Cached IPs', 'dam-spam' ); ?></h2>
		<form method="post" action="">
			<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
			<input type="hidden" name="dam_spam_clear_cache" value="true">
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Clear Cache', 'dam-spam' ); ?>" type="submit"></p>
		</form>
		<table>
			<tr>
				<?php if ( count( $badips ) > 0 ) { arsort( $badips ); ?>
					<td width="30%"><?php esc_html_e( 'Bad IPs', 'dam-spam' ); ?></td>
				<?php } ?>
				<?php if ( count( $goodips ) > 0 ) { ?>
					<td width="30%"><?php esc_html_e( 'Good IPs', 'dam-spam' ); ?></td>
				<?php } ?>
			</tr>
			<tr>
				<?php if ( count( $badips ) > 0 ) { ?>
					<td valign="top" id="badips"><?php
						$allowed_html = dam_spam_get_ajax_allowed_html();
						$show = dam_spam_load( 'get_bad_cache', 'x', $stats, $options );
						echo wp_kses( $show, $allowed_html );
					?></td>
				<?php } ?>
				<?php if ( count( $goodips ) > 0 ) { arsort( $goodips ); ?>
					<td valign="top" id="goodips"><?php
						$allowed_html = dam_spam_get_ajax_allowed_html();
						$show = dam_spam_load( 'get_good_cache', 'x', $stats, $options );
						echo wp_kses( $show, $allowed_html );
						?>
					</td>
				<?php } ?>
			</tr>
		</table>
	<?php } ?>
</div>