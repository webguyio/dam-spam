<?php
/*
Plugin Name: Dam Spam
Plugin URI: https://damspam.com/
Description: Fork of Stop Spammers.
Version: 0.3
Author: Web Guy
Author URI: https://webguy.io/
License: GPL
License URI: https://www.gnu.org/licenses/gpl.html
Domain Path: /languages
Text Domain: dam-spam
*/

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// ============================================================================
// Constants & Configuration
// ============================================================================

define( 'DS_VERSION', '0.3' );
define( 'DS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DS_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );

// ============================================================================
// Admin UI Functions
// ============================================================================

add_filter( 'admin_body_class', 'ds_body_class' );
function ds_body_class( $classes ) {
	$screen = get_current_screen();
	if ( strpos( $screen->id, 'dam-spam' ) !== false || strpos( $screen->id, 'ds-' ) === 0 ) {
		$classes .= ' dam-spam';
	}
	return $classes;
}

add_action( 'admin_print_styles', 'ds_styles' );
function ds_styles() {
	wp_enqueue_style( 'ds-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), DS_VERSION );
}

add_action( 'admin_notices', 'ds_admin_notice' );
function ds_admin_notice() {
	$user_id = get_current_user_id();
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	$http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$admin_url = $protocol . '://' . $http_host . $request_uri;
	$param = ( count( $_GET ) ) ? '&' : '?';
	if ( !get_user_meta( $user_id, 'ds_notice_dismissed_1' ) && current_user_can( 'manage_options' ) ) {
		echo '<div class="notice notice-info"><p><a href="' . esc_url( $admin_url . $param . 'dismiss' ) . '" class="alignright" style="text-decoration:none"><big>✕</big></a><big><strong>Dam Spam</strong> — ' . esc_html__( 'Thank you for helping us dam spam!', 'dam-spam' ) . ' 💜</big><p><a href="https://webguy.io/donate" class="button-primary" style="border-color:green;background:green" target="_blank">' . esc_html__( 'Donate', 'dam-spam' ) . '</a></p></div>';
	}
}

add_action( 'admin_init', 'ds_notice_dismissed' );
function ds_notice_dismissed() {
	$user_id = get_current_user_id();
	if ( isset( $_GET['dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dismiss_notice' ) ) {
		add_user_meta( $user_id, 'ds_notice_dismissed_1', 'true', true );
	}
}

add_action( 'admin_notices', 'ds_wc_admin_notice' );
function ds_wc_admin_notice() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$user_id = get_current_user_id();
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		$http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$admin_url = $protocol . '://' . $http_host . $request_uri;
		$param = ( count( $_GET ) ) ? '&' : '?';
		if ( !get_user_meta( $user_id, 'ds_wc_notice_dismissed' ) && current_user_can( 'manage_options' ) ) {
			echo '<div class="notice notice-info"><p style="color:purple"><a href="' . esc_url( $admin_url . $param . 'dswc-dismiss' ) . '" class="alignright" style="text-decoration:none"><big>✕</big></a>' . esc_html__( '<big><strong>WooCommerce Detected</strong></big> | We recommend <a href="admin.php?page=ds-protections">adjusting these options</a> if you experience any issues using WooCommerce and Dam Spam together.', 'dam-spam' ) . '</p></div>';
		}
	}
}

add_action( 'admin_init', 'ds_wc_notice_dismissed' );
function ds_wc_notice_dismissed() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$user_id = get_current_user_id();
		if ( isset( $_GET['dswc-dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dismiss_wc_notice' ) ) {
			add_user_meta( $user_id, 'ds_wc_notice_dismissed', 'true', true );
		}
	}
}

