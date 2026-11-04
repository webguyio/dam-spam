<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class dam_spam_get_good_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$goodips   = $stats['goodips'];
		$cachedel  = 'delete_gcache';
		$container = 'goodips';
		$icons = dam_spam_get_icon_urls();
		extract( $icons );
		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$show	   = '';
		foreach ( $goodips as $key => $value ) {
			$who	 = "<a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"dam_spam_check\" href=\"https://whois.domaintools.com/" . esc_attr( $key ) . "\"><img src=\"$whois\" class=\"icon-action\"></a>";
			$show   .= "<a href=\"https://www.stopforumspam.com/search?q=" . esc_attr( $key ) . "\" target=\"dam_spam_check\">" . esc_html( $key ) . ": " . esc_html( $value ) . "</a> ";
			$onclick = "onclick=\"damSpamAjaxProcess(" . dam_spam_js_arg( $key ) . "," . dam_spam_js_arg( $container ) . "," . dam_spam_js_arg( $cachedel ) . "," . dam_spam_js_arg( $ajaxurl ) . ");return false;\"";
			// translators: %s is the date and reason for cache entry
			$show   .= " <a href=\"\" $onclick title=\"" . esc_attr( sprintf( __( 'Delete %s from Cache', 'dam-spam' ), $key ) ) . "\" alt=\"" . esc_attr( sprintf( __( 'Delete %s from Cache', 'dam-spam' ), $key ) ) . "\" ><img src=\"$trash\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"damSpamAjaxProcess(" . dam_spam_js_arg( $key ) . "," . dam_spam_js_arg( $container ) . ",'add_black'," . dam_spam_js_arg( $ajaxurl ) . ");return false;\"";
			// translators: %s is the IP address with actions
			$show   .= " <a href=\"\" $onclick title=\"" . esc_attr( sprintf( __( 'Add to %s Block List', 'dam-spam' ), $key ) ) . "\" alt=\"" . esc_attr__( 'Add to Block List', 'dam-spam' ) . "\" ><img src=\"$down\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"damSpamAjaxProcess(" . dam_spam_js_arg( $key ) . "," . dam_spam_js_arg( $container ) . ",'add_white'," . dam_spam_js_arg( $ajaxurl ) . ");return false;\"";
			// translators: %s is the date and reason details
			$show   .= " <a href=\"\" $onclick title=\"" . esc_attr( sprintf( __( 'Add to %s Allow List', 'dam-spam' ), $key ) ) . "\" alt=\"" . esc_attr__( 'Add to Allow List', 'dam-spam' ) . "\" ><img src=\"$up\" class=\"icon-action\"></a>";
			$show   .= $who;
			$show   .= "<br>";
		}
		return $show;
	}
}

?>