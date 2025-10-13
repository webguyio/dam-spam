<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
	esc_html_e( 'Because of a conflict with Jetpack Protect, Dam Spam has been deactivated. To reactivate, you do not need to disable Jetpack, just its Protect feature.', 'dam-spam' );
	return;
}

ds_fix_post_vars();
$stats = ds_get_stats();
extract( $stats );
$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

$counters = array(
	'count_check_cloudflare'         => esc_html__( 'Pass Cloudflare', 'dam-spam' ),
	'count_check_good_cache'         => esc_html__( 'Pass Good Cache', 'dam-spam' ),
	'count_check_akismet'            => esc_html__( 'Reported by Akismet', 'dam-spam' ),
	'count_check_general_allow_list' => esc_html__( 'Pass Generated Allow List', 'dam-spam' ),
	'count_check_google'             => esc_html__( 'Pass Google', 'dam-spam' ),
	'count_check_misc_allow_list'    => esc_html__( 'Pass Allow List', 'dam-spam' ),
	'count_check_paypal'             => esc_html__( 'Pass PayPal', 'dam-spam' ),
	'count_check_scripts'            => esc_html__( 'Pass Scripts', 'dam-spam' ),
	'count_check_valid_ip'           => esc_html__( 'Pass Uncheckable IP', 'dam-spam' ),
	'count_check_allowed_email'      => esc_html__( 'Allow List Email', 'dam-spam' ),
	'count_check_blocked_user_id'    => esc_html__( 'Allow Username', 'dam-spam' ),
	'count_check_allow_list'         => esc_html__( 'Pass Allow List IP', 'dam-spam' ),
	'count_check_yahoo_merchant'     => esc_html__( 'Pass Yahoo Merchant', 'dam-spam' ),
	'count_check_404'                => esc_html__( '404 Exploit Attempt', 'dam-spam' ),
	'count_check_accept'             => esc_html__( 'Bad or Missing Accept Header', 'dam-spam' ),
	'count_check_admin'              => esc_html__( 'Admin Login Attempt', 'dam-spam' ),
	'count_check_admin_log'          => esc_html__( 'Passed Login OK', 'dam-spam' ),
	'count_check_agent'              => esc_html__( 'Bad or Missing User Agent', 'dam-spam' ),
	'count_check_amazon'             => esc_html__( 'Amazon AWS', 'dam-spam' ),
	'count_check_aws'                => esc_html__( 'Amazon AWS Allow', 'dam-spam' ),
	'count_check_bad_cache'          => esc_html__( 'Bad Cache', 'dam-spam' ),
	'count_check_blocked_email'      => esc_html__( 'Block List Email', 'dam-spam' ),
	'count_check_blocked_user_id'    => esc_html__( 'Block Username', 'dam-spam' ),
	'count_check_blocked_ip'         => esc_html__( 'Block List IP', 'dam-spam' ),
	'count_check_botscout'           => esc_html__( 'BotScout', 'dam-spam' ),
	'count_check_disposable'         => esc_html__( 'Disposable Email', 'dam-spam' ),
	'count_check_dnsbl'              => esc_html__( 'DNSBL Hit', 'dam-spam' ),
	'count_check_exploits'           => esc_html__( 'Exploit Attempt', 'dam-spam' ),
	'count_check_google_safe'        => esc_html__( 'Google Safe Browsing', 'dam-spam' ),
	'count_check_honeypot'           => esc_html__( 'Project Honeypot', 'dam-spam' ),
	'count_check_hosting'            => esc_html__( 'Known Spam Host', 'dam-spam' ),
	'count_check_invalid_ip'         => esc_html__( 'Block Invalid IP', 'dam-spam' ),
	'count_check_long'               => esc_html__( 'Long Email', 'dam-spam' ),
	'count_check_short'              => esc_html__( 'Short Email', 'dam-spam' ),
	'count_check_bbcode'             => esc_html__( 'BBCode in Request', 'dam-spam' ),
	'count_check_referer'            => esc_html__( 'Bad HTTP_REFERER', 'dam-spam' ),
	'count_check_session'            => esc_html__( 'Session Speed', 'dam-spam' ),
	'count_check_sfs'                => esc_html__( 'Stop Forum Spam', 'dam-spam' ),
	'count_check_spam_words'         => esc_html__( 'Spam Words', 'dam-spam' ),
	'count_check_url_short'          => esc_html__( 'Short URLs', 'dam-spam' ),
	'count_check_tld'                => esc_html__( 'Email TLD', 'dam-spam' ),
	'count_check_ubiquity'           => esc_html__( 'Ubiquity Servers', 'dam-spam' ),
	'count_check_multi'              => esc_html__( 'Repeated Hits', 'dam-spam' ),
	'count_check_form'               => esc_html__( 'Check for Standard Form', 'dam-spam' ),
	'count_check_ad'                 => esc_html__( 'Andorra', 'dam-spam' ),
	'count_check_ae'                 => esc_html__( 'United Arab Emirates', 'dam-spam' ),
	'count_check_af'                 => esc_html__( 'Afghanistan', 'dam-spam' ),
	'count_check_al'                 => esc_html__( 'Albania', 'dam-spam' ),
	'count_check_am'                 => esc_html__( 'Armenia', 'dam-spam' ),
	'count_check_ar'                 => esc_html__( 'Argentina', 'dam-spam' ),
	'count_check_at'                 => esc_html__( 'Austria', 'dam-spam' ),
	'count_check_au'                 => esc_html__( 'Australia', 'dam-spam' ),
	'count_check_ax'                 => esc_html__( 'Aland Islands', 'dam-spam' ),
	'count_check_az'                 => esc_html__( 'Azerbaijan', 'dam-spam' ),
	'count_check_ba'                 => esc_html__( 'Bosnia And Herzegovina', 'dam-spam' ),
	'count_check_bb'                 => esc_html__( 'Barbados', 'dam-spam' ),
	'count_check_bd'                 => esc_html__( 'Bangladesh', 'dam-spam' ),
	'count_check_be'                 => esc_html__( 'Belgium', 'dam-spam' ),
	'count_check_bg'                 => esc_html__( 'Bulgaria', 'dam-spam' ),
	'count_check_bh'                 => esc_html__( 'Bahrain', 'dam-spam' ),
	'count_check_bn'                 => esc_html__( 'Brunei Darussalam', 'dam-spam' ),
	'count_check_bo'                 => esc_html__( 'Bolivia', 'dam-spam' ),
	'count_check_br'                 => esc_html__( 'Brazil', 'dam-spam' ),
	'count_check_bs'                 => esc_html__( 'Bahamas', 'dam-spam' ),
	'count_check_by'                 => esc_html__( 'Belarus', 'dam-spam' ),
	'count_check_bz'                 => esc_html__( 'Belize', 'dam-spam' ),
	'count_check_ca'                 => esc_html__( 'Canada', 'dam-spam' ),
	'count_check_cd'                 => esc_html__( 'Congo, Democratic Republic', 'dam-spam' ),
	'count_check_ch'                 => esc_html__( 'Switzerland', 'dam-spam' ),
	'count_check_cl'                 => esc_html__( 'Chile', 'dam-spam' ),
	'count_check_cn'                 => esc_html__( 'China', 'dam-spam' ),
	'count_check_co'                 => esc_html__( 'Colombia', 'dam-spam' ),
	'count_check_cr'                 => esc_html__( 'Costa Rica', 'dam-spam' ),
	'count_check_cu'                 => esc_html__( 'Cuba', 'dam-spam' ),
	'count_check_cw'                 => esc_html__( 'Curaçao', 'dam-spam' ),
	'count_check_cy'                 => esc_html__( 'Cyprus', 'dam-spam' ),
	'count_check_cz'                 => esc_html__( 'Czech Republic', 'dam-spam' ),
	'count_check_de'                 => esc_html__( 'Germany', 'dam-spam' ),
	'count_check_dk'                 => esc_html__( 'Denmark', 'dam-spam' ),
	'count_check_do'                 => esc_html__( 'Dominican Republic', 'dam-spam' ),
	'count_check_dz'                 => esc_html__( 'Algeria', 'dam-spam' ),
	'count_check_ec'                 => esc_html__( 'Ecuador', 'dam-spam' ),
	'count_check_ee'                 => esc_html__( 'Estonia', 'dam-spam' ),
	'count_check_es'                 => esc_html__( 'Spain', 'dam-spam' ),
	'count_check_eu'                 => esc_html__( 'European Union', 'dam-spam' ),
	'count_check_fi'                 => esc_html__( 'Finland', 'dam-spam' ),
	'count_check_fj'                 => esc_html__( 'Fiji', 'dam-spam' ),
	'count_check_fr'                 => esc_html__( 'France', 'dam-spam' ),
	'count_check_gb'                 => esc_html__( 'Great Britain', 'dam-spam' ),
	'count_check_ge'                 => esc_html__( 'Georgia', 'dam-spam' ),
	'count_check_gf'                 => esc_html__( 'French Guiana', 'dam-spam' ),
	'count_check_gi'                 => esc_html__( 'Gibraltar', 'dam-spam' ),
	'count_check_gp'                 => esc_html__( 'Guadeloupe', 'dam-spam' ),
	'count_check_gr'                 => esc_html__( 'Greece', 'dam-spam' ),
	'count_check_gt'                 => esc_html__( 'Guatemala', 'dam-spam' ),
	'count_check_gu'                 => esc_html__( 'Guam', 'dam-spam' ),
	'count_check_gy'                 => esc_html__( 'Guyana', 'dam-spam' ),
	'count_check_hk'                 => esc_html__( 'Hong Kong', 'dam-spam' ),
	'count_check_hn'                 => esc_html__( 'Honduras', 'dam-spam' ),
	'count_check_hr'                 => esc_html__( 'Croatia', 'dam-spam' ),
	'count_check_ht'                 => esc_html__( 'Haiti', 'dam-spam' ),
	'count_check_hu'                 => esc_html__( 'Hungary', 'dam-spam' ),
	'count_check_id'                 => esc_html__( 'Indonesia', 'dam-spam' ),
	'count_check_ie'                 => esc_html__( 'Ireland', 'dam-spam' ),
	'count_check_il'                 => esc_html__( 'Israel', 'dam-spam' ),
	'count_check_in'                 => esc_html__( 'India', 'dam-spam' ),
	'count_check_iq'                 => esc_html__( 'Iraq', 'dam-spam' ),
	'count_check_ir'                 => esc_html__( 'Iran, Islamic Republic Of', 'dam-spam' ),
	'count_check_is'                 => esc_html__( 'Iceland', 'dam-spam' ),
	'count_check_it'                 => esc_html__( 'Italy', 'dam-spam' ),
	'count_check_jm'                 => esc_html__( 'Jamaica', 'dam-spam' ),
	'count_check_jo'                 => esc_html__( 'Jordan', 'dam-spam' ),
	'count_check_jp'                 => esc_html__( 'Japan', 'dam-spam' ),
	'count_check_ke'                 => esc_html__( 'Kenya', 'dam-spam' ),
	'count_check_kg'                 => esc_html__( 'Kyrgyzstan', 'dam-spam' ),
	'count_check_kh'                 => esc_html__( 'Cambodia', 'dam-spam' ),
	'count_check_kr'                 => esc_html__( 'Korea', 'dam-spam' ),
	'count_check_kw'                 => esc_html__( 'Kuwait', 'dam-spam' ),
	'count_check_ky'                 => esc_html__( 'Cayman Islands', 'dam-spam' ),
	'count_check_kz'                 => esc_html__( 'Kazakhstan', 'dam-spam' ),
	'count_check_la'                 => esc_html__( 'Lao People\'s Democratic Republic', 'dam-spam' ),
	'count_check_lb'                 => esc_html__( 'Lebanon', 'dam-spam' ),
	'count_check_lk'                 => esc_html__( 'Sri Lanka', 'dam-spam' ),
	'count_check_lt'                 => esc_html__( 'Lithuania', 'dam-spam' ),
	'count_check_lu'                 => esc_html__( 'Luxembourg', 'dam-spam' ),
	'count_check_lv'                 => esc_html__( 'Latvia', 'dam-spam' ),
	'count_check_md'                 => esc_html__( 'Moldova', 'dam-spam' ),
	'count_check_me'                 => esc_html__( 'Montenegro', 'dam-spam' ),
	'count_check_mk'                 => esc_html__( 'Macedonia', 'dam-spam' ),
	'count_check_mm'                 => esc_html__( 'Myanmar', 'dam-spam' ),
	'count_check_mn'                 => esc_html__( 'Mongolia', 'dam-spam' ),
	'count_check_mo'                 => esc_html__( 'Macao', 'dam-spam' ),
	'count_check_mp'                 => esc_html__( 'Northern Mariana Islands', 'dam-spam' ),
	'count_check_mq'                 => esc_html__( 'Martinique', 'dam-spam' ),
	'count_check_mt'                 => esc_html__( 'Malta', 'dam-spam' ),
	'count_check_mv'                 => esc_html__( 'Maldives', 'dam-spam' ),
	'count_check_mx'                 => esc_html__( 'Mexico', 'dam-spam' ),
	'count_check_my'                 => esc_html__( 'Malaysia', 'dam-spam' ),
	'count_check_nc'                 => esc_html__( 'New Caledonia', 'dam-spam' ),
	'count_check_ni'                 => esc_html__( 'Nicaragua', 'dam-spam' ),
	'count_check_nl'                 => esc_html__( 'Netherlands', 'dam-spam' ),
	'count_check_no'                 => esc_html__( 'Norway', 'dam-spam' ),
	'count_check_np'                 => esc_html__( 'Nepal', 'dam-spam' ),
	'count_check_nz'                 => esc_html__( 'New Zealand', 'dam-spam' ),
	'count_check_om'                 => esc_html__( 'Oman', 'dam-spam' ),
	'count_check_pa'                 => esc_html__( 'Panama', 'dam-spam' ),
	'count_check_pe'                 => esc_html__( 'Peru', 'dam-spam' ),
	'count_check_pg'                 => esc_html__( 'Papua New Guinea', 'dam-spam' ),
	'count_check_ph'                 => esc_html__( 'Philippines', 'dam-spam' ),
	'count_check_pk'                 => esc_html__( 'Pakistan', 'dam-spam' ),
	'count_check_pl'                 => esc_html__( 'Poland', 'dam-spam' ),
	'count_check_pr'                 => esc_html__( 'Puerto Rico', 'dam-spam' ),
	'count_check_ps'                 => esc_html__( 'Palestinian Territory, Occupied', 'dam-spam' ),
	'count_check_pt'                 => esc_html__( 'Portugal', 'dam-spam' ),
	'count_check_pw'                 => esc_html__( 'Palau', 'dam-spam' ),
	'count_check_py'                 => esc_html__( 'Paraguay', 'dam-spam' ),
	'count_check_qa'                 => esc_html__( 'Qatar', 'dam-spam' ),
	'count_check_ro'                 => esc_html__( 'Romania', 'dam-spam' ),
	'count_check_rs'                 => esc_html__( 'Serbia', 'dam-spam' ),
	'count_check_ru'                 => esc_html__( 'Russian Federation', 'dam-spam' ),
	'count_check_sa'                 => esc_html__( 'Saudi Arabia', 'dam-spam' ),
	'count_check_sc'                 => esc_html__( 'Seychelles', 'dam-spam' ),
	'count_check_se'                 => esc_html__( 'Sweden', 'dam-spam' ),
	'count_check_sg'                 => esc_html__( 'Singapore', 'dam-spam' ),
	'count_check_si'                 => esc_html__( 'Slovenia', 'dam-spam' ),
	'count_check_sk'                 => esc_html__( 'Slovakia', 'dam-spam' ),
	'count_check_sv'                 => esc_html__( 'El Salvador', 'dam-spam' ),
	'count_check_sx'                 => esc_html__( 'Sint Maarten', 'dam-spam' ),
	'count_check_sy'                 => esc_html__( 'Syrian Arab Republic', 'dam-spam' ),
	'count_check_th'                 => esc_html__( 'Thailand', 'dam-spam' ),
	'count_check_tj'                 => esc_html__( 'Tajikistan', 'dam-spam' ),
	'count_check_tm'                 => esc_html__( 'Turkmenistan', 'dam-spam' ),
	'count_check_tr'                 => esc_html__( 'Turkey', 'dam-spam' ),
	'count_check_tt'                 => esc_html__( 'Trinidad And Tobago', 'dam-spam' ),
	'count_check_tw'                 => esc_html__( 'Taiwan', 'dam-spam' ),
	'count_check_ua'                 => esc_html__( 'Ukraine', 'dam-spam' ),
	'count_check_uk'                 => esc_html__( 'United Kingdom', 'dam-spam' ),
	'count_check_us'                 => esc_html__( 'United States', 'dam-spam' ),
	'count_check_uy'                 => esc_html__( 'Uruguay', 'dam-spam' ),
	'count_check_uz'                 => esc_html__( 'Uzbekistan', 'dam-spam' ),
	'count_check_vc'                 => esc_html__( 'Saint Vincent And Grenadines', 'dam-spam' ),
	'count_check_ve'                 => esc_html__( 'Venezuela', 'dam-spam' ),
	'count_check_vn'                 => esc_html__( 'Viet Nam', 'dam-spam' ),
	'count_check_ye'                 => esc_html__( 'Yemen', 'dam-spam' ),
	'count_captcha'                  => esc_html__( 'Passed CAPTCHA', 'dam-spam' ),
	'count_captcha_fail'             => esc_html__( 'Failed CAPTCHA', 'dam-spam' ),
	'count_pass'                     => esc_html__( 'Total Pass', 'dam-spam' ),
);