function ds_admin_menu() {
	if ( !function_exists( 'ds_admin_menu_l' ) ) {
		ds_require( 'settings/settings.php' );
	}
	ds_admin_menu_l();
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ds_summary_link' );
function ds_summary_link( $links ) {
	$links = array_merge( array( '<a href="' . admin_url( 'admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>' ), $links );
	return $links;
}

// ============================================================================
// Main Initialization
// ============================================================================

add_action( 'init', 'ds_init', 0 );
add_filter( 'ds_addons_allow', 'ds_addons_d', 0 );
add_filter( 'ds_addons_block', 'ds_addons_d', 0 );
add_filter( 'ds_addons_get', 'ds_addons_d', 0 );

function ds_init() {
	remove_action( 'init', 'ds_init' );
	add_filter( 'pre_user_login', 'ds_user_reg_filter', 1, 1 );
	if ( !empty( $_POST ) && array_key_exists( 'jetpack_protect_num', $_POST ) ) {
		return;
	}
	if ( function_exists( 'wp_emember_is_member_logged_in' ) ) {
		if ( !empty( $_POST ) && array_key_exists( 'login_pwd', $_POST ) ) {
			return;
		}
	}
	add_action( 'akismet_spam_caught', 'ds_log_akismet' );
	$muswitch = 'N';
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		switch_to_blog( 1 );
		$muswitch = get_option( 'ds_muswitch' );
		if ( $muswitch != 'N' ) {
			$muswitch = 'Y';
		}
		restore_current_blog();
		if ( $muswitch == 'Y' ) {
			define( 'DS_MU', $muswitch );
			ds_require( 'includes/multisite.php' );
			ds_global_setup();
		}
	} else {
		define( 'DS_MU', $muswitch );
	}
	if ( function_exists( 'is_user_logged_in' ) ) {
		if ( is_user_logged_in() ) {
			remove_filter( 'pre_user_login', 'ds_user_reg_filter', 1 );
			if ( current_user_can( 'manage_options' ) ) {
				ds_require( 'includes/admins.php' );
				return;
			}
		}
	}
	global $wp_version;
	if ( !version_compare( $wp_version, "3.1", "<" ) ) {
		add_action( 'user_register', 'ds_new_user_ip' );
		add_action( 'wp_login', 'ds_log_user_ip', 10, 2 );
	}
	if ( function_exists( 'wp_emember_is_member_logged_in' ) ) {
		if ( wp_emember_is_member_logged_in() ) {
			return;
		}
	}
	if ( isset( $_POST ) && !empty( $_POST ) ) {
		if ( array_key_exists( 'ds_block', $_POST ) && array_key_exists( 'kn', $_POST ) ) {
			if ( !empty( $_POST['kn'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kn'] ) ), 'ds_block' ) ) {
				$options = ds_get_options();
				$stats = ds_get_stats();
				$post = get_post_variables();
				ds_load( 'challenge', ds_get_ip(), $stats, $options, $post );
				return;
			}
		}
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
			return;
		}
		$post = get_post_variables();
		if ( !empty( $post['email'] ) || !empty( $post['author'] ) || !empty( $post['comment'] ) ) {
			$reason = ds_check_white();
			if ( $reason !== false ) {
				return;
			}
			ds_check_post();
		}
	} else {
		$addons = array();
		$addons = apply_filters( 'ds_addons_get', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$options = ds_get_options();
					$stats = ds_get_stats();
					$post = get_post_variables();
					$reason = ds_load( $add, ds_get_ip(), $stats, $options );
					if ( $reason !== false ) {
						remove_filter( 'pre_user_login', 'ds_user_reg_filter', 1 );
						ds_log_bad( ds_get_ip(), $reason, $add[1], $add );
						return;
					}
				}
			}
		}
	}
	add_action( 'template_redirect', 'ds_check_404s' );
	add_action( 'ds_caught', 'ds_caught_action', 10, 2 );
	add_action( 'ds_ok', 'ds_ok', 10, 2 );
	$options = ds_get_options();
	if ( isset( $options['form_captcha_login'] ) and $options['form_captcha_login'] === 'Y' ) {
		add_action( 'login_form', 'ds_add_captcha' );
	}
	if ( isset( $options['form_captcha_registration'] ) and $options['form_captcha_registration'] === 'Y' ) {
		add_action( 'register_form', 'ds_add_captcha' );
	}
	if ( isset( $options['form_captcha_comment'] ) and $options['form_captcha_comment'] === 'Y' ) {
		add_action( 'comment_form_after_fields', 'ds_add_captcha' );
	}
}

