<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$stats   = ds_get_stats();
extract( $stats );
$now	 = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = ds_get_options();
extract( $options );
// temp: not used in file
$nonce   = '';
$ajaxurl = admin_url( 'admin-ajax.php' );

// update options
if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = $_POST['ds_control'];
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'update_options', $_POST ) ) {
		if ( array_key_exists( 'ds_cache', $_POST ) ) {
			$ds_cache			= stripslashes( sanitize_text_field( $_POST['ds_cache'] ) );
			$options['ds_cache'] = $ds_cache;
		}
		if ( array_key_exists( 'ds_good', $_POST ) ) {
			$ds_good			   = stripslashes( sanitize_text_field( $_POST['ds_good'] ) );
			$options['ds_good'] = $ds_good;
		}
		ds_set_options( $options );
	}
}

// clear the cache
if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = $_POST['ds_control'];
}

if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'ds_clear_cache', $_POST ) ) {
		// clear the cache
		$badips		      = array();
		$goodips		  = array();
		$stats['badips']  = $badips;
		$stats['goodips'] = $goodips;
		ds_set_stats( $stats );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Cache Cleared', 'dam-spam' ) . '</p></div>';
	}
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Cache — Dam Spam', 'dam-spam' ); ?></h1>
	<?php
	if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<div class="ds-info-box">
	<p><?php esc_html_e( '
		Whenever a user tries to leave a comment, register, or log in, they are
		recorded in the Good Cache if they pass or the Bad Cache if they fail.
		If a user is blocked from access, they are added to the Bad Cache. You
		can see the caches here.
	', 'dam-spam' ); ?></p>
	<form method="post" action="">
		<input type="hidden" name="update_options" value="update">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<label class="keyhead">
			<?php esc_html_e( 'Bad IP Cache Size', 'dam-spam' ); ?>
			<br>
			<select name="ds_cache">
				<option value="0" <?php if ( $ds_cache == '0' ) { echo 'selected="true"'; } ?>>0</option>
				<option value="10" <?php if ( $ds_cache == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $ds_cache == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $ds_cache == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $ds_cache == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $ds_cache == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="200" <?php if ( $ds_cache == '200' ) { echo 'selected="true"'; } ?>>200</option>
				<option value="500" <?php if ( $ds_cache == '500' ) { echo 'selected="true"'; } ?>>500</option>
				<option value="1000" <?php if ( $ds_cache == '1000' ) { echo 'selected="true"'; } ?>>1000</option>
			</select>
		</label>
		<br>
		<br>
		<label class="keyhead">
			<?php esc_html_e( 'Good IP Cache Size', 'dam-spam' ); ?>
			<br>
			<select name="ds_good">
				<option value="1" <?php if ( $ds_good == '1' ) { echo 'selected="true"'; } ?>>1</option>
				<option value="2" <?php if ( $ds_good == '2' ) { echo 'selected="true"'; } ?>>2</option>
				<option value="3" <?php if ( $ds_good == '3' ) { echo 'selected="true"'; } ?>>3</option>
				<option value="4" <?php if ( $ds_good == '4' ) { echo 'selected="true"'; } ?>>4</option>
				<option value="10" <?php if ( $ds_good == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $ds_good == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $ds_good == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $ds_good == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $ds_good == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="200" <?php if ( $ds_good == '200' ) { echo 'selected="true"'; } ?>>200</option>
				<option value="500" <?php if ( $ds_good == '500' ) { echo 'selected="true"'; } ?>>500</option>
				<option value="1000" <?php if ( $ds_good == '1000' ) { echo 'selected="true"'; } ?>>1000</option>
			</select>
		</label>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<?php
	if ( count( $badips ) == 0 && count( $goodips ) == 0 ) {
		esc_html_e( 'Nothing in the cache.', 'dam-spam' );
	} else {
		?>
		<h2><?php esc_html_e( 'Cached Values', 'dam-spam' ); ?></h2>
		<form method="post" action="">
			<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
			<input type="hidden" name="ds_clear_cache" value="true">
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Clear the Cache', 'dam-spam' ); ?>" type="submit"></p>
		</form>
		<table>
			<tr>
				<?php
				if ( count( $badips ) > 0 ) {
					arsort( $badips );
					?>
					<td width="30%"><?php esc_html_e( 'Bad IPs', 'dam-spam' ); ?></td>
					<?php
				}
				?>
				<?php
				if ( count( $goodips ) > 0 ) {
					?>
					<td width="30%"><?php esc_html_e( 'Good IPs', 'dam-spam' ); ?></td>
					<?php
				}
				?>
			</tr>
			<tr>
				<?php
				if ( count( $badips ) > 0 ) {
					?>
					<td valign="top" id="badips"><?php
						// use the be_load to get badips
						$show = be_load( 'ds_get_bcache', 'x', $stats,
							$options );
						echo esc_html( $show );
						?></td>
					<?php
				}
				?>
				<?php
				if ( count( $goodips ) > 0 ) {
					arsort( $goodips );
					?>
					<td valign="top" id="goodips"><?php
						// use the be_load to get badips
						$show = be_load( 'ds_get_gcache', 'x', $stats,
							$options );
						echo esc_html( $show );
						?></td>
					<?php
				}
				?>
			</tr>
		</table>
		<?php
	}
?>
