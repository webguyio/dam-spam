<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( 'Access Blocked' );
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- Admin cleanup/diagnostic page requires direct DB access
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

dam_spam_fix_post_vars();

$active_tab = !empty( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'disable-users';

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-head"><?php esc_html_e( 'Cleanup — Dam Spam', 'dam-spam' ); ?></h1>
	<?php if ( array_key_exists( 'autol', $_POST ) || array_key_exists( 'delo', $_POST ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
	}
	?>
	<div class="dam-spam-info-box">
		<h2 class="nav-tab-wrapper">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=dam-spam-cleanup&tab=disable-users' ) ); ?>" class="nav-tab <?php echo 'disable-users' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Disable Users', 'dam-spam' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=dam-spam-cleanup&tab=delete-comments' ) ); ?>" class="nav-tab <?php echo 'delete-comments' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Delete Comments', 'dam-spam' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=dam-spam-cleanup&tab=db-cleanup' ) ); ?>" class="nav-tab <?php echo 'db-cleanup' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Database Cleanup', 'dam-spam' ); ?></a>
		</h2>
		<br>
		<?php
		global $wpdb;
		$ptab = $wpdb->options;
		$nonce = '';
		if ( array_key_exists( 'dam_spam_opt_control', $_POST ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_POST['dam_spam_opt_control'] ) );
		}
		if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
			if ( array_key_exists( 'view', $_POST ) ) {
				$op = sanitize_text_field( wp_unslash( $_POST['view'] ) );
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Admin diagnostic tool, capability checked, nonce verified, input sanitized. Viewing any option is intentional functionality.
				$v = get_option( $op );
				if ( is_serialized( $v ) && false !== @unserialize( $v ) ) {
					$v = @unserialize( $v );
				}
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r -- Intentional debug output for viewing option contents
				$v = print_r( $v, true );
				$v = htmlentities( $v );
				// translators: %s is the option name
				printf( '<h2>' . esc_html__( 'contents of %s', 'dam-spam' ) . '</h2><pre>%s</pre>', esc_html( $op ), esc_html( $v ) );
			}
			if ( array_key_exists( 'autol', $_POST ) ) {
				foreach ( sanitize_text_field( wp_unslash( $_POST['autol'] ) ) as $name ) {
					$name = sanitize_text_field( wp_unslash( $name ) );
					$au = substr( $name, 0, strpos( $name, '_' ) );
					if ( strtolower( $au ) === 'no' ) {
						$au = 'yes';
					} else {
						$au = 'no';
					}
					$name = substr( $name, strpos( $name, '_' ) + 1 );
					// translators: %1$s is the option name, %2$s is the new autoload value
					printf( esc_html__( 'changing %1$s autoload to %2$s', 'dam-spam' ), esc_html( $name ), esc_html( $au ) );
					echo '<br>';
					$wpdb->update(
						$ptab,
						array( 'autoload' => $au ),
						array( 'option_name' => $name ),
						array( '%s' ),
						array( '%s' )
					);
				}
			}
			if ( array_key_exists( 'delo', $_POST ) ) {
				foreach ( sanitize_text_field( wp_unslash( $_POST['delo'] ) ) as $name ) {
					$name = sanitize_key( wp_unslash( $name ) );
					// translators: %s is the option name being deleted
					printf( esc_html__( 'deleting %s', 'dam-spam' ), esc_html( $name ) );
					echo '<br>';
					$wpdb->delete(
						$ptab,
						array( 'option_name' => $name ),
						array( '%s' )
					);
				}
			}
		}
		$magic_string = __( "I am sure I want to delete all pending comments and realize this can't be undone", 'dam-spam' );
		if ( isset( $_POST['dam_spam_delete_pending_comment'] ) && isset( $_POST['dam_spam_delete_pending_comment_confirmation_text'] ) && sanitize_text_field( wp_unslash( $_POST['dam_spam_delete_pending_comment_confirmation_text'] ) ) === $magic_string ) {
			if ( !current_user_can( 'manage_options' ) ) {
				return;
			}
			$wpdb->delete(
				$wpdb->comments,
				array( 'comment_approved' => '0' ),
				array( '%s' )
			);
			esc_html_e( 'Comments deleted.', 'dam-spam' );
		}
		$sysops = array(
			'_transient_',
			'active_plugins',
			'admin_email',
			'advanced_edit',
			'avatar_default',
			'avatar_rating',
			'blocklist_keys',
			'blog_charset',
			'blog_public',
			'blogdescription',
			'blogname',
			'can_compress_scripts',
			'category_base',
			'close_comments_days_old',
			'close_comments_for_old_posts',
			'comment_max_links',
			'comment_moderation',
			'comment_order',
			'comment_registration',
			'comment_allowlist',
			'comments_notify',
			'comments_per_page',
			'cron',
			'current_theme',
			'dashboard_widget_options',
			'date_format',
			'db_version',
			'default_category',
			'default_comment_status',
			'default_comments_page',
			'default_email_category',
			'default_link_category',
			'default_ping_status',
			'default_pingback_flag',
			'default_post_edit_rows',
			'default_post_format',
			'default_role',
			'embed_autourls',
			'embed_size_h',
			'embed_size_w',
			'enable_app',
			'enable_xmlrpc',
			'fileupload_url',
			'ftp_credentials',
			'gmt_offset',
			'gzipcompression',
			'hack_file',
			'home',
			'ht_user_roles',
			'html_type',
			'image_default_align',
			'image_default_link_type',
			'image_default_size',
			'initial_db_version',
			'large_size_h',
			'large_size_w',
			'links_recently_updated_append',
			'links_recently_updated_prepend',
			'links_recently_updated_time',
			'links_updated_date_format',
			'mailserver_login',
			'mailserver_pass',
			'mailserver_port',
			'mailserver_url',
			'medium_size_h',
			'medium_size_w',
			'moderation_keys',
			'moderation_notify',
			'page_comments',
			'page_for_posts',
			'page_on_front',
			'permalink_structure',
			'ping_sites',
			'posts_per_page',
			'posts_per_rss',
			'recently_edited',
			'require_name_email',
			'rdam_spam_use_excerpt',
			'show_avatars',
			'show_on_front',
			'sidebars_widgets',
			'siteurl',
			'start_of_week',
			'sticky_posts',
			'stylesheet',
			'tag_base',
			'template',
			'theme_modam_spam_harptab',
			'theme_modam_spam_twentyeleven',
			'theme_switched',
			'thread_comments',
			'thread_comments_depth',
			'thumbnail_crop',
			'thumbnail_size_h',
			'thumbnail_size_w',
			'time_format',
			'timezone_string',
			'uninstall_plugins',
			'upload_path',
			'upload_url_path',
			'uploadam_spam_use_yearmonth_folders',
			'use_balanceTags',
			'use_smilies',
			'use_trackback',
			'users_can_register',
			'widget_archives',
			'widget_categories',
			'widget_meta',
			'widget_recent-comments',
			'widget_recent-posts',
			'widget_rss',
			'widget_search',
			'widget_text',
			'akismet_available_servers',
			'auth_key',
			'auth_salt',
			'akismet_connectivity_time',
			'akismet_discard_month',
			'akismet_spam_count',
			'akismet_show_user_comments_approved',
			'akismet_strictness',
			'category_children',
			'db_upgraded',
			'recently_activated',
			'rewrite_rules',
			'wordpress_api_key',
			'theme_modam_spam_',
			'widget_',
			'_user_roles',
			'logged_in_key',
			'logged_in_salt',
			'nonce_key',
			'nonce_salt',
			'nav_menu_options',
			'auto_core_update_notified',
			'link_manager_enabled',
			'WPLANG',
			'dam_spam_options',
			'dam_spam_stats',
			'blacklist_keys',
			'comment_whitelist',
			'customize_stashed_theme_mods',
			'finished_splitting_shared_terms',
			'fresh_site',
			'recovery_keys',
			'recovery_mode_email_last_sent',
			'show_comments_cookies_opt_in',
			'site_icon',
			'theme_switch_menu_locations',
			'wp_page_for_privacy_policy',
		);
		$sql = $wpdb->prepare( 'SELECT * FROM %i ORDER BY autoload, option_name', $ptab );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql is prepared above
		$arows = $wpdb->get_results( $sql, ARRAY_A );
		$rows = array();
		foreach ( $arows as $row ) {
			$uop = true;
			$name = $row['option_name'];
			if ( !in_array( $name, $sysops ) ) {
				foreach ( $sysops as $op ) {
					if ( strpos( $name, $op ) !== false ) {
						$uop = false;
						break;
					}
				}
			} else {
				$uop = false;
			}
			if ( $uop ) {
				$rows[] = $row;
			}
		}
		$nonce = wp_create_nonce( 'dam_spam_update' );
		?>
		<form method="post" name="DOIT2" action="">
			<input type="hidden" name="dam_spam_opt_control" value="<?php echo esc_attr( $nonce ); ?>">
			<?php if ( !isset( $_GET['tab'] ) || sanitize_key( wp_unslash( $_GET['tab'] ) ) === 'disable-users' ) : ?>
				<?php include_once DAM_SPAM_PATH . 'includes/user-list-filter.php'; ?>
			<?php endif; ?>
			<?php
			$pending_comment_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT comment_ID FROM %i WHERE comment_approved = %s', $wpdb->comments, '0' ) );
			$pending_comments_count = count( $pending_comment_ids );
			if ( isset( $_GET['tab'] ) && sanitize_key( wp_unslash( $_GET['tab'] ) ) === 'delete-comments' ) {
				if ( $pending_comments_count > 0 ) {
					?>
					<p>
						<?php
						printf(
							// translators: %s is the number of pending comments
							esc_html( _n(
								'You have %s pending comment in your site. Do you want to delete it?',
								'You have %s pending comments in your site. Do you want to delete all of them?',
								$pending_comments_count,
								'dam-spam'
							) ),
							esc_html( number_format_i18n( $pending_comments_count ) )
						);
						?>
					</p>
					<p>
						<?php esc_html_e( 'You have to type the following text into the textbox to delete all the pending comments:', 'dam-spam' ); ?>
					</p>
					<blockquote>
						<em>
							<?php echo esc_html( $magic_string ); ?>
						</em>
					</blockquote>
					<textarea name="dam_spam_delete_pending_comment_confirmation_text"></textarea>
					<button name="dam_spam_delete_pending_comment" class="button-primary"><?php esc_html_e( 'Delete', 'dam-spam' ); ?></button>
					<?php
				} else {
					?>
					<p>
						<?php esc_html_e( 'There are no pending or spam comments.', 'dam-spam' ); ?>
					</p>
					<?php
				}
			}
			?>
			<?php if ( isset( $_GET['tab'] ) && sanitize_key( wp_unslash( $_GET['tab'] ) ) === 'db-cleanup' ) : ?>
			<p><?php esc_html_e( 'Inspect and delete orphan or suspicious options or change plugin options so that they don\'t autoload. Be aware that you can break some plugins by deleting their options.', 'dam-spam' ); ?></p>
			<table id="dam-spam-table" name="dam-spam-table" cellspacing="2">
				<thead>
					<tr>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'Option', 'dam-spam' ); ?></th>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'Autoload', 'dam-spam' ); ?></th>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'Size', 'dam-spam' ); ?></th>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'Change Autoload', 'dam-spam' ); ?></th>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'Delete', 'dam-spam' ); ?></th>
						<th class="dam-spam-cleanup"><?php esc_html_e( 'View Contents', 'dam-spam' ); ?></th>
					</tr>
				</thead>
				<?php
				foreach ( $rows as $row ) {
					$option_name = $row['option_name'];
					$option_value = $row['option_value'];
					$autoload = $row['autoload'];
					$sz = strlen( $option_value );
					$sz = number_format( $sz );
					?>
					<tr class="dam-spam-cleanup-tr">
						<td align="center"><?php echo esc_html( $option_name ); ?></td>
						<td align="center"><?php echo esc_html( $autoload ); ?></td>
						<td align="center"><?php echo esc_html( $sz ); ?></td>
						<td align="center"><input type="checkbox" value="<?php echo esc_attr( $autoload . '_' . $option_name ); ?>" name="autol[]">&nbsp;<?php echo esc_html( $autoload ); ?></td>
						<td align="center"><input type="checkbox" value="<?php echo esc_attr( $option_name ); ?>" name="delo[]"></td>
						<td align="center"><button type="submit" name="view" value="<?php echo esc_attr( $option_name ); ?>"><?php esc_html_e( 'View', 'dam-spam' ); ?></button></td>
					</tr>
					<?php
				}
				?>
			</table>
			<p class="submit"><input class="button-primary" value="<?php esc_attr_e( 'Update', 'dam-spam' ); ?>" type="submit" onclick="return confirm('Are you sure? These changes are permanent.');"></p>
			<?php endif; ?>
		</form>
		<?php
		$m1 = memory_get_usage();
		$m3 = memory_get_peak_usage();
		$m1 = number_format( $m1 );
		$m3 = number_format( $m3 );
		// translators: %1$s is current memory usage, %2$s is peak memory usage
		printf( '<p>' . esc_html__( 'Memory Usage Currently: %1$s Peak: %2$s', 'dam-spam' ) . '</p>', esc_html( $m1 ), esc_html( $m3 ) );
		$nonce = wp_create_nonce( 'dam_spam_update2' );
		$showtransients = false;
		if ( $showtransients && dam_spam_count_transients() > 0 ) { ?>
			<hr>
			<p><?php esc_html_e( 'WordPress creates temporary objects in the database called transients. You can clean these up safely and it might speed things up.', 'dam-spam' ); ?></p>
			<form method="post" name="DOIT2" action="">
				<input type="hidden" name="dam_spam_opt_tdel" value="<?php echo esc_attr( $nonce ); ?>">
				<p class="submit"><input class="button-primary" value="<?php esc_attr_e( 'Delete Transients', 'dam-spam' ); ?>" type="submit"></p>
			</form>
			<?php
			$nonce = '';
			if ( array_key_exists( 'dam_spam_opt_tdel', $_POST ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['dam_spam_opt_tdel'] ) );
			}
			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update2' ) ) {
				dam_spam_delete_transients();
			}
			?>
			<p><?php
			$countT = dam_spam_count_transients();
			// translators: %s is the number of transients found
			printf( esc_html__( 'Currently there are %s found.', 'dam-spam' ), esc_html( $countT ) );
			?></p>
		<?php
		}
		?>
	</div>
</div>

<?php

function dam_spam_count_transients() {
	$blog_id = absint( get_current_blog_id() );
	if ( $blog_id < 1 ) {
		return 0;
	}
	global $wpdb;
	$optimeout = time() - 60;
	$table = $wpdb->get_blog_prefix( $blog_id ) . 'options';
	$count = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM %i WHERE INSTR(option_name, %s) > 0",
			$table,
			'DAM_SPAM_SECRET_WORD'
		)
	);
	if ( empty( $count ) ) {
		$count = 0;
	}
	return $count;
}

function dam_spam_delete_transients() {
	$blog_id = absint( get_current_blog_id() );
	if ( $blog_id < 1 ) {
		return;
	}
	global $wpdb;
	$optimeout = time() - 60;
	$table = $wpdb->get_blog_prefix( $blog_id ) . 'options';
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM %i WHERE INSTR(option_name, %s) > 0",
			$table,
			'DAM_SPAM_SECRET_WORD'
		)
	);
}

?>