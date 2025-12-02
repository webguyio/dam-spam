<?php
/*
Plugin Name: Dam Spam
Plugin URI: https://damspam.com/
Description: Fork of Stop Spammers.
Version: 0.7
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

define( 'DAM_SPAM_VERSION', '0.7' );
define( 'DAM_SPAM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DAM_SPAM_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );

// ============================================================================
// Admin UI Functions
// ============================================================================

add_filter( 'admin_body_class', 'dam_spam_body_class' );
function dam_spam_body_class( $classes ) {
	$screen = get_current_screen();
	if ( strpos( $screen->id, 'dam-spam' ) !== false || strpos( $screen->id, 'dam-spam-' ) === 0 ) {
		$classes .= ' dam-spam';
	}
	return $classes;
}

add_action( 'admin_print_styles', 'dam_spam_styles' );
function dam_spam_styles() {
	wp_enqueue_style( 'dam-spam-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), DAM_SPAM_VERSION );
}

add_action( 'admin_notices', 'dam_spam_admin_notice' );
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Only counting GET params for URL building, not processing form data
function dam_spam_admin_notice() {
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
	
	if ( ! get_user_meta( $user_id, 'dam_spam_notice_dismissed_2025' ) && current_user_can( 'manage_options' ) ) {
		$dismiss_url = wp_nonce_url( $admin_url . $param . 'dismiss', 'dismiss_notice' );
		echo '<div class="notice notice-info"><p><a href="' . esc_url( $dismiss_url ) . '" class="alignright" style="text-decoration:none"><big>✕</big></a><big><strong>' . esc_html__( 'Thank you for helping us Dam Spam!', 'dam-spam' ) . '</strong></big><p><a href="https://damspam.com/donations" class="button-primary" style="border-color:#c6ac40;background:#c6ac40" target="_blank">' . esc_html__( 'I Need Your Help', 'dam-spam' ) . '</a></p></div>';
	}
}

add_action( 'admin_init', 'dam_spam_notice_dismissed' );
function dam_spam_notice_dismissed() {
	$user_id = get_current_user_id();
	if ( isset( $_GET['dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dismiss_notice' ) ) {
		update_user_meta( $user_id, 'dam_spam_notice_dismissed_2025', 'true' );
	}
}

add_action( 'admin_notices', 'dam_spam_wc_admin_notice' );
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Only counting GET params for URL building, not processing form data
function dam_spam_wc_admin_notice() {
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
		if ( !get_user_meta( $user_id, 'dam_spam_wc_notice_dismissed' ) && current_user_can( 'manage_options' ) ) {
			echo '<div class="notice notice-info"><p style="color:purple"><a href="' . esc_url( $admin_url . $param . 'dam-spam-wc-dismiss' ) . '" class="alignright" style="text-decoration:none"><big>✕</big></a>' . esc_html__( '<big><strong>WooCommerce Detected</strong></big> | We recommend <a href="admin.php?page=dam-spam-protections">adjusting these options</a> if you experience any issues using WooCommerce and Dam Spam together.', 'dam-spam' ) . '</p></div>';
		}
	}
}

add_action( 'admin_init', 'dam_spam_wc_notice_dismissed' );
function dam_spam_wc_notice_dismissed() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		$user_id = get_current_user_id();
		if ( isset( $_GET['dam-spam-wc-dismiss'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'dismiss_wc_notice' ) ) {
			add_user_meta( $user_id, 'dam_spam_wc_notice_dismissed', 'true', true );
		}
	}
}

function dam_spam_admin_menu() {
	if ( !function_exists( 'dam_spam_admin_menu_l' ) ) {
		dam_spam_require( 'settings/settings.php' );
	}
	dam_spam_admin_menu_l();
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dam_spam_summary_link' );
function dam_spam_summary_link( $links ) {
	$links = array_merge( array( '<a href="' . admin_url( 'admin.php?page=dam-spam' ) . '">' . esc_html__( 'Settings', 'dam-spam' ) . '</a>' ), $links );
	return $links;
}

// ============================================================================
// Main Initialization
// ============================================================================

add_action( 'init', 'dam_spam_init', 0 );
add_filter( 'dam_spam_addons_allow', 'dam_spam_addons_d', 0 );
add_filter( 'dam_spam_addons_block', 'dam_spam_addons_d', 0 );
add_filter( 'dam_spam_addons_get', 'dam_spam_addons_d', 0 );
function dam_spam_init() {
	remove_action( 'init', 'dam_spam_init' );
	add_filter( 'pre_user_login', 'dam_spam_user_reg_filter', 1, 1 );
	add_action( 'akismet_spam_caught', 'dam_spam_log_akismet' );
	$muswitch = 'N';
	if ( is_multisite() ) {
		switch_to_blog( 1 );
		$muswitch = get_option( 'dam_spam_muswitch' );
		if ( $muswitch != 'N' ) {
			$muswitch = 'Y';
		}
		restore_current_blog();
		if ( $muswitch == 'Y' ) {
			define( 'DAM_SPAM_MU', $muswitch );
			dam_spam_require( 'includes/multisite.php' );
			dam_spam_global_setup();
		}
	} else {
		define( 'DAM_SPAM_MU', $muswitch );
	}
	if ( wp_doing_ajax() && is_user_logged_in() && current_user_can( 'edit_posts' ) && !is_admin() ) {
		return;
	}
	if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
		dam_spam_require( 'includes/admins.php' );
	}
	if ( is_user_logged_in() ) {
		remove_filter( 'pre_user_login', 'dam_spam_user_reg_filter', 1 );
		return;
	}
	add_action( 'user_register', 'dam_spam_new_user_ip' );
	add_action( 'wp_login', 'dam_spam_log_user_ip', 10, 2 );
	if ( isset( $_POST ) && !empty( $_POST ) ) {
		if ( array_key_exists( 'dam_spam_block', $_POST ) && array_key_exists( 'kn', $_POST ) ) {
			if ( !empty( $_POST['kn'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kn'] ) ), 'dam_spam_block' ) ) {
				$options = dam_spam_get_options();
				$stats = dam_spam_get_stats();
				$post = dam_spam_get_post_variables();
				dam_spam_load( 'challenge', dam_spam_get_ip(), $stats, $options, $post );
				return;
			}
		}
		$post = dam_spam_get_post_variables();
		if ( !empty( $post['email'] ) || !empty( $post['author'] ) || !empty( $post['comment'] ) ) {
			$reason = dam_spam_check_white();
			if ( $reason !== false ) {
				return;
			}
			dam_spam_check_post();
		}
	} else {
		$addons = array();
		$addons = apply_filters( 'dam_spam_addons_get', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$options = dam_spam_get_options();
					$stats = dam_spam_get_stats();
					$post = dam_spam_get_post_variables();
					$reason = dam_spam_load( $add, dam_spam_get_ip(), $stats, $options );
					if ( $reason !== false ) {
						remove_filter( 'pre_user_login', 'dam_spam_user_reg_filter', 1 );
						dam_spam_log_bad( dam_spam_get_ip(), $reason, $add[1], $add );
						return;
					}
				}
			}
		}
	}
	add_action( 'template_redirect', 'dam_spam_check_404s' );
	add_action( 'dam_spam_caught', 'dam_spam_caught_action', 10, 2 );
	add_action( 'dam_spam_ok', 'dam_spam_ok', 10, 2 );
	$options = dam_spam_get_options();
	if ( isset( $options['form_captcha_login'] ) and $options['form_captcha_login'] === 'Y' ) {
		add_action( 'login_form', 'dam_spam_add_captcha' );
	}
	if ( isset( $options['form_captcha_registration'] ) and $options['form_captcha_registration'] === 'Y' ) {
		add_action( 'register_form', 'dam_spam_add_captcha' );
	}
	if ( isset( $options['form_captcha_comment'] ) and $options['form_captcha_comment'] === 'Y' ) {
		add_action( 'comment_form_after_fields', 'dam_spam_add_captcha' );
	}
}

// ============================================================================
// Core Loading Functions
// ============================================================================

function dam_spam_require( $file ) {
	require_once( $file );
}

function dam_spam_load( $file, $ip, &$stats = array(), &$options = array(), &$post = array() ) {
	if ( empty( $file ) ) {
		return false;
	}
	if ( !class_exists( 'dam_spam_module' ) ) {
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
		return false;
	}
	require_once( $fd );
	$class_name = 'dam_spam_' . str_replace( '-', '_', basename( $fd, '.php' ) );
	$class = new $class_name();
	$result = $class->process( $ip, $stats, $options, $post );
	$class = null;
	unset( $class );
	return $result;
}

function dam_spam_load_module() {
	if ( !class_exists( 'dam_spam_module' ) ) {
		require_once( 'classes/module.php' );
	}
}

// ============================================================================
// Options & Stats Management
// ============================================================================

function dam_spam_get_options() {
	$options = get_option( 'dam_spam_options' );
	if ( !empty( $options ) && is_array( $options ) && array_key_exists( 'version', $options ) && $options['version'] == DAM_SPAM_VERSION ) {
		return $options;
	}
	dam_spam_auto_migrate_from_old_dam_spam();
	dam_spam_auto_migrate_from_stop_spammers();
	return dam_spam_load( 'get_options', '' );
}

function dam_spam_set_options( $options ) {
	update_option( 'dam_spam_options', $options );
}

function dam_spam_get_stats() {
	$stats = get_option( 'dam_spam_stats' );
	if ( !empty( $stats ) && is_array( $stats ) && array_key_exists( 'version', $stats ) && $stats['version'] == DAM_SPAM_VERSION ) {
		return $stats;
	}
	return dam_spam_load( 'get_stats', '' );
}

function dam_spam_set_stats( &$stats, $addon = array() ) {
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
	update_option( 'dam_spam_stats', $stats );
}

// ============================================================================
// IP & User Functions
// ============================================================================

function dam_spam_get_ip() {
	$ip = '';
	if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	}
	return $ip;
}

function dam_spam_new_user_ip( $user_id ) {
	$ip = dam_spam_get_ip();
	update_user_meta( $user_id, 'signup_ip', $ip );
}

function dam_spam_log_user_ip( $user_login = "", $user = "" ) {
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
	$ip = dam_spam_get_ip();
	$oldip = get_user_meta( $user_id, 'signup_ip', true );
	if ( empty( $oldip ) || $ip != $oldip ) {
		update_user_meta( $user_id, 'signup_ip', $ip );
	}
}

function dam_spam_sfs_ip_column_head( $column_headers ) {
	$column_headers['signup_ip'] = 'IP Address';
	return $column_headers;
}

// ============================================================================
// Check Functions
// ============================================================================

function dam_spam_check_white() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$answer = dam_spam_load( 'check_allow_list', dam_spam_get_ip(), $stats, $options, $post );
	return $answer;
}

function dam_spam_check_white_block() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$post['block'] = true;
	$answer = dam_spam_load( 'check_allow_list', dam_spam_get_ip(), $stats, $options, $post );
	return $answer;
}

function dam_spam_check_post() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$ret = dam_spam_load( 'check_post', dam_spam_get_ip(), $stats, $options, $post );
	return $ret;
}

function dam_spam_check_site_get() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$ret = dam_spam_load( 'check_site_get', dam_spam_get_ip(), $stats, $options, $post );
	return $ret;
}

function dam_spam_check_404s() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$ret = dam_spam_load( 'check_404s', dam_spam_get_ip(), $stats, $options );
	return $ret;
}

// ============================================================================
// Logging Functions
// ============================================================================

function dam_spam_log_good( $ip, $reason, $check, $addon = array() ) {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$post['reason'] = $reason;
	$post['check'] = $check;
	$post['addon'] = $addon;
	return dam_spam_load( 'log_good', dam_spam_get_ip(), $stats, $options, $post );
}

function dam_spam_log_bad( $ip, $reason, $check, $addon = array() ) {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$post['reason'] = $reason;
	$post['check'] = $check;
	$post['addon'] = $addon;
	return dam_spam_load( 'log_bad', dam_spam_get_ip(), $stats, $options, $post );
}

function dam_spam_log_akismet() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	if ( $options['check_akismet'] != 'Y' ) {
		return false;
	}
	$reason = dam_spam_check_white();
	if ( $reason !== false ) {
		return;
	}
	$post = dam_spam_get_post_variables();
	$post['reason'] = esc_html__( 'from Akismet', 'dam-spam' );
	$post['check'] = 'check_akismet';
	$answer = dam_spam_load( 'log_bad', dam_spam_get_ip(), $stats, $options, $post );
	return $answer;
}

// ============================================================================
// CAPTCHA Functions
// ============================================================================

function dam_spam_add_captcha() {
	$options = dam_spam_get_options();
	$html = '';
	switch ( $options['check_captcha'] ) {
		case 'G':
			wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), '1', array(
				'strategy'  => 'async',
				'in_footer' => true,
			) );
			$recaptchaapisite = $options['recaptchaapisite'];
			$html = '<input type="hidden" name="recaptcha" value="recaptcha">';
			$html .= '<div class="g-recaptcha" data-sitekey="' . esc_attr( $recaptchaapisite ) . '"></div>';
		break;
		case 'H':
			wp_enqueue_script( 'hcaptcha', 'https://hcaptcha.com/1/api.js', array(), '1', array(
				'strategy'  => 'async',
				'in_footer' => true,
			) );
			$hcaptchaapisite = $options['hcaptchaapisite'];
			$html = '<input type="hidden" name="h-captcha" value="h-captcha">';
			$html .= '<div class="h-captcha" data-sitekey="' . esc_attr( $hcaptchaapisite ) . '"></div>';
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

function dam_spam_captcha_verify() {
	static $verified = null;
	if ( $verified !== null ) {
		return $verified;
	}
	global $wpdb;
	$options = dam_spam_get_options();
	$ip = dam_spam_get_ip();
	switch ( $options['check_captcha'] ) {
		case 'G':
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking for response, shouldn't have nonce
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
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking for response, shouldn't have nonce
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
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking for response, shouldn't have nonce
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
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking for response, shouldn't have nonce
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
	}
	$verified = true;
	return true;
}

add_filter( 'authenticate', 'dam_spam_login_captcha_verify', 99 );
function dam_spam_login_captcha_verify( $user ) {
	$options = dam_spam_get_options();
	if ( !isset( $options['form_captcha_login'] ) or $options['form_captcha_login'] !== 'Y' ) {
		return $user;	
	}
	$response = dam_spam_captcha_verify();
	if ( $response !== true ) {
		return new WP_Error( 'dam_spam_captcha_error', $response );
	}
	return $user;
}

add_filter( 'registration_errors', 'dam_spam_registration_captcha_verify', 10 );
function dam_spam_registration_captcha_verify( $errors ) {
	$options = dam_spam_get_options();
	if ( !isset( $options['form_captcha_registration'] ) or $options['form_captcha_registration'] !== 'Y' ) {
		return $errors;	
	}
	$response = dam_spam_captcha_verify();
	if ( $response !== true ) {
		$errors->add( 'dam_spam_captcha_error', $response );
	}
	return $errors;
}

add_filter( 'pre_comment_approved', 'dam_spam_comment_captcha_verify', 99, 1 );
function dam_spam_comment_captcha_verify( $approved ) {
	$options = dam_spam_get_options();
	if ( !isset( $options['form_captcha_comment'] ) or $options['form_captcha_comment'] !== 'Y' ) {
		return $approved;	
	}
	$response = dam_spam_captcha_verify();
	if ( $response !== true ) {
		return new WP_Error( 'dam_spam_captcha_error', $response, 403 );
	}
	return $approved;
}

// ============================================================================
// User Registration & Authentication
// ============================================================================

function dam_spam_user_reg_filter( $user_login ) {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	$post['author'] = $user_login;
	$post['addon'] = 'check_Register';
	if ( $options['filter_registrations'] != 'Y' ) {
		remove_filter( 'pre_user_login', 'dam_spam_user_reg_filter', 1 );
		return $user_login;
	}
	$reason = dam_spam_load( 'check_bad_cache', dam_spam_get_ip(), $stats, $options, $post );
	if ( $reason !== false ) {
		$reject_message = $options['reject_message'];
		$post['reason'] = esc_html__( 'Failed Registration: Bad Cache', 'dam-spam' );
		$host['check'] = 'check_bad_cache';
		$answer = dam_spam_load( 'log_bad', dam_spam_get_ip(), $stats, $options, $post );
		wp_die( esc_html( $reject_message ), esc_html__( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
		exit();
	}
	$reason = dam_spam_load( 'check_periods', dam_spam_get_ip(), $stats, $options, $post );
	if ( $reason !== false ) { 
		wp_die( 'Registration Access Blocked', esc_html__( 'Login Access Blocked', 'dam-spam' ), array( 'response' => 403 ) );
	}
	$reason = dam_spam_check_white();
	if ( $reason !== false ) {
		$post['reason'] = esc_html__( 'Passed Registration: ', 'dam-spam' ) . $reason;
		$answer = dam_spam_load( 'log_good', dam_spam_get_ip(), $stats, $options, $post );
		return $user_login;
	}
	$ret = dam_spam_load( 'check_post', dam_spam_get_ip(), $stats, $options, $post );
	$post['reason'] = esc_html__( 'Passed Registration ', 'dam-spam' ) . $ret;
	$answer = dam_spam_load( 'log_good', dam_spam_get_ip(), $stats, $options, $post );
	return $user_login;
}

add_action( 'wp', 'dam_spam_login_redirect' );
function dam_spam_login_redirect() {
	global $pagenow, $post;
	$options = dam_spam_get_options();
	if ( get_option( 'dam_spam_enable_custom_login', '' ) and $options['dam_spam_private_mode'] == "Y" and ( !is_user_logged_in() && ( !$post || $post->post_name != 'login' ) ) ) {
		wp_safe_redirect( site_url( 'login' ) );
		exit;
	} elseif ( $options['dam_spam_private_mode'] == "Y" and ( !is_user_logged_in() && ( $pagenow != 'wp-login.php' and ( !$post || $post->post_name != 'login' ) ) ) ) {
		auth_redirect();
	}
}

function dam_spam_sfs_check_admin() {
	dam_spam_sfs_reg_add_user_to_allowlist();
}

function dam_spam_sfs_reg_add_user_to_allowlist() {
	$options = dam_spam_get_options();
	$stats = dam_spam_get_stats();
	$post = dam_spam_get_post_variables();
	return dam_spam_load( 'add_to_allow_list', dam_spam_get_ip(), $stats, $options );
}

// ============================================================================
// Helper & Utility Functions
// ============================================================================

function dam_spam_get_post_variables() {
	$answer = array(
		'email' => '',
		'author' => '',
		'pwd' => '',
		'comment' => '',
		'subject' => '',
		'url' => ''
	);
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking for raw post data, can't have nonce
	if ( empty( $_POST ) || !is_array( $_POST ) ) {
		return $answer;
	}
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Checking raw post data, can't have nonce
	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Raw data needed for spam detection, sanitized at output
	$p = array_map( 'wp_unslash', $_POST );
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
				$pkey = sanitize_key( $pkey );
				if ( stripos( $pkey, $srch ) !== false ) {
					if ( is_array( $pval ) ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Converting array to string for processing
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
				$pkey = sanitize_key( $pkey );
				if ( stripos( $pkey, 'input_' ) !== false ) {
					if ( is_array( $pval ) ) {
						// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Converting array to string for processing
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

function dam_spam_addons_d( $config = array() ) {
	return $config;
}

function dam_spam_caught_action( $ip = '', $post = array() ) {}

function dam_spam_ok( $ip = '', $post = array() ) {}

// ============================================================================
// Classes
// ============================================================================

class Dam_Spam_Reg_Date {
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
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Necessary for user registration date sorting
				'meta_key' => 'registerdate',
				'orderby' => 'meta_value'
			) );
		}
		return $vars;
	}
}
new Dam_Spam_Reg_Date();

// ============================================================================
// Required Files
// ============================================================================

require_once( 'includes/utilities.php' );

require_once( 'settings/advanced.php' );

?>