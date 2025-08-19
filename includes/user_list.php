<p><strong>
<?php
echo count( $user_list );
echo esc_html__( ' record(s) are shown.', 'dam-spam' );
if ( $total > count( $user_list ) ) {
	echo ' ' . esc_html( $total ) . ' ' . esc_html__( 'are found total.', 'dam-spam' );
}
?>
</strong></p>

<hr>

<input type="button" value="<?php echo esc_html__( 'Select all', 'dam-spam' ) ?>" onclick="ds_markALL(this.form['f_users[]']);">
<input type="button" value="<?php echo esc_html__( 'Deselect all', 'dam-spam' ) ?>" onclick="ds_unmarkALL(this.form['f_users[]']);">
<input type="button" class="button-secondary-red" value="<?php echo esc_html__( 'Disable users', 'dam-spam' ) ?>" onclick="if(confirm('Yes, disable all marked users.')){this.form.op.value='disable';this.form.submit();}">
<input type="button" class="button-primary" value="<?php echo esc_html__( 'Enable users', 'dam-spam' ) ?>" onclick="if(confirm('Yes, activate all marked users.')){this.form.op.value='activate';this.form.submit();}">

<table cellpadding="3"><tr>
	<th>No.</th>
	<th>Check</th>
	<th class="clickable header sort" width="150" align="left" onclick="jQuery('#sort_order').val('login'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Username', 'dam-spam' ) ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('mail'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Email', 'dam-spam' ) ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Status', 'dam-spam' ) ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'First Name', 'dam-spam' ) ?></th>
	<th class="clickable header sort" align="left" onclick="jQuery('#sort_order').val('disabled'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Last Name', 'dam-spam' ) ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('userlevel'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Role', 'dam-spam' ) ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('regdate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Reg Date', 'dam-spam' ) ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('logindate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Last Login', 'dam-spam' ) ?></th>
	<th class="clickable header sort" width="120" onclick="jQuery('#sort_order').val('logindate'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Published Posts', 'dam-spam' ) ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('spam'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Spam Comments', 'dam-spam' ) ?></th>
	<th class="clickable header sort" onclick="jQuery('#sort_order').val('comments'); jQuery('#inactive-user-deleter-form').submit();"><?php esc_html_e( 'Approved Comments', 'dam-spam' ) ?></th></tr>
	<?php
	$i = 0;
	$stroked = 0;
	foreach( $user_list as $UR ) {
		$i++;
		$class = $i % 2 ? 'odd' : 'even';
		echo '<tr align="center" class="' . esc_attr( $class ) . '"><td>' . esc_html( $i ) . '.</td><td>';
		$login = ( empty( $UR['url'] ) ? $UR['login'] : "<a href=\"$UR[url]\" target=\"_blank\">$UR[login]</a>" );
		if ( !empty( $UR['removetime'] ) ) {
			$login = '<s>' . $login . '</s> *';
			//to remove checkbox
			$UR['ID'] = 1;
			$stroked ++;
		}
		$UR['USL'] = @unserialize( $UR['USL'] );
		$isAdministrator = ( is_array($UR['USL'] ) && !empty( $UR['USL']['administrator'] ) );
		if ( $isAdministrator || $UR['ID'] == 1 ) {
			echo "-";
		} else {
			echo '<input type="checkbox" name="f_users[]" value="' . esc_attr( $UR[ID] ) . '"'
				. ( isset( $_POST['f_users'] ) && in_array( $UR['ID'], $_POST['f_users'] ) ? 'checked' : '' )
				. '>';
			}
			//last_login_classipress date preferable
			$last_login = $UR['last_login_classipress'] ? strtotime( $UR['last_login_classipress'] ) : $UR['last_login'];
			$status = $UR['disabled_time'] ? esc_html__( 'blocked', 'dam-spam' ) . date( ' [d M Y]', $UR['disabled_time'] ) : esc_html__( 'active', 'dam-spam' );
			echo "</td>\n<td align=\"left\">" . esc_html( $login ) . "</td>"
			. "<td align=\"left\">" . esc_html( $UR[mail] ) . "</td>"
			. "<td align=\"left\">" . esc_html( $status ) . "</td>"
			. "<td align=\"left\">" . esc_html( $UR[first_name] ) . "</td>"
			. "<td align=\"left\">" . esc_html( $UR[last_name] ) . "</td>"
			. "</td><td>" . ( is_array( $UR['USL'] ) && !empty( $UR['USL'] ) ? implode( ', ', array_keys( $UR['USL'] ) ) : '-' ) . "</td><td>"
			. esc_html( date( 'd M Y', strtotime( $UR['dt_reg'] ) ) ) . "</td>"
			. '<td>' . ( $last_login ? esc_html( date( 'd M Y', $last_login ) ) : '?' ) . "</td>"
			. '<td>' . ( $UR['recs'] ? esc_html( $UR['recs'] ) : '-' )
			. "</td><td>"
			. ( $UR['spam'] ? esc_html( $UR['spam'] ) : '-' )
			. "</td><td>"
			. ( $UR['approved'] ? esc_html( $UR['approved'] ) : '-' )
			. "</td></tr>\n";
		}
	?>
</table>

<?php
if ( $stroked ) {
	echo '<p>* - striked through logins - user is informed (by email) about deletion and marked to delete soon.<p>';
}
?>

<script>
	function ds_markALL(f_elm) {
		if (f_elm.length > 0) {
			for(i=0; i<f_elm.length; i++)
				f_elm[i].checked = true;
		} else f_elm.checked = true;
	}
	function ds_unmarkALL(f_elm) {
		if (f_elm.length > 0) {
			for(i=0; i<f_elm.length; i++)
				f_elm[i].checked = false;
		} else f_elm.checked = false;
	}
</script>

<style>
	.clickable {
		cursor: pointer;
	}
	.header:after {
		content: ' \25bc';
		font-size: 0.8em;
	}
	.reverse:after{
		content: ' \25b2'!important;
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
		text-decoration: none;
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
</style>

<script src="https://unpkg.com/jquery@latest/dist/jquery.min.js"></script>
<script>
	$('.sort').click(function() {
		if ($(this).hasClass('header')) {
			$(this).addClass('reverse');
			$(this).removeClass('header');
		}
		else { 
			$(this).addClass('header');
			$(this).removeClass('reverse'); 
		}
		var table = $(this).parents('table').eq(0)
		var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()))
		this.asc = !this.asc
		if (!this.asc){rows = rows.reverse()}
		for (var i = 0; i < rows.length; i++){table.append(rows[i])}
	})
	function comparer(index) {
		return function(a, b) {
			var valA = getCellValue(a, index), valB = getCellValue(b, index)
			return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB)
		}
	}
	function getCellValue(row, index){ return $(row).children('td').eq(index).text() }
</script>
