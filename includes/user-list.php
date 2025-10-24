<?php

wp_enqueue_script( 'jquery' );
wp_add_inline_script( 'jquery', "
	jQuery(document).ready(function($) {
		$('.sort').click(function() {
			if ( $(this).hasClass('header') ) {
				$(this).addClass('reverse');
				$(this).removeClass('header');
			} else {
				$(this).addClass('header');
				$(this).removeClass('reverse');
			}
			var table = $(this).parents('table').eq(0);
			var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
			this.asc = !this.asc;
			if ( !this.asc ) {
				rows = rows.reverse();
			}
			for ( var i = 0; i < rows.length; i++ ) {
				table.append(rows[i]);
			}
		});

		function comparer(index) {
			return function(a, b) {
				var valA = getCellValue(a, index), valB = getCellValue(b, index);
				return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
			}
		}

		function getCellValue(row, index) {
			return $(row).children('td').eq(index).text();
		}
	});

	function ds_markALL(f_elm) {
		if ( f_elm.length > 0 ) {
			for ( var i = 0; i < f_elm.length; i++ ) {
				f_elm[i].checked = true;
			}
		} else {
			f_elm.checked = true;
		}
	}

	function ds_unmarkALL(f_elm) {
		if ( f_elm.length > 0 ) {
			for ( var i = 0; i < f_elm.length; i++ ) {
				f_elm[i].checked = false;
			}
		} else {
			f_elm.checked = false;
		}
	}
" );

wp_add_inline_style( 'wp-admin', "
	.clickable {
		cursor: pointer;
	}
	.header:after {
		content: ' \\25bc';
		font-size: 0.8em;
	}
	.reverse:after {
		content: ' \\25b2'!important;
		font-size: 0.8em;
	}
	.odd {
		background-color: #FFFFEE;
	}
	.even {
		background-color: #EEFFFF;
	}
	.button-secondary-red {
		background: #ba0000;
		border-color: #690000 #690000 #690000;
		-webkit-box-shadow: 0 1px 0 #690000;
		box-shadow: 0 1px 0 #690000;
		color: #fff;
		text-decoration: none;
		text-shadow: 0 -1px 1px #690000, 1px 0 1px #690000, 0 1px 1px #690000, -1px 0 1px #690000;
		display: inline-block;
		font-size: 13px;
		line-height: 26px;
		height: 28px;
		margin: 0;
		padding: 0 10px 1px;
		cursor: pointer;
		border-width: 1px;
		border-style: solid;
		-webkit-appearance: none;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		white-space: nowrap;
	}
	.button-secondary-red:hover {
		background: #ca0000;
		color: #fff;
	}
" );
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

<input type="button" value="<?php esc_attr_e( 'Select All', 'dam-spam' ); ?>" onclick="ds_markALL(this.form['f_users[]']);">
<input type="button" value="<?php esc_attr_e( 'Deselect All', 'dam-spam' ); ?>" onclick="ds_unmarkALL(this.form['f_users[]']);">
<input type="button" class="button-secondary-red" value="<?php esc_attr_e( 'Disable Users', 'dam-spam' ); ?>" onclick="if(confirm('<?php echo esc_js( __( 'Yes, disable all marked users.', 'dam-spam' ) ); ?>')){this.form.op.value='disable';this.form.submit();}">
<input type="button" class="button-primary" value="<?php esc_attr_e( 'Enable Users', 'dam-spam' ); ?>" onclick="if(confirm('<?php echo esc_js( __( 'Yes, activate all marked users.', 'dam-spam' ) ); ?>')){this.form.op.value='activate';this.form.submit();}">

<table cellpadding="3"><tr>
	<th>No.</th>
	<th>Check</th>
	<th class="clickable header sort" width="150" align="left" onclick="jQuery('#sort_order').val('login'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Username', 'dam-spam' ); ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('mail'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Email', 'dam-spam' ); ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Status', 'dam-spam' ); ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'First Name', 'dam-spam' ); ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Last Name', 'dam-spam' ); ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('userlevel'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Role', 'dam-spam' ); ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('regdate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Reg Date', 'dam-spam' ); ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('logindate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Last Login', 'dam-spam' ); ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('logindate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Published Posts', 'dam-spam' ); ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('spam'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Spam Comments', 'dam-spam' ); ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('comments'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Approved Comments', 'dam-spam' ); ?></th></tr>
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
			echo '<input type="checkbox" name="f_users[]" value="' . esc_attr( $UR['ID'] ) . '" ' . $checked . '>';
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