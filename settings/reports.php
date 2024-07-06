<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

if ( !current_user_can( 'manage_options' ) ) {
	die( __( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$trash	  = DS_PLUGIN_URL . 'images/trash.png';
$down	  = DS_PLUGIN_URL . 'images/down.png';
$up		  = DS_PLUGIN_URL . 'images/up.png';
$whois	  = DS_PLUGIN_URL . 'images/whois.png';
$stophand = DS_PLUGIN_URL . 'images/stop.png';
$search   = DS_PLUGIN_URL . 'images/search.png';
$now	  = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head">Dam Spam — <?php _e( 'Log Report', 'dam-spam' ); ?></h1>
	<?php
	// $ip = ds_get_ip();
	$stats = ds_get_stats();
	extract( $stats );
	$options = ds_get_options();
	extract( $options );
	$ip	   = $_SERVER['REMOTE_ADDR'];
	$nonce = '';
	$msg   = '';
	if ( array_key_exists( 'ds_control', $_POST ) ) {
		$nonce = $_POST['ds_control'];
	}
	if ( wp_verify_nonce( $nonce, 'ds_update' ) ) {
		if ( array_key_exists( 'ds_clear_hist', $_POST ) ) {
			// clean out the history
			$hist			  = array();
			$stats['hist']	  = $hist;
			$spcount		  = 0;
			$stats['spcount'] = $spcount;
			$spdate		      = $now;
			$stats['spdate']  = $spdate;
			ds_set_stats( $stats );
			extract( $stats ); // extract again to get the new options
			$msg			  = '<div class="notice notice-success"><p>' . __( 'Activity Log Cleared', 'dam-spam' ) . '</p></div>';
		}
		if ( array_key_exists( 'ds_update_log_size', $_POST ) ) {
			// update log size
			if ( array_key_exists( 'ds_hist', $_POST ) ) {
				$ds_hist			= stripslashes( sanitize_text_field( $_POST['ds_hist'] ) );
				$options['ds_hist'] = $ds_hist;
				$msg				= '<div class="notice notice-success"><p>' . __( 'Options Updated', 'dam-spam' ) . '</p></div>';
				// update the options
				ds_set_options( $options );
			}
		}
	}
	if ( !empty( $msg ) ) {
		echo $msg;
	}
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->spam );
	if ( $num_comm->spam > 0 && DS_MU != 'Y' ) { ?>
		<p><?php _e( 'There are <a href="edit-comments.php?comment_status=spam">' . $num . '</a> spam comments waiting for you to report them.', 'dam-spam' ); ?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->moderated );
	if ( $num_comm->moderated > 0 && DS_MU != 'Y' ) { ?>
		<p><?php _e( 'There are <a href="edit-comments.php?comment_status=moderated">' . $num . '</a> comments waiting to be moderated.', 'dam-spam' ); ?></p>
	<?php }
	$nonce = wp_create_nonce( 'ds_update' );
	?>
	<!-- <script>
	setTimeout(function() {
		window.location.reload(1);
	}, 10000);
	</script> -->
	<form method="post" action="">
		<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
		<input type="hidden" name="ds_update_log_size" value="true">
		<h2><?php _e( 'History Size', 'dam-spam' ); ?></h2>
		<?php _e( 'Select the number of events to save in the history.', 'dam-spam' ); ?><br>
		<p class="submit">
			<select name="ds_hist">
				<option value="10" <?php if ( $ds_hist == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $ds_hist == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $ds_hist == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $ds_hist == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $ds_hist == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="150" <?php if ( $ds_hist == '150' ) { echo 'selected="true"'; } ?>>150</option>
			</select>
			<input class="button-primary" value="<?php _e( 'Update Log Size', 'dam-spam' ); ?>" type="submit">
		</p>
		<form method="post" action="">
			<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
			<input type="hidden" name="ds_clear_hist" value="true">
			<p class="submit"><input class="button-primary" value="<?php _e( 'Clear Recent Activity', 'dam-spam' ); ?>" type="submit"></p>
		</form>
		<?php
		if ( empty( $hist ) ) {
			_e( '<p>Nothing in the log.</p>', 'dam-spam' );
		} else { ?>
		<br>
		<input type="text" id="dsinput" onkeyup="ds_search()" placeholder="<?php _e( 'Date Search', 'dam-spam' ); ?>" title="<?php _e( 'Filter by a Value', 'dam-spam' ); ?>">
		<table id="dstable" name="sstable" cellspacing="2">
			<thead>
				<tr style="background-color:#675682;color:white;text-align:center;text-transform:uppercase;font-weight:600">
					<th onclick="sortTable(0)" class="filterhead ds-cleanup"><?php _e( 'Date/Time', 'dam-spam' ); ?></th>
					<th class="ds-cleanup"><?php _e( 'Email', 'dam-spam' ); ?></th>
					<th class="ds-cleanup"><?php _e( 'IP', 'dam-spam' ); ?></th>
					<th class="ds-cleanup"><?php _e( 'Author, User/Pwd', 'dam-spam' ); ?></th>
					<th class="ds-cleanup"><?php _e( 'Script', 'dam-spam' ); ?></th>
					<th class="ds-cleanup"><?php _e( 'Reason', 'dam-spam' ); ?></th>
			<?php if ( function_exists( 'is_multisite' ) && is_multisite() ) { ?>
			</thead>
			<tbody>
			<?php } ?>
			</tr>
			<?php
			// sort list by date descending
			krsort( $hist );
			foreach ( $hist as $key => $data ) {
				// $hist[$now] = array( $ip, $email, $author ,$sname, 'begin' );
				$em = strip_tags( trim( $data[1] ) );
				$dt = strip_tags( $key );
				$ip = $data[0];
				$au = strip_tags( $data[2] );
				$id = strip_tags( $data[3] );
				if ( empty( $au ) ) {
					$au = ' -- ';
				}
				if ( empty( $em ) ) {
					$em = ' -- ';
				}
				$reason = $data[4];
				$blog   = 1;
				if ( count( $data ) > 5 ) {
					$blog = $data[5];
				}
				if ( empty( $blog ) ) {
					$blog = 1;
				}
				if ( empty( $reason ) ) {
					$reason = "passed";
				}
				$stopper	 = '<a title="' . esc_attr__( 'Check Stop Forum Spam (SFS)', 'dam-spam' ) . '" target="_blank" href="https://www.stopforumspam.com/search.php?q=' . $ip . '"><img src="' . $stophand . '" class="icon-action"></a>';
				$honeysearch = '<a title="' . esc_attr__( 'Check Project HoneyPot', 'dam-spam' ) . '" target="_blank" href="https://www.projecthoneypot.org/ip_' . $ip . '"><img src="' . $search . '" class="icon-action"></a>';
				$botsearch   = '<a title="' . esc_attr__( 'Check BotScout', 'dam-spam' ) . '" target="_blank" href="https://botscout.com/search.htm?stype=q&sterm=' . $ip . '"><img src="' . $search . '" class="icon-action"></a>';
				$who		 = '<br><a title="' . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . '" target="_blank" href="https://whois.domaintools.com/' . $ip . '"><img src="' . $whois . '" class="icon-action"></a>';
				echo '
					<tr style="background-color:white">
					<td>' . $dt . '</td>
					<td>' . $em . '</td>
					<td>' . $ip, $who, $stopper, $honeysearch, $botsearch . '';
				if ( stripos( $reason, 'passed' ) !== false && ( $id == '/' || strpos( $id, 'login' ) ) !== false || strpos( $id, 'register' ) !== false && !in_array( $ip, $blist ) && !in_array( $ip, $wlist ) ) {
					$ajaxurl = admin_url( 'admin-ajax.php' );
					echo '<a href="" onclick="sfs_ajax_process(\'' . $ip . '\',\'log\',\'add_black\',\'' . $ajaxurl . '\');return false;" title="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '" alt="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '"><img src="' . $down . '" class="icon-action"></a>';
					$options = get_option( 'ds_options' );
					$apikey  = $options['apikey'];
					if ( !empty( $apikey ) && !empty( $em ) ) {
						$href = 'href="#"';
						$onclick = 'onclick="sfs_ajax_report_spam(this,\'registration\',\'' . $blog . '\',\'' . $ajaxurl . '\',\'' . $em . '\',\'' . $ip . '\',\'' . $au . '\');return false;"';
						echo '| ';
						echo '<a title="' . esc_attr__( 'Report to Stop Forum Spam (SFS)', 'dam-spam' ) . '" ' . $href, $onclick . ' class="delete:the-comment-list:comment-$id::delete=1 delete vim-d vim-destructive">' . __( 'Report to SFS', 'dam-spam' ) . '</a>';
					}
				}
				echo '
					</td><td>' . $au . '</td>
					<td>' . $id . '</td>
					<td>' . $reason . '</td>';
				if ( function_exists( 'is_multisite' ) && is_multisite() ) {
					// switch to blog and back
					$blogname  = get_blog_option( $blog, 'blogname' );
					$blogadmin = esc_url( get_admin_url( $blog ) );
					$blogadmin = trim( $blogadmin, '/' );
					echo '<td style="font-size:.9em;padding:2px" align="center">';
					echo '<a href="' . $blogadmin . '/edit-comments.php">' . $blogname . '</a>';
					echo '</td>';
				}
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
		<script>
		function sortTable(n) {
			var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
			table = document.getElementById("dstable");
			switching = true;
			// set the sorting direction to ascending
			dir = "asc";
			// make a loop that will continue until no switching has been done
			while (switching) {
				// start by saying: no switching is done
				switching = false;
				rows = table.rows;
				// loop through all table rows (except the first, which contains table headers)
				for (i = 1; i < (rows.length - 1); i++) {
					// start by saying there should be no switching
					shouldSwitch = false;
					// get the two elements you want to compare, one from current row and one from the next
					x = rows[i].getElementsByTagName("TD")[n];
					y = rows[i + 1].getElementsByTagName("TD")[n];
					// check if the two rows should switch place, based on the direction, asc or desc
					if (dir == "asc") {
						if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
							// if so, mark as a switch and break the loop
							shouldSwitch = true;
							break;
						}
					} else if (dir == "desc") {
						if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
							// if so, mark as a switch and break the loop
							shouldSwitch = true;
							break;
						}
					}
				}
				if (shouldSwitch) {
					// if a switch has been marked, make the switch and mark that a switch has been done
					rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
					switching = true;
					// each time a switch is done, increase this count by 1
					switchcount++;
				} else {
					// if no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again
					if (switchcount == 0 && dir == "asc") {
						dir = "desc";
						switching = true;
					}
				}
			}
		}
		function ds_search() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("dsinput");
			filter = input.value.toUpperCase();
			table = document.getElementById("dstable");
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				if (td) {
					txtValue = td.textContent || td.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = '';
					} else {
						tr[i].style.display = "none";
					}
				}
			}
		}
		</script>
	<?php } ?>
</div>