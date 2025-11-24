<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_sfs extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$query = "https://www.stopforumspam.com/api?ip=$ip";
		$check = '';
		$check = $this->getafile( $query, 'GET' );
		if ( empty( $check ) ) {
			return false;
		}
		if ( strpos( $check, 'ERR:' ) !== false ) {
			return $check;
		}
		$lastseen  = '';
		$frequency = '';
		$n		   = strpos( $check, '<appears>yes</appears>' );
		if ( $n !== false ) {
			if ( strpos( $check, '<lastseen>', $n ) !== false ) {
				$k		  = strpos( $check, '<lastseen>', $n );
				$k		 += 10;
				$j		  = strpos( $check, '</lastseen>', $k );
				$lastseen = gmdate( 'Y-m-d', time() );
				if ( ( $j - $k ) > 12 && ( $j - $k ) < 24 ) {
					$lastseen = substr( $check, $k, $j - $k );
				}
				if ( strpos( $lastseen, ' ' ) ) {
					$lastseen = substr( $lastseen, 0, strpos( $lastseen, ' ' ) );
				}
				if ( strpos( $check, '<frequency>', $n ) !== false ) {
					$k		 = strpos( $check, '<frequency>', $n );
					$k		+= 11;
					$j		 = strpos( $check, '</frequency', $k );
					$frequency = '9999';
					if ( ( $j - $k ) && ( $j - $k ) < 7 ) {
						$frequency = substr( $check, $k, $j - $k );
					}
				}
			}
			$freq	 = 2;
			$maxtime = 99;
			$sfsfreq = $options['sfsfreq'];
			$sfsage  = $options['sfsage'];
			if ( ( $frequency >= $sfsfreq ) && ( strtotime( $lastseen ) > ( time() - ( 60 * 60 * 24 * $sfsage ) ) ) ) {
				// translators: %s is the frequency value from Stop Forum Spam
				return sprintf( esc_html__( 'SFS Last Seen: %1$s, Frequency: %2$s', 'dam-spam' ), $lastseen, $frequency );
			}
		}
		return false;
	}
}

?>
