<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

function ds_admin_menu_l() {
	add_menu_page(
		'Dam Spam',
		'Dam Spam',
		'manage_options',
		'dam-spam',
		'ds_summary_menu',
		'dashicons-shield-alt'
	);
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'protect' ) ) {
		return;
	}
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Summary — Dam Spam', 'dam-spam' ),
		esc_html__( 'Summary', 'dam-spam' ),
		'manage_options',
		'dam-spam',
		'ds_summary_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Protections — Dam Spam', 'dam-spam' ),
		esc_html__( 'Protections', 'dam-spam' ),
		'manage_options',
		'ds-protections',
		'ds_protections_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Allowed — Dam Spam', 'dam-spam' ),
		esc_html__( 'Allowed', 'dam-spam' ),
		'manage_options',
		'ds-allowed',
		'ds_allowed_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Blocked — Dam Spam', 'dam-spam' ),
		esc_html__( 'Blocked', 'dam-spam' ),
		'manage_options',
		'ds-blocked',
		'ds_blocked_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Challenges — Dam Spam', 'dam-spam' ),
		esc_html__( 'Challenges', 'dam-spam' ),
		'manage_options',
		'ds-challenges',
		'ds_challenges_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'APIs — Dam Spam', 'dam-spam' ),
		esc_html__( 'APIs', 'dam-spam' ),
		'manage_options',
		'ds-apis',
		'ds_apis_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Cache — Dam Spam', 'dam-spam' ),
		esc_html__( 'Cache', 'dam-spam' ),
		'manage_options',
		'ds-cache',
		'ds_cache_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Logs — Dam Spam', 'dam-spam' ),
		esc_html__( 'Logs', 'dam-spam' ),
		'manage_options',
		'ds-logs',
		'ds_logs_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Testing — Dam Spam', 'dam-spam' ),
		esc_html__( 'Testing', 'dam-spam' ),
		'manage_options',
		'ds-testing',
		'ds_testing_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Cleanup — Dam Spam', 'dam-spam' ),
		esc_html__( 'Cleanup', 'dam-spam' ),
		'manage_options',
		'ds-cleanup',
		'ds_cleanup_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Advanced — Dam Spam', 'dam-spam' ),
		esc_html__( 'Advanced', 'dam-spam' ),
		'manage_options',
		'ds-advanced',
		'ds_advanced_menu'
	);
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		add_submenu_page(
			'dam-spam',
			esc_html__( 'Multisite — Dam Spam', 'dam-spam' ),
			esc_html__( 'Multisite', 'dam-spam' ),
			'manage_options',
			'ds-multisite',
			'ds_multisite_menu'
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

function ds_testing_menu() {
	include_setting( "testing.php" );
}

function ds_multisite_menu() {
	include_setting( "multisite.php" );
}

function include_setting( $file ) {
	$ppath = plugin_dir_path( __FILE__ );
	if ( file_exists( $ppath . $file ) ) {
		require_once( $ppath . $file );
	} else {
		// translators: %1$s is the directory path, %2$s is the filename
		printf( '<br>' . esc_html__( 'Missing File: %1$s %2$s', 'dam-spam' ), esc_html( $ppath ), esc_html( $file ) ) . '<br>';
	}
}

function ds_fix_post_vars() {
	if ( !empty( $_POST ) ) {
		$keys = isset( $_POST ) ? ( array ) array_keys( $_POST ) : array();
		foreach ( $keys as $key ) {
			try {
				$key = sanitize_key( $key );
				if ( isset( $_POST[$key] ) && is_string( $_POST[$key] ) ) {
					if ( isset( $_POST[$key] ) && strpos( wp_unslash( $_POST[$key] ), "\n" ) !== false ) {
						$val2 = sanitize_textarea_field( wp_unslash( $_POST[$key] ) );
					} else {
						$val2 = sanitize_text_field( wp_unslash( $_POST[$key] ) );
					}
					$_POST[$key] = $val2;
				}
			} catch ( Exception $e ) {}
		}
	}
}

?>
