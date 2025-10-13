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
	$optionlist = array(
		'check_ad',
		'check_ae',
		'check_af',
		'check_al',
		'check_am',
		'check_ar',
		'check_at',
		'check_au',
		'check_ax',
		'check_az',
		'check_ba',
		'check_bb',
		'check_bd',
		'check_be',
		'check_bg',
		'check_bh',
		'check_bn',
		'check_bo',
		'check_br',
		'check_bs',
		'check_by',
		'check_bz',
		'check_ca',
		'check_cd',
		'check_ch',
		'check_cl',
		'check_cn',
		'check_co',
		'check_cr',
		'check_cu',
		'check_cw',
		'check_cy',
		'check_cz',
		'check_de',
		'check_dk',
		'check_do',
		'check_dz',
		'check_ec',
		'check_ee',
		'check_es',
		'check_eu',
		'check_fi',
		'check_fj',
		'check_fr',
		'check_gb',
		'check_ge',
		'check_gf',
		'check_gi',
		'check_gp',
		'check_gr',
		'check_gt',
		'check_gu',
		'check_gy',
		'check_hk',
		'check_hn',
		'check_hr',
		'check_ht',
		'check_hu',
		'check_id',
		'check_ie',
		'check_il',
		'check_in',
		'check_iq',
		'check_ir',
		'check_is',
		'check_it',
		'check_jm',
		'check_jo',
		'check_jp',
		'check_ke',
		'check_kg',
		'check_kh',
		'check_kr',
		'check_kw',
		'check_ky',
		'check_kz',
		'check_la',
		'check_lb',
		'check_lk',
		'check_lt',
		'check_lu',
		'check_lv',
		'check_md',
		'check_me',
		'check_mk',
		'check_mm',
		'check_mn',
		'check_mo',
		'check_mp',
		'check_mq',
		'check_mt',
		'check_mv',
		'check_mx',
		'check_my',
		'check_nc',
		'check_ni',
		'check_nl',
		'check_no',
		'check_np',
		'check_nz',
		'check_om',
		'check_pa',
		'check_pe',
		'check_pg',
		'check_ph',
		'check_pk',
		'check_pl',
		'check_pr',
		'check_ps',
		'check_pt',
		'check_pw',
		'check_py',
		'check_qa',
		'check_ro',
		'check_rs',
		'check_ru',
		'check_sa',
		'check_sc',
		'check_se',
		'check_sg',
		'check_si',
		'check_sk',
		'check_sv',
		'check_sx',
		'check_sy',
		'check_th',
		'check_tj',
		'check_tm',
		'check_tr',
		'check_tt',
		'check_tw',
		'check_ua',
		'check_uk',
		'check_us',
		'check_uy',
		'check_uz',
		'check_vc',
		'check_ve',
		'check_vn',
		'check_ye'
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
				<small><?php esc_html_e( 'WooCommerce Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
	 	<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_gravity_form">
				<input class="ds_toggle" type="checkbox" id="check_gravity_form" name="check_gravity_form" value="Y" <?php if ( $check_gravity_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Gravity Forms', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="check_wp_form">
				<input class="ds_toggle" type="checkbox" id="check_wp_form" name="check_wp_form" value="Y" <?php if ( $check_wp_form == 'Y' ) { echo 'checked="checked"'; } ?> <?php if ( $check_form == 'Y' ) { echo 'disabled'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'WP Forms', 'dam-spam' ); ?></small>
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
		<br>
		<br>
		<div id="blockcountries" class="mainsection"><?php esc_html_e( 'Block Countries', 'dam-spam' ); ?></div>
		<br>
		<div class="checkbox switcher">
			<label class="ds-subhead" for="countries">
				<input class="ds_toggle" type="checkbox" id="countries" name="ds_set" value="1" onclick='var t=ss.ds_set.checked;var els=document.getElementsByTagName("INPUT");for (index = 0; index < els.length; ++index){if (els[index].type=="checkbox"){if (els[index].name.match(/^check_[a-z]{2}$/)){els[index].checked=t;}}}'>
				<small><span class="button-primary"><?php esc_html_e( 'Check All', 'dam-spam' ); ?></span></small>
			</label>
		</div>
		<br>
		<div class="stat-box">
			<input name="check_ad" type="checkbox" value="Y" <?php if ( $check_ad == "Y" ) { echo 'checked="checked"'; } ?>>Andorra
		</div>
		<div class="stat-box">
			<input name="check_ae" type="checkbox" value="Y" <?php if ( $check_ae == "Y" ) { echo 'checked="checked"'; } ?>>United Arab Emirates
		</div>
		<div class="stat-box">
			<input name="check_af" type="checkbox" value="Y" <?php if ( $check_af == "Y" ) { echo 'checked="checked"'; } ?>>Afghanistan
		</div>
		<div class="stat-box">
			<input name="check_al" type="checkbox" value="Y" <?php if ( $check_al == "Y" ) { echo 'checked="checked"'; } ?>>Albania
		</div>
		<div class="stat-box">
			<input name="check_am" type="checkbox" value="Y" <?php if ( $check_am == "Y" ) { echo 'checked="checked"'; } ?>>Armenia
		</div>
		<div class="stat-box">
			<input name="check_ar" type="checkbox" value="Y" <?php if ( $check_ar == "Y" ) { echo 'checked="checked"'; } ?>>Argentina
		</div>
		<div class="stat-box">
			<input name="check_at" type="checkbox" value="Y" <?php if ( $check_at == "Y" ) { echo 'checked="checked"'; } ?>>Austria
		</div>
		<div class="stat-box">
			<input name="check_au" type="checkbox" value="Y" <?php if ( $check_au == "Y" ) { echo 'checked="checked"'; } ?>>Australia
		</div>
		<div class="stat-box">
			<input name="check_ax" type="checkbox" value="Y" <?php if ( $check_ax == "Y" ) { echo 'checked="checked"'; } ?>>Aland Islands
		</div>
		<div class="stat-box">
			<input name="check_az" type="checkbox" value="Y" <?php if ( $check_az == "Y" ) { echo 'checked="checked"'; } ?>>Azerbaijan
		</div>
		<div class="stat-box">
			<input name="check_ba" type="checkbox" value="Y" <?php if ( $check_ba == "Y" ) { echo 'checked="checked"'; } ?>>Bosnia And Herzegovina
		</div>
		<div class="stat-box">
			<input name="check_bb" type="checkbox" value="Y" <?php if ( $check_bb == "Y" ) { echo 'checked="checked"'; } ?>>Barbados
		</div>
		<div class="stat-box">
			<input name="check_bd" type="checkbox" value="Y" <?php if ( $check_bd == "Y" ) { echo 'checked="checked"'; } ?>>Bangladesh
		</div>
		<div class="stat-box">
			<input name="check_be" type="checkbox" value="Y" <?php if ( $check_be == "Y" ) { echo 'checked="checked"'; } ?>>Belgium
		</div>
		<div class="stat-box">
			<input name="check_bg" type="checkbox" value="Y" <?php if ( $check_bg == "Y" ) { echo 'checked="checked"'; } ?>>Bulgaria
		</div>
		<div class="stat-box">
			<input name="check_bh" type="checkbox" value="Y" <?php if ( $check_bh == "Y" ) { echo 'checked="checked"'; } ?>>Bahrain
		</div>
		<div class="stat-box">
			<input name="check_bn" type="checkbox" value="Y" <?php if ( $check_bn == "Y" ) { echo 'checked="checked"'; } ?>>Brunei Darussalam
		</div>
		<div class="stat-box">
			<input name="check_bo" type="checkbox" value="Y" <?php if ( $check_bo == "Y" ) { echo 'checked="checked"'; } ?>>Bolivia
		</div>
		<div class="stat-box">
			<input name="check_br" type="checkbox" value="Y" <?php if ( $check_br == "Y" ) { echo 'checked="checked"'; } ?>>Brazil
		</div>
		<div class="stat-box">
			<input name="check_bs" type="checkbox" value="Y" <?php if ( $check_bs == "Y" ) { echo 'checked="checked"'; } ?>>Bahamas
		</div>
		<div class="stat-box">
			<input name="check_by" type="checkbox" value="Y" <?php if ( $check_by == "Y" ) { echo 'checked="checked"'; } ?>>Belarus
		</div>
		<div class="stat-box">
			<input name="check_bz" type="checkbox" value="Y" <?php if ( $check_bz == "Y" ) { echo 'checked="checked"'; } ?>>Belize
		</div>
		<div class="stat-box">
			<input name="check_ca" type="checkbox" value="Y" <?php if ( $check_ca == "Y" ) { echo 'checked="checked"'; } ?>>Canada
		</div>
		<div class="stat-box">
			<input name="check_cd" type="checkbox" value="Y" <?php if ( $check_cd == "Y" ) { echo 'checked="checked"'; } ?>>Congo, Democratic Republic
		</div>
		<div class="stat-box">
			<input name="check_ch" type="checkbox" value="Y" <?php if ( $check_ch == "Y" ) { echo 'checked="checked"'; } ?>>Switzerland
		</div>
		<div class="stat-box">
			<input name="check_cl" type="checkbox" value="Y" <?php if ( $check_cl == "Y" ) { echo 'checked="checked"'; } ?>>Chile
		</div>
		<div class="stat-box">
			<input name="check_cn" type="checkbox" value="Y" <?php if ( $check_cn == "Y" ) { echo 'checked="checked"'; } ?>>China
		</div>
		<div class="stat-box">
			<input name="check_co" type="checkbox" value="Y" <?php if ( $check_co == "Y" ) { echo 'checked="checked"'; } ?>>Colombia
		</div>
		<div class="stat-box">
			<input name="check_cr" type="checkbox" value="Y" <?php if ( $check_cr == "Y" ) { echo 'checked="checked"'; } ?>>Costa Rica
		</div>
		<div class="stat-box">
			<input name="check_cu" type="checkbox" value="Y" <?php if ( $check_cu == "Y" ) { echo 'checked="checked"'; } ?>>Cuba
		</div>
		<div class="stat-box">
			<input name="check_cw" type="checkbox" value="Y" <?php if ( $check_cw == "Y" ) { echo 'checked="checked"'; } ?>>CuraÃ§ao
		</div>
		<div class="stat-box">
			<input name="check_cy" type="checkbox" value="Y" <?php if ( $check_cy == "Y" ) { echo 'checked="checked"'; } ?>>Cyprus
		</div>
		<div class="stat-box">
			<input name="check_cz" type="checkbox" value="Y" <?php if ( $check_cz == "Y" ) { echo 'checked="checked"'; } ?>>Czech Republic
		</div>
		<div class="stat-box">
			<input name="check_de" type="checkbox" value="Y" <?php if ( $check_de == "Y" ) { echo 'checked="checked"'; } ?>>Germany
		</div>
		<div class="stat-box">
			<input name="check_dk" type="checkbox" value="Y" <?php if ( $check_dk == "Y" ) { echo 'checked="checked"'; } ?>>Denmark
		</div>
		<div class="stat-box">
			<input name="check_do" type="checkbox" value="Y" <?php if ( $check_do == "Y" ) { echo 'checked="checked"'; } ?>>Dominican Republic
		</div>
		<div class="stat-box">
			<input name="check_dz" type="checkbox" value="Y" <?php if ( $check_dz == "Y" ) { echo 'checked="checked"'; } ?>>Algeria
		</div>
		<div class="stat-box">
			<input name="check_ec" type="checkbox" value="Y" <?php if ( $check_ec == "Y" ) { echo 'checked="checked"'; } ?>>Ecuador
		</div>
		<div class="stat-box">
			<input name="check_ee" type="checkbox" value="Y" <?php if ( $check_ee == "Y" ) { echo 'checked="checked"'; } ?>>Estonia
		</div>
		<div class="stat-box">
			<input name="check_es" type="checkbox" value="Y" <?php if ( $check_es == "Y" ) { echo 'checked="checked"'; } ?>>Spain
		</div>
		<div class="stat-box">
			<input name="check_eu" type="checkbox" value="Y" <?php if ( $check_eu == "Y" ) { echo 'checked="checked"'; } ?>>European Union
		</div>
		<div class="stat-box">
			<input name="check_fi" type="checkbox" value="Y" <?php if ( $check_fi == "Y" ) { echo 'checked="checked"'; } ?>>Finland
		</div>
		<div class="stat-box">
			<input name="check_fj" type="checkbox" value="Y" <?php if ( $check_fj == "Y" ) { echo 'checked="checked"'; } ?>>Fiji
		</div>
		<div class="stat-box">
			<input name="check_fr" type="checkbox" value="Y" <?php if ( $check_fr == "Y" ) { echo 'checked="checked"'; } ?>>France
		</div>
		<div class="stat-box">
			<input name="check_gb" type="checkbox" value="Y" <?php if ( $check_gb == "Y" ) { echo 'checked="checked"'; } ?>>Great Britain
		</div>
		<div class="stat-box">
			<input name="check_ge" type="checkbox" value="Y" <?php if ( $check_ge == "Y" ) { echo 'checked="checked"'; } ?>>Georgia
		</div>
		<div class="stat-box">
			<input name="check_gf" type="checkbox" value="Y" <?php if ( $check_gf == "Y" ) { echo 'checked="checked"'; } ?>>French Guiana
		</div>
		<div class="stat-box">
			<input name="check_gi" type="checkbox" value="Y" <?php if ( $check_gi == "Y" ) { echo 'checked="checked"'; } ?>>Gibraltar
		</div>
		<div class="stat-box">
			<input name="check_gp" type="checkbox" value="Y" <?php if ( $check_gp == "Y" ) { echo 'checked="checked"'; } ?>>Guadeloupe
		</div>
		<div class="stat-box">
			<input name="check_gr" type="checkbox" value="Y" <?php if ( $check_gr == "Y" ) { echo 'checked="checked"'; } ?>>Greece
		</div>
		<div class="stat-box">
			<input name="check_gt" type="checkbox" value="Y" <?php if ( $check_gt == "Y" ) { echo 'checked="checked"'; } ?>>Guatemala
		</div>
		<div class="stat-box">
			<input name="check_gu" type="checkbox" value="Y" <?php if ( $check_gu == "Y" ) { echo 'checked="checked"'; } ?>>Guam
		</div>
		<div class="stat-box">
			<input name="check_gy" type="checkbox" value="Y" <?php if ( $check_gy == "Y" ) { echo 'checked="checked"'; } ?>>Guyana
		</div>
		<div class="stat-box">
			<input name="check_hk" type="checkbox" value="Y" <?php if ( $check_hk == "Y" ) { echo 'checked="checked"'; } ?>>Hong Kong
		</div>
		<div class="stat-box">
			<input name="check_hn" type="checkbox" value="Y" <?php if ( $check_hn == "Y" ) { echo 'checked="checked"'; } ?>>Honduras
		</div>
		<div class="stat-box">
			<input name="check_hr" type="checkbox" value="Y" <?php if ( $check_hr == "Y" ) { echo 'checked="checked"'; } ?>>Croatia
		</div>
		<div class="stat-box">
			<input name="check_ht" type="checkbox" value="Y" <?php if ( $check_ht == "Y" ) { echo 'checked="checked"'; } ?>>Haiti
		</div>
		<div class="stat-box">
			<input name="check_hu" type="checkbox" value="Y" <?php if ( $check_hu == "Y" ) { echo 'checked="checked"'; } ?>>Hungary
		</div>
		<div class="stat-box">
			<input name="check_id" type="checkbox" value="Y" <?php if ( $check_id == "Y" ) { echo 'checked="checked"'; } ?>>Indonesia
		</div>
		<div class="stat-box">
			<input name="check_ie" type="checkbox" value="Y" <?php if ( $check_ie == "Y" ) { echo 'checked="checked"'; } ?>>Ireland
		</div>
		<div class="stat-box">
			<input name="check_il" type="checkbox" value="Y" <?php if ( $check_il == "Y" ) { echo 'checked="checked"'; } ?>>Israel
		</div>
		<div class="stat-box">
			<input name="check_in" type="checkbox" value="Y" <?php if ( $check_in == "Y" ) { echo 'checked="checked"'; } ?>>India
		</div>
		<div class="stat-box">
			<input name="check_iq" type="checkbox" value="Y" <?php if ( $check_iq == "Y" ) { echo 'checked="checked"'; } ?>>Iraq
		</div>
		<div class="stat-box">
			<input name="check_ir" type="checkbox" value="Y" <?php if ( $check_ir == "Y" ) { echo 'checked="checked"'; } ?>>Iran, Islamic Republic Of
		</div>
		<div class="stat-box">
			<input name="check_is" type="checkbox" value="Y" <?php if ( $check_is == "Y" ) { echo 'checked="checked"'; } ?>>Iceland
		</div>
		<div class="stat-box">
			<input name="check_it" type="checkbox" value="Y" <?php if ( $check_it == "Y" ) { echo 'checked="checked"'; } ?>>Italy
		</div>
		<div class="stat-box">
			<input name="check_jm" type="checkbox" value="Y" <?php if ( $check_jm == "Y" ) { echo 'checked="checked"'; } ?>>Jamaica
		</div>
		<div class="stat-box">
			<input name="check_jo" type="checkbox" value="Y" <?php if ( $check_jo == "Y" ) { echo 'checked="checked"'; } ?>>Jordan
		</div>
		<div class="stat-box">
			<input name="check_jp" type="checkbox" value="Y" <?php if ( $check_jp == "Y" ) { echo 'checked="checked"'; } ?>>Japan
		</div>
		<div class="stat-box">
			<input name="check_ke" type="checkbox" value="Y" <?php if ( $check_ke == "Y" ) { echo 'checked="checked"'; } ?>>Kenya
		</div>
		<div class="stat-box">
			<input name="check_kg" type="checkbox" value="Y" <?php if ( $check_kg == "Y" ) { echo 'checked="checked"'; } ?>>Kyrgyzstan
		</div>
		<div class="stat-box">
			<input name="check_kh" type="checkbox" value="Y" <?php if ( $check_kh == "Y" ) { echo 'checked="checked"'; } ?>>Cambodia
		</div>
		<div class="stat-box">
			<input name="check_kr" type="checkbox" value="Y" <?php if ( $check_kr == "Y" ) { echo 'checked="checked"'; } ?>>Korea
		</div>
		<div class="stat-box">
			<input name="check_kw" type="checkbox" value="Y" <?php if ( $check_kw == "Y" ) { echo 'checked="checked"'; } ?>>Kuwait
		</div>
		<div class="stat-box">
			<input name="check_ky" type="checkbox" value="Y" <?php if ( $check_ky == "Y" ) { echo 'checked="checked"'; } ?>>Cayman Islands
		</div>
		<div class="stat-box">
			<input name="check_kz" type="checkbox" value="Y" <?php if ( $check_kz == "Y" ) { echo 'checked="checked"'; } ?>>Kazakhstan
		</div>
		<div class="stat-box">
			<input name="check_la" type="checkbox" value="Y" <?php if ( $check_la == "Y" ) { echo 'checked="checked"'; } ?>>Lao People's Democratic Republic
		</div>
		<div class="stat-box">
			<input name="check_lb" type="checkbox" value="Y" <?php if ( $check_lb == "Y" ) { echo 'checked="checked"'; } ?>>Lebanon
		</div>
		<div class="stat-box">
			<input name="check_lk" type="checkbox" value="Y" <?php if ( $check_lk == "Y" ) { echo 'checked="checked"'; } ?>>Sri Lanka
		</div>
		<div class="stat-box">
			<input name="check_lt" type="checkbox" value="Y" <?php if ( $check_lt == "Y" ) { echo 'checked="checked"'; } ?>>Lithuania
		</div>
		<div class="stat-box">
			<input name="check_lu" type="checkbox" value="Y" <?php if ( $check_lu == "Y" ) { echo 'checked="checked"'; } ?>>Luxembourg
		</div>
		<div class="stat-box">
			<input name="check_lv" type="checkbox" value="Y" <?php if ( $check_lv == "Y" ) { echo 'checked="checked"'; } ?>>Latvia
		</div>
		<div class="stat-box">
			<input name="check_md" type="checkbox" value="Y" <?php if ( $check_md == "Y" ) { echo 'checked="checked"'; } ?>>Moldova
		</div>
		<div class="stat-box">
			<input name="check_me" type="checkbox" value="Y" <?php if ( $check_me == "Y" ) { echo 'checked="checked"'; } ?>>Montenegro
		</div>
		<div class="stat-box">
			<input name="check_mk" type="checkbox" value="Y" <?php if ( $check_mk == "Y" ) { echo 'checked="checked"'; } ?>>Macedonia
		</div>
		<div class="stat-box">
			<input name="check_mm" type="checkbox" value="Y" <?php if ( $check_mm == "Y" ) { echo 'checked="checked"'; } ?>>Myanmar
		</div>
		<div class="stat-box">
			<input name="check_mn" type="checkbox" value="Y" <?php if ( $check_mn == "Y" ) { echo 'checked="checked"'; } ?>>Mongolia
		</div>
		<div class="stat-box">
			<input name="check_mo" type="checkbox" value="Y" <?php if ( $check_mo == "Y" ) { echo 'checked="checked"'; } ?>>Macao
		</div>
		<div class="stat-box">
			<input name="check_mp" type="checkbox" value="Y" <?php if ( $check_mp == "Y" ) { echo 'checked="checked"'; } ?>>Northern Mariana Islands
		</div>
		<div class="stat-box">
			<input name="check_mq" type="checkbox" value="Y" <?php if ( $check_mq == "Y" ) { echo 'checked="checked"'; } ?>>Martinique
		</div>
		<div class="stat-box">
			<input name="check_mt" type="checkbox" value="Y" <?php if ( $check_mt == "Y" ) { echo 'checked="checked"'; } ?>>Malta
		</div>
		<div class="stat-box">
			<input name="check_mv" type="checkbox" value="Y" <?php if ( $check_mv == "Y" ) { echo 'checked="checked"'; } ?>>Maldives
		</div>
		<div class="stat-box">
			<input name="check_mx" type="checkbox" value="Y" <?php if ( $check_mx == "Y" ) { echo 'checked="checked"'; } ?>>Mexico
		</div>
		<div class="stat-box">
			<input name="check_my" type="checkbox" value="Y" <?php if ( $check_my == "Y" ) { echo 'checked="checked"'; } ?>>Malaysia
		</div>
		<div class="stat-box">
			<input name="check_nc" type="checkbox" value="Y" <?php if ( $check_nc == "Y" ) { echo 'checked="checked"'; } ?>>New Caledonia
		</div>
		<div class="stat-box">
			<input name="check_ni" type="checkbox" value="Y" <?php if ( $check_ni == "Y" ) { echo 'checked="checked"'; } ?>>Nicaragua
		</div>
		<div class="stat-box">
			<input name="check_nl" type="checkbox" value="Y" <?php if ( $check_nl == "Y" ) { echo 'checked="checked"'; } ?>>Netherlands
		</div>
		<div class="stat-box">
			<input name="check_no" type="checkbox" value="Y" <?php if ( $check_no == "Y" ) { echo 'checked="checked"'; } ?>>Norway
		</div>
		<div class="stat-box">
			<input name="check_np" type="checkbox" value="Y" <?php if ( $check_np == "Y" ) { echo 'checked="checked"'; } ?>>Nepal
		</div>
		<div class="stat-box">
			<input name="check_nz" type="checkbox" value="Y" <?php if ( $check_nz == "Y" ) { echo 'checked="checked"'; } ?>>New Zealand
		</div>
		<div class="stat-box">
			<input name="check_om" type="checkbox" value="Y" <?php if ( $check_om == "Y" ) { echo 'checked="checked"'; } ?>>Oman
		</div>
		<div class="stat-box">
			<input name="check_pa" type="checkbox" value="Y" <?php if ( $check_pa == "Y" ) { echo 'checked="checked"'; } ?>>Panama
		</div>
		<div class="stat-box">
			<input name="check_pe" type="checkbox" value="Y" <?php if ( $check_pe == "Y" ) { echo 'checked="checked"'; } ?>>Peru
		</div>
		<div class="stat-box">
			<input name="check_pg" type="checkbox" value="Y" <?php if ( $check_pg == "Y" ) { echo 'checked="checked"'; } ?>>Papua New Guinea
		</div>
		<div class="stat-box">
			<input name="check_ph" type="checkbox" value="Y" <?php if ( $check_ph == "Y" ) { echo 'checked="checked"'; } ?>>Philippines
		</div>
		<div class="stat-box">
			<input name="check_pk" type="checkbox" value="Y" <?php if ( $check_pk == "Y" ) { echo 'checked="checked"'; } ?>>Pakistan
		</div>
		<div class="stat-box">
			<input name="check_pl" type="checkbox" value="Y" <?php if ( $check_pl == "Y" ) { echo 'checked="checked"'; } ?>>Poland
		</div>
		<div class="stat-box">
			<input name="check_pr" type="checkbox" value="Y" <?php if ( $check_pr == "Y" ) { echo 'checked="checked"'; } ?>>Puerto Rico
		</div>
		<div class="stat-box">
			<input name="check_ps" type="checkbox" value="Y" <?php if ( $check_ps == "Y" ) { echo 'checked="checked"'; } ?>>Palestinian Territory, Occupied
		</div>
		<div class="stat-box">
			<input name="check_pt" type="checkbox" value="Y" <?php if ( $check_pt == "Y" ) { echo 'checked="checked"'; } ?>>Portugal
		</div>
		<div class="stat-box">
			<input name="check_pw" type="checkbox" value="Y" <?php if ( $check_pw == "Y" ) { echo 'checked="checked"'; } ?>>Palau
		</div>
		<div class="stat-box">
			<input name="check_py" type="checkbox" value="Y" <?php if ( $check_py == "Y" ) { echo 'checked="checked"'; } ?>>Paraguay
		</div>
		<div class="stat-box">
			<input name="check_qa" type="checkbox" value="Y" <?php if ( $check_qa == "Y" ) { echo 'checked="checked"'; } ?>>Qatar
		</div>
		<div class="stat-box">
			<input name="check_ro" type="checkbox" value="Y" <?php if ( $check_ro == "Y" ) { echo 'checked="checked"'; } ?>>Romania
		</div>
		<div class="stat-box">
			<input name="check_rs" type="checkbox" value="Y" <?php if ( $check_rs == "Y" ) { echo 'checked="checked"'; } ?>>Serbia
		</div>
		<div class="stat-box">
			<input name="check_ru" type="checkbox" value="Y" <?php if ( $check_ru == "Y" ) { echo 'checked="checked"'; } ?>>Russian Federation
		</div>
		<div class="stat-box">
			<input name="check_sa" type="checkbox" value="Y" <?php if ( $check_sa == "Y" ) { echo 'checked="checked"'; } ?>>Saudi Arabia
		</div>
		<div class="stat-box">
			<input name="check_sc" type="checkbox" value="Y" <?php if ( $check_sc == "Y" ) { echo 'checked="checked"'; } ?>>Seychelles
		</div>
		<div class="stat-box">
			<input name="check_se" type="checkbox" value="Y" <?php if ( $check_se == "Y" ) { echo 'checked="checked"'; } ?>>Sweden
		</div>
		<div class="stat-box">
			<input name="check_sg" type="checkbox" value="Y" <?php if ( $check_sg == "Y" ) { echo 'checked="checked"'; } ?>>Singapore
		</div>
		<div class="stat-box">
			<input name="check_si" type="checkbox" value="Y" <?php if ( $check_si == "Y" ) { echo 'checked="checked"'; } ?>>Slovenia
		</div>
		<div class="stat-box">
			<input name="check_sk" type="checkbox" value="Y" <?php if ( $check_sk == "Y" ) { echo 'checked="checked"'; } ?>>Slovakia
		</div>
		<div class="stat-box">
			<input name="check_sv" type="checkbox" value="Y" <?php if ( $check_sv == "Y" ) { echo 'checked="checked"'; } ?>>El Salvador
		</div>
		<div class="stat-box">
			<input name="check_sx" type="checkbox" value="Y" <?php if ( $check_sx == "Y" ) { echo 'checked="checked"'; } ?>>Sint Maarten
		</div>
		<div class="stat-box">
			<input name="check_sy" type="checkbox" value="Y" <?php if ( $check_sy == "Y" ) { echo 'checked="checked"'; } ?>>Syrian Arab Republic
		</div>
		<div class="stat-box">
			<input name="check_th" type="checkbox" value="Y" <?php if ( $check_th == "Y" ) { echo 'checked="checked"'; } ?>>Thailand
		</div>
		<div class="stat-box">
			<input name="check_tj" type="checkbox" value="Y" <?php if ( $check_tj == "Y" ) { echo 'checked="checked"'; } ?>>Tajikistan
		</div>
		<div class="stat-box">
			<input name="check_tm" type="checkbox" value="Y" <?php if ( $check_tm == "Y" ) { echo 'checked="checked"'; } ?>>Turkmenistan
		</div>
		<div class="stat-box">
			<input name="check_tr" type="checkbox" value="Y" <?php if ( $check_tr == "Y" ) { echo 'checked="checked"'; } ?>>Turkey
		</div>
		<div class="stat-box">
			<input name="check_tt" type="checkbox" value="Y" <?php if ( $check_tt == "Y" ) { echo 'checked="checked"'; } ?>>Trinidad And Tobago
		</div>
		<div class="stat-box">
			<input name="check_tw" type="checkbox" value="Y" <?php if ( $check_tw == "Y" ) { echo 'checked="checked"'; } ?>>Taiwan
		</div>
		<div class="stat-box">
			<input name="check_ua" type="checkbox" value="Y" <?php if ( $check_ua == "Y" ) { echo 'checked="checked"'; } ?>>Ukraine
		</div>
		<div class="stat-box">
			<input name="check_uk" type="checkbox" value="Y" <?php if ( $check_uk == "Y" ) { echo 'checked="checked"'; } ?>>United Kingdom
		</div>
		<div class="stat-box">
			<input name="check_us" type="checkbox" value="Y" <?php if ( $check_us == "Y" ) { echo 'checked="checked"'; } ?>>United States
		</div>
		<div class="stat-box">
			<input name="check_uy" type="checkbox" value="Y" <?php if ( $check_uy == "Y" ) { echo 'checked="checked"'; } ?>>Uruguay
		</div>
		<div class="stat-box">
			<input name="check_uz" type="checkbox" value="Y" <?php if ( $check_uz == "Y" ) { echo 'checked="checked"'; } ?>>Uzbekistan
		</div>
		<div class="stat-box">
			<input name="check_vc" type="checkbox" value="Y" <?php if ( $check_vc == "Y" ) { echo 'checked="checked"'; } ?>>Saint Vincent And Grenadines
		</div>
		<div class="stat-box">
			<input name="check_ve" type="checkbox" value="Y" <?php if ( $check_ve == "Y" ) { echo 'checked="checked"'; } ?>>Venezuela
		</div>
		<div class="stat-box">
			<input name="check_vn" type="checkbox" value="Y" <?php if ( $check_vn == "Y" ) { echo 'checked="checked"'; } ?>>Viet Nam
		</div>
		<div class="stat-box">
			<input name="check_ye" type="checkbox" value="Y" <?php if ( $check_ye == "Y" ) { echo 'checked="checked"'; } ?>>Yemen
		</div>
		<br style="clear:both">
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>
