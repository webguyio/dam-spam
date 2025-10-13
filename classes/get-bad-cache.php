<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class get_bad_cache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$badips	   = $stats['badips'];
		$cachedel  = 'delete_bcache';
		$container = 'badips';
		$trash	   = DS_PLUGIN_URL . 'assets/images/trash.png';
		$down	   = DS_PLUGIN_URL . 'assets/images/down.png';
		$up		   = DS_PLUGIN_URL . 'assets/images/up.png';
		$whois	   = DS_PLUGIN_URL . 'assets/images/whois.png';
		$stophand  = DS_PLUGIN_URL . 'assets/images/stop.png';
		$search	   = DS_PLUGIN_URL . 'assets/images/search.png';
		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$show	   = '';
		foreach ( $badips as $key => $value ) {
			$who	 = "<a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$key\"><img src=\"$whois\" class=\"icon-action\"></a>";
			$show   .= "<a href=\"https://www.stopforumspam.com/search?q=$key\" target=\"_blank\">$key: $value</a> ";
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','$cachedel','$ajaxurl');return false;\"";
			// translators: %s is the date and reason for cache entry
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Delete %s from Cache', 'dam-spam' ), $key ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete %s from Cache', 'dam-spam' ), $key ) . "\" ><img src=\"$trash\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','add_black','$ajaxurl');return false;\"";
			// translators: %s is the IP address with actions
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Add to %s Block List', 'dam-spam' ), $key ) . "\" alt=\"" . esc_attr__( 'Add to Block List', 'dam-spam' ) . "\" ><img src=\"$down\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','add_white','$ajaxurl');return false;\"";
			// translators: %s is the date and reason details
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Add to %s Allow List', 'dam-spam' ), $key ) . "\" alt=\"" . esc_attr__( 'Add to Allow List', 'dam-spam' ) . "\" ><img src=\"$up\" class=\"icon-action\"></a>";
			$show   .= $who;
			$show   .= "<br>";
		}
		return $show;
	}
}

?>
