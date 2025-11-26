<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Template displays data passed from parent, no direct form processing
// phpcs:disable WordPress.DB.DirectDatabaseQuery -- User list filtering requires direct queries

$nonce_field = wp_create_nonce( 'dam_spam_user_filter_nonce' );

?>

<input type="hidden" name="op" value="search_users">
<input type="hidden" name="dam_spam_user_filter_nonce" value="<?php echo esc_attr( $nonce_field ); ?>">

<table>
	<tr>
		<td colspan="2">
			<h3>
				<div class="section-title">
					<?php esc_html_e( 'Flags', 'dam-spam' ) ?>
				</div>
			</h3>
			<hr width="50%" align="left">
			<br>
			<?php esc_html_e( 'Show in list if... ', 'dam-spam' ) ?>
			<select name="flagsCND">
				<?php
				$flags_cnd = isset( $_POST['flagsCND'] ) ? sanitize_text_field( wp_unslash( $_POST['flagsCND'] ) ) : '';
				?>
				<option value="intersept" <?php selected( $flags_cnd, 'intersept' ); ?>>
					<?php esc_html_e( 'ALL are true', 'dam-spam' ) ?>
				</option>
				<option value="add" <?php selected( $flags_cnd, 'add' ); ?>>
					<?php esc_html_e( 'ANY are true', 'dam-spam' ) ?>
				</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php esc_html_e( 'User has...', 'dam-spam' ) ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Approved Comments', 'dam-spam' ) ?>
		</td>
		<td align="left" width="250">
			<?php
			$f_approve = isset( $_POST['f_approve'] ) ? sanitize_text_field( wp_unslash( $_POST['f_approve'] ) ) : '0';
			?>
			<label for="flag_approve_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="flag_approve_no" type="radio" name="f_approve" value="no" <?php checked( $f_approve, 'no' ); ?>>
			</label>
			<label for="flag_approve_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="flag_approve_yes" type="radio" name="f_approve" value="yes" <?php checked( $f_approve, 'yes' ); ?>>
			</label>
			<label for="flag_approve_nomatter">
				<?php esc_html_e( 'Ignore', 'dam-spam' ) ?>
				<input id="flag_approve_nomatter" type="radio" name="f_approve" value="0" <?php checked( $f_approve, '0' ); ?>>
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Spam Comments', 'dam-spam' ) ?>
		</td>
		<td align="left">
			<?php
			$has_spam = isset( $_POST['has_spam'] ) ? sanitize_text_field( wp_unslash( $_POST['has_spam'] ) ) : '0';
			?>
			<label for="flag_has_spam_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="flag_has_spam_no" type="radio" name="has_spam" value="no" <?php checked( $has_spam, 'no' ); ?>>
			</label>
			<label for="flag_has_spam_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="flag_has_spam_yes" type="radio" name="has_spam" value="yes" <?php checked( $has_spam, 'yes' ); ?>>
			</label>
			<label for="flag_has_spam_nomatter">
				<?php esc_html_e( 'Ignore', 'dam-spam' ) ?>
				<input id="flag_has_spam_nomatter" type="radio" name="has_spam" value="0" <?php checked( $has_spam, '0' ); ?>>
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Same First/Last Name', 'dam-spam' ) ?>
		</td>
		<td align="left" width="250">
			<?php
			$dam_spam_check_name = isset( $_POST['dam_spam_check_name'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_check_name'] ) ) : 'no';
			?>
			<label for="dam_spam_check_name_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="dam_spam_check_name_no" type="radio" name="dam_spam_check_name" value="no" <?php checked( $dam_spam_check_name, 'no' ); ?>>
			</label>
			<label for="dam_spam_check_name_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="dam_spam_check_name_yes" type="radio" name="dam_spam_check_name" value="yes" <?php checked( $dam_spam_check_name, 'yes' ); ?>>
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Specific TLD (.xxx, .blog)', 'dam-spam' ) ?>
		</td>
		<td align="left" width="250">
			<?php
			$dam_spam_domain = isset( $_POST['dam_spam_domain'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_domain'] ) ) : 'no';
			$dam_spam_domain_text = isset( $_POST['dam_spam_domain_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dam_spam_domain_text'] ) ) : '';
			?>
			<label for="dam_spam_domain_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="dam_spam_domain_no" type="radio" name="dam_spam_domain" value="no" <?php checked( $dam_spam_domain, 'no' ); ?>>
			</label>
			<label for="dam_spam_domain_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="dam_spam_domain_yes" type="radio" name="dam_spam_domain" value="yes" <?php checked( $dam_spam_domain, 'yes' ); ?>>
			</label>
			<textarea cols="100" rows="2" name="dam_spam_domain_text"><?php echo esc_textarea( $dam_spam_domain_text ); ?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2">
			<h3>
				<div class="section-title">
					<?php esc_html_e( 'Filters', 'dam-spam' ) ?>
				</div>
			</h3>
			<hr width="50%" align="left">
			<br>
			<label for="usernameFilter">
				<?php esc_html_e( 'Username', 'dam-spam' ) ?>
			</label>
			<?php
			$dam_spam_username = isset( $_POST['dam_spam_username'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_username'] ) ) : '';
			?>
			<input type="text" size="15" name="dam_spam_username" value="<?php echo esc_attr( $dam_spam_username ); ?>" id="usernameFilter">
			<br>
			<small>
				<?php esc_html_e( 'Refine list by a username (test, example).', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2">
			<label for="flag_daysleft">
			<?php esc_html_e( 'User was created', 'dam-spam' ) ?>
				<select name="f_daysleft">
				<?php
				$f_daysleft = isset( $_POST['f_daysleft'] ) ? sanitize_text_field( wp_unslash( $_POST['f_daysleft'] ) ) : '1';
				?>
					<option value="1" <?php selected( $f_daysleft, '1' ); ?>>
						<?php esc_html_e( 'more', 'dam-spam' ) ?>
					</option>
					<option value="0" <?php selected( $f_daysleft, '0' ); ?>>
						<?php esc_html_e( 'less', 'dam-spam' ) ?>
					</option>
				</select>
				<?php esc_html_e( 'than', 'dam-spam' ) ?>
				<?php
				$daysleft = isset( $_POST['daysleft'] ) ? absint( $_POST['daysleft'] ) : 7;
				?>
				<input type="text" size="4" name="daysleft" value="<?php echo esc_attr( $daysleft ); ?>">
				<?php esc_html_e( 'days ago.', 'dam-spam' ) ?>
			</label>
			<br>
			<small>
				<?php esc_html_e( 'Users need time to begin commenting. This filter can show recent registrations.', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2">
			<label for="f_lastlogin">
			<?php esc_html_e( 'Last time user logged in is more than', 'dam-spam' ) ?>
				<select name="f_lastlogin">
					<option value="0">
						<?php esc_html_e( 'No Filter', 'dam-spam' ) ?>
					</option>
					<?php
					$f_lastlogin = isset( $_POST['f_lastlogin'] ) ? absint( $_POST['f_lastlogin'] ) : 0;
					$columns = array( 15, 30, 60, 90, 180, 360, 720 );
					foreach ( $columns as $v ) {
						echo '<option value="' . esc_attr( $v ) . '" ' . selected( $f_lastlogin, $v, false ) . '>' . esc_html( $v ) . '</option>';
					}
					?>
				</select>
				<?php esc_html_e( 'days ago.', 'dam-spam' ) ?>
			</label>
			<br>
			<small>
				<?php esc_html_e( 'Search by last login.', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label for="user_role">
				<?php esc_html_e( 'User role ', 'dam-spam' ) ?>
			</label>
			<select name="user_role">
				<?php
				global $wp_roles;
				$roles = array( '' => 'Any Role' ) + $wp_roles->get_names();
				$user_role = isset( $_POST['user_role'] ) ? sanitize_text_field( wp_unslash( $_POST['user_role'] ) ) : '';
				foreach ( $roles as $roleId => $roleName ) {
					echo '<option value="' . esc_attr( $roleId ) . '" ' . selected( $user_role, $roleId, false ) . '>' . esc_html( $roleName ) . '</option>';
				}
				?>
			</select>
			<br>
			<small>
				<?php esc_html_e( 'Filter by user role.', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
			<h3>
				<div class="section-title">
					<?php esc_html_e( 'Table Formatting', 'dam-spam' ) ?>
				</div>
			</h3>
			<hr width="50%" align="left">
			<br>
			<label for="sort_order">
				<?php esc_html_e( 'Show', 'dam-spam' ) ?>
			</label>
			<select id="max_size_output" name="max_size_output">
			<?php
			$max_size_output = isset( $_POST['max_size_output'] ) ? sanitize_text_field( wp_unslash( $_POST['max_size_output'] ) ) : '150';
			$columns = array( '150', '300', '500', '1000', '3000', 'All' );
			foreach ( $columns as $v ) {
				echo '<option value="' . esc_attr( $v ) . '" ' . selected( $max_size_output, $v, false ) . '>' . esc_html( $v ) . '</option>';
			}
			?>
			</select>
			<?php esc_html_e( 'records', 'dam-spam' ) ?>
			<br>
			<small>
				<?php echo esc_html__( 'Max sent allowed is', 'dam-spam' ) . ' ' . esc_html( ini_get( 'max_input_vars' ) ) . ' ' . esc_html__( 'input vars.', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input class="button-primary" type="submit" value="<?php esc_html_e( 'Search', 'dam-spam' ) ?>" name="dam_spam_search">
		</td>
	</tr>
</table>

<a name="outputs"></a>

<?php

$name = array();
if ( isset( $_POST['dam_spam_search'] ) && isset( $_POST['dam_spam_user_filter_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_user_filter_nonce'] ) ), 'dam_spam_user_filter_nonce' ) ) {
	if ( isset( $_POST['dam_spam_username'] ) ) {
		$sanitized_post = array();
		foreach ( $_POST as $key => $value ) {
			$sanitized_post[ sanitize_key( $key ) ] = wp_unslash( $value );
		}
		$userListObject = dam_spam_getUsersList( $sanitized_post, '' );
		$user_list = $userListObject->rows;
		$total = $userListObject->total;
		if ( empty( $userListObject->rows ) ) {
			echo '<p><strong>' . esc_html__( 'No users are found.', 'dam-spam' ) . '</strong></p>';
		} else {
			include_once 'user-list.php';
		}
	}
}

function dam_spam_isVIPUser( $userID ) {
	global $user_ID;
	if ( $userID == $user_ID ) {
		return esc_html__( 'I can\'t delete your profile!', 'dam-spam' );
	}
	if ( $userID == 1 ) {
		return esc_html__( 'I will never delete the super user!', 'dam-spam' );
	}
	return false;
}

function dam_spam_getUsersList( $environment, $ARGS = array() ) {
	global $wpdb;
	$conditions = array();
	$conditions_sec2 = array( 1 );
	$joins = array(
		"FROM {$wpdb->prefix}users WU",
		"LEFT JOIN {$wpdb->prefix}comments WC ON WC.user_id = WU.ID",
		"LEFT JOIN {$wpdb->prefix}usermeta WUCAP ON WUCAP.user_id = WU.ID AND WUCAP.meta_key = 'wp_capabilities'",
		"LEFT JOIN {$wpdb->prefix}usermeta WUMD ON WUMD.user_id = WU.ID AND WUMD.meta_key = '_IUD_deltime'",
		"LEFT JOIN {$wpdb->prefix}usermeta WUMDIS ON WUMDIS.user_id = WU.ID AND WUMDIS.meta_key = '_IUD_userBlockedTime'"
	);
	$havings = array();
	$groupBy = array( 'WU.ID, WU.user_login, WU.user_email, WU.user_url, WU.user_registered, WU.display_name, WUCAP.meta_value, WUM21.meta_value, WUMD.meta_value, WUMDIS.meta_value' );
	if ( !empty( $ARGS['f_approve'] ) ) {
		if ( $ARGS['f_approve'] == 'yes' ) {
			$conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}comments WCAPP WHERE WCAPP.user_id = WU.ID AND WCAPP.comment_approved = 1)";
		} else {
			$conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}comments WCAPP WHERE WCAPP.user_id = WU.ID AND WCAPP.comment_approved = 1)";
		}
	}
	if ( !empty( $ARGS['has_spam'] ) ) {
		if ( $ARGS['has_spam'] === 'yes' ) {
			$conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}comments WCSPM WHERE WCSPM.user_id = WU.ID AND WCSPM.comment_approved = 'spam' )";
		} else {
			$conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}comments WCSPM WHERE WCSPM.user_id = WU.ID AND WCSPM.comment_approved = 'spam' )";
		}
	}
	if ( !empty( $ARGS['f_userdisabled'] ) ) {
		if ( $ARGS['f_userdisabled'] === 'yes' ) {
			$conditions[] = "WUMDIS.meta_value > 0";
		} else {
			$conditions[] = "(WUMDIS.meta_value is NULL OR WUMDIS.meta_value = 0)";
		}
	}
	if ( !empty( $ARGS['f_lastlogin'] ) ) {
		$days = absint( $ARGS['f_lastlogin'] );
		$time = time() - $days * 86400;
		$timeStr = gmdate( 'Y-m-d H:i:s', $time );
		$conditions[] = $wpdb->prepare( "(WUM2.meta_value < %d OR WUM21.meta_value < %s )", $time, $timeStr );
	}
	if ( !empty( $ARGS['has_recs'] ) ) {
		if ( $ARGS['has_recs'] === 'yes' ) {
			$conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID AND NOT WP.post_type in ( 'attachment', 'revision' ) AND WP.post_status = 'publish' )";
		} else {
			$conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID AND NOT WP.post_type in ( 'attachment', 'revision' ) AND WP.post_status = 'publish' )";
		}
	}
	if ( !empty( $ARGS['dam_spam_username'] ) ) {
		$like = '%' . $wpdb->esc_like( sanitize_text_field( wp_unslash( $ARGS['dam_spam_username'] ) ) ) . '%';
		$conditions_sec2[] = $wpdb->prepare( "WU.user_login like %s", $like );
	}
	if ( !empty( $ARGS['dam_spam_domain'] ) ) {
		if ( $ARGS['dam_spam_domain'] === 'yes' && !empty( $ARGS['dam_spam_domain_text'] ) ) {
			$domain_text = sanitize_textarea_field( wp_unslash( $ARGS['dam_spam_domain_text'] ) );
			$domains = array_map( 'trim', explode( ',', $domain_text ) );
			$domain_conditions = array();
			foreach ( $domains as $domain ) {
				if ( !empty( $domain ) ) {
					$like = '%' . $wpdb->esc_like( $domain );
					$domain_conditions[] = $wpdb->prepare( "WU.user_email like %s", $like );
				}
			}
			if ( !empty( $domain_conditions ) ) {
				$conditions_sec2[] = '(' . implode( ' OR ', $domain_conditions ) . ')';
			}
		}
	}
	$days = empty( $ARGS['daysleft'] ) ? 0 : absint( $ARGS['daysleft'] );
	if ( $days >= 0 ) {
		$tmStr = gmdate( 'Y-m-d H:i:s', time() - $days * 86400 );
		if ( empty( $ARGS['f_daysleft'] ) ) {
			$conditions_sec2[] = $wpdb->prepare( "WU.user_registered >= %s", $tmStr );
		} else {
			$conditions_sec2[] = $wpdb->prepare( "WU.user_registered < %s", $tmStr );
		}
	}
	if ( !empty( $ARGS['user_role'] ) ) {
		$user_role = sanitize_text_field( wp_unslash( $ARGS['user_role'] ) );
		$conditions[] = $wpdb->prepare( 'LOCATE(%s, WUCAP.meta_value) > 0', $user_role );
	}
	if ( is_plugin_active( 'user-login-history/user-login-history.php' ) && false ) {
		$PLUGIN_LAST_LOGIN_FIELD = 'MAX(UNIX_TIMESTAMP(WUM2.time_login))';
		$joins[] = "LEFT JOIN {$wpdb->prefix}fa_user_logins WUM2 ON WUM2.user_id = WU.ID";
	} elseif ( is_plugin_active( 'when-last-login/when-last-login.php' ) ) {
		$PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
		$groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
		$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'when_last_login'";
	} elseif ( is_plugin_active( 'wp-last-login/wp-last-login.php' ) ) {
		$PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
		$groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
		$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'wp-last-login'";
	} else {
		$PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
		$groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
		$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'last_login_gtm'";
	}
	if ( !empty( $ARGS['f_usereverlogin'] ) ) {
		if ( $ARGS['f_usereverlogin'] === 'yes' ) {
			$havings[] = "(last_login > 0 OR WUM21.meta_value > '1970-01-02 00:00:01' )";
		} else {
			$havings[] = "((last_login = 0 OR last_login IS NULL) AND (WUM21.meta_value is NULL OR WUM21.meta_value <= '1970-01-02 00:00:01' ))";
		}
	}
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM21 ON WUM21.user_id = WU.ID AND WUM21.meta_key = 'last_login'";
	if ( !empty( $conditions ) ) {
		$flags_cnd = isset( $ARGS['flagsCND'] ) ? sanitize_text_field( wp_unslash( $ARGS['flagsCND'] ) ) : '';
		$operator = ( $flags_cnd === 'add' ) ? ' OR ' : ' AND ';
		$conditions_sec2[] = implode( $operator, $conditions );
	}
	if ( !empty( $ARGS['dam_spam_check_name'] ) ) {
		if ( $ARGS['dam_spam_check_name'] === 'yes' ) {
			$havings[] = 'first_name = last_name and first_name!=""';
		}
	}
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM211 ON WUM211.user_id = WU.ID AND WUM211.meta_key = 'first_name'";
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM212 ON WUM212.user_id = WU.ID AND WUM212.meta_key = 'last_name'";
	$query = "
		SELECT SQL_CALC_FOUND_ROWS SUM(WC.comment_approved = 1) as approved, SUM(WC.comment_approved = 'spam' ) as spam,
		WU.ID, WU.user_login as login, WU.user_email as mail, WU.user_url as url, WU.user_registered as dt_reg, WU.display_name as name,
		WUMDIS.meta_value as disabled_time, WUM211.meta_value AS first_name,WUM212.meta_value AS last_name,
		WUCAP.meta_value as USL, {$PLUGIN_LAST_LOGIN_FIELD} as last_login, WUM21.meta_value as last_login_classipress, WUMD.meta_value as removetime
		" . implode( " ", $joins ) . "
		WHERE (" . implode( ' ) AND ( ', $conditions_sec2 ) . ")
		GROUP BY " . implode( ', ', $groupBy ) . ( !empty( $havings ) ? ' HAVING ' . implode( ' AND ', $havings ) : '' );
	$sort_order = isset( $ARGS['sort_order'] ) ? sanitize_text_field( wp_unslash( $ARGS['sort_order'] ) ) : '';
	switch ( $sort_order ) {
		case 'logindate':
			$sort_order = 'WUM21.meta_value DESC, WUM2.meta_value DESC';
			break;
		case 'name':
			$sort_order = 'WU.display_name';
			break;
		case 'mail':
			$sort_order = 'WU.user_email';
			break;
		case 'regdate':
			$sort_order = 'WU.user_registered';
			break;
		case 'spam':
			$sort_order = 'SUM(WC.comment_approved = \'spam\' ) DESC, WU.user_login';
			break;
		case 'userlevel':
			$sort_order = 'WUCAP.meta_value DESC, WU.user_login';
			break;
		case 'comments':
			$sort_order = 'SUM(WC.comment_approved = 1) DESC, WU.user_login';
			break;
		case 'disabled':
			$sort_order = 'WUMDIS.meta_value';
			break;
		case 'posts':
		default:
			$sort_order = 'WU.user_login';
	}
	$max_size = isset( $ARGS['max_size_output'] ) ? sanitize_text_field( wp_unslash( $ARGS['max_size_output'] ) ) : '150';
	$query .= $max_size == 'all' ? ' ' : ' LIMIT ' . absint( $max_size );
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Complex dynamic query with sanitized inputs
	$rows = $wpdb->get_results( $query, ARRAY_A );
	$total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
	$user_list = array();
	if ( !empty( $rows ) ) {
		foreach ( $rows as $k => $UR ) {
			$UR['recs'] = 0;
			$user_list[$UR['ID']] = $UR;
		}
	}
	$tmStr_prepared = isset( $tmStr ) ? $tmStr : gmdate( 'Y-m-d H:i:s' );
	if ( empty( $ARGS['f_daysleft'] ) ) {
		$query = "
			SELECT COUNT(WP.ID) as recs, WU.ID
			FROM {$wpdb->posts} WP
			LEFT JOIN {$wpdb->users} WU ON WP.post_author = WU.ID
			WHERE 1 
			AND NOT WP.post_type in ( 'attachment', 'revision' ) AND post_status = 'publish'
			GROUP BY WU.ID
			HAVING COUNT(WP.ID) > 0";
	} else {
		$query = $wpdb->prepare( "
			SELECT COUNT(WP.ID) as recs, WU.ID
			FROM {$wpdb->posts} WP
			LEFT JOIN {$wpdb->users} WU ON WP.post_author = WU.ID
			WHERE 1 AND WU.user_registered < %s 
			AND NOT WP.post_type in ( 'attachment', 'revision' ) AND post_status = 'publish'
			GROUP BY WU.ID
			HAVING COUNT(WP.ID) > 0",
			$tmStr_prepared
		);
	}
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query is prepared above
	$rows = $wpdb->get_results( $query, ARRAY_A );
	if ( !empty( $rows ) ) {
		foreach ( $rows as $k => $UR ) {
			$id = $UR['ID'];
			if ( isset( $user_list[$id] ) ) {
				$user_list[$id]['recs'] = $UR['recs'];
			}
		}
	}
	$result = new \stdClass();
	$result->rows = $user_list;
	$result->total = $total;
	return $result;
}

