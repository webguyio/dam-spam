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

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

dam_spam_fix_post_vars();
$stats = dam_spam_get_stats();
extract( $stats );
$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

$counters = array(
	'count_check_cloudflare'         => esc_html__( 'Pass Cloudflare', 'dam-spam' ),
	'count_check_good_cache'         => esc_html__( 'Pass Good Cache', 'dam-spam' ),
	'count_check_akismet'            => esc_html__( 'Reported by Akismet', 'dam-spam' ),
	'count_check_google'             => esc_html__( 'Pass Google', 'dam-spam' ),
	'count_check_misc_allow_list'    => esc_html__( 'Pass Allow List', 'dam-spam' ),
	'count_check_paypal'             => esc_html__( 'Pass PayPal', 'dam-spam' ),
	'count_check_scripts'            => esc_html__( 'Pass Scripts', 'dam-spam' ),
	'count_check_valid_ip'           => esc_html__( 'Pass Uncheckable IP', 'dam-spam' ),
	'count_check_allowed_email'      => esc_html__( 'Allow List Email', 'dam-spam' ),
	'count_check_blocked_user_id'    => esc_html__( 'Allow Username', 'dam-spam' ),
	'count_check_allow_list'         => esc_html__( 'Pass Allow List IP', 'dam-spam' ),
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
	'count_captcha'                  => esc_html__( 'Passed CAPTCHA', 'dam-spam' ),
	'count_captcha_fail'             => esc_html__( 'Failed CAPTCHA', 'dam-spam' ),
	'count_pass'                     => esc_html__( 'Total Pass', 'dam-spam' ),
);

$message = '';
$msg = '';
$nonce = '';

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) );
}

if ( wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	if ( array_key_exists( 'clear', $_POST ) ) {
		foreach ( $counters as $v1 => $v2 ) {
			$stats[$v1] = 0;
		}
		$addonstats = array();
		$stats['addonstats'] = $addonstats;
		$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Summary Cleared', 'dam-spam' ) . '</p></div>';
		dam_spam_set_stats( $stats );
		extract( $stats );
	}
	if ( array_key_exists( 'update_total', $_POST ) ) {
		$stats['spam_multisite_count'] = isset( $_POST['spam_multisite_count'] ) ? sanitize_text_field( wp_unslash( $_POST['spam_multisite_count'] ) ) : '';
		$stats['spam_multisite_date'] = isset( $_POST['spam_multisite_date'] ) ? sanitize_text_field( wp_unslash( $_POST['spam_multisite_date'] ) ) : '';
		dam_spam_set_stats( $stats );
		extract( $stats );
	}
}

$nonce = wp_create_nonce( 'dam_spam_update' );

?>

<div id="dam-spam-plugin" class="wrap">
	<h1 id="dam-spam-head"><?php esc_html_e( 'Summary — Dam Spam', 'dam-spam' ); ?></h1>
	<br>
	<?php esc_html_e( 'Version', 'dam-spam' ); ?>: <strong><?php echo esc_html( DAM_SPAM_VERSION ); ?></strong>
	<?php
	$ip = dam_spam_get_ip();
	?>
	| <?php esc_html_e( 'IP', 'dam-spam' ); ?>: <strong><?php echo esc_html( $ip ); ?></strong>
	<?php
	$answer = dam_spam_load( 'check_valid_ip', dam_spam_get_ip() );
	if ( $answer === false ) {
		$answer = dam_spam_load( 'check_cloudflare', dam_spam_get_ip() );
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
		if ( $num_comm->spam > 0 && DAM_SPAM_MU !== 'Y' ) {
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
		if ( $num_comm->moderated > 0 && DAM_SPAM_MU !== 'Y' ) {
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
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="clear" value="clear summary">
		<p class="submit" style="clear:both"><input class="button-primary" value="<?php esc_attr_e( 'Clear Summary', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<?php
	function dam_spam_control() {
		if ( array_key_exists( 'resetOptions', $_POST ) && isset( $_POST['dam_spam_control'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ), 'dam_spam_update' ) ) {
			dam_spam_force_reset_options();
		}
		$ip = dam_spam_get_ip();
		$nonce = wp_create_nonce( 'dam_spam_options' );
		$options = dam_spam_get_options();
		extract( $options );
	}
	function dam_spam_force_reset_options() {
		$dam_spam_opt = isset( $_POST['dam_spam_opt'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_opt'] ) ) : '';
		if ( !wp_verify_nonce( $dam_spam_opt, 'dam_spam_options' ) ) {
			esc_html_e( 'Session Timeout — Please Refresh the Page', 'dam-spam' );
			exit;
		}
		if ( !function_exists( 'dam_spam_reset_options' ) ) {
			dam_spam_require( 'includes/dam-spam-init-options.php' );
		}
		dam_spam_reset_options();
		delete_option( 'dam_spam_cache' );
	}
	?>
</div>