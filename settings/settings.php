<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

function ds_admin_menu_l() {
	add_menu_page(
		'Dam Spam', // $page_title,
		'Dam Spam', // $menu_title,
		'manage_options', // $capability,
		'dam-spam', // $menu_slug,
		'ds_summary_menu', // $function
		'dashicons-shield-alt'
	);
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
		return;
	}
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Summary — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Summary', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'dam-spam', // $menu_slug,
		'ds_summary_menu' // $function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Protections — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Protections', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-protections', // $menu_slug,
		'ds_protections_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Allowed — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Allowed', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-allowed', // $menu_slug,
		'ds_allowed_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Blocked — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Blocked', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-blocked', // $menu_slug,
		'ds_blocked_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Challenges — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Challenges', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-challenges', // $menu_slug,
		'ds_challenges_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'APIs — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'APIs', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-apis', // $menu_slug,
		'ds_apis_menu'
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Cache — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Cache', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-cache', // $menu_slug,
		'ds_cache_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Logs — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Logs', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-logs', // $menu_slug,
		'ds_logs_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Diagnostics — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Diagnostics', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-diagnostics', // $menu_slug,
		'ds_diagnostics_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Cleanup — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Cleanup', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-cleanup', // $menu_slug,
		'ds_cleanup_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		esc_html__( 'Advanced — Dam Spam', 'dam-spam' ), // $page_title,
		esc_html__( 'Advanced', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-advanced', // $menu_slug,
		'ds_advanced_menu' // function
	);
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		add_submenu_page(
			'dam-spam', // plugins parent
			esc_html__( 'Multisite — Dam Spam', 'dam-spam' ), // $page_title,
			esc_html__( 'Multisite', 'dam-spam' ), // $menu_title,
			'manage_options', // $capability,
			'ds-multisite', // $menu_slug,
			'ds_multisite_menu' // function
		);
	}
}

function ds_summary_menu() {
	include_setting( "summary.php" );
}

function ds_protections_menu() {
	include_setting( "protections.php" );
}

function ds_allowed_menu() {
	include_setting( "allowed.php" );
}

function ds_blocked_menu() {
	include_setting( "blocked.php" );
}

function ds_challenges_menu() {
	include_setting( "challenges.php" );
}

function ds_apis_menu() {
	include_setting( "apis.php" );
}

function ds_cache_menu() {
	include_setting( "cache.php" );
}

function ds_logs_menu() {
	include_setting( "logs.php" );
}

function ds_cleanup_menu() {
	include_setting( "cleanup.php" );
}

function ds_diagnostics_menu() {
	include_setting( "diagnostics.php" );
}

function ds_multisite_menu() {
	include_setting( "multisite.php" );
}

function include_setting( $file ) {
	sfs_errorsonoff();
	$ppath = plugin_dir_path( __FILE__ );
	if ( file_exists( $ppath . $file ) ) {
		require_once( $ppath . $file );
	} else {
		printf( '<br>' . esc_html__( 'Missing File: %1$s %2$s', 'dam-spam' ), esc_html( $ppath, $file ) ) . '<br>';
	}
	sfs_errorsonoff( 'off' );
}

function ds_fix_post_vars() {
	if ( !empty( $_POST ) ) {
		$keys = isset( $_POST ) ? ( array ) array_keys( $_POST ) : array();
		foreach ( $keys as $key ) {
			try {
				$key = sanitize_key( $key ); 
				if ( is_string( $_POST[$key] ) ) {
					if ( strpos( $_POST[$key], "\n" ) !== false ) {
						$val2 = sanitize_textarea_field( $_POST[$key] );
					} else {
						$val2 = sanitize_text_field( $_POST[$key] );
					}
					$_POST[$key] = $val2;
				}
			} catch ( Exception $e ) {}
		}
	}
}

?>
