<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_check_vpn extends dam_spam_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$contact_email = defined( 'DAM_SPAM_MAIL' ) ? DAM_SPAM_MAIL : get_option( 'admin_email' );
		$ban_threshold = 0.99;
		$url = add_query_arg(
			array(
				'ip'      => $ip,
				'contact' => $contact_email,
				'flags'   => 'b',
			),
			'https://check.getipintel.net/check.php'
		);
		$response = $this->getafile( $url, 'GET' );
		if ( empty( $response ) ) {
			return false;
		}
		if ( strpos( $response, 'ERR:' ) !== false ) {
			return false;
		}
		$score = floatval( trim( $response ) );
		if ( $score < 0 ) {
			return false;
		}
		if ( $score >= $ban_threshold ) {
			// translators: %s is the GetIPIntel reputation score
			return sprintf( esc_html__( 'VPN/Bad IP Detected (score: %s)', 'dam-spam' ), number_format( $score, 2 ) );
		}
		return false;
	}
}

?>