// ============================================================================
// Core Loading Functions
// ============================================================================

function ds_require( $file ) {
	require_once( $file );
}

function ds_load( $file, $ip, &$stats = array(), &$options = array(), &$post = array() ) {
	if ( empty( $file ) ) {
		return false;
	}
	if ( !class_exists( 'ds_module' ) ) {
		require_once( 'classes/module.php' );
	}
	if ( is_array( $file ) ) {
		if ( !file_exists( $file[0] ) ) {
			return false;
		}
		$class = new $file[1]();
		$result = $class->process( $ip, $stats, $options, $post );
		$class = null;
		unset( $class );
		return $result;
	}
	$ppath = plugin_dir_path( __FILE__ ) . 'classes/';
	$fd = $ppath . $file . '.php';
	$fd = str_replace( "/", DIRECTORY_SEPARATOR, $fd );
	if ( !file_exists( $fd ) ) {
		$ppath = plugin_dir_path( __FILE__ ) . 'classes/';
		$class_file = str_replace( '_', '-', $file );
		$fd = $ppath . $class_file . '.php';
		$fd = str_replace( "/", DIRECTORY_SEPARATOR, $fd );
	}
	if ( !file_exists( $fd ) ) {
		$ppath = plugin_dir_path( __FILE__ ) . 'modules/';
		$module_file = str_replace( '_', '-', $file );
		$fd = $ppath . $module_file . '.php';
		$fd = str_replace( "/", DIRECTORY_SEPARATOR, $fd );
	}
	if ( !file_exists( $fd ) ) {
		$ppath = plugin_dir_path( __FILE__ ) . 'modules/countries/';
		$country_file = str_replace( '_', '-', $file );
		$fd = $ppath . $country_file . '.php';
		$fd = str_replace( "/", DIRECTORY_SEPARATOR, $fd );
	}
	if ( !file_exists( $fd ) ) {
		return false;
	}
	require_once( $fd );
	$class_name = str_replace( '-', '_', basename( $fd, '.php' ) );
	$class = new $class_name();
	$result = $class->process( $ip, $stats, $options, $post );
	$class = null;
	unset( $class );
	return $result;
}

function ds_load_module() {
	if ( !class_exists( 'ds_module' ) ) {
		require_once( 'classes/module.php' );
	}
}

// ============================================================================
// Options & Stats Management
// ============================================================================

function ds_get_options() {
	$options = get_option( 'ds_options' );
	if ( !empty( $options ) && is_array( $options ) && array_key_exists( 'version', $options ) && $options['version'] == DS_VERSION ) {
		return $options;
	}
	ds_auto_migrate_from_stop_spammers();
	return ds_load( 'get_options', '' );
}

function ds_set_options( $options ) {
	update_option( 'ds_options', $options );
}

function ds_get_stats() {
	$stats = get_option( 'ds_stats' );
	if ( !empty( $stats ) && is_array( $stats ) && array_key_exists( 'version', $stats ) && $stats['version'] == DS_VERSION ) {
		return $stats;
	}
	return ds_load( 'get_stats', '' );
}

function ds_set_stats( &$stats, $addon = array() ) {
	if ( empty( $addon ) || !is_array( $addon ) ) {
		if ( $stats['spam_count'] == 0 || empty( $stats['spam_date'] ) ) {
			$stats['spam_date'] = gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		}
		if ( $stats['spam_multisite_count'] == 0 || empty( $stats['spam_multisite_date'] ) ) {
			$stats['spam_multisite_date'] = gmdate( 'Y/m/d', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		}
	} else {
		$addonstats = array();
		if ( array_key_exists( 'addonstats', $stats ) ) {
			$addonstats = $stats['addonstats'];
		}
		$addstats = array();
		if ( array_key_exists( $addon[1], $addonstats ) ) {
			$addstats = $addonstats[$addon[1]];
		} else {
			$addstats = array( 0, $addon );
		}
		$addstats[0] ++;
		$addonstats[$addon[1]] = $addstats;
		$stats['addonstats'] = $addonstats;
	}
	update_option( 'ds_stats', $stats );
}

// ============================================================================
// IP & User Functions
// ============================================================================

function ds_get_ip() {
	$ip = '';
	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}
	return $ip;
}

