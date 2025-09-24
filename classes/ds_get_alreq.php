<?php
// this does the get for the tbody in Allow Requests

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

class ds_get_alreq {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		$trash		 = DS_PLUGIN_URL . 'images/trash.png';
		$down		 = DS_PLUGIN_URL . 'images/down.png';
		$up			 = DS_PLUGIN_URL . 'images/up.png';
		$whois	     = DS_PLUGIN_URL . 'images/whois.png';
		$ajaxurl	 = admin_url( 'admin-ajax.php' );
		$show		 = '';
		$nwlrequests = array();
		// sfs_debug_msg( 'wlrequests ' . print_r( $wlrequests, true ) );
		foreach ( $wlrequests as $key => $value ) {
			$sw = true;
			if ( !empty( $ip ) && $ip != 'x' ) {
				if ( $key == $ip ) {
					// sfs_debug_msg( "wlreq matched '$ip'" );
					$sw = false;
				}
				if ( $ip == trim( $value[0] ) ) { // match IP
					// sfs_debug_msg( "wlreq val 0 '$value[0]'" );
					$sw = false;
				}
				if ( $ip == trim( $value[1] ) ) { // match email
					// sfs_debug_msg( "wlreq val 1 '$value[1]'" );
					$sw = false;
				}
			}
			$container = 'wlreq';
			if ( $sw ) {
				$nwlrequests[$key] = $value;
				$show			    .= "<tr>";
				$trsh				 = "<a href=\"\" onclick=\"sfs_ajax_process('$key','wlreq','delete_wl_row','$ajaxurl');return false;\" title=\"" . esc_attr__( 'Delete Row', 'dam-spam' ) . "\" alt=\"" . esc_attr__( 'Delete Row', 'dam-spam' ) . "\" ><img src=\"$trash\" class=\"icon-action\"></a>";
				$addtoblock		     = "<a href=\"\"onclick=\"sfs_ajax_process('$value[0]','$container','add_black','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Add %s to Block List', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Add %s to Block List', 'dam-spam' ), $value[0] ) . "\"><img src=\"$down\" class=\"icon-action\"></a>";
				$addtoallow		     = "<a href=\"\"onclick=\"sfs_ajax_process('$value[0]','$container','add_white','$ajaxurl', '$value[1]');return false;\" title=\"" . sprintf( esc_attr__( 'Add %s to Allow List', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Add %s to Allow List', 'dam-spam' ), $value[0] ) . "\"><img src=\"$up\" class=\"icon-action\"></a>";
				$show			    .= "<td>$key $trsh $addtoblock $addtoallow</td>";
				$who				 = "<br><a title=\"" . esc_attr__( 'Look up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$value[0]\"><img src=\"$whois\" class=\"icon-action\"/></a> ";
				$trsh				 = "<a href=\"\" onclick=\"sfs_ajax_process('$value[0]','wlreq','delete_wlip','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[0] ) . "\"><img src=\"$trash\" class=\"icon-action\"></a>";
				$show			    .= "<td>$value[0] $who $trsh</td>";
				$trsh				 = "<a href=\"\" onclick=\"sfs_ajax_process('$value[1]','wlreq','delete_wlem','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[1] ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[1] ) . "\"><img src=\"$trash\" class=\"icon-action\"></a>";
				$show			    .= "<td><a target=\"_blank\" href=\"mailto:$value[1]?subject=Website Access\">$value[1] $trsh</td>";
				$show			    .= "<td>$value[3]</td>";
				$show			    .= "<td>$value[4]</td>";
				$show			    .= "<tr>";
			}
		}
		$stats['wlrequests'] = $nwlrequests;
		// sfs_debug_msg( 'nwlrequests ' . print_r( $nwlrequests, true ) );
		if ( array_key_exists( 'addon', $post ) ) {
			ds_set_stats( $stats, $post['addon'] );
		} else {
			ds_set_stats( $stats );
		}
		return $show;
	}
}

?>