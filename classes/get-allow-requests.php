<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class get_allow_requests {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		extract( $stats );
		extract( $options );
		$trash	 = DS_PLUGIN_URL . 'assets/images/trash.png';
		$down	 = DS_PLUGIN_URL . 'assets/images/down.png';
		$up		 = DS_PLUGIN_URL . 'assets/images/up.png';
		$whois	 = DS_PLUGIN_URL . 'assets/images/whois.png';
		$ajaxurl = admin_url( 'admin-ajax.php' );
		$show	 = '';
		$nallow_list_requests = array();
		foreach ( $allow_list_requests as $key => $value ) {
			$sw = true;
			if ( !empty( $ip ) && $ip != 'x' ) {
				if ( $key == $ip ) {
					$sw = false;
				}
				if ( $ip == trim( $value[0] ) ) {
					$sw = false;
				}
				if ( $ip == trim( $value[1] ) ) {
					$sw = false;
				}
			}
			$container = 'allow_list_request';
			if ( $sw ) {
				$nallow_list_requests[$key] = $value;
				$show .= "<tr>";
				$trsh = "<a href=\"\" onclick=\"sfs_ajax_process('$key','allow_list_request','delete_wl_row','$ajaxurl');return false;\" title=\"" . esc_attr__( 'Delete Row', 'dam-spam' ) . "\" alt=\"" . esc_attr__( 'Delete Row', 'dam-spam' ) . "\" ><img src=\"$trash\" class=\"icon-action\"></a>";
				// translators: Delete Row action title and alt text
				$addtoblock = "<a href=\"\" onclick=\"sfs_ajax_process('$value[0]','$container','add_black','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Add %s to Block List', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Add %s to Block List', 'dam-spam' ), $value[0] ) . "\"><img src=\"$down\" class=\"icon-action\"></a>";
				// translators: %s is the IP address being added to block list
				$addtoallow = "<a href=\"\" onclick=\"sfs_ajax_process('$value[0]','$container','add_white','$ajaxurl', '$value[1]');return false;\" title=\"" . sprintf( esc_attr__( 'Add %s to Allow List', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Add %s to Allow List', 'dam-spam' ), $value[0] ) . "\"><img src=\"$up\" class=\"icon-action\"></a>";
				$show .= "<td>$key $trsh $addtoblock $addtoallow</td>";
				$who = "<br><a title=\"" . esc_attr__( 'Look up WHOIS', 'dam-spam' ) . "\" target=\"_blank\" href=\"https://whois.domaintools.com/$value[0]\"><img src=\"$whois\" class=\"icon-action\"/></a> ";
				// translators: %s is the IP address for WHOIS lookup
				$trsh = "<a href=\"\" onclick=\"sfs_ajax_process('$value[0]','allow_list_request','delete_wlip','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[0] ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[0] ) . "\"><img src=\"$trash\" class=\"icon-action\"></a>";
				$show .= "<td>$value[0] $who $trsh</td>";
				// translators: %s is the IP address being deleted from allow requests
				$trsh = "<a href=\"\" onclick=\"sfs_ajax_process('$value[1]','allow_list_request','delete_wlem','$ajaxurl');return false;\" title=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[1] ) . "\" alt=\"" . sprintf( esc_attr__( 'Delete all %s', 'dam-spam' ), $value[1] ) . "\"><img src=\"$trash\" class=\"icon-action\"></a>";
				$show .= "<td><a target=\"_blank\" href=\"mailto:$value[1]?subject=Website Access\">$value[1] $trsh</td>";
				$show .= "<td>$value[3]</td>";
				$show .= "<td>$value[4]</td>";
				$show .= "<tr>";
			}
		}
		$stats['allow_list_requests'] = $nallow_list_requests;
		if ( array_key_exists( 'addon', $post ) ) {
			ds_set_stats( $stats, $post['addon'] );
		} else {
			ds_set_stats( $stats );
		}
		return $show;
	}
}

?>
