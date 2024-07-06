<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( __( 'Access Blocked', 'dam-spam' ) );
}

if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
	_e( '<div>Jetpack Protect has been detected. Because of a conflict, Dam Spam has disabled itself.<br>You do not need to disable Jetpack, just the Protect feature.</div>', 'dam-spam' );
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
	'cntchkcloudflare'	  => __( 'Pass Cloudflare', 'dam-spam' ),
	'cntchkgcache'		  => __( 'Pass Good Cache', 'dam-spam' ),
	'cntchkakismet'	      => __( 'Reported by Akismet', 'dam-spam' ),
	'cntchkgenallowlist'  => __( 'Pass Generated Allow List', 'dam-spam' ),
	'cntchkgoogle'		  => __( 'Pass Google', 'dam-spam' ),
	'cntchkmiscallowlist' => __( 'Pass Allow List', 'dam-spam' ),
	'cntchkpaypal'		  => __( 'Pass PayPal', 'dam-spam' ),
	'cntchkscripts'	      => __( 'Pass Scripts', 'dam-spam' ),
	'cntchkvalidip'	      => __( 'Pass Uncheckable IP', 'dam-spam' ),
	'cntchkwlem'		  => __( 'Allow List Email', 'dam-spam' ),
	'cntchkuserid'		  => __( 'Allow Username', 'dam-spam' ),
	'cntchkwlist'		  => __( 'Pass Allow List IP', 'dam-spam' ),
	'cntchkyahoomerchant' => __( 'Pass Yahoo Merchant', 'dam-spam' ),
	'cntchk404'		      => __( '404 Exploit Attempt', 'dam-spam' ),
	'cntchkaccept'		  => __( 'Bad or Missing Accept Header', 'dam-spam' ),
	'cntchkadmin'		  => __( 'Admin Login Attempt', 'dam-spam' ),
	'cntchkadminlog'	  => __( 'Passed Login OK', 'dam-spam' ),
	'cntchkagent'		  => __( 'Bad or Missing User Agent', 'dam-spam' ),
	'cntchkamazon'		  => __( 'Amazon AWS', 'dam-spam' ),
	'cntchkaws'		      => __( 'Amazon AWS Allow', 'dam-spam' ),
	'cntchkbcache'		  => __( 'Bad Cache', 'dam-spam' ),
	'cntchkblem'		  => __( 'Block List Email', 'dam-spam' ),
	'cntchkuserid'		  => __( 'Block Username', 'dam-spam' ),
	'cntchkblip'		  => __( 'Block List IP', 'dam-spam' ),
	'cntchkbotscout'	  => __( 'BotScout', 'dam-spam' ),
	'cntchkdisp'		  => __( 'Disposable Email', 'dam-spam' ),
	'cntchkdnsbl'		  => __( 'DNSBL Hit', 'dam-spam' ),
	'cntchkexploits'	  => __( 'Exploit Attempt', 'dam-spam' ),
	'cntchkgooglesafe'	  => __( 'Google Safe Browsing', 'dam-spam' ),
	'cntchkhoney'		  => __( 'Project Honeypot', 'dam-spam' ),
	'cntchkhosting'	      => __( 'Known Spam Host', 'dam-spam' ),
	'cntchkinvalidip'	  => __( 'Block Invalid IP', 'dam-spam' ),
	'cntchklong'		  => __( 'Long Email', 'dam-spam' ),
	'cntchkshort'		  => __( 'Short Email', 'dam-spam' ),
	'cntchkbbcode'		  => __( 'BBCode in Request', 'dam-spam' ),
	'cntchkreferer'	      => __( 'Bad HTTP_REFERER', 'dam-spam' ),
	'cntchksession'	      => __( 'Session Speed', 'dam-spam' ),
	'cntchksfs'		      => __( 'Stop Forum Spam', 'dam-spam' ),
	'cntchkspamwords'	  => __( 'Spam Words', 'dam-spam' ),
	'cntchkurlshort'	  => __( 'Short URLs', 'dam-spam' ),
	'cntchktld'		      => __( 'Email TLD', 'dam-spam' ),
	'cntchkubiquity'	  => __( 'Ubiquity Servers', 'dam-spam' ),
	'cntchkmulti'		  => __( 'Repeated Hits', 'dam-spam' ),
	'cntchkform'		  => __( 'Check for Standard Form', 'dam-spam' ),
	'cntchkAD'			  => __( 'Andorra', 'dam-spam' ),
	'cntchkAE'			  => __( 'United Arab Emirates', 'dam-spam' ),
	'cntchkAF'			  => __( 'Afghanistan', 'dam-spam' ),
	'cntchkAL'			  => __( 'Albania', 'dam-spam' ),
	'cntchkAM'			  => __( 'Armenia', 'dam-spam' ),
	'cntchkAR'			  => __( 'Argentina', 'dam-spam' ),
	'cntchkAT'			  => __( 'Austria', 'dam-spam' ),
	'cntchkAU'			  => __( 'Australia', 'dam-spam' ),
	'cntchkAX'			  => __( 'Aland Islands', 'dam-spam' ),
	'cntchkAZ'			  => __( 'Azerbaijan', 'dam-spam' ),
	'cntchkBA'			  => __( 'Bosnia And Herzegovina', 'dam-spam' ),
	'cntchkBB'			  => __( 'Barbados', 'dam-spam' ),
	'cntchkBD'			  => __( 'Bangladesh', 'dam-spam' ),
	'cntchkBE'			  => __( 'Belgium', 'dam-spam' ),
	'cntchkBG'			  => __( 'Bulgaria', 'dam-spam' ),
	'cntchkBH'			  => __( 'Bahrain', 'dam-spam' ),
	'cntchkBN'			  => __( 'Brunei Darussalam', 'dam-spam' ),
	'cntchkBO'			  => __( 'Bolivia', 'dam-spam' ),
	'cntchkBR'			  => __( 'Brazil', 'dam-spam' ),
	'cntchkBS'			  => __( 'Bahamas', 'dam-spam' ),
	'cntchkBY'			  => __( 'Belarus', 'dam-spam' ),
	'cntchkBZ'			  => __( 'Belize', 'dam-spam' ),
	'cntchkCA'			  => __( 'Canada', 'dam-spam' ),
	'cntchkCD'			  => __( 'Congo, Democratic Republic', 'dam-spam' ),
	'cntchkCH'			  => __( 'Switzerland', 'dam-spam' ),
	'cntchkCL'			  => __( 'Chile', 'dam-spam' ),
	'cntchkCN'			  => __( 'China', 'dam-spam' ),
	'cntchkCO'			  => __( 'Colombia', 'dam-spam' ),
	'cntchkCR'			  => __( 'Costa Rica', 'dam-spam' ),
	'cntchkCU'			  => __( 'Cuba', 'dam-spam' ),
	'cntchkCW'			  => __( 'CuraÃ§ao', 'dam-spam' ),
	'cntchkCY'			  => __( 'Cyprus', 'dam-spam' ),
	'cntchkCZ'			  => __( 'Czech Republic', 'dam-spam' ),
	'cntchkDE'			  => __( 'Germany', 'dam-spam' ),
	'cntchkDK'			  => __( 'Denmark', 'dam-spam' ),
	'cntchkDO'			  => __( 'Dominican Republic', 'dam-spam' ),
	'cntchkDZ'			  => __( 'Algeria', 'dam-spam' ),
	'cntchkEC'			  => __( 'Ecuador', 'dam-spam' ),
	'cntchkEE'			  => __( 'Estonia', 'dam-spam' ),
	'cntchkES'			  => __( 'Spain', 'dam-spam' ),
	'cntchkEU'			  => __( 'European Union', 'dam-spam' ),
	'cntchkFI'			  => __( 'Finland', 'dam-spam' ),
	'cntchkFJ'			  => __( 'Fiji', 'dam-spam' ),
	'cntchkFR'			  => __( 'France', 'dam-spam' ),
	'cntchkGB'			  => __( 'Great Britain', 'dam-spam' ),
	'cntchkGE'			  => __( 'Georgia', 'dam-spam' ),
	'cntchkGF'			  => __( 'French Guiana', 'dam-spam' ),
	'cntchkGI'			  => __( 'Gibraltar', 'dam-spam' ),
	'cntchkGP'			  => __( 'Guadeloupe', 'dam-spam' ),
	'cntchkGR'			  => __( 'Greece', 'dam-spam' ),
	'cntchkGT'			  => __( 'Guatemala', 'dam-spam' ),
	'cntchkGU'			  => __( 'Guam', 'dam-spam' ),
	'cntchkGY'			  => __( 'Guyana', 'dam-spam' ),
	'cntchkHK'			  => __( 'Hong Kong', 'dam-spam' ),
	'cntchkHN'			  => __( 'Honduras', 'dam-spam' ),
	'cntchkHR'			  => __( 'Croatia', 'dam-spam' ),
	'cntchkHT'			  => __( 'Haiti', 'dam-spam' ),
	'cntchkHU'			  => __( 'Hungary', 'dam-spam' ),
	'cntchkID'			  => __( 'Indonesia', 'dam-spam' ),
	'cntchkIE'			  => __( 'Ireland', 'dam-spam' ),
	'cntchkIL'			  => __( 'Israel', 'dam-spam' ),
	'cntchkIN'			  => __( 'India', 'dam-spam' ),
	'cntchkIQ'			  => __( 'Iraq', 'dam-spam' ),
	'cntchkIR'			  => __( 'Iran, Islamic Republic Of', 'dam-spam' ),
	'cntchkIS'			  => __( 'Iceland', 'dam-spam' ),
	'cntchkIT'			  => __( 'Italy', 'dam-spam' ),
	'cntchkJM'			  => __( 'Jamaica', 'dam-spam' ),
	'cntchkJO'			  => __( 'Jordan', 'dam-spam' ),
	'cntchkJP'			  => __( 'Japan', 'dam-spam' ),
	'cntchkKE'			  => __( 'Kenya', 'dam-spam' ),
	'cntchkKG'			  => __( 'Kyrgyzstan', 'dam-spam' ),
	'cntchkKH'			  => __( 'Cambodia', 'dam-spam' ),
	'cntchkKR'			  => __( 'Korea', 'dam-spam' ),
	'cntchkKW'			  => __( 'Kuwait', 'dam-spam' ),
	'cntchkKY'			  => __( 'Cayman Islands', 'dam-spam' ),
	'cntchkKZ'			  => __( 'Kazakhstan', 'dam-spam' ),
	'cntchkLA'			  => __( 'Lao People\'s Democratic Republic', 'dam-spam' ),
	'cntchkLB'			  => __( 'Lebanon', 'dam-spam' ),
	'cntchkLK'			  => __( 'Sri Lanka', 'dam-spam' ),
	'cntchkLT'			  => __( 'Lithuania', 'dam-spam' ),
	'cntchkLU'			  => __( 'Luxembourg', 'dam-spam' ),
	'cntchkLV'			  => __( 'Latvia', 'dam-spam' ),
	'cntchkMD'			  => __( 'Moldova', 'dam-spam' ),
	'cntchkME'			  => __( 'Montenegro', 'dam-spam' ),
	'cntchkMK'			  => __( 'Macedonia', 'dam-spam' ),
	'cntchkMM'			  => __( 'Myanmar', 'dam-spam' ),
	'cntchkMN'			  => __( 'Mongolia', 'dam-spam' ),
	'cntchkMO'			  => __( 'Macao', 'dam-spam' ),
	'cntchkMP'			  => __( 'Northern Mariana Islands', 'dam-spam' ),
	'cntchkMQ'			  => __( 'Martinique', 'dam-spam' ),
	'cntchkMT'			  => __( 'Malta', 'dam-spam' ),
	'cntchkMV'			  => __( 'Maldives', 'dam-spam' ),
	'cntchkMX'			  => __( 'Mexico', 'dam-spam' ),
	'cntchkMY'			  => __( 'Malaysia', 'dam-spam' ),
	'cntchkNC'			  => __( 'New Caledonia', 'dam-spam' ),
	'cntchkNI'			  => __( 'Nicaragua', 'dam-spam' ),
	'cntchkNL'			  => __( 'Netherlands', 'dam-spam' ),
	'cntchkNO'			  => __( 'Norway', 'dam-spam' ),
	'cntchkNP'			  => __( 'Nepal', 'dam-spam' ),
	'cntchkNZ'			  => __( 'New Zealand', 'dam-spam' ),
	'cntchkOM'			  => __( 'Oman', 'dam-spam' ),
	'cntchkPA'			  => __( 'Panama', 'dam-spam' ),
	'cntchkPE'			  => __( 'Peru', 'dam-spam' ),
	'cntchkPG'			  => __( 'Papua New Guinea', 'dam-spam' ),
	'cntchkPH'			  => __( 'Philippines', 'dam-spam' ),
	'cntchkPK'			  => __( 'Pakistan', 'dam-spam' ),
	'cntchkPL'			  => __( 'Poland', 'dam-spam' ),
	'cntchkPR'			  => __( 'Puerto Rico', 'dam-spam' ),
	'cntchkPS'			  => __( 'Palestinian Territory, Occupied', 'dam-spam' ),
	'cntchkPT'			  => __( 'Portugal', 'dam-spam' ),
	'cntchkPW'			  => __( 'Palau', 'dam-spam' ),
	'cntchkPY'			  => __( 'Paraguay', 'dam-spam' ),
	'cntchkQA'			  => __( 'Qatar', 'dam-spam' ),
	'cntchkRO'			  => __( 'Romania', 'dam-spam' ),
	'cntchkRS'			  => __( 'Serbia', 'dam-spam' ),
	'cntchkRU'			  => __( 'Russian Federation', 'dam-spam' ),
	'cntchkSA'			  => __( 'Saudi Arabia', 'dam-spam' ),
	'cntchkSC'			  => __( 'Seychelles', 'dam-spam' ),
	'cntchkSE'			  => __( 'Sweden', 'dam-spam' ),
	'cntchkSG'			  => __( 'Singapore', 'dam-spam' ),
	'cntchkSI'			  => __( 'Slovenia', 'dam-spam' ),
	'cntchkSK'			  => __( 'Slovakia', 'dam-spam' ),
	'cntchkSV'			  => __( 'El Salvador', 'dam-spam' ),
	'cntchkSX'			  => __( 'Sint Maarten', 'dam-spam' ),
	'cntchkSY'			  => __( 'Syrian Arab Republic', 'dam-spam' ),
	'cntchkTH'			  => __( 'Thailand', 'dam-spam' ),
	'cntchkTJ'			  => __( 'Tajikistan', 'dam-spam' ),
	'cntchkTM'			  => __( 'Turkmenistan', 'dam-spam' ),
	'cntchkTR'			  => __( 'Turkey', 'dam-spam' ),
	'cntchkTT'			  => __( 'Trinidad And Tobago', 'dam-spam' ),
	'cntchkTW'			  => __( 'Taiwan', 'dam-spam' ),
	'cntchkUA'			  => __( 'Ukraine', 'dam-spam' ),
	'cntchkUK'			  => __( 'United Kingdom', 'dam-spam' ),
	'cntchkUS'			  => __( 'United States', 'dam-spam' ),
	'cntchkUY'			  => __( 'Uruguay', 'dam-spam' ),
	'cntchkUZ'			  => __( 'Uzbekistan', 'dam-spam' ),
	'cntchkVC'			  => __( 'Saint Vincent And Grenadines', 'dam-spam' ),
	'cntchkVE'			  => __( 'Venezuela', 'dam-spam' ),
	'cntchkVN'			  => __( 'Viet Nam', 'dam-spam' ),
	'cntchkYE'			  => __( 'Yemen', 'dam-spam' ),
	'cntcap'			  => __( 'Passed CAPTCHA', 'dam-spam' ), // captha success
	'cntncap'			  => __( 'Failed CAPTCHA', 'dam-spam' ), // captha not success
	'cntpass'			  => __( 'Total Pass', 'dam-spam' ), // passed
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
		$msg			  	 = '<div class="notice notice-success is-dismissible"><p>' . __( 'Summary Cleared', 'dam-spam' ) . '</p></div>';
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
	<h1 id="ds-head"><?php _e( 'Dam Spam — Summary', 'dam-spam' ); ?></h1><br>
	<?php _e( 'Version', 'dam-spam' ); ?> <strong><?php echo DS_VERSION; ?></strong>
	<?php if ( !empty( $summary ) ) { ?>
	<?php }
	$ip = ds_get_ip();
	?>
	| <?php _e( 'Your current IP address is', 'dam-spam' ); ?>: <strong><?php echo $ip; ?></strong>
	<?php
	// check the IP to see if we are local
	$ansa = be_load( 'chkvalidip', ds_get_ip() );
	if ( $ansa == false ) {
		$ansa = be_load( 'chkcloudflare', ds_get_ip() );
	}
	if ( $ansa !== false ) { ?>
		<p><?php _e( 'This address is invalid for testing for the following reason:
			  <span style="font-weight:bold;font-size:1.2em">' . $ansa . '</span>.<br>
			  If you working on a local installation of WordPress, this might be
			  OK. However, if the plugin reports that your
			  IP is invalid it may be because you are using Cloudflare or a proxy
			  server to access this page. This will make
			  it impossible for the plugin to check IP addresses. You may want to
			  go to the Dam Spam Testing page in
			  order to test all possible reasons that your IP is not appearing as
			  the IP of the machine that your using to
			  browse this site.<br>
			  It is possible to use the plugin if this problem appears, but most
			  checking functions will be turned off. The
			  plugin will still perform spam checks which do not require an
			  IP.<br>
			  If the error says that this is a Cloudflare IP address, you can fix
			  this by installing the Cloudflare plugin. If
			  you use Cloudflare to protect and speed up your site then you MUST
			  install the Cloudflare plugin. This plugin
			  will be crippled until you install it.', 'dam-spam' ); ?></p>
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
		echo $msg;
	}
	$current_user_name = wp_get_current_user()->user_login;
	if ( $current_user_name == 'admin' ) {
		_e( '<span class="notice notice-warning" style="display:block">SECURITY RISK: You are using the username "admin." This is an invitation to hackers to try and guess your password. Please change this.</span>', 'dam-spam' );
	}
	$showcf = false; // hide this for now
	if ( $showcf && array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) && !function_exists( 'cloudflare_init' ) && !defined( 'W3TC' ) ) {
		_e( '<span class="notice notice-warning" style="display:block">WARNING: Cloudflare Remote IP address detected. Please make sure to <a href="https://support.cloudflare.com/hc/sections/200805497-Restoring-Visitor-IPs" target="_blank">restore visitor IPs</a>.</span>', 'dam-spam' );
	}
	?>
	<h2><?php _e( 'Summary of Spam', 'dam-spam' ); ?></h2>
	<div class="main-stats">
	<?php if ( $spcount > 0 ) { ?>
		<p><?php _e( 'Dam Spam has stopped <strong>' . $spcount . '</strong> spammers since ' . $spdate . '.', 'dam-spam' ); ?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->spam );
	if ( $num_comm->spam > 0 && DS_MU != 'Y' ) { ?>
		<p><?php _e( 'There are <a href="edit-comments.php?comment_status=spam">' . $num . '</a> spam comments waiting for you to report.', 'dam-spam' ); ?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->moderated );
	if ( $num_comm->moderated > 0 && DS_MU != 'Y' ) { ?>
		<p><?php _e( 'There are <a href="edit-comments.php?comment_status=moderated">' . $num . '</a> comments waiting to be moderated.', 'dam-spam' ); ?></p></div>
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
		echo $summary;
	?>
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
		<input type="hidden" name="clear" value="clear summary">
		<p class="submit" style="clear:both"><input class="button-primary" value="<?php _e( 'Clear Summary', 'dam-spam' ); ?>" type="submit"></p>
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
			_e( 'Session Timeout — Please Refresh the Page', 'dam-spam' );
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