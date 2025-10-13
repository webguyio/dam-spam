<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_session {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( !isset( $_POST ) || empty( $_POST ) ) {
			if ( !isset( $_COOKIE['ds_protection_time'] ) ) {
				setcookie( 'ds_protection_time', strtotime( 'now' ), strtotime( '+1 min' ) );
			}
			return false;
		}
		$sname = '';
		if ( array_key_exists( 'REQUEST_URI', $_SERVER ) ) {
			$sname = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		} elseif ( array_key_exists( 'SCRIPT_URI', $_SERVER ) ) {
			$sname = isset( $_SERVER['SCRIPT_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_URI'] ) ) : '';
			if ( strpos( $sname, '?' ) !== false ) {
				$sname = substr( $sname, 0, strpos( $sname, '?' ) );
			}
		} elseif ( array_key_exists( 'PHP_SELF', $_SERVER ) ) {
			$sname = isset( $_SERVER['PHP_SELF'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ), 1 ) : '';
		}
		if ( empty( $sname ) ) {
			return false;
		}
		$sesstime = 2;
		if ( !defined( 'WP_CACHE' ) || ( !WP_CACHE ) ) {
			if ( strpos( $sname, 'wp-login.php' ) === false ) {
				if ( isset( $_COOKIE['ds_time'] ) ) {
					$stime = absint( $_COOKIE['ds_time'] );
					$tm = strtotime( 'now' ) - $stime;
					if ( $tm > 0 && $tm <= $sesstime ) {
						// translators: %s is the number of seconds for session speed
						return sprintf( esc_html__( 'Session Speed — %s seconds', 'dam-spam' ), $tm );
					}
				}
			}
		}
		return false;
	}
}

?>
