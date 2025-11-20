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
$nonce = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = isset( $_POST['ds_control'] ) ? sanitize_text_field( wp_unslash( $_POST['ds_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
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
		'check_tor',
		'check_akismet',
		'filter_registrations',
		'check_form',
		'check_woo_form',
		'check_gravity_form',
		'check_wp_form',
		'ds_private_mode',
		'check_ubiquity',
		'enable_custom_password'
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
	ds_set_options( $options );
	extract( $options );
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Protections — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<form method="post" action="" name="ss">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div id="formchecking" class="mainsection"><?php esc_html_e( 'Form Checking', 'dam-spam' ); ?></div>
		<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			echo '<p><span style="color:purple">' . esc_html__( 'WooCommerce detected. If you experience any issues using WooCommerce and Dam Spam together, you may need to adjust these settings.', 'dam-spam' ) . '</span></p>';
		} ?>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_form">
				<input class="ds_toggle" type="checkbox" id="check_form" name="check_form" value="Y" <?php if ( $check_form == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Only Check Native WordPress Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_woo_form">
				<input class="ds_toggle" type="checkbox" id="check_woo_form" name="check_woo_form" value="Y" <?php if ( $check_woo_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Ignore WooCommerce Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
	 	<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_gravity_form">
				<input class="ds_toggle" type="checkbox" id="check_gravity_form" name="check_gravity_form" value="Y" <?php if ( $check_gravity_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Ignore Gravity Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_wp_form">
				<input class="ds_toggle" type="checkbox" id="check_wp_form" name="check_wp_form" value="Y" <?php if ( $check_wp_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Ignore WP Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="membersonly" class="mainsection"><?php esc_html_e( 'Private Mode', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="ds_private_mode">
				<input class="ds_toggle" type="checkbox" id="ds_private_mode" name="ds_private_mode" value="Y" <?php if ( $ds_private_mode == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Users Must Be Logged in to View Site', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="preventlockouts" class="mainsection"><?php esc_html_e( 'Prevent Lockouts', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="add_to_allow_list">
				<input class="ds_toggle" type="checkbox" id="add_to_allow_list" name="add_to_allow_list" value="Y" <?php if ( $add_to_allow_list == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Automatically Add Admins to Allow List', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_admin_log">
				<input class="ds_toggle" type="checkbox" id="check_admin_log" name="check_admin_log" value="Y" <?php if ( $check_admin_log == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check Credentials on All Login Attempts', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div id="validaterequests" class="mainsection"><?php esc_html_e( 'Validate Requests', 'dam-spam' ); ?></div>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_accept">
				<input class="ds_toggle" type="checkbox" id="check_accept" name="check_accept" value="Y" <?php if ( $check_accept == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block Missing HTTP_ACCEPT Header', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_referer">
				<input class="ds_toggle" type="checkbox" id="check_referer" name="check_referer" value="Y" <?php if ( $check_referer == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block Invalid HTTP_REFERER', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_disposable">
				<input class="ds_toggle" type="checkbox" id="check_disposable" name="check_disposable" value="Y" <?php if ( $check_disposable == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block Disposable Email Addresses', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_long">
				<input class="ds_toggle" type="checkbox" id="check_long" name="check_long" value="Y" <?php if ( $check_long == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Long Emails, Usernames, and Passwords', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_short">
				<input class="ds_toggle" type="checkbox" id="check_short" name="check_short" value="Y" <?php if ( $check_short == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Short Emails and Usernames', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_bbcode">
				<input class="ds_toggle" type="checkbox" id="check_bbcode" name="check_bbcode" value="Y" <?php if ( $check_bbcode == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for BBCode', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_periods">
				<input class="ds_toggle" type="checkbox" id="check_periods" name="check_periods" value="Y" <?php if ( $check_periods == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Periods', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_hyphens">
				<input class="ds_toggle" type="checkbox" id="check_hyphens" name="check_hyphens" value="Y" <?php if ( $check_hyphens == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Hyphens', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_session">
				<input class="ds_toggle" type="checkbox" id="check_session" name="check_session" value="Y" onclick="ds_show_quick()" <?php if ( $check_session == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Quick Responses', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<span id="ds_show_quick" style="display:none">
			<p><?php esc_html_e( 'Response Timeout Value: ', 'dam-spam' ); ?>
			<input name="sesstime" type="text" value="<?php echo esc_attr( $sesstime ); ?>" size="2"><br></p>
		</span>
		<script>
		function ds_show_quick() {
			var checkBox = document.getElementById("check_session");
			var text = document.getElementById("ds_show_quick");
			if (checkBox.checked == true) {
				text.style.display = "block";
			} else {
				text.style.display = "none";
			}
		}
		ds_show_quick();
		</script>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_404">
				<input class="ds_toggle" type="checkbox" id="check_404" name="check_404" value="Y" <?php if ( $check_404 == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block 404 Exploit Probing', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_akismet">
				<input class="ds_toggle" type="checkbox" id="check_akismet" name="check_akismet" value="Y" <?php if ( $check_akismet == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block IPs Detected by Akismet', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_exploits">
				<input class="ds_toggle" type="checkbox" id="check_exploits" name="check_exploits" value="Y" <?php if ( $check_exploits == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Exploits', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_admin">
				<input class="ds_toggle" type="checkbox" id="check_admin" name="check_admin" value="Y" <?php if ( $check_admin == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Block Login Attempts for "admin" Username', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_ubiquity">
				<input class="ds_toggle" type="checkbox" id="check_ubiquity" name="check_ubiquity" value="Y" <?php if ( $check_ubiquity == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check Ubiquity-Nobis and Other Blacklists', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_hosting">
				<input class="ds_toggle" type="checkbox" id="check_hosting" name="check_hosting" value="Y" <?php if ( $check_hosting == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Major Hosting Companies and Cloud Services', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_tor">
				<input class="ds_toggle" type="checkbox" id="check_tor" name="check_tor" value="Y" <?php if ( $check_tor == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Tor Exit Nodes', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_multi">
				<input class="ds_toggle" type="checkbox" id="check_multi" name="check_multi" value="Y" onclick="ds_show_check_multi()" <?php if ( $check_multi == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Many Hits in a Short Time', 'dam-spam' ); ?></small>
			</label>
		</div>
		<span id="ds_show_check_multi" style="display:none">
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
		<script>
		function ds_show_check_multi() {
			var checkBox = document.getElementById("check_multi");
			var text = document.getElementById("ds_show_check_multi");
			if (checkBox.checked == true) {
				text.style.display = "block";
			} else {
				text.style.display = "none";
			}
		}
		jQuery(function() {
			ds_show_check_multi();
		});
		</script>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_amazon">
				<input class="ds_toggle" type="checkbox" id="check_amazon" name="check_amazon" value="Y" <?php if ( $check_amazon == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Check for Amazon Cloud', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="filter_registrations">
				<input class="ds_toggle" type="checkbox" id="filter_registrations" name="filter_registrations" value="Y" <?php if ( $filter_registrations == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Filter Login Requests', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br style="clear:both">
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
