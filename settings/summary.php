<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
	esc_html_e( 'Jetpack Protect has been detected. Because of a conflict, Dam Spam has disabled itself. You do not need to disable Jetpack, just the Protect feature.', 'dam-spam' );
	return;
}

ds_fix_post_vars();
$stats = ds_get_stats();
extract( $stats );
$now = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

// counter list - this should be copied from the get option utility
// counters should have the same name as the YN switch for the check
// I see lots of missing counters here
$counters = array(
	'cntchkcloudflare'	  => esc_html__( 'Pass Cloudflare', 'dam-spam' ),
	'cntchkgcache'		  => esc_html__( 'Pass Good Cache', 'dam-spam' ),
	'cntchkakismet'	      => esc_html__( 'Reported by Akismet', 'dam-spam' ),
	'cntchkgenallowlist'  => esc_html__( 'Pass Generated Allow List', 'dam-spam' ),
	'cntchkgoogle'		  => esc_html__( 'Pass Google', 'dam-spam' ),
	'cntchkmiscallowlist' => esc_html__( 'Pass Allow List', 'dam-spam' ),
	'cntchkpaypal'		  => esc_html__( 'Pass PayPal', 'dam-spam' ),
	'cntchkscripts'	      => esc_html__( 'Pass Scripts', 'dam-spam' ),
	'cntchkvalidip'	      => esc_html__( 'Pass Uncheckable IP', 'dam-spam' ),
	'cntchkwlem'		  => esc_html__( 'Allow List Email', 'dam-spam' ),
	'cntchkuserid'		  => esc_html__( 'Allow Username', 'dam-spam' ),
	'cntchkwlist'		  => esc_html__( 'Pass Allow List IP', 'dam-spam' ),
	'cntchkyahoomerchant' => esc_html__( 'Pass Yahoo Merchant', 'dam-spam' ),
	'cntchk404'		      => esc_html__( '404 Exploit Attempt', 'dam-spam' ),
	'cntchkaccept'		  => esc_html__( 'Bad or Missing Accept Header', 'dam-spam' ),
	'cntchkadmin'		  => esc_html__( 'Admin Login Attempt', 'dam-spam' ),
	'cntchkadminlog'	  => esc_html__( 'Passed Login OK', 'dam-spam' ),
	'cntchkagent'		  => esc_html__( 'Bad or Missing User Agent', 'dam-spam' ),
	'cntchkamazon'		  => esc_html__( 'Amazon AWS', 'dam-spam' ),
	'cntchkaws'		      => esc_html__( 'Amazon AWS Allow', 'dam-spam' ),
	'cntchkbcache'		  => esc_html__( 'Bad Cache', 'dam-spam' ),
	'cntchkblem'		  => esc_html__( 'Block List Email', 'dam-spam' ),
	'cntchkuserid'		  => esc_html__( 'Block Username', 'dam-spam' ),
	'cntchkblip'		  => esc_html__( 'Block List IP', 'dam-spam' ),
	'cntchkbotscout'	  => esc_html__( 'BotScout', 'dam-spam' ),
	'cntchkdisp'		  => esc_html__( 'Disposable Email', 'dam-spam' ),
	'cntchkdnsbl'		  => esc_html__( 'DNSBL Hit', 'dam-spam' ),
	'cntchkexploits'	  => esc_html__( 'Exploit Attempt', 'dam-spam' ),
	'cntchkgooglesafe'	  => esc_html__( 'Google Safe Browsing', 'dam-spam' ),
	'cntchkhoney'		  => esc_html__( 'Project Honeypot', 'dam-spam' ),
	'cntchkhosting'	      => esc_html__( 'Known Spam Host', 'dam-spam' ),
	'cntchkinvalidip'	  => esc_html__( 'Block Invalid IP', 'dam-spam' ),
	'cntchklong'		  => esc_html__( 'Long Email', 'dam-spam' ),
	'cntchkshort'		  => esc_html__( 'Short Email', 'dam-spam' ),
	'cntchkbbcode'		  => esc_html__( 'BBCode in Request', 'dam-spam' ),
	'cntchkreferer'	      => esc_html__( 'Bad HTTP_REFERER', 'dam-spam' ),
	'cntchksession'	      => esc_html__( 'Session Speed', 'dam-spam' ),
	'cntchksfs'		      => esc_html__( 'Stop Forum Spam', 'dam-spam' ),
	'cntchkspamwords'	  => esc_html__( 'Spam Words', 'dam-spam' ),
	'cntchkurlshort'	  => esc_html__( 'Short URLs', 'dam-spam' ),
	'cntchktld'		      => esc_html__( 'Email TLD', 'dam-spam' ),
	'cntchkubiquity'	  => esc_html__( 'Ubiquity Servers', 'dam-spam' ),
	'cntchkmulti'		  => esc_html__( 'Repeated Hits', 'dam-spam' ),
	'cntchkform'		  => esc_html__( 'Check for Standard Form', 'dam-spam' ),
	'cntchkAD'			  => esc_html__( 'Andorra', 'dam-spam' ),
	'cntchkAE'			  => esc_html__( 'United Arab Emirates', 'dam-spam' ),
	'cntchkAF'			  => esc_html__( 'Afghanistan', 'dam-spam' ),
	'cntchkAL'			  => esc_html__( 'Albania', 'dam-spam' ),
	'cntchkAM'			  => esc_html__( 'Armenia', 'dam-spam' ),
	'cntchkAR'			  => esc_html__( 'Argentina', 'dam-spam' ),
	'cntchkAT'			  => esc_html__( 'Austria', 'dam-spam' ),
	'cntchkAU'			  => esc_html__( 'Australia', 'dam-spam' ),
	'cntchkAX'			  => esc_html__( 'Aland Islands', 'dam-spam' ),
	'cntchkAZ'			  => esc_html__( 'Azerbaijan', 'dam-spam' ),
	'cntchkBA'			  => esc_html__( 'Bosnia And Herzegovina', 'dam-spam' ),
	'cntchkBB'			  => esc_html__( 'Barbados', 'dam-spam' ),
	'cntchkBD'			  => esc_html__( 'Bangladesh', 'dam-spam' ),
	'cntchkBE'			  => esc_html__( 'Belgium', 'dam-spam' ),
	'cntchkBG'			  => esc_html__( 'Bulgaria', 'dam-spam' ),
	'cntchkBH'			  => esc_html__( 'Bahrain', 'dam-spam' ),
	'cntchkBN'			  => esc_html__( 'Brunei Darussalam', 'dam-spam' ),
	'cntchkBO'			  => esc_html__( 'Bolivia', 'dam-spam' ),
	'cntchkBR'			  => esc_html__( 'Brazil', 'dam-spam' ),
	'cntchkBS'			  => esc_html__( 'Bahamas', 'dam-spam' ),
	'cntchkBY'			  => esc_html__( 'Belarus', 'dam-spam' ),
	'cntchkBZ'			  => esc_html__( 'Belize', 'dam-spam' ),
	'cntchkCA'			  => esc_html__( 'Canada', 'dam-spam' ),
	'cntchkCD'			  => esc_html__( 'Congo, Democratic Republic', 'dam-spam' ),
	'cntchkCH'			  => esc_html__( 'Switzerland', 'dam-spam' ),
	'cntchkCL'			  => esc_html__( 'Chile', 'dam-spam' ),
	'cntchkCN'			  => esc_html__( 'China', 'dam-spam' ),
	'cntchkCO'			  => esc_html__( 'Colombia', 'dam-spam' ),
	'cntchkCR'			  => esc_html__( 'Costa Rica', 'dam-spam' ),
	'cntchkCU'			  => esc_html__( 'Cuba', 'dam-spam' ),
	'cntchkCW'			  => esc_html__( 'CuraÃ§ao', 'dam-spam' ),
	'cntchkCY'			  => esc_html__( 'Cyprus', 'dam-spam' ),
	'cntchkCZ'			  => esc_html__( 'Czech Republic', 'dam-spam' ),
	'cntchkDE'			  => esc_html__( 'Germany', 'dam-spam' ),
	'cntchkDK'			  => esc_html__( 'Denmark', 'dam-spam' ),
	'cntchkDO'			  => esc_html__( 'Dominican Republic', 'dam-spam' ),
	'cntchkDZ'			  => esc_html__( 'Algeria', 'dam-spam' ),
	'cntchkEC'			  => esc_html__( 'Ecuador', 'dam-spam' ),
	'cntchkEE'			  => esc_html__( 'Estonia', 'dam-spam' ),
	'cntchkES'			  => esc_html__( 'Spain', 'dam-spam' ),
	'cntchkEU'			  => esc_html__( 'European Union', 'dam-spam' ),
	'cntchkFI'			  => esc_html__( 'Finland', 'dam-spam' ),
	'cntchkFJ'			  => esc_html__( 'Fiji', 'dam-spam' ),
	'cntchkFR'			  => esc_html__( 'France', 'dam-spam' ),
	'cntchkGB'			  => esc_html__( 'Great Britain', 'dam-spam' ),
	'cntchkGE'			  => esc_html__( 'Georgia', 'dam-spam' ),
	'cntchkGF'			  => esc_html__( 'French Guiana', 'dam-spam' ),
	'cntchkGI'			  => esc_html__( 'Gibraltar', 'dam-spam' ),
	'cntchkGP'			  => esc_html__( 'Guadeloupe', 'dam-spam' ),
	'cntchkGR'			  => esc_html__( 'Greece', 'dam-spam' ),
	'cntchkGT'			  => esc_html__( 'Guatemala', 'dam-spam' ),
	'cntchkGU'			  => esc_html__( 'Guam', 'dam-spam' ),
	'cntchkGY'			  => esc_html__( 'Guyana', 'dam-spam' ),
	'cntchkHK'			  => esc_html__( 'Hong Kong', 'dam-spam' ),
	'cntchkHN'			  => esc_html__( 'Honduras', 'dam-spam' ),
	'cntchkHR'			  => esc_html__( 'Croatia', 'dam-spam' ),
	'cntchkHT'			  => esc_html__( 'Haiti', 'dam-spam' ),
	'cntchkHU'			  => esc_html__( 'Hungary', 'dam-spam' ),
	'cntchkID'			  => esc_html__( 'Indonesia', 'dam-spam' ),
	'cntchkIE'			  => esc_html__( 'Ireland', 'dam-spam' ),
	'cntchkIL'			  => esc_html__( 'Israel', 'dam-spam' ),
	'cntchkIN'			  => esc_html__( 'India', 'dam-spam' ),
	'cntchkIQ'			  => esc_html__( 'Iraq', 'dam-spam' ),
	'cntchkIR'			  => esc_html__( 'Iran, Islamic Republic Of', 'dam-spam' ),
	'cntchkIS'			  => esc_html__( 'Iceland', 'dam-spam' ),
	'cntchkIT'			  => esc_html__( 'Italy', 'dam-spam' ),
	'cntchkJM'			  => esc_html__( 'Jamaica', 'dam-spam' ),
	'cntchkJO'			  => esc_html__( 'Jordan', 'dam-spam' ),
	'cntchkJP'			  => esc_html__( 'Japan', 'dam-spam' ),
	'cntchkKE'			  => esc_html__( 'Kenya', 'dam-spam' ),
	'cntchkKG'			  => esc_html__( 'Kyrgyzstan', 'dam-spam' ),
	'cntchkKH'			  => esc_html__( 'Cambodia', 'dam-spam' ),
	'cntchkKR'			  => esc_html__( 'Korea', 'dam-spam' ),
	'cntchkKW'			  => esc_html__( 'Kuwait', 'dam-spam' ),
	'cntchkKY'			  => esc_html__( 'Cayman Islands', 'dam-spam' ),
	'cntchkKZ'			  => esc_html__( 'Kazakhstan', 'dam-spam' ),
	'cntchkLA'			  => esc_html__( 'Lao People\'s Democratic Republic', 'dam-spam' ),
	'cntchkLB'			  => esc_html__( 'Lebanon', 'dam-spam' ),
	'cntchkLK'			  => esc_html__( 'Sri Lanka', 'dam-spam' ),
	'cntchkLT'			  => esc_html__( 'Lithuania', 'dam-spam' ),
	'cntchkLU'			  => esc_html__( 'Luxembourg', 'dam-spam' ),
	'cntchkLV'			  => esc_html__( 'Latvia', 'dam-spam' ),
	'cntchkMD'			  => esc_html__( 'Moldova', 'dam-spam' ),
	'cntchkME'			  => esc_html__( 'Montenegro', 'dam-spam' ),
	'cntchkMK'			  => esc_html__( 'Macedonia', 'dam-spam' ),
	'cntchkMM'			  => esc_html__( 'Myanmar', 'dam-spam' ),
	'cntchkMN'			  => esc_html__( 'Mongolia', 'dam-spam' ),
	'cntchkMO'			  => esc_html__( 'Macao', 'dam-spam' ),
	'cntchkMP'			  => esc_html__( 'Northern Mariana Islands', 'dam-spam' ),
	'cntchkMQ'			  => esc_html__( 'Martinique', 'dam-spam' ),
	'cntchkMT'			  => esc_html__( 'Malta', 'dam-spam' ),
	'cntchkMV'			  => esc_html__( 'Maldives', 'dam-spam' ),
	'cntchkMX'			  => esc_html__( 'Mexico', 'dam-spam' ),
	'cntchkMY'			  => esc_html__( 'Malaysia', 'dam-spam' ),
	'cntchkNC'			  => esc_html__( 'New Caledonia', 'dam-spam' ),
	'cntchkNI'			  => esc_html__( 'Nicaragua', 'dam-spam' ),
	'cntchkNL'			  => esc_html__( 'Netherlands', 'dam-spam' ),
	'cntchkNO'			  => esc_html__( 'Norway', 'dam-spam' ),
	'cntchkNP'			  => esc_html__( 'Nepal', 'dam-spam' ),
	'cntchkNZ'			  => esc_html__( 'New Zealand', 'dam-spam' ),
	'cntchkOM'			  => esc_html__( 'Oman', 'dam-spam' ),
	'cntchkPA'			  => esc_html__( 'Panama', 'dam-spam' ),
	'cntchkPE'			  => esc_html__( 'Peru', 'dam-spam' ),
	'cntchkPG'			  => esc_html__( 'Papua New Guinea', 'dam-spam' ),
	'cntchkPH'			  => esc_html__( 'Philippines', 'dam-spam' ),
	'cntchkPK'			  => esc_html__( 'Pakistan', 'dam-spam' ),
	'cntchkPL'			  => esc_html__( 'Poland', 'dam-spam' ),
	'cntchkPR'			  => esc_html__( 'Puerto Rico', 'dam-spam' ),
	'cntchkPS'			  => esc_html__( 'Palestinian Territory, Occupied', 'dam-spam' ),
	'cntchkPT'			  => esc_html__( 'Portugal', 'dam-spam' ),
	'cntchkPW'			  => esc_html__( 'Palau', 'dam-spam' ),
	'cntchkPY'			  => esc_html__( 'Paraguay', 'dam-spam' ),
	'cntchkQA'			  => esc_html__( 'Qatar', 'dam-spam' ),
	'cntchkRO'			  => esc_html__( 'Romania', 'dam-spam' ),
	'cntchkRS'			  => esc_html__( 'Serbia', 'dam-spam' ),
	'cntchkRU'			  => esc_html__( 'Russian Federation', 'dam-spam' ),
	'cntchkSA'			  => esc_html__( 'Saudi Arabia', 'dam-spam' ),
	'cntchkSC'			  => esc_html__( 'Seychelles', 'dam-spam' ),
	'cntchkSE'			  => esc_html__( 'Sweden', 'dam-spam' ),
	'cntchkSG'			  => esc_html__( 'Singapore', 'dam-spam' ),
	'cntchkSI'			  => esc_html__( 'Slovenia', 'dam-spam' ),
	'cntchkSK'			  => esc_html__( 'Slovakia', 'dam-spam' ),
	'cntchkSV'			  => esc_html__( 'El Salvador', 'dam-spam' ),
	'cntchkSX'			  => esc_html__( 'Sint Maarten', 'dam-spam' ),
	'cntchkSY'			  => esc_html__( 'Syrian Arab Republic', 'dam-spam' ),
	'cntchkTH'			  => esc_html__( 'Thailand', 'dam-spam' ),
	'cntchkTJ'			  => esc_html__( 'Tajikistan', 'dam-spam' ),
	'cntchkTM'			  => esc_html__( 'Turkmenistan', 'dam-spam' ),
	'cntchkTR'			  => esc_html__( 'Turkey', 'dam-spam' ),
	'cntchkTT'			  => esc_html__( 'Trinidad And Tobago', 'dam-spam' ),
	'cntchkTW'			  => esc_html__( 'Taiwan', 'dam-spam' ),
	'cntchkUA'			  => esc_html__( 'Ukraine', 'dam-spam' ),
	'cntchkUK'			  => esc_html__( 'United Kingdom', 'dam-spam' ),
	'cntchkUS'			  => esc_html__( 'United States', 'dam-spam' ),
	'cntchkUY'			  => esc_html__( 'Uruguay', 'dam-spam' ),
	'cntchkUZ'			  => esc_html__( 'Uzbekistan', 'dam-spam' ),
	'cntchkVC'			  => esc_html__( 'Saint Vincent And Grenadines', 'dam-spam' ),
	'cntchkVE'			  => esc_html__( 'Venezuela', 'dam-spam' ),
	'cntchkVN'			  => esc_html__( 'Viet Nam', 'dam-spam' ),
	'cntchkYE'			  => esc_html__( 'Yemen', 'dam-spam' ),
	'cntcap'			  => esc_html__( 'Passed CAPTCHA', 'dam-spam' ), // captha success
	'cntncap'			  => esc_html__( 'Failed CAPTCHA', 'dam-spam' ), // captha not success
	'cntpass'			  => esc_html__( 'Total Pass', 'dam-spam' ), // passed
);