if ( isset( $_POST['op'] ) && isset( $_POST['dam_spam_user_filter_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dam_spam_user_filter_nonce'] ) ), 'dam_spam_user_filter_nonce' ) ) {
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Access Blocked', 'dam-spam' ) );
	}
	$op = sanitize_text_field( wp_unslash( $_POST['op'] ) );
	if ( isset( $_POST['f_users'] ) && is_array( $_POST['f_users'] ) ) {
		$f_users = array_map( 'absint', wp_unslash( $_POST['f_users'] ) );
		switch ( $op ) {
			case 'disable':
				echo esc_html__( 'Disabling...', 'dam-spam' ) . '<br>';
				$count_disabled = 0;
				foreach ( $f_users as $user_id_to_disable ) {
					$result = dam_spam_isVIPUser( $user_id_to_disable );
					if ( $result === false ) {
						$tm = get_user_meta( $user_id_to_disable, '_IUD_userBlockedTime', true );
						if ( !$tm ) {
							update_user_meta( $user_id_to_disable, '_IUD_userBlockedTime', time() );
							$count_disabled++;
						}
					} else {
						echo esc_html( $result ) . '<br>';
					}
				}
				if ( $count_disabled == 1 ) {
					echo esc_html( $count_disabled ) . ' ' . esc_html__( 'user was disabled.', 'dam-spam' );
				} else {
					echo esc_html( $count_disabled ) . ' ' . esc_html__( 'users were disabled.', 'dam-spam' );
				}
				break;
			case 'activate':
				echo esc_html__( 'Enabling accounts...', 'dam-spam' ) . '<br>';
				$count_enabled = 0;
				foreach ( $f_users as $user_id_to_enable ) {
					$tm = get_user_meta( $user_id_to_enable, '_IUD_userBlockedTime', true );
					if ( $tm ) {
						delete_user_meta( $user_id_to_enable, '_IUD_userBlockedTime' );
						$count_enabled++;
					}
				}
				if ( $count_enabled == 1 ) {
					echo esc_html( $count_enabled ) . ' ' . esc_html__( 'user was enabled.', 'dam-spam' );
				} else {
					echo esc_html( $count_enabled ) . ' ' . esc_html__( 'users were enabled.', 'dam-spam' );
				}
				break;
		}
	}
}

?>