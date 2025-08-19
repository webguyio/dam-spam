<input type="hidden" name="op" value="search_users">

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
				<option value="intersept" <?php echo !empty( $_POST[ 'flagsCND'] ) &&$_POST[ 'flagsCND'] == 'intersept' ? 'selected' : '' ?>>
					<?php esc_html_e( 'ALL are true', 'dam-spam' ) ?>
				</option>
				<option value="add" <?php echo !empty( $_POST[ 'flagsCND'] ) && $_POST['flagsCND'] == 'add' ? 'selected' : '' ?>>
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
			<label for="flag_approve_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="flag_approve_no" type="radio" name="f_approve" value="no" <?php if ( isset( $_POST[ 'f_approve'] ) and $_POST[ 'f_approve'] === 'no' ) { echo 'checked'; } ?>>
			</label>
			<label for="flag_approve_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="flag_approve_yes" type="radio" name="f_approve" value="yes" <?php if ( isset( $_POST[ 'f_approve'] ) and $_POST[ 'f_approve'] === 'yes' ) { echo 'checked'; } ?>>
			</label>
			<label for="flag_approve_nomatter">
				<?php esc_html_e( 'Ignore', 'dam-spam' ) ?>
				<input id="flag_approve_nomatter" type="radio" name="f_approve" value="0" <?php echo empty( $_POST[ 'f_approve'] ) ? 'checked' : '' ?>>
			</label>
		</td>
	</tr>
	<tr>
	<?php // if ( !isset( $_POST[ 'has_spam'] ) ) $_POST[ 'has_spam'] = 'yes'; ?>
		<td>
			<?php esc_html_e( 'Spam Comments', 'dam-spam' ) ?>
		</td>
		<td align="left">
			<label for="flag_has_spam_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="flag_has_spam_no" type="radio" name="has_spam" value="no" <?php if ( isset( $_POST[ 'has_spam'] ) and $_POST[ 'has_spam']==='no' ) { echo 'checked'; } ?>>
			</label>
			<label for="flag_has_spam_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="flag_has_spam_yes" type="radio" name="has_spam" value="yes" <?php if ( isset( $_POST[ 'has_spam'] ) and $_POST[ 'has_spam'] === 'yes' ) { echo 'checked'; } ?>>
			</label>
			<label for="flag_has_spam_nomatter">
				<?php esc_html_e( 'Ignore', 'dam-spam' ) ?>
				<input id="flag_has_spam_nomatter" type="radio" name="has_spam" value="0" <?php echo empty( $_POST[ 'has_spam'] ) ? 'checked' : '' ?>>
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Same First/Last Name', 'dam-spam' ) ?>
		</td>
		<td align="left" width="250">
			<label for="ds_check_name_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="ds_domain_no" type="radio" name="ds_check_name" value="no" <?php echo empty( $_POST[ 'ds_check_name'] ) ? 'checked' : '' ?> <?php if ( isset( $_POST[ 'ds_check_name'] ) and $_POST[ 'ds_domain'] === 'no' ) { echo 'checked';} ?>>
			</label>
			<label for="ds_check_name_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="ds_check_name_yes" type="radio" name="ds_check_name" value="yes" <?php if ( isset( $_POST[ 'ds_check_name'] ) and $_POST[ 'ds_check_name'] === 'yes' ) { echo 'checked';} ?>>
			</label>
		</td>
	</tr>
	<tr>
		<td>
			<?php esc_html_e( 'Specific TLD (e.g. .xxx, .blog)', 'dam-spam' ) ?>
		</td>
		<td align="left" width="250">
			<label for="ds_domain_no">
				<?php esc_html_e( 'No', 'dam-spam' ) ?>
				<input id="ds_domain_no" type="radio" name="ds_domain" value="no" <?php echo empty( $_POST[ 'ds_domain'] ) ? 'checked' : '' ?> <?php if ( isset( $_POST[ 'ds_domain'] ) and $_POST[ 'ds_domain'] === 'no' ) { echo 'checked'; } ?>>
			</label>
			<label for="ds_domain_yes">
				<?php esc_html_e( 'Yes', 'dam-spam' ) ?>
				<input id="ds_domain_yes" type="radio" name="ds_domain" value="yes" <?php if ( isset( $_POST[ 'ds_domain'] ) and $_POST[ 'ds_domain'] === 'yes' ) { echo 'checked'; } ?>>
			</label>
			<textarea cols="100" rows="2" name="ds_domain_text">
				<?php echo isset( $_POST[ 'ds_domain_text'] ) ? esc_html( htmlspecialchars( $_POST['ds_domain_text'] ) ) : '' ?>
			</textarea>
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
			<input type="text" size="15" name="ds_username" value="<?php echo isset( $_POST['ds_username'] ) ? esc_html( htmlspecialchars( $_POST['ds_username'] ) ) : '' ?>" id="usernameFilter">
			<br>
			<small>
				<?php esc_html_e( 'Refine list by a username (e.g. test, example, etc.).', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr valign="top">
		<td colspan="2">
			<label for="flag_daysleft">
			<?php esc_html_e( 'User was created', 'dam-spam' ) ?>
				<select name="f_daysleft">
				<?php if ( !isset( $_POST[ 'f_daysleft'] ) ) $_POST[ 'f_daysleft'] = 1; ?>
					<option value="1" <?php !empty( $_POST[ 'f_daysleft'] ) ? 'selected' : '' ?>>
						<?php esc_html_e( 'more', 'dam-spam' ) ?>
					</option>
					<option value="0" <?php empty( $_POST[ 'f_daysleft'] ) ? 'selected' : '' ?>>
						<?php esc_html_e( 'less', 'dam-spam' ) ?>
					</option>
				</select>
				<?php esc_html_e( 'than', 'dam-spam' ) ?>
				<input type="text" size="4" name="daysleft" value="<?php echo isset( $_POST['daysleft'] ) ? intval( $_POST['daysleft'] ) : 7 ?>">
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
					<?php $columns = array( 15, 30, 60, 90, 180, 360, 720 ); foreach ( $columns as $v ) { print '<option value="' . esc_attr( $v ) . '" ' . ( $_POST[ 'f_lastlogin'] == $v ? 'selected' : '' ) . '>' . esc_html( $v ) . '</option>'; } ?>
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
				<?php global $wp_roles; $roles = array( '' => 'Any Role' ) + $wp_roles->get_names(); foreach ( $roles as $roleId => $roleName ) { print '<option value="' . esc_attr( $roleId ) . '" ' . ($_POST['user_role'] == $roleId ? 'selected ' : ' ' ) . '>' . esc_html( $roleName ) . '</option>'; } ?>
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
			<?php $columns = array( '150', '300', '500', '1000', '3000', 'All' ); foreach ( $columns as $v ) {
				print '<option value="' . esc_attr( $v ) . '" ' . ( $_POST['max_size_output'] == $v ? 'selected' : '' ) . '>' . esc_html( $v ) . '</option>';
			} ?>
			</select>
			<?php esc_html_e( 'records', 'dam-spam' ) ?>
			<br>
			<small>
				<?php echo esc_html__( 'Max sent allowed is', 'dam-spam' ) . ' ' . ini_get( 'max_input_vars' ) . ' ' . esc_html__( 'input vars.', 'dam-spam' ) ?>
			</small>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input class="button-primary" type="submit" value="<?php esc_html_e( 'Search', 'dam-spam' ) ?>" name="ds_search">
			<button class="button-primary" onclick="window.open('<?php echo esc_url( admin_url( "admin-ajax.php" ) ) ?>' + '?action=iud_getCsvUserList&' + jQuery('#inactive-user-deleter-form').serialize()); return false;">
				<?php esc_html_e( 'Export to CSV', 'dam-spam' ) ?>
			</button>
		</td>
	</tr>
</table>

<a name="outputs"></a>

<?php

$name = array();
if ( isset( $_POST['ds_search'] ) ) {
	if ( isset( $_POST['ds_username'] ) ) {
		$userListObject = ds_getUsersList( $_POST, '' );
		$user_list = $userListObject->rows;
		$total = $userListObject->total;
		if ( empty( $userListObject->rows ) ) {
			echo '<p><strong>' . esc_html__( 'No users are found.', 'dam-spam' ) . '</strong></p>';
		} else {
			include_once 'user_list.php';
		}
	}
}

function ds_isVIPUser( $userID ) {
	global $user_ID;
	if ( $userID == $user_ID ) {
		// i never will delete current user
		return esc_html__( 'I can\'t delete your profile!', 'dam-spam' );
	}
	if ( $userID == 1 ) {
		return esc_html__( 'I will never delete the super user!', 'dam-spam' );
	}
	return false;
}

function ds_getUsersList( $environment, $ARGS = array() ) {
	global $wpdb;
	$conditions = array();
	$conditions_sec2 = array( 1 );
	$joins = array( "FROM {$wpdb->prefix}users WU", "LEFT JOIN {$wpdb->prefix}comments WC ON WC.user_id = WU.ID", "LEFT JOIN {$wpdb->prefix}usermeta WUCAP ON WUCAP.user_id = WU.ID AND WUCAP.meta_key = 'wp_capabilities'", "LEFT JOIN {$wpdb->prefix}usermeta WUMD ON WUMD.user_id = WU.ID AND WUMD.meta_key = '_IUD_deltime'", "LEFT JOIN {$wpdb->prefix}usermeta WUMDIS ON WUMDIS.user_id = WU.ID AND WUMDIS.meta_key = '_IUD_userBlockedTime'" );
	$havings = array();
	$groupBy = array( 'WU.ID, WU.user_login, WU.user_email, WU.user_url, WU.user_registered, WU.display_name, WUCAP.meta_value, WUM21.meta_value, WUMD.meta_value, WUMDIS.meta_value' );
	if ( !empty( $ARGS['f_approve'] ) ) {
		//user with approved comments
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
	if ( !empty($ARGS['f_userdisabled'] ) ) {
		if ($ARGS['f_userdisabled'] === 'yes' ) {
			$conditions[] = "WUMDIS.meta_value > 0";
		} else {
			$conditions[] = "(WUMDIS.meta_value is NULL OR WUMDIS.meta_value = 0)";
		}
	}
	if ( !empty( $ARGS['f_lastlogin'] ) ) {
		$days = ( int )$ARGS['f_lastlogin'] + 0;
		$time = time() - $days * 86400;
		$timeStr = date( 'Y-m-d H:i:s', $time );
		$conditions[] = "(WUM2.meta_value < $time OR WUM21.meta_value < '$timeStr' )";
	}
	if ( !empty( $ARGS['has_recs'] ) ) {
		if ( $ARGS['has_recs'] === 'yes' ) {
			$conditions[] = "EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID
			AND NOT WP.post_type in ( 'attachment', 'revision' ) AND WP.post_status = 'publish' )";
		} else {
			// ignore user with posts
			$conditions[] = "NOT EXISTS (SELECT * FROM {$wpdb->prefix}posts WP WHERE WP.post_author = WU.ID
			AND NOT WP.post_type in ( 'attachment', 'revision' ) AND WP.post_status = 'publish' )";
		}
	}
	// section two
	if ( !empty( $ARGS['ds_username'] ) ) {
		$like = '%' . $wpdb->esc_like( $ARGS['ds_username'] ) . '%';
		$conditions_sec2[] = $wpdb->prepare( "WU.user_login like %s", $like );
	}
	if ( !empty( $ARGS['ds_domain'] ) ) {
		if ( $ARGS['ds_domain'] === 'yes' ) {
			$domains = explode( ',', $ARGS['ds_domain_text'] );
			$query1 = '';
			for ( $i = 0;$i < count($domains);$i++ ) {
				$like = '%' . $domains[$i];
				$query1.= "WU.user_email like '$like' or ";
			}
			$query1 = trim( $query1, 'or ' );
			$conditions_sec2[] = $query1;
		}
	}
	$days = empty( $ARGS['daysleft'] ) ? 0 : $ARGS['daysleft'] + 0;
	if ( $days >= 0 ) {
		$tmStr = date( 'Y-m-d H:i:s', time() - $days * 86400 );
		if ( empty( $ARGS['f_daysleft'] ) ) {
			$conditions_sec2[] = "WU.user_registered >= '$tmStr'";
		} else {
			$conditions_sec2[] = "WU.user_registered < '$tmStr'";
		}
	}
	if ( !empty( $ARGS['user_role'] ) ) {
		$conditions[] = 'LOCATE(\'' . esc_sql( $ARGS['user_role'] ) . '\', WUCAP.meta_value) > 0';
	}
	if ( is_plugin_active( 'user-login-history/user-login-history.php' ) && false ) {
		// user-login-history plugin case
		$PLUGIN_LAST_LOGIN_FIELD = 'MAX(UNIX_TIMESTAMP(WUM2.time_login))';
		$joins[] = "LEFT JOIN {$wpdb->prefix}fa_user_logins WUM2 ON WUM2.user_id = WU.ID";
	} else if ( is_plugin_active( 'when-last-login/when-last-login.php' ) ) {
		// when-last-login plugin case
		$PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
		$groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
		$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'when_last_login'";
	} else if ( is_plugin_active( 'wp-last-login/wp-last-login.php' ) ) {
		// wp-last-login plugin case
		$PLUGIN_LAST_LOGIN_FIELD = 'WUM2.meta_value';
		$groupBy[] = $PLUGIN_LAST_LOGIN_FIELD;
		$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM2 ON WUM2.user_id = WU.ID AND WUM2.meta_key = 'wp-last-login'";
	} else {
		//use own data
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
	// Classipress case last-login
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM21 ON WUM21.user_id = WU.ID AND WUM21.meta_key = 'last_login'";
	if ( !empty( $conditions ) ) {
		$conditions_sec2[] = implode( $ARGS['flagsCND'] == 'add' ? 'OR ' : 'AND ', $conditions );
	}
	if ( !empty( $ARGS['ds_check_name'] ) ) {
		if ( $ARGS['ds_check_name'] === 'yes' ) {
			$havings[] = 'first_name = last_name and first_name!=""';
		}
	}
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM211 ON WUM211.user_id = WU.ID AND WUM211.meta_key = 'first_name'";
	$joins[] = "LEFT JOIN {$wpdb->prefix}usermeta WUM212 ON WUM212.user_id = WU.ID AND WUM212.meta_key = 'last_name'";
	// first action - comments published
	$query = "
		SELECT SQL_CALC_FOUND_ROWS SUM(WC.comment_approved = 1) as approved, SUM(WC.comment_approved = 'spam' ) as spam,
		WU.ID, WU.user_login as login, WU.user_email as mail, WU.user_url as url, WU.user_registered as dt_reg, WU.display_name as name,
		WUMDIS.meta_value as disabled_time, WUM211.meta_value AS first_name,WUM212.meta_value AS last_name,
		WUCAP.meta_value as USL, {$PLUGIN_LAST_LOGIN_FIELD} as last_login, WUM21.meta_value as last_login_classipress, WUMD.meta_value as removetime
		" . implode(" ", $joins) . "
		WHERE (" . implode( ' ) AND ( ', $conditions_sec2 ) . ")
		GROUP BY " . implode( ', ', $groupBy ) . ( !empty( $havings ) ? ' HAVING ' . implode( ' AND ', $havings ) : '' );
	switch ( $ARGS['sort_order'] ) {
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
	$query.= $ARGS['max_size_output'] == 'all' ? ' ' : ' LIMIT ' . ( $ARGS['max_size_output'] + 0 );
	$rows = $wpdb->get_results( $query, ARRAY_A );
	$total = $wpdb->get_var( "SELECT FOUND_ROWS();" );
	$user_list = array();
	if ( !empty( $rows ) ) {
		foreach ( $rows as $k => $UR ) {
			$UR['recs'] = 0;
			$user_list[$UR['ID']] = $UR;
		}
	}
	// clean up with registration lifetime ctiteria + check user norecs criteria + count publish posts
	$query = "
		SELECT COUNT(WP.ID) as recs, WU.ID
		FROM $wpdb->posts WP
		LEFT JOIN $wpdb->users WU ON WP.post_author = WU.ID
		WHERE 1 " . (empty($ARGS['f_daysleft']) ? '' : "AND WU.user_registered < '$tmStr' ") . "
		AND NOT WP.post_type in ( 'attachment', 'revision' ) AND post_status = 'publish'
		GROUP BY WU.ID
		HAVING COUNT(WP.ID) > 0";
	$rows = $wpdb->get_results( $query, ARRAY_A );
	if ( !empty( $rows ) ) {
		foreach ( $rows as $k => $UR ) {
			$id = $UR['ID'];
			if ( isset( $user_list[$id] ) ) $user_list[$id]['recs'] = $UR['recs'];
		}
	}
	$result = new \stdClass();
	$result->rows = $user_list;
	$result->total = $total;
	return $result;
}

if ( isset( $_POST['op'] ) ) {
	switch ( $_POST['op'] ) {
		case 'disable':
		// disable accounts
		echo esc_html__( 'Disabling...', 'dam-spam' ) . '<br>';
		$cnt_disabled = 0;
		foreach ( $_POST['f_users'] as $user_id_to_disable ) {
			$result = ds_isVIPUser( $user_id_to_disable );
			if ( $result === false ) {
				$tm = get_user_meta( $user_id_to_disable, '_IUD_userBlockedTime', true );
				if ( !$tm ) {
					update_user_meta( $user_id_to_disable, '_IUD_userBlockedTime', time() );
					$cnt_disabled++;
				}
			} else {
				echo esc_html( $result ) . '<br>';
			}
		}
		// output actions status
		if ( $cnt_disabled == 1 ) {
			echo esc_html( $cnt_disabled ) . ' ' . esc_html__( 'user was disabled.', 'dam-spam' );
		} else {
			echo esc_html( $cnt_disabled ) . ' ' . esc_html__( 'users were disabled.', 'dam-spam' );
		}
		break;
		case 'activate':
		// enable accounts
		echo esc_html__( 'Enabling accounts...', 'dam-spam' ) . '<br>';
		$cnt_enabled = 0;
		foreach ( $_POST['f_users'] as $user_id_to_enable ) {
			$tm = get_user_meta( $user_id_to_enable, '_IUD_userBlockedTime', true );
			if ( $tm ) {
				delete_user_meta( $user_id_to_enable, '_IUD_userBlockedTime' );
				$cnt_enabled++;
			}
		}
		// output actions status
		if ( $cnt_enabled == 1 ) {
			echo esc_html( $cnt_enabled ) . ' ' . esc_html__( 'user was enabled.', 'dam-spam' );
		} else {
			echo esc_html( $cnt_enabled ) . ' ' . esc_html__( 'users were enabled.', 'dam-spam' );
		}
		break;
	}
}

?>