$message = '';
$msg = '';
$nonce = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['ds_control'] ) );
}

if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'clear', $_POST ) ) {
		foreach ( $counters as $v1 => $v2 ) {
			$stats[$v1] = 0;
		}
		$addonstats = array();
		$stats['addonstats'] = $addonstats;
		$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Summary Cleared', 'dam-spam' ) . '</p></div>';
		ds_set_stats( $stats );
		extract( $stats );
	}
	if ( array_key_exists( 'update_total', $_POST ) ) {
		$stats['spam_multisite_count'] = isset( $_POST['spam_multisite_count'] ) ? sanitize_text_field( wp_unslash( $_POST['spam_multisite_count'] ) ) : '';
		$stats['spam_multisite_date'] = isset( $_POST['spam_multisite_date'] ) ? sanitize_text_field( wp_unslash( $_POST['spam_multisite_date'] ) ) : '';
		ds_set_stats( $stats );
		extract( $stats );
	}
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Summary — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php esc_html_e( 'Version', 'dam-spam' ); ?>: <strong><?php echo esc_html( DS_VERSION ); ?></strong>
	<?php
	$ip = ds_get_ip();
	?>
	| <?php esc_html_e( 'IP', 'dam-spam' ); ?>: <strong><?php echo esc_html( $ip ); ?></strong>
	<?php
	$answer = ds_load( 'check_valid_ip', ds_get_ip() );
	if ( $answer === false ) {
		$answer = ds_load( 'check_cloudflare', ds_get_ip() );
	}
	if ( $answer !== false ) {
		?>
		<p>
		<?php
		printf(
			// translators: %s is the reason why the IP address is invalid for testing
			esc_html__( 'This address is invalid for testing for the following reason: %s. If you\'re testing locally, this might be okay. However, if your site\'s DNS is hosted through Cloudflare, you\'ll need to restore IPs.', 'dam-spam' ),
			esc_html( $answer )
		);
		?>
		</p>
		<?php
	}
	$sname = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$sname = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
	if ( empty( $sname ) && isset( $_SERVER['SCRIPT_NAME'] ) ) {
		$script_name = sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) );
		$_SERVER['REQUEST_URI'] = $script_name;
		$sname = $script_name;
	}
	if ( strpos( $sname, '?' ) !== false ) {
		$sname = substr( $sname, 0, strpos( $sname, '?' ) );
	}
	if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	}
	$current_user_name = wp_get_current_user()->user_login;
	if ( $current_user_name === 'admin' ) {
		echo '<span class="notice notice-warning" style="display:block">' . esc_html__( 'SECURITY RISK: You are using the username "admin." Please change it.', 'dam-spam' ) . '</span>';
	}
	$showcf = false;
	if ( $showcf && array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) && !function_exists( 'cloudflare_init' ) && !defined( 'W3TC' ) ) {
		echo '<span class="notice notice-warning" style="display:block">';
		esc_html_e( 'WARNING: Cloudflare Remote IP address detected. Please make sure to ', 'dam-spam' );
		echo '<a href="https://developers.cloudflare.com/support/troubleshooting/restoring-visitor-ips/restoring-original-visitor-ips/" target="_blank">';
		esc_html_e( 'restore visitor IPs', 'dam-spam' );
		echo '</a>.</span>';
	}
	?>
	<br>
	<h2><?php esc_html_e( 'Summary of Spam', 'dam-spam' ); ?></h2>
	<div class="main-stats">
		<?php if ( $spam_count > 0 ) { ?>
			<p>
			<?php
			printf(
				// translators: %1$s is the number of spammers blocked, %2$s is the start date
				esc_html__( 'Dam Spam has stopped %1$s spammers since %2$s.', 'dam-spam' ),
				esc_html( $spam_count ),
				esc_html( $spam_date )
			);
			?>
			</p>
		<?php }
		$num_comm = wp_count_comments();
		$num = number_format_i18n( $num_comm->spam );
		if ( $num_comm->spam > 0 && DS_MU !== 'Y' ) {
			?>
			<p>
			<?php
			printf(
				// translators: %1$s and %3$s are opening/closing link tags, %2$s is the number of spam comments
				esc_html__( 'There are %1$s%2$s%3$s spam comments waiting to be reported.', 'dam-spam' ),
				'<a href="edit-comments.php?comment_status=spam">',
				esc_html( $num ),
				'</a>'
			);
			?>
			</p>
		<?php }
		$num_comm = wp_count_comments();
		$num = number_format_i18n( $num_comm->moderated );
		if ( $num_comm->moderated > 0 && DS_MU !== 'Y' ) {
			?>
			<p>
			<?php
			printf(
				// translators: %1$s and %3$s are opening/closing link tags, %2$s is the number of comments waiting to be moderated
				esc_html__( 'There are %1$s%2$s%3$s comments waiting to be moderated.', 'dam-spam' ),
				'<a href="edit-comments.php?comment_status=moderated">',
				esc_html( $num ),
				'</a>'
			);
			?>
			</p>
		<?php } ?>
	</div>
	<?php
	$summary = '';
	foreach ( $counters as $v1 => $v2 ) {
		if ( !empty( $stats[$v1] ) ) {
			$summary .= '<div class="stat-box">' . esc_html( $v2 ) . ': ' . esc_html( $stats[$v1] ) . '</div>';
		}
	}
	$addonstats = isset( $stats['addonstats'] ) ? $stats['addonstats'] : array();
	foreach ( $addonstats as $key => $data ) {
		$summary .= '<div class="stat-box">' . esc_html( $key ) . ': ' . esc_html( $data[0] ) . '</div>';
	}
	echo wp_kses_post( $summary );
	?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="clear" value="clear summary">
		<p class="submit" style="clear:both"><input class="button-primary" value="<?php esc_attr_e( 'Clear Summary', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<?php
	function ds_control() {
		if ( array_key_exists( 'resetOptions', $_POST ) && isset( $_POST['ds_control'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ds_control'] ) ), 'ds_update' ) ) {
			ds_force_reset_options();
		}
		$ip = ds_get_ip();
		$nonce = wp_create_nonce( 'ds_options' );
		$options = ds_get_options();
		extract( $options );
	}
	function ds_force_reset_options() {
		$ds_opt = isset( $_POST['ds_opt'] ) ? sanitize_text_field( wp_unslash( $_POST['ds_opt'] ) ) : '';
		if ( !wp_verify_nonce( $ds_opt, 'ds_options' ) ) {
			esc_html_e( 'Session Timeout — Please Refresh the Page', 'dam-spam' );
			exit;
		}
		if ( !function_exists( 'ds_reset_options' ) ) {
			ds_require( 'includes/ds-init-options.php' );
		}
		ds_reset_options();
		delete_option( 'ds_cache' );
	}
	?>
</div>