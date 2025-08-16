<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class ds_get_bcache {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		// gets the innerhtml for cache - same as get gcache except for names
		$badips	   = $stats['badips'];
		$cachedel  = 'delete_bcache';
		$container = 'badips';
		$trash	   = DS_PLUGIN_URL . 'images/trash.png';
		$down	   = DS_PLUGIN_URL . 'images/down.png';
		$up		   = DS_PLUGIN_URL . 'images/up.png';
		$whois	   = DS_PLUGIN_URL . 'images/whois.png';
		$stophand  = DS_PLUGIN_URL . 'images/stop.png';
		$search	   = DS_PLUGIN_URL . 'images/search.png';
		$ajaxurl   = admin_url( 'admin-ajax.php' );
		$show	   = '';
		foreach ( $badips as $key => $value ) {
			$who	 = "<a title=\"" . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$key\"><img src=\"$whois\" class=\"icon-action\"></a>";
			$show   .= "<a href=\"https://www.stopforumspam.com/search?q=$key\" target=\"_blank\">$key: $value</a> ";
			// try AJAX on the delete from bad cache
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','$cachedel','$ajaxurl');return false;\"";
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Delete %s from Cache', 'dam-spam' ), $key ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete %s from Cache', 'dam-spam' ) $key ) . "\" ><img src=\"$trash\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','add_black','$ajaxurl');return false;\"";
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Add to %s Block List', 'dam-spam' ), $key ) . "\" alt=\"" . esc_attr__( 'Add to Block List', 'dam-spam' ) . "\" ><img src=\"$down\" class=\"icon-action\"></a> ";
			$onclick = "onclick=\"sfs_ajax_process('$key','$container','add_white','$ajaxurl');return false;\"";
			$show   .= " <a href=\"\" $onclick title=\"" . sprintf( esc_attr__( 'Add to %s Allow List', 'dam-spam' ), $key ) . "\" alt=\"" . esc_attr__( 'Add to Allow List', 'dam-spam' ) . "\" ><img src=\"$up\" class=\"icon-action\"></a>";
			$show   .= $who;
			$show   .= "<br>";
		}
		return $show;
	}
}

?>