function ds_new_user_ip( $user_id ) {
	$ip = ds_get_ip();
	update_user_meta( $user_id, 'signup_ip', $ip );
}

function ds_log_user_ip( $user_login = "", $user = "" ) {
	if ( empty( $user ) ) {
		return;
	}
	if ( empty( $user_login ) ) {
		return;
	}
	if ( !isset( $user->ID ) ) {
		return;
	}
	$user_id = $user->ID;
	$ip = ds_get_ip();
	$oldip = get_user_meta( $user_id, 'signup_ip', true );
	if ( empty( $oldip ) || $ip != $oldip ) {
		update_user_meta( $user_id, 'signup_ip', $ip );
	}
}

function ds_sfs_ip_column_head( $column_headers ) {
	$column_headers['signup_ip'] = 'IP Address';
	return $column_headers;
}

// ============================================================================
// Check Functions
// ============================================================================

function ds_check_white() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$answer = ds_load( 'check_allow_list', ds_get_ip(), $stats, $options, $post );
	return $answer;
}

function ds_check_white_block() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$post['block'] = true;
	$answer = ds_load( 'check_allow_list', ds_get_ip(), $stats, $options, $post );
	return $answer;
}

function ds_check_post() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$ret = ds_load( 'check_post', ds_get_ip(), $stats, $options, $post );
	return $ret;
}

function ds_check_site_get() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$ret = ds_load( 'check_site_get', ds_get_ip(), $stats, $options, $post );
	return $ret;
}

function ds_check_404s() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$ret = ds_load( 'check_404s', ds_get_ip(), $stats, $options );
	return $ret;
}

// ============================================================================
// Logging Functions
// ============================================================================

function ds_log_good( $ip, $reason, $check, $addon = array() ) {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$post['reason'] = $reason;
	$post['check'] = $check;
	$post['addon'] = $addon;
	return ds_load( 'log_good', ds_get_ip(), $stats, $options, $post );
}

function ds_log_bad( $ip, $reason, $check, $addon = array() ) {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$post['reason'] = $reason;
	$post['check'] = $check;
	$post['addon'] = $addon;
	return ds_load( 'log_bad', ds_get_ip(), $stats, $options, $post );
}

function ds_log_akismet() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	if ( $options['check_akismet'] != 'Y' ) {
		return false;
	}
	$reason = ds_check_white();
	if ( $reason !== false ) {
		return;
	}
	$post = get_post_variables();
	$post['reason'] = esc_html__( 'from Akismet', 'dam-spam' );
	$post['check'] = 'check_akismet';
	$answer = ds_load( 'log_bad', ds_get_ip(), $stats, $options, $post );
	return $answer;
}

// ============================================================================
// CAPTCHA Functions
// ============================================================================

