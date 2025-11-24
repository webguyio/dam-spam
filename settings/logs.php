<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

dam_spam_fix_post_vars();
$icons = dam_spam_get_icon_urls();
extract( $icons );
$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

?>

<div id="dam-spam-plugin" class="wrap">
	<h1 id="dam-spam-head"><?php esc_html_e( 'Logs — Dam Spam', 'dam-spam' ); ?></h1>
	<?php
	$stats = dam_spam_get_stats();
	extract( $stats );
	$options = dam_spam_get_options();
	extract( $options );
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$nonce = '';
	$msg = '';
	if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
		$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
	}
	if ( wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
		if ( array_key_exists( 'dam_spam_clear_hist', $_POST ) ) {
			$hist = array();
			$stats['hist'] = $hist;
			$spam_count = 0;
			$stats['spam_count'] = $spam_count;
			$spam_date = $now;
			$stats['spam_date'] = $spam_date;
			dam_spam_set_stats( $stats );
			extract( $stats );
			$msg = '<div class="notice notice-success"><p>' . esc_html__( 'Log Cleared', 'dam-spam' ) . '</p></div>';
		}
		if ( array_key_exists( 'dam_spam_update_log_size', $_POST ) ) {
			if ( array_key_exists( 'dam_spam_hist', $_POST ) ) {
				$dam_spam_hist = isset( $_POST['dam_spam_hist'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_hist'] ) ) : '';
				$options['dam_spam_hist'] = $dam_spam_hist;
				$msg = '<div class="notice notice-success"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
				dam_spam_set_options( $options );
			}
		}
	}
	if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	}
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->spam );
	if ( $num_comm->spam > 0 && DAM_SPAM_MU != 'Y' ) { ?>
		<p><?php
			// translators: %s is the name of the log file being displayed
			printf( esc_html__( 'There are %1$s%2$s%3$s spam comments waiting to be reported.', 'dam-spam' ), '<a href="edit-comments.php?comment_status=spam">', esc_html( $num ), '</a>' );
		?></p>
	<?php }
	$num_comm = wp_count_comments();
	$num	  = number_format_i18n( $num_comm->moderated );
	if ( $num_comm->moderated > 0 && DAM_SPAM_MU != 'Y' ) { ?>
		<p><?php
			// translators: %s is the name of the log file being displayed
			printf( esc_html__( 'There are %1$s%2$s%3$s comments waiting to be moderated.', 'dam-spam' ), '<a href="edit-comments.php?comment_status=moderated">', esc_html( $num ), '</a>' );
		?></p>
	<?php }
	$nonce = wp_create_nonce( 'dam_spam_update' );
	?>
	<form method="post" action="">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="dam_spam_update_log_size" value="true">
		<h2><?php esc_html_e( 'Log Size', 'dam-spam' ); ?></h2>
		<?php esc_html_e( 'Select the number of events to save in the log.', 'dam-spam' ); ?><br>
		<p class="submit">
			<select name="dam_spam_hist">
				<option value="10" <?php if ( $dam_spam_hist == '10' ) { echo 'selected="true"'; } ?>>10</option>
				<option value="25" <?php if ( $dam_spam_hist == '25' ) { echo 'selected="true"'; } ?>>25</option>
				<option value="50" <?php if ( $dam_spam_hist == '50' ) { echo 'selected="true"'; } ?>>50</option>
				<option value="75" <?php if ( $dam_spam_hist == '75' ) { echo 'selected="true"'; } ?>>75</option>
				<option value="100" <?php if ( $dam_spam_hist == '100' ) { echo 'selected="true"'; } ?>>100</option>
				<option value="150" <?php if ( $dam_spam_hist == '150' ) { echo 'selected="true"'; } ?>>150</option>
			</select>
			<input class="button-primary" value="<?php esc_html_e( 'Update Log Size', 'dam-spam' ); ?>" type="submit"><br>
			<em><small><?php esc_html_e( 'Warning: Changing the log size will wipe current logs.', 'dam-spam' ); ?></small></em>
		</p>
		<form method="post" action="">
			<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
			<input type="hidden" name="dam_spam_clear_hist" value="true">
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Clear Logs', 'dam-spam' ); ?>" type="submit"></p>
		</form>
		<?php
		if ( empty( $hist ) ) {
			esc_html_e( 'Nothing in the log.', 'dam-spam' );
		} else { ?>
		<br>
		<input type="text" id="dam-spam-input" onkeyup="damSpamSearch()" placeholder="<?php esc_html_e( 'Date Search', 'dam-spam' ); ?>" title="<?php esc_html_e( 'Filter by a Value', 'dam-spam' ); ?>">
		<table id="dam-spam-table" name="dam-spam-table" cellspacing="2">
			<thead>
				<tr>
					<th onclick="damSpamSortTable(0)" class="filterhead dam-spam-cleanup"><?php esc_html_e( 'Date/Time', 'dam-spam' ); ?></th>
					<th class="dam-spam-cleanup"><?php esc_html_e( 'Email', 'dam-spam' ); ?></th>
					<th class="dam-spam-cleanup"><?php esc_html_e( 'IP', 'dam-spam' ); ?></th>
					<th class="dam-spam-cleanup"><?php esc_html_e( 'User', 'dam-spam' ); ?></th>
					<th class="dam-spam-cleanup"><?php esc_html_e( 'Script', 'dam-spam' ); ?></th>
					<th class="dam-spam-cleanup"><?php esc_html_e( 'Reason', 'dam-spam' ); ?></th>
			<?php if ( function_exists( 'is_multisite' ) && is_multisite() ) { ?>
			</thead>
			<tbody>
			<?php } ?>
			</tr>
			<?php
			krsort( $hist );
			foreach ( $hist as $key => $data ) {
				$em = wp_strip_all_tags( trim( $data[1] ) );
				$dt = wp_strip_all_tags( $key );
				$ip = $data[0];
				$au = wp_strip_all_tags( $data[2] );
				$id = wp_strip_all_tags( $data[3] );
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
				$stopper	 = '<a title="' . esc_attr__( 'Check Stop Forum Spam', 'dam-spam' ) . '" target="_blank" href="https://www.stopforumspam.com/search.php?q=' . $ip . '"><img src="' . $stop . '" class="icon-action"></a>';
				$honeysearch = '<a title="' . esc_attr__( 'Check Project HoneyPot', 'dam-spam' ) . '" target="_blank" href="https://www.projecthoneypot.org/ip_' . $ip . '"><img src="' . $search . '" class="icon-action"></a>';
				$botsearch   = '<a title="' . esc_attr__( 'Check BotScout', 'dam-spam' ) . '" target="_blank" href="https://botscout.com/search.htm?stype=q&sterm=' . $ip . '"><img src="' . $search . '" class="icon-action"></a>';
				$who		 = '<br><a title="' . esc_attr__( 'Look Up WHOIS', 'dam-spam' ) . '" target="_blank" href="https://whois.domaintools.com/' . $ip . '"><img src="' . $whois . '" class="icon-action"></a>';
				echo '
					<tr>
					<td>' . wp_kses_post( $dt ) . '</td>
					<td>' . wp_kses_post( $em ) . '</td>
					<td>' . esc_html( $ip ) . wp_kses_post( $who . $stopper . $honeysearch . $botsearch );
				if ( stripos( $reason, 'passed' ) !== false && ( $id == '/' || strpos( $id, 'login' ) ) !== false || strpos( $id, 'register' ) !== false && !in_array( $ip, $block_list ) && !in_array( $ip, $allow_list ) ) {
					$ajaxurl = admin_url( 'admin-ajax.php' );
					echo '<a href="" onclick="damSpamAjaxProcess(\'' . esc_js( $ip ) . '\',\'log\',\'add_black\',\'' . esc_js( $ajaxurl ) . '\');return false;" title="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '" alt="' . esc_attr__( 'Add to Block List', 'dam-spam' ) . '"><img src="' . esc_url( $down ) . '" class="icon-action"></a>';
					$options = get_option( 'dam_spam_options' );
					$apikey  = $options['apikey'];
					if ( !empty( $apikey ) && !empty( $em ) ) {
						echo '| ';
						echo '<a title="' . esc_attr__( 'Report to Stop Forum Spam', 'dam-spam' ) . '" href="#" onclick="damSpamAjaxReportSpam(this,\'registration\',\'' . esc_js( $blog ) . '\',\'' . esc_js( $ajaxurl ) . '\',\'' . esc_js( $em ) . '\',\'' . esc_js( $ip ) . '\',\'' . esc_js( $au ) . '\');return false;" class="delete:the-comment-list:comment-$id::delete=1 delete vim-d vim-destructive">' . esc_html__( 'Report to SFS', 'dam-spam' ) . '</a>';
					}
				}
				echo '
					</td><td>' . wp_kses_post( $au ) . '</td>
					<td>' . wp_kses_post( $id ) . '</td>
					<td>' . wp_kses_post( $reason ) . '</td>';
				if ( function_exists( 'is_multisite' ) && is_multisite() ) {
					$blogname  = get_blog_option( $blog, 'blogname' );
					$blogadmin = esc_url( get_admin_url( $blog ) );
					$blogadmin = trim( $blogadmin, '/' );
					echo '<td>';
					echo '<a href="' . esc_url( $blogadmin ) . '/edit-comments.php">' . esc_html( $blogname ) . '</a>';
					echo '</td>';
				}
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
	<?php } ?>
</div>
