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
		__( 'Summary — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Summary', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'dam-spam', // $menu_slug,
		'ds_summary_menu' // $function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Protection Options — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Protection Options', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-options', // $menu_slug,
		'ds_options_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Allow Lists — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Allow Lists', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-allow-list', // $menu_slug,
		'ds_allow_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Block Lists — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Block Lists', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-block-list', // $menu_slug,
		'ds_block_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Challenge & Block — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Challenge & Block', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-challenge', // $menu_slug,
		'ds_challenges_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Web Services — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Web Services', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-webservices', // $menu_slug,
		'ds_webservices_menu'
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Cache — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Cache', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-cache', // $menu_slug,
		'ds_cache_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Log Report — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Log Report', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-reports', // $menu_slug,
		'ds_reports_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Diagnostics — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Diagnostics', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-diagnostics', // $menu_slug,
		'ds_diagnostics_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Cleanup — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Cleanup', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-maintenance', // $menu_slug,
		'ds_maintenance_menu' // function
	);
	add_submenu_page(
		'dam-spam', // plugins parent
		__( 'Advanced — Dam Spam', 'dam-spam' ), // $page_title,
		__( 'Advanced', 'dam-spam' ), // $menu_title,
		'manage_options', // $capability,
		'ds-advanced', // $menu_slug,
		'ds_advanced_menu' // function
	);
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		add_submenu_page(
			'dam-spam', // plugins parent
			__( 'Multisite — Dam Spam', 'dam-spam' ), // $page_title,
			__( 'Network', 'dam-spam' ), // $menu_title,
			'manage_options', // $capability,
			'ds-network', // $menu_slug,
			'ds_network_menu' // function
		);
	}
}

function ds_summary_menu() {
	include_setting( "summary.php" );
}

function ds_options_menu() {
	include_setting( "options.php" );
}

function ds_allow_menu() {
	include_setting( "allow.php" );
}

function ds_block_menu() {
	include_setting( "block.php" );
}

function ds_challenges_menu() {
	include_setting( "challenge.php" );
}

function ds_webservices_menu() {
	include_setting( "webservices.php" );
}

function ds_cache_menu() {
	include_setting( "cache.php" );
}

function ds_reports_menu() {
	include_setting( "reports.php" );
}

function ds_maintenance_menu() {
	include_setting( "maintenance.php" );
}

function ds_diagnostics_menu() {
	include_setting( "diagnostics.php" );
}

function ds_network_menu() {
	include_setting( "network.php" );
}

function include_setting( $file ) {
	sfs_errorsonoff();
	$ppath = plugin_dir_path( __FILE__ );
	if ( file_exists( $ppath . $file ) ) {
		require_once( $ppath . $file );
	} else {
		_e( '<br>Missing File: ' . $ppath, $file . ' <br>', 'dam-spam' );
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
