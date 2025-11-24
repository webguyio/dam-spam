<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_tld {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$tld = $options['bad_tlds'];
		if ( empty( $tld ) ) {
			return false;
		}
		foreach ( $post as $key => $value ) {
			foreach ( $tld as $ft ) {
				if ( empty( $key ) ) {
					continue;
				}
				if ( strpos( $value, '.' ) === false ) {
					continue;
				}
				$ft   = strtolower( trim( $ft ) );
				$dlvl = substr_count( $ft, '.' );
				if ( $dlvl == 0 ) {
					continue;
				}
				$t  = explode( '.', $value );
				$tt = implode( '.', array_slice( $t, count( $t ) - $dlvl, $dlvl ) );
				$tt = '.' . trim( strtolower( $tt ) );
				if ( $ft == $tt ) {
					// translators: %s is the blocked top-level domain
					return sprintf( esc_html__( 'TLD Blocked: %1$s: %2$s: %3$s', 'dam-spam' ), $key, $value, $ft );
				}
			}
		}
		return false;
	}
}

?>