function ds_add_captcha() {
	$options = ds_get_options();
	$html = '';
	switch ( $options['check_captcha'] ) {
		case 'G':
			wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1', false );
			$recaptchaapisite = $options['recaptchaapisite'];
			$html = '<input type="hidden" name="recaptcha" value="recaptcha">';
			$html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $recaptchaapisite ) . '"></div>';
		break;
		case 'H':
			wp_enqueue_script( 'hcaptcha', 'https://hcaptcha.com/1/api.js', array(), '1', false );
			$hcaptchaapisite = $options['hcaptchaapisite'];
			$html = '<input type="hidden" name="h-captcha" value="h-captcha">';
			$html .= '<div class="h-captcha" data-sitekey="' . esc_attr( $hcaptchaapisite ) . '"></div>';
		break;
		case 'S':
			$solvmediaapivchallenge = $options['solvmediaapivchallenge'];
			wp_enqueue_script( 'solvmedia', 'https://api-secure.solvemedia.com/papi/challenge.script?k=' . $solvmediaapivchallenge, array(), '1', false );
			$html = '<noscript>';
			$html .= '<iframe src="https://api-secure.solvemedia.com/papi/challenge.noscript?k=' . esc_attr( $solvmediaapivchallenge ) . '" height="300" width="500" frameborder="0"></iframe><br>';
			$html .= '<textarea name="adcopy_challenge" rows="3" cols="40"></textarea>';
			$html .= '<input type="hidden" name="adcopy_response" value="manual_challenge">';
			$html .= '</noscript>';
		break;
	}
	$allowed_html = array(
		'input' => array(
			'type' => array(),
			'name' => array(),
			'value' => array()
		),
		'div' => array(
			'class' => array(),
			'data-sitekey' => array()
		),
		'noscript' => array(),
		'iframe' => array(
			'src' => array(),
			'height' => array(),
			'width' => array(),
			'frameborder' => array()
		),
		'textarea' => array(
			'name' => array(),
			'rows' => array(),
			'cols' => array()
		),
		'br' => array()
	);
	echo wp_kses( $html, $allowed_html );
}

function ds_captcha_verify() {
	static $verified = null;
	if ( $verified !== null ) {
		return $verified;
	}
	global $wpdb;
	$options = ds_get_options();
	$ip = ds_get_ip();
	switch ( $options['check_captcha'] ) {
		case 'G':
			if ( !array_key_exists( 'recaptcha', $_POST ) || empty( $_POST['recaptcha'] ) || !array_key_exists( 'g-recaptcha-response', $_POST ) ) {
				$verified = esc_html__( 'Error: Please complete the reCAPTCHA.', 'dam-spam' );
				return $verified;
			}
			$recaptchaapisecret = $options['recaptchaapisecret'];
			$recaptchaapisite = $options['recaptchaapisite'];
			if ( empty( $recaptchaapisecret ) || empty( $recaptchaapisite ) ) {
				$verified = esc_html__( 'Error: reCAPTCHA keys are not set.', 'dam-spam' );
				return $verified;
			}
			$g = isset( $_POST['g-recaptcha-response'] ) ? sanitize_textarea_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
			$response = wp_safe_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
				'body' => array(
					'secret' => $recaptchaapisecret,
					'response' => $g,
					'remoteip' => $ip
				)
			) );
			if ( is_wp_error( $response ) ) {
				$verified = esc_html__( 'Error: reCAPTCHA connection failed.', 'dam-spam' );
				return $verified;
			}
			$parsed = json_decode( wp_remote_retrieve_body( $response ) );
			if ( !isset( $parsed->success ) || $parsed->success !== true ) {
				$verified = esc_html__( 'Error: reCAPTCHA verification failed.', 'dam-spam' );
				return $verified;
			}
		break;
		case 'H':
			if ( !array_key_exists( 'h-captcha', $_POST ) || empty( $_POST['h-captcha'] ) || !array_key_exists( 'h-captcha-response', $_POST ) ) {
				$verified = esc_html__( 'Error: Please complete the hCaptcha.', 'dam-spam' );
				return $verified;
			}
			$hcaptchaapisecret = $options['hcaptchaapisecret'];
			$hcaptchaapisite = $options['hcaptchaapisite'];
			if ( empty( $hcaptchaapisecret ) || empty( $hcaptchaapisite ) ) {
				$verified = esc_html__( 'Error: hCaptcha keys are not set.', 'dam-spam' );
				return $verified;
			}
			$h = isset( $_POST['h-captcha-response'] ) ? sanitize_textarea_field( wp_unslash( $_POST['h-captcha-response'] ) ) : '';
			$response = wp_safe_remote_post( 'https://hcaptcha.com/siteverify', array(
				'body' => array(
					'secret' => $hcaptchaapisecret,
					'response' => $h,
					'remoteip' => $ip
				)
			) );
			if ( is_wp_error( $response ) ) {
				$verified = esc_html__( 'Error: hCaptcha connection failed.', 'dam-spam' );
				return $verified;
			}
			$parsed = json_decode( wp_remote_retrieve_body( $response ) );
			if ( !isset( $parsed->success ) || $parsed->success !== true ) {
				$verified = esc_html__( 'Error: hCaptcha verification failed.', 'dam-spam' );
				return $verified;
			}
		break;
		case 'S':
			if ( !array_key_exists( 'adcopy_challenge', $_POST ) || empty( $_POST['adcopy_challenge'] ) ) {
				$verified = esc_html__( 'Error: Please complete the CAPTCHA.', 'dam-spam' );
				return $verified;
			}
			$solvmediaapivchallenge = $options['solvmediaapivchallenge'];
			$solvmediaapiverify = $options['solvmediaapiverify'];
			if ( empty( $solvmediaapivchallenge ) || empty( $solvmediaapiverify ) ) {
				$verified = esc_html__( 'Error: Solve Media keys are not set.', 'dam-spam' );
				return $verified;
			}
			$adcopy_challenge = isset( $_POST['adcopy_challenge'] ) ? sanitize_textarea_field( wp_unslash( $_POST['adcopy_challenge'] ) ) : '';
			$adcopy_response = isset( $_POST['adcopy_response'] ) ? sanitize_textarea_field( wp_unslash( $_POST['adcopy_response'] ) ) : '';
			$response = wp_safe_remote_post( 'https://verify.solvemedia.com/papi/verify/', array(
				'body' => array(
					'privatekey' => $solvmediaapiverify,
					'challenge' => $adcopy_challenge,
					'response' => $adcopy_response,
					'remoteip' => $ip
				)
			) );
			if ( is_wp_error( $response ) ) {
				$verified = esc_html__( 'Error: Solve Media connection failed.', 'dam-spam' );
				return $verified;
			}
			$result = wp_remote_retrieve_body( $response );
			if ( strpos( $result, 'true' ) === false ) {
				$verified = esc_html__( 'Error: Solve Media verification failed.', 'dam-spam' );
				return $verified;
			}
		break;
	}
	$verified = true;
	return true;
}