$message  = '';
$nonce	  = '';

if ( array_key_exists( 'ds_control', $_POST ) ) {
	$nonce = $_POST['ds_control'];
}

if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
	if ( array_key_exists( 'clear', $_POST ) ) {
		foreach ( $counters as $v1 => $v2 ) {
			$stats[$v1] = 0;
		}
		$addonstats		     = array();
		$stats['addonstats'] = $addonstats;
		$msg			  	 = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Summary Cleared', 'dam-spam' ) . '</p></div>';
		ds_set_stats( $stats );
		extract( $stats ); // extract again to get the new options
	}
	if ( array_key_exists( 'update_total', $_POST ) ) {
		$stats['spmcount'] = sanitize_text_field( $_POST['spmcount'] );
		$stats['spmdate']  = sanitize_text_field( $_POST['spmdate'] );
		ds_set_stats( $stats );
		extract( $stats ); // extract again to get the new options
	}
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Summary — Dam Spam', 'dam-spam' ); ?></h1><br>
	<?php esc_html_e( 'Version', 'dam-spam' ); ?> <strong><?php echo esc_html( DS_VERSION ); ?></strong>
	<?php if ( !empty( $summary ) ) { ?>
	<?php }
	$ip = ds_get_ip();
	?>
	| <?php esc_html_e( 'Your current IP address is', 'dam-spam' ); ?>: <strong><?php echo esc_html( $ip ); ?></strong>
	<?php
	// check the IP to see if we are local
	$ansa = be_load( 'chkvalidip', ds_get_ip() );
	if ( $ansa == false ) {
		$ansa = be_load( 'chkcloudflare', ds_get_ip() );
	}
	if ( $ansa !== false ) { ?>
		<p>
		<?php printf(
			esc_html__( 'This address is invalid for testing for the following reason: %s.
			If you working on a local installation of WordPress, this might be
			OK. However, if the plugin reports that your
			IP is invalid it may be because you are using Cloudflare or a proxy
			server to access this page. This will make
			it impossible for the plugin to check IP addresses. You may want to
			go to the Dam Spam Testing page in
			order to test all possible reasons that your IP is not appearing as
			the IP of the machine that your using to browse this site.
			It is possible to use the plugin if this problem appears, but most
			checking functions will be turned off. The
			plugin will still perform spam checks which do not require an IP.
			If the error says that this is a Cloudflare IP address, you can fix
			this by installing the Cloudflare plugin. If
			you use Cloudflare to protect and speed up your site then you MUST
			install the Cloudflare plugin. This plugin
			will be crippled until you install it.', 'dam-spam' ),
			esc_html( $ansa )
		); ?>
		</p>
	<?php }
	// need the current guy
	$sname = '';
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$sname = $_SERVER["REQUEST_URI"];
	}
	if ( empty( $sname ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		$sname			  	    = $_SERVER["SCRIPT_NAME"];
	}
	if ( strpos( $sname, '?' ) !== false ) {
		$sname = substr( $sname, 0, strpos( $sname, '?' ) );
	}
	if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	}
	$current_user_name = wp_get_current_user()->user_login;
	if ( $current_user_name == 'admin' ) {
		echo '<span class="notice notice-warning" style="display:block">' . esc_html__( 'SECURITY RISK: You are using the username "admin." This is an invitation to hackers to try and guess your password. Please change this.', 'dam-spam' ) . '</span>';
	}
	$showcf = false; // hide this for now
	if ( $showcf && array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) && !function_exists( 'cloudflare_init' ) && !defined( 'W3TC' ) ) {
		echo '<span class="notice notice-warning" style="display:block">' . esc_html__( 'WARNING: Cloudflare Remote IP address detected. Please make sure to', 'dam-spam' ) . '<a href="https://support.cloudflare.com/hc/sections/200805497-Restoring-Visitor-IPs" target="_blank">' . esc_html__( 'restore visitor IPs', 'dam-spam' ) . '</a>.</span>';
	}
	?>
	<h2><?php esc_html_e( 'Summary of Spam', 'dam-spam' ); ?></h2>
	<div class="main-stats">
	<?php if ( $spcount > 0 ) { ?>
		<p><?php printf( esc_html__( 'Dam Spam has stopped %1$s spammers since %2$s.', 'dam-spam' ), esc_html( $spcount, $spdate ) ); ?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->spam );
	if ( $num_comm->spam > 0 && DS_MU != 'Y' ) { ?>
		<p><?php printf( esc_html__( 'There are %1$s%2$s%3$s spam comments waiting for you to report.', 'dam-spam' ), '<a href="edit-comments.php?comment_status=spam">', esc_html( $num ), '</a>' ); ?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->moderated );
	if ( $num_comm->moderated > 0 && DS_MU != 'Y' ) { ?>
		<p><?php printf( esc_html__( 'There are %1$s%2$s%3$s comments waiting to be moderated.', 'dam-spam' ), '<a href="edit-comments.php?comment_status=moderated">', esc_html( $num ), '</a>' ); ?></p></div>
	<?php }
	$summary = '';
	foreach ( $counters as $v1 => $v2 ) {
		if ( !empty( $stats[$v1] ) ) {
			  $summary .= "<div class='stat-box'>$v2: " . $stats[$v1] . "</div>";
		} else {
		// echo "  $v1 - $v2 , ";
		}
	}
	$addonstats = $stats['addonstats'];
	foreach ( $addonstats as $key => $data ) {
	// count is in data[0] and use the plugin name
		$summary .= "<div class='stat-box'>$key: " . $data[0] . "</div>";
	} ?>
	<?php
		echo wp_kses_post( $summary );
	?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="clear" value="clear summary">
		<p class="submit" style="clear:both"><input class="button-primary" value="<?php esc_html_e( 'Clear Summary', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<?php
	function ds_control()  {
		// this is the display of information about the page.
		if ( array_key_exists( 'resetOptions', $_POST ) ) {
			ds_force_reset_options();
		}
		$ip 	 = ds_get_ip();
		$nonce   = wp_create_nonce( 'ds_options' );
		$options = ds_get_options();
		extract( $options );
	}
	function ds_force_reset_options() {
		$ds_opt = sanitize_text_field( $_POST['ds_opt'] );
		if ( !wp_verify_nonce( $ds_opt, 'ds_options' ) ) {	
			esc_html_e( 'Session Timeout — Please Refresh the Page', 'dam-spam' );
			exit;
		}
		if ( !function_exists( 'ds_reset_options' ) ) {
			ds_require( 'includes/ds-init-options.php' );
		}
		ds_reset_options();
		// clear the cache
		delete_option( 'ds_cache' );
	} ?>
</div>