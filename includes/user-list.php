<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Template displays data, form processing handled by parent with nonce verification

?>

<p><strong>
<?php
echo esc_html( count( $user_list ) );
esc_html_e( ' record(s) are shown.', 'dam-spam' );
if ( $total > count( $user_list ) ) {
	echo ' ' . esc_html( $total ) . ' ';
	esc_html_e( 'are found total.', 'dam-spam' );
}
?>
</strong></p>

<hr>

<input type="hidden" id="sort_order" name="sort_order" value="">

<input type="button" value="<?php esc_attr_e( 'Select All', 'dam-spam' ); ?>" onclick="damSpamMarkAll(document.querySelectorAll('input[name=\'f_users[]\']'));">
<input type="button" value="<?php esc_attr_e( 'Deselect All', 'dam-spam' ); ?>" onclick="damSpamUnmarkAll(document.querySelectorAll('input[name=\'f_users[]\']'));">
<input type="button" class="button-secondary-red" value="<?php esc_attr_e( 'Disable Users', 'dam-spam' ); ?>" onclick="if(confirm('<?php echo esc_js( __( 'Yes, disable all marked users.', 'dam-spam' ) ); ?>')){document.querySelector('input[name=op]').value='disable';document.querySelector('form[name=DOIT2]').submit();}">
<input type="button" class="button-primary" value="<?php esc_attr_e( 'Enable Users', 'dam-spam' ); ?>" onclick="if(confirm('<?php echo esc_js( __( 'Yes, activate all marked users.', 'dam-spam' ) ); ?>')){document.querySelector('input[name=op]').value='activate';document.querySelector('form[name=DOIT2]').submit();}">

<table cellpadding="3"><tr>
	<th>No.</th>
	<th>Check</th>
	<th class="clickable header" width="150" align="left" onclick="damSpamSortUserList('login')"><?php esc_html_e( 'Username', 'dam-spam' ); ?></th>
	<th class="clickable header" align="left" onclick="damSpamSortUserList('mail')"><?php esc_html_e( 'Email', 'dam-spam' ); ?></th>
	<th class="clickable header" align="left" onclick="damSpamSortUserList('disabled')"><?php esc_html_e( 'Status', 'dam-spam' ); ?></th>
	<th class="clickable header" align="left" onclick="damSpamSortUserList('first_name')"><?php esc_html_e( 'First Name', 'dam-spam' ); ?></th>
	<th class="clickable header" align="left" onclick="damSpamSortUserList('last_name')"><?php esc_html_e( 'Last Name', 'dam-spam' ); ?></th>
	<th class="clickable header" onclick="damSpamSortUserList('userlevel')"><?php esc_html_e( 'Role', 'dam-spam' ); ?></th>
	<th class="clickable header" width="120" onclick="damSpamSortUserList('regdate')"><?php esc_html_e( 'Reg Date', 'dam-spam' ); ?></th>
	<th class="clickable header" width="120" onclick="damSpamSortUserList('logindate')"><?php esc_html_e( 'Last Login', 'dam-spam' ); ?></th>
	<th class="clickable header" width="120" onclick="damSpamSortUserList('posts')"><?php esc_html_e( 'Published Posts', 'dam-spam' ); ?></th>
	<th class="clickable header" onclick="damSpamSortUserList('spam')"><?php esc_html_e( 'Spam Comments', 'dam-spam' ); ?></th>
	<th class="clickable header" onclick="damSpamSortUserList('comments')"><?php esc_html_e( 'Approved Comments', 'dam-spam' ); ?></th></tr>
	<?php
	$i = 0;
	$stroked = 0;
	foreach ( $user_list as $UR ) {
		$i++;
		$class = $i % 2 ? 'odd' : 'even';
		echo '<tr align="center" class="' . esc_attr( $class ) . '"><td>' . esc_html( $i ) . '.</td><td>';
		$login = ( empty( $UR['url'] ) ? esc_html( $UR['login'] ) : '<a href="' . esc_url( $UR['url'] ) . '" target="_blank">' . esc_html( $UR['login'] ) . '</a>' );
		if ( !empty( $UR['removetime'] ) ) {
			$login = '<s>' . $login . '</s> *';
			$UR['ID'] = 1;
			$stroked++;
		}
		$UR['USL'] = maybe_unserialize( $UR['USL'] );
		$isAdministrator = ( is_array( $UR['USL'] ) && !empty( $UR['USL']['administrator'] ) );
		if ( $isAdministrator || absint( $UR['ID'] ) === 1 ) {
			echo '-';
		} else {
			$checked = ( isset( $_POST['f_users'] ) && is_array( $_POST['f_users'] ) && in_array( absint( $UR['ID'] ), array_map( 'absint', $_POST['f_users'] ) ) ) ? ' checked' : '';
			echo '<input type="checkbox" name="f_users[]" value="' . esc_attr( $UR['ID'] ) . '" ' . esc_attr( $checked ) . '>';
		}
		$last_login = $UR['last_login_classipress'] ? strtotime( $UR['last_login_classipress'] ) : absint( $UR['last_login'] );
		$status = $UR['disabled_time'] ? esc_html__( 'blocked', 'dam-spam' ) . ' ' . esc_html( gmdate( '[d M Y]', absint( $UR['disabled_time'] ) ) ) : esc_html__( 'active', 'dam-spam' );
		echo '</td>' . "\n" . '<td align="left">' . wp_kses_post( $login ) . '</td>'
		. '<td align="left">' . esc_html( $UR['mail'] ) . '</td>'
		. '<td align="left">' . esc_html( $status ) . '</td>'
		. '<td align="left">' . esc_html( $UR['first_name'] ) . '</td>'
		. '<td align="left">' . esc_html( $UR['last_name'] ) . '</td>'
		. '<td>' . ( is_array( $UR['USL'] ) && !empty( $UR['USL'] ) ? esc_html( implode( ', ', array_keys( $UR['USL'] ) ) ) : '-' ) . '</td><td>'
		. esc_html( gmdate( 'd M Y', strtotime( $UR['dt_reg'] ) ) ) . '</td>'
		. '<td>' . ( $last_login ? esc_html( gmdate( 'd M Y', $last_login ) ) : '?' ) . '</td>'
		. '<td>' . ( $UR['recs'] ? esc_html( $UR['recs'] ) : '-' ) . '</td><td>'
		. ( $UR['spam'] ? esc_html( $UR['spam'] ) : '-' ) . '</td><td>'
		. ( $UR['approved'] ? esc_html( $UR['approved'] ) : '-' ) . '</td></tr>' . "\n";
	}
	?>
</table>

<?php
if ( $stroked ) {
	echo '<p>* ' . esc_html__( 'Striked through logins - user is informed (by email) about deletion and marked to delete soon.', 'dam-spam' ) . '</p>';
}
?>