add_filter( 'authenticate', 'ds_login_captcha_verify', 99 );
function ds_login_captcha_verify( $user ) {
	$options = ds_get_options();
	if ( !isset( $options['form_captcha_login'] ) or $options['form_captcha_login'] !== 'Y' ) {
		return $user;	
	}
	$response = ds_captcha_verify();
	if ( $response !== true ) {
		return new WP_Error( 'ds_captcha_error', $response );
	}
	return $user;
}

add_filter( 'registration_errors', 'ds_registration_captcha_verify', 10 );
function ds_registration_captcha_verify( $errors ) {
	$options = ds_get_options();
	if ( !isset( $options['form_captcha_registration'] ) or $options['form_captcha_registration'] !== 'Y' ) {
		return $errors;	
	}
	$response = ds_captcha_verify();
	if ( $response !== true ) {
		$errors->add( 'ds_captcha_error', $response );
	}
	return $errors;
}

add_filter( 'pre_comment_approved', 'ds_comment_captcha_verify', 99, 1 );
function ds_comment_captcha_verify( $approved ) {
	$options = ds_get_options();
	if ( !isset( $options['form_captcha_comment'] ) or $options['form_captcha_comment'] !== 'Y' ) {
		return $approved;	
	}
	$response = ds_captcha_verify();
	if ( $response !== true ) {
		return new WP_Error( 'ds_captcha_error', $response, 403 );
	}
	return $approved;
}

// ============================================================================
// User Registration & Authentication
// ============================================================================

