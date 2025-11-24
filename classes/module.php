<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_module {
	public $searchname = '';
	public $searchlist = array();

	public static function getafile( $f, $method = 'GET' ) {
		if ( !class_exists( 'WP_Http' ) ) {
			include_once( ABSPATH . WPINC . '/class-http.php' );
		}
		$request		  = new WP_Http;
		$parms			  = array();
		$parms['timeout'] = 10;
		$parms['method']  = $method;
		$result		      = $request->request( $f, $parms );
		if ( empty( $result ) ) {
			return '';
		}
		if ( is_array( $result ) ) {
			$answer = $result['body'];
			return $answer;
		}
		if ( is_object( $result ) ) {
			$answer = 'ERR: ' . $result->get_error_message();
			return $answer;
		}
		return '';
	}

	public static function getSname() {
		$sname = '';
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$sname = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}
		if ( empty( $sname ) ) {
			if ( isset( $_SERVER['SCRIPT_NAME'] ) ) {
				$script_name = sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) );
				$_SERVER['REQUEST_URI'] = $script_name;
				$sname = $script_name;
				if ( isset( $_SERVER['QUERY_STRING'] ) && !empty( $_SERVER['QUERY_STRING'] ) ) {
					$query_string = sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) );
					$_SERVER['REQUEST_URI'] .= '?' . sanitize_text_field( wp_unslash( $query_string ) );
				}
			}
		}
		if ( empty( $sname ) ) {
			$sname = '';
		}
		return $sname;
	}

	public static function cidr2str( $ipl, $bits ) {
		$ipl = ip2long( $ipl );
		$ipl = sprintf( "%u", $ipl );
		$num = pow( 2, 32 - $bits ) - 1;
		$ipl = $ipl + 0;
		$ipl = $ipl | $num;
		$ipl ++;
		return long2ip( $ipl );
	}

	public function searchList( $needle, &$haystack ) {
		$searchname = $this->searchname;
		if ( !is_array( $haystack ) ) {
			return false;
		}
		$needle = strtolower( $needle );
		if ( empty( $needle ) ) {
			return false;
		}
		foreach ( $haystack as $search ) {
			$search = trim( strtolower( $search ) );
			$reason = $search;
			if ( empty( $search ) ) {
				continue;
			}
			if ( $needle == $search ) {
				return "$searchname: $needle";
			}
			if ( substr_count( $needle, '.' ) == 3
				 && strpos( $search, '.' ) !== false
				 && strpos( $search, '/' ) !== false
			) {
				list( $subnet, $mask ) = explode( '/', $search );
				$x2 = ip2long( $needle ) & ~( ( 1 << ( 32 - $mask ) ) - 1 );
				$x3 = ip2long( $subnet ) & ~( ( 1 << ( 32 - $mask ) ) - 1 );
				if ( $x2 == $x3 ) {
					return "$searchname: $reason";
				}
			}
			if ( strpos( $search, '*' ) !== false || strpos( $search, '?' ) !== false ) {
				if ( dam_spam_module::wildcard_match( $search, $needle ) ) {
					return "$searchname: $reason: $needle";
				}
				continue;
			}
			if ( strlen( $needle ) > strlen( $search ) ) {
				$n = substr( $needle, 0, strlen( $search ) );
				if ( $n == $search ) {
					return "$searchname: $reason";
				}
			}
		}
		return false;
	}

	public static function wildcard_match( $pattern, $value ) {
		if ( is_array( $value ) ) {
			$return = array();
			foreach ( $value as $string ) {
				if ( wildcard_match( $pattern, $string ) ) {
					$return[] = $string;
				}
			}
			return $return;
		}
		$pattern = preg_split( '/((?<!\\\)\*)|((?<!\\\)\?)/', $pattern, null,
			PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		foreach ( $pattern as $key => $part ) {
			if ( $part == '?' ) {
				$pattern[$key] = '.';
			} elseif ( $part == '*' ) {
				$pattern[$key] = '.*';
			} else {
				$pattern[$key] = preg_quote( $part );
			}
		}
		$pattern = implode( '', $pattern );
		$pattern = '/^' . $pattern . '$/';
		return preg_match( $pattern, $value );
	}

	public function searchcache( $needle, &$haystack ) {
		$searchname = $this->searchname;
		if ( !is_array( $haystack ) ) {
			return false;
		}
		$needle = strtolower( $needle );
		foreach ( $haystack as $search => $reason ) {
			$search = trim( strtolower( $search ) );
			if ( empty( $search ) ) {
				continue;
			}
			if ( $needle == $search ) {
				return "$searchname: $needle";
			}
			if ( strpos( $search, '*' ) !== false || strpos( $search, '?' ) !== false ) {
				if ( dam_spam_module::wildcard_match( $search, $needle ) ) {
					return "$searchname: $reason: $needle";
				}
			}
			if ( strlen( $needle ) > strlen( $search ) ) {
				$n = substr( $needle, 0, strlen( $search ) );
				if ( $n == $search ) {
					return "$searchname: $reason";
				}
			}
			if ( substr_count( $needle, '.' ) == 3 && strpos( $search, '/' ) !== false ) {
				list( $subnet, $mask ) = explode( '/', $search );
				$x2 = ip2long( $needle ) & ~( ( 1 << ( 32 - $mask ) ) - 1 );
				$x3 = ip2long( $subnet ) & ~( ( 1 << ( 32 - $mask ) ) - 1 );
				if ( $x2 == $x3 ) {
					return "$searchname: $reason";
				}
			}
		}
		return false;
	}

	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		return dam_spam_module::ipListMatch( $ip );
	}

	public function ipListMatch( $ip ) {
		$ipt = dam_spam_module::ip2numstr( $ip );
		foreach ( $this->searchlist as $c ) {
			if ( !is_array( $c ) ) {
				if ( substr_count( $c, '.' ) == 3 ) {
					if ( strpos( $c, '/' ) !== false ) {
						$c = dam_spam_module::cidr2ip( $c );
					} else {
						$c = array( $c, $c );
					}
				}
				if ( !is_array( $c ) ) {
					$this->searchname = $c;
				}
			}
			if ( is_array( $c ) ) {
				list( $ips, $ipe ) = $c;
				if ( strpos( $ips, '.' ) === false
					 && strpos( $ips, ':' ) === false
				) {
					if ( $ipt < $ips ) {
						return false;
					}
					if ( $ipt >= $ips && $ipt <= $ipe ) {
						return $this->searchname . ': ' . $ip;
					}
				} elseif ( strpos( $ips, ':' ) !== false ) {
					if ( $ip >= $ips && $ip <= $ipe ) {
						return $this->searchname . ': ' . $ip;
					}
				} else {
					$ips = dam_spam_module::ip2numstr( $ips );
					$ipe = dam_spam_module::ip2numstr( $ipe );
					if ( $ipt >= $ips && $ipt <= $ipe ) {
						if ( is_array( $ip ) ) {
							// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Debug output for admin testing interface
							echo esc_html__( 'Array in IP: ', 'dam-spam' ) . esc_html( print_r( $ip, true ) ) . '<br>';
							$ip = $ip[0];
						}
						return $this->searchname . ': ' . $ip;
					}
				}
			}
		}
		return false;
	}

	public static function ip2numstr( $ip ) {
		if ( long2ip( ip2long( $ip ) ) != $ip ) {
			return false;
		}
		list( $b1, $b2, $b3, $b4 ) = explode( '.', $ip );
		$b1 = str_pad( $b1, 3, '0', STR_PAD_LEFT );
		$b2 = str_pad( $b2, 3, '0', STR_PAD_LEFT );
		$b3 = str_pad( $b3, 3, '0', STR_PAD_LEFT );
		$b4 = str_pad( $b4, 3, '0', STR_PAD_LEFT );
		$s  = $b1 . $b2 . $b3 . $b4;
		return $s;
	}

	public static function cidr2ip( $cidr ) {
		if ( strpos( $cidr, '/' ) === false ) {
			return false;
		}
		list( $ip, $bits ) = explode( '/', $cidr );
		$ip = dam_spam_module::fixip( $ip );
		if ( $ip === false ) {
			return false;
		}
		$start = $ip;
		$end   = ip2long( $ip );
		$end   = sprintf( "%u", $end );
		$end1  = $end + 0;
		$num   = pow( 2, 32 - $bits ) - 1;
		$end   = ( $end + 0 ) | $num;
		$end   = $end + 1;
		$end2  = long2ip( $end );
		if ( $end == '128.0.0.0' ) {
		}
		$start = dam_spam_module::cidrStart2str( $start, $bits );
		return array( $start, $end2 );
	}

	public static function fixip( $ip ) {
		$ip = trim( $ip );
		if ( empty( $ip ) ) {
			return false;
		}
		if ( strpos( $ip, '.' ) === false ) {
			return false;
		}
		if ( count( explode( '.', $ip ) ) == 2 ) {
			$ip .= '.0.0';
		}
		if ( count( explode( '.', $ip ) ) == 3 ) {
			$ip .= '.0';
		}
		if ( long2ip( ip2long( $ip ) ) != $ip ) {
			return false;
		}
		return $ip;
	}

	public static function cidrStart2str( $ipl, $bits ) {
		$ipl = ip2long( $ipl );
		$ipl = sprintf( "%u", $ipl );
		$num = pow( 2, 32 - $bits ) - 1;
		$ipl = $ipl + 0;
		$z = pow( 2, 33 ) - 1;
		$z = $num ^ $z;
		$ipl = $ipl & $z;
		return long2ip( $ipl );
	}
}

?>