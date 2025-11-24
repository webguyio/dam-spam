<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_periods extends dam_spam_module { 
	public function process( $ip, &$stats=array(), &$options=array(), &$post=array() ) {
		if ( array_key_exists( 'email', $post ) && $options['check_periods'] == 'Y' ) {
			$email = $post['email'];
			if ( !empty( $email ) ) {
				list( $text, $domain ) = explode( '@', $email, 2 );
				$domain = $this->remove_tld( $domain );
				if ( substr_count( $domain, "." ) >= 1 ) {
					// translators: %s is the email address with too many periods
					return sprintf( esc_html__( 'Too Many Periods in: %s', 'dam-spam' ), $email );
					return true;
				} else if ( substr_count( $text, "." ) >= 2 ) {
					// translators: %s is the email address with too many periods
					return sprintf( esc_html__( 'Too Many Periods in: %s', 'dam-spam' ), $email );
					return true;
				}
			}
		}
		if ( array_key_exists( 'user_email', $post ) && $options['check_periods'] == 'Y') {
			$email = $post['user_email'];
			if ( !empty( $email ) ) {
				list( $text, $domain ) = explode( '@', $email, 2 );
				$domain = $this->remove_tld( $domain );
				if ( substr_count( $domain, "." ) >= 2 ) {
					// translators: %s is the email address with too many periods
					return sprintf( esc_html__( 'Too Many Periods in: %s', 'dam-spam' ), $email );
					return true;
				} else if ( substr_count( $text, "." ) >= 2 ) {
					// translators: %s is the email address with too many periods
					return sprintf( esc_html__( 'Too Many Periods in: %s', 'dam-spam' ), $email );
					return true;
				}
			}
		}
		return false;
	}
	private function remove_tld( $domain ) {
		$domain_split = explode( '.', $domain );
		$domain_array = array_slice( $domain_split, -2, 2 );
		$tld_two = implode( '.', $domain_array );
		$tld_one = end( $domain_split );
		// last updated from https://raw.githubusercontent.com/fbraz3/publicsuffix-json/master/public_suffix_list.json on ???
		$tld_array = array_flip( json_decode( file_get_contents( __DIR__ . '/config/domains.json' ) ) );
		if ( isset( $tld_array[$tld_two] ) ) {
			return str_replace( ".$tld_two", "", $domain );
		} else if ( isset( $tld_array[$tld_one] ) ) {
			return str_replace( ".$tld_one", "", $domain );
		}
	}
}

?>