function ds_user_reg_filter( $user_login ) {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	$post['author'] = $user_login;
	$post['addon'] = 'check_Register';
	if ( $options['filter_registrations'] != 'Y' ) {
		remove_filter( 'pre_user_login', 'ds_user_reg_filter', 1 );
		return $user_login;
	}
	$reason = ds_load( 'check_bad_cache', ds_get_ip(), $stats, $options, $post );
	if ( $reason !== false ) {
		$reject_message = $options['reject_message'];
		$post['reason'] = esc_html__( 'Failed Registration: Bad Cache', 'dam-spam' );
		$host['check'] = 'check_bad_cache';
		$answer = ds_load( 'log_bad', ds_get_ip(), $stats, $options, $post );
		wp_die( esc_html( $reject_message ), esc_html__( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
		exit();
	}
	$reason = ds_load( 'check_periods', ds_get_ip(), $stats, $options, $post );
	if ( $reason !== false ) { 
		wp_die( 'Registration Access Blocked', esc_html__( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
	}
	$reason = ds_check_white();
	if ( $reason !== false ) {
		$post['reason'] = esc_html__( 'Passed Registration: ', 'dam-spam' ) . $reason;
		$answer = ds_load( 'log_good', ds_get_ip(), $stats, $options, $post );
		return $user_login;
	}
	$ret = ds_load( 'check_post', ds_get_ip(), $stats, $options, $post );
	$post['reason'] = esc_html__( 'Passed Registration ', 'dam-spam' ) . $ret;
	$answer = ds_load( 'log_good', ds_get_ip(), $stats, $options, $post );
	return $user_login;
}

add_action( 'wp', 'ds_login_redirect' );
function ds_login_redirect() {
	global $pagenow, $post;
	$options = ds_get_options();
	if ( get_option( 'ds_enable_custom_login', '' ) and $options['ds_private_mode'] == "Y" and ( !is_user_logged_in() && ( !$post || $post->post_name != 'login' ) ) ) {
		wp_redirect( site_url( 'login' ) );
		exit;
	} elseif ( $options['ds_private_mode'] == "Y" and ( !is_user_logged_in() && ( $pagenow != 'wp-login.php' and ( !$post || $post->post_name != 'login' ) ) ) ) {
		auth_redirect();
	}
}

function ds_sfs_check_admin() {
	ds_sfs_reg_add_user_to_allowlist();
}

function ds_sfs_reg_add_user_to_allowlist() {
	$options = ds_get_options();
	$stats = ds_get_stats();
	$post = get_post_variables();
	return ds_load( 'add_to_allow_list', ds_get_ip(), $stats, $options );
}

// ============================================================================
// Helper & Utility Functions
// ============================================================================

function get_post_variables() {
	$answer = array(
		'email' => '',
		'author' => '',
		'pwd' => '',
		'comment' => '',
		'subject' => '',
		'url' => ''
	);
	if ( empty( $_POST ) || !is_array( $_POST ) ) {
		return $answer;
	}
	$p = $_POST;
	$search = array(
		'email' => array(
			'email',
			'e-mail',
			'user_email',
			'email-address',
			'your-email'
		),
		'author' => array(
			'author',
			'name',
			'username',
			'user_login',
			'signup_for',
			'log',
			'user',
			'_id',
			'your-name'
		),
		'pwd' => array(
			'pwd',
			'password',
			'psw',
			'pass',
			'secret'
		),
		'comment' => array(
			'comment',
			'message',
			'reply',
			'body',
			'excerpt',
			'your-message'
		),
		'subject' => array(
			'subject',
			'subj',
			'topic',
			'your-subject'
		),
		'url' => array(
			'url',
			'link',
			'site',
			'website',
			'blog_name',
			'blogname',
			'your-website'
		)
	);
	$emfound = false;
	foreach ( $search as $var => $sa ) {
		foreach ( $sa as $srch ) {
			foreach ( $p as $pkey => $pval ) {
				if ( stripos( $pkey, $srch ) !== false ) {
					if ( is_array( $pval ) ) {
						$pval = print_r( $pval, true );
					}
					$answer[$var] = $pval;
					break;
				}
			}
			if ( !empty( $answer[$var] ) ) {
				break;
			}
		}
		if ( empty( $answer[$var] ) && $var == 'email' ) {
			foreach ( $p as $pkey => $pval ) {
				if ( stripos( $pkey, 'input_' ) ) {
					if ( is_array( $pval ) ) {
						$pval = print_r( $pval, true );
					}
					if ( strpos( $pval, '@' ) !== false && strrpos( $pval, '.' ) > strpos( $pval, '@' ) ) {
						$answer[$var] = $pval;
						break;
					}
				}
			}
		}
	}
	foreach ( $answer as $key => $value ) {
		$answer[$key] = $value;
	}
	if ( strlen( $answer['email'] ) > 80 ) {
		$answer['email'] = substr( $answer['email'], 0, 77 ) . '...';
	}
	if ( strlen( $answer['author'] ) > 80 ) {
		$answer['author'] = substr( $answer['author'], 0, 77 ) . '...';
	}
	if ( strlen( $answer['pwd'] ) > 32 ) {
		$answer['pwd'] = substr( $answer['pwd'], 0, 29 ) . '...';
	}
	if ( strlen( $answer['comment'] ) > 999 ) {
		$answer['comment'] = substr( $answer['comment'], 0, 996 ) . '...';
	}
	if ( strlen( $answer['subject'] ) > 80 ) {
		$answer['subject'] = substr( $answer['subject'], 0, 77 ) . '...';
	}
	if ( strlen( $answer['url'] ) > 80 ) {
		$answer['url'] = substr( $answer['url'], 0, 77 ) . '...';
	}
	return $answer;
}

function really_clean( $s ) {
	if ( empty( $s ) ) {
		return '';
	}
	$ss = array_slice( unpack( "c*", "\0" . $s ), 1 );
	if ( empty( $ss ) ) {
		return $s;
	}
	$s = '';
	for ( $j = 0; $j < count( $ss ); $j ++ ) {
		if ( $ss[$j] < 127 && $ss[$j] > 31 ) {
			$s .= pack( 'C', $ss[$j] );
		}
	}
	return $s;
}

function ds_addons_d( $config = array() ) {
	return $config;
}

function ds_caught_action( $ip = '', $post = array() ) {}

function ds_ok( $ip = '', $post = array() ) {}

// ============================================================================
// Classes
// ============================================================================

class DSRegDate {
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}
	public function init() {
		add_filter( 'manage_users_columns', array( $this, 'users_columns' ) );
		add_action( 'manage_users_custom_column', array( $this, 'users_custom_column' ), 10, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this, 'users_sortable_columns' ) );
		add_filter( 'request', array( $this, 'users_orderby_column' ) );
	}
	public static function users_columns( $columns ) {
		$columns['registerdate'] = _x( 'Registered', 'user', 'dam-spam' );
		return $columns;
	}
	public static function users_custom_column( $value, $column_name, $user_id ) {
		global $mode;
		$mode = empty( $_REQUEST['mode'] ) ? 'list' : sanitize_text_field( wp_unslash( $_REQUEST['mode'] ) );
		if ( 'registerdate' != $column_name ) {
			return $value;
		} else {
			$user = get_userdata( $user_id );
			if ( is_multisite() && ( 'list' == $mode ) ) {
				$formatted_date = 'F jS, Y';
			} else {
				$formatted_date = 'F jS, Y \a\t g:i a';
			}
			$registered = strtotime( get_date_from_gmt( $user->user_registered ) );
			$registerdate = '<span>' . date_i18n( $formatted_date, $registered ) . '</span>' ;
			return $registerdate;
		}
	}
	public static function users_sortable_columns( $columns ) {
		$custom = array(
			'registerdate' => 'registered',
		);
		return wp_parse_args( $custom, $columns );
	}
	public static function users_orderby_column( $vars ) {
		if ( isset( $vars['orderby'] ) && 'registerdate' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'meta_key' => 'registerdate',
				'orderby' => 'meta_value'
			) );
		}
		return $vars;
	}
}
new DSRegDate();

// ============================================================================
// Required Files
// ============================================================================

require_once( 'includes/utilities.php' );

require_once( 'settings/advanced.php' );

?>