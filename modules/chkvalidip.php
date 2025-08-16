<?php
// returns false if the IP is valid - returns reason if IP is invalid

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class chkvalidip {
	// check for a fake IP
	private static function _is_fake_ip( $client_ip ) {
		$client_host = '';
		// remote host
		$host_by_ip = gethostbyaddr( $client_ip );
		// IPv6 special
		if ( self::_is_ipv6( $client_ip ) ) {
			if ( self::_is_ipv6( $host_by_ip ) && inet_pton( $client_ip ) === inet_pton( $host_by_ip ) ) {
				// no domain
				return false;
			} else {
				// has domain
				$record = dns_get_record( $host_by_ip, DNS_AAAA );
				if ( empty( $record ) || empty( $record[0]['ipv6'] ) ) {
					// no reverse entry
					return true;
				} else {
					return inet_pton( $client_ip ) !== inet_pton( $record[0]['ipv6'] );
				}
			}
		}
		// IPv4 / comment
		if ( empty( $client_host ) ) {
			$ip_by_host = gethostbyname( $host_by_ip );
			if ( $ip_by_host === $host_by_ip ) {
				return false;
			}
			/* IPv4 / Trackback */
		} else {
			if ( $host_by_ip === $client_ip ) {
				return true;
			}
			$ip_by_host = gethostbyname( $client_host );
		}
		if ( strpos( $client_ip, $this->_cut_ip( $ip_by_host ) ) === false ) {
			return true;
		}
		return false;
	}
	// check for IPv6
	private static function _is_ipv6( $ip ) {
		// return !$this->_is_ipv4( $ip );
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) !== false;
	}
	// check for IPv4
	private static function _is_ipv4( $ip ) {
		// return preg_match( '/^\d{1,3}( \.\d{1,3} ){3,3}$/', $ip );
		return filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) !== false;
	}
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( empty( $ip ) ) {
			return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
		}
		if ( strpos( $ip, ':' ) === false && strpos( $ip, '.' ) === false ) {
			return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
		}
		if ( defined( 'AF_INET6' ) && strpos( $ip, ':' ) !== false ) {
			try {
				if ( !@inet_pton( $ip ) ) {
					return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
				}
			} catch ( Exception $e ) {
				return esc_html__( 'Invalid IP: ', 'dam-spam' ) . $ip;
			}
		}
		// check IPv4 for local private IP addresses
		if ( $ip == '127.0.0.1' ) {
			return esc_html__( 'Accessing Site Through localhost', 'dam-spam' );
		}
		$priv = array(
			array( '100000000000', '100255255255' ),
			array( '172016000000', '172031255255' ),
			array( '192168000000', '192168255255' )
		);
		$ip2  = be_module::ip2numstr( $ip );
		foreach ( $priv as $ips ) {
			if ( $ip2 >= $ips[0] && $ip2 <= $ips[1] ) {
				return esc_html__( 'Local IP Address: ', 'dam-spam' ) . $ip;
			}
			if ( $ip2 < $ips[1] ) {
				break;
			} // sorted so we can bail
		}
		// use the experimental check fake IP routine
		// doesn't work on older PHPs or some servers without IPv6 support enables
		/*
		try {
			if ( $this->_is_fake_ip( $ip ) ) {
				return "Fake IP (experimental) $ip";
			}
		} catch ( Exception $e ) {
			return $e;
		}
		*/
		// check for IPv6
		$lip = "127.0.0.1";
		if ( substr( $ip, 0, 2 ) == 'FB' || substr( $ip, 0, 2 ) == 'fb' ) {
			return esc_html__( 'Local IP Address: ', 'dam-spam' ) . $ip;
		}
		// see if server and browser are running on same server
		if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
			$lip = $_SERVER["SERVER_ADDR"];
			if ( $ip == $lip ) {
				return esc_html__( 'IP Same as Server: ', 'dam-spam' ) . $ip;
			}
		} else if ( array_key_exists( 'LOCAL_ADDR', $_SERVER ) ) { // IIS 7?
			$lip = $_SERVER["LOCAL_ADDR"];
			if ( $ip == $lip ) {
				return esc_html__( 'IP Same as Server: ', 'dam-spam' ) . $ip;
			}
		} else { // IIS 6 no server address use a gethost by name? hope we never get here
			try {
				$lip = @gethostbyname( $_SERVER['SERVER_NAME'] );
				if ( $ip == $lip ) {
					return esc_html__( 'IP Same as Server: ', 'dam-spam' ) . $ip;
				}
			} catch ( Exception $e ) {
			// can't make this work - ignore
			}
		}
		// we can do this with IPv4 addresses - check if same /24 subnet
		$j = strrpos( $ip, '.' );
		if ( $j === false ) {
			return false;
		}
		$k = strrpos( $lip, '.' );
		if ( $k === false ) {
			return false;
		}
		if ( substr( $ip, 0, $j ) == substr( $lip, 0, $k ) ) {
			return esc_html__( 'IP same /24 subnet as server ', 'dam-spam' ) . $ip;
		}
		return false;
	}
}

?>