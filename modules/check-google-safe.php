<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_google_safe extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		if ( empty( $stats ) ) {
			return false;
		}
		if ( !array_key_exists( 'googleapi', $stats ) ) {
			return false;
		}
		if ( !array_key_exists( 'content', $stats ) ) {
			return false;
		}
		$googleapi = $stats['googleapi'];
		$content   = $stats['content'];
		$post	   = array();
		preg_match_all( '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', $content, $post, PREG_PATTERN_ORDER );
		$urls1 = array();
		$urls2 = array();
		$urls3 = array();
		if ( is_array( $post ) && is_array( $post[1] ) ) {
			$urls1 = array_unique( $post[1] );
		} else {
			$urls1 = array();
		}
		preg_match_all( '/\[url=(.+)\]/iU', $content, $post, PREG_PATTERN_ORDER );
		if ( is_array( $post ) && is_array( $post[0] ) ) {
			$urls2 = array_unique( $post[0] );
		} else {
			$urls2 = array();
		}
		$urls3 = array_merge( $urls1, $urls2 );
		if ( !is_array( $urls3 ) ) {
			return false;
		}
		if ( empty( $urls3 ) ) {
			return false;
		}
		for ( $j = 0; $j < count( $urls3 ) && $j < 4; $j ++ ) {
			$url = $urls3[$j];
			if ( !empty( $url ) ) {
				$url = urldecode( $url );
				if ( strpos( $url, 'http://' ) !== 0 && strpos( $url, 'https://' ) !== 0 ) {
					$url = 'https://' . $url;
				}
				$data = array(
					'client' => array(
						'clientId' => 'dam-spam',
						'clientVersion' => DAM_SPAM_VERSION
					),
					'threatInfo' => array(
						'threatTypes' => array( 'MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION' ),
						'platformTypes' => array( 'ANY_PLATFORM' ),
						'threatEntryTypes' => array( 'URL' ),
						'threatEntries' => array(
							array( 'url' => $url )
						)
					)
				);
				$response = wp_remote_post(
					'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $googleapi,
					array(
						'headers' => array( 'Content-Type' => 'application/json' ),
						'body' => wp_json_encode( $data ),
						'timeout' => 10
					)
				);
				if ( is_wp_error( $response ) ) {
					continue;
				}
				$body = wp_remote_retrieve_body( $response );
				$result = json_decode( $body, true );
				if ( !empty( $result['matches'] ) ) {
					$threat_type = $result['matches'][0]['threatType'];
					$threat_name = $threat_type;
					if ( $threat_type === 'SOCIAL_ENGINEERING' ) {
						$threat_name = 'phishing';
					} elseif ( $threat_type === 'MALWARE' ) {
						$threat_name = 'malware';
					} elseif ( $threat_type === 'UNWANTED_SOFTWARE' ) {
						$threat_name = 'unwanted software';
					} elseif ( $threat_type === 'POTENTIALLY_HARMFUL_APPLICATION' ) {
						$threat_name = 'harmful application';
					}
					return esc_html__( 'Google Safe: ', 'dam-spam' ) . $threat_name;
				}
			}
		}
		return false;
	}
}

?>