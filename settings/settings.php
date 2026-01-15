<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

function dam_spam_admin_menu_l() {
	add_menu_page(
		'Dam Spam',
		'Dam Spam',
		'manage_options',
		'dam-spam',
		'dam_spam_summary_menu',
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
		'dam_spam_summary_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Protections — Dam Spam', 'dam-spam' ),
		esc_html__( 'Protections', 'dam-spam' ),
		'manage_options',
		'dam-spam-protections',
		'dam_spam_protections_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Allowed — Dam Spam', 'dam-spam' ),
		esc_html__( 'Allowed', 'dam-spam' ),
		'manage_options',
		'dam-spam-allowed',
		'dam_spam_allowed_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Blocked — Dam Spam', 'dam-spam' ),
		esc_html__( 'Blocked', 'dam-spam' ),
		'manage_options',
		'dam-spam-blocked',
		'dam_spam_blocked_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Challenges — Dam Spam', 'dam-spam' ),
		esc_html__( 'Challenges', 'dam-spam' ),
		'manage_options',
		'dam-spam-challenges',
		'dam_spam_challenges_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'APIs — Dam Spam', 'dam-spam' ),
		esc_html__( 'APIs', 'dam-spam' ),
		'manage_options',
		'dam-spam-apis',
		'dam_spam_apis_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Cache — Dam Spam', 'dam-spam' ),
		esc_html__( 'Cache', 'dam-spam' ),
		'manage_options',
		'dam-spam-cache',
		'dam_spam_cache_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Logs — Dam Spam', 'dam-spam' ),
		esc_html__( 'Logs', 'dam-spam' ),
		'manage_options',
		'dam-spam-logs',
		'dam_spam_logs_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Testing — Dam Spam', 'dam-spam' ),
		esc_html__( 'Testing', 'dam-spam' ),
		'manage_options',
		'dam-spam-testing',
		'dam_spam_testing_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Cleanup — Dam Spam', 'dam-spam' ),
		esc_html__( 'Cleanup', 'dam-spam' ),
		'manage_options',
		'dam-spam-cleanup',
		'dam_spam_cleanup_menu'
	);
	add_submenu_page(
		'dam-spam',
		esc_html__( 'Advanced — Dam Spam', 'dam-spam' ),
		esc_html__( 'Advanced', 'dam-spam' ),
		'manage_options',
		'dam-spam-advanced',
		'dam_spam_advanced_menu'
	);
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		add_submenu_page(
			'dam-spam',
			esc_html__( 'Multisite — Dam Spam', 'dam-spam' ),
			esc_html__( 'Multisite', 'dam-spam' ),
			'manage_options',
			'dam-spam-multisite',
			'dam_spam_multisite_menu'
		);
	}
}

function dam_spam_summary_menu() {
	dam_spam_include_setting( "summary.php" );
}

function dam_spam_protections_menu() {
	dam_spam_include_setting( "protections.php" );
}

function dam_spam_allowed_menu() {
	dam_spam_include_setting( "allowed.php" );
}

function dam_spam_blocked_menu() {
	dam_spam_include_setting( "blocked.php" );
}

function dam_spam_challenges_menu() {
	dam_spam_include_setting( "challenges.php" );
}

function dam_spam_apis_menu() {
	dam_spam_include_setting( "apis.php" );
}

function dam_spam_cache_menu() {
	dam_spam_include_setting( "cache.php" );
}

function dam_spam_logs_menu() {
	dam_spam_include_setting( "logs.php" );
}

function dam_spam_cleanup_menu() {
	dam_spam_include_setting( "cleanup.php" );
}

function dam_spam_testing_menu() {
	dam_spam_include_setting( "testing.php" );
}

function dam_spam_multisite_menu() {
	dam_spam_include_setting( "multisite.php" );
}

function dam_spam_include_setting( $file ) {
	$ppath = DAM_SPAM_PATH . 'settings/';
	if ( file_exists( $ppath . $file ) ) {
		require_once( $ppath . $file );
	} else {
		// translators: %1$s is the directory path, %2$s is the filename
		printf( '<br>' . esc_html__( 'Missing File: %1$s %2$s', 'dam-spam' ), esc_html( $ppath ), esc_html( $file ) ) . '<br>';
	}
}

function dam_spam_fix_post_vars() {
	// phpcs:disable WordPress.Security.NonceVerification -- Utility function that sanitizes POST data before nonce verification
	if ( !empty( $_POST ) ) {
		$keys = isset( $_POST ) ? ( array ) array_keys( $_POST ) : array();
		foreach ( $keys as $key ) {
			try {
				$key = sanitize_key( $key );
				if ( isset( $_POST[$key] ) && is_string( $_POST[$key] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Checking for newlines before sanitization on next line
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