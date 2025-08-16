<?php

if ( !defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	die();
}

$stats   = ds_get_stats();
$options = ds_get_options();

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$now = date( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

// for session speed checks
// if ( !isset( $_POST ) || empty( $_POST ) ) { // no post defined
// $_SESSION['ds_time'] = time();
// if ( !isset( $_COOKIE['ds_time'] ) ) { // if previous set do not reset
// setcookie( 'ds_time', strtotime( "now" ), strtotime( '+1 min' ) );
// }
// }
$ip  = ds_get_ip();
$hip = "unknown";

if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
	$hip = $_SERVER["SERVER_ADDR"];
}

$email   = '';
$author  = '';
$subject = '';
$body	 = '';

if ( array_key_exists( 'ip', $_POST ) ) {
	if ( filter_var( $_POST['ip'], FILTER_VALIDATE_IP ) ) {
		$ip = sanitize_text_field( $_POST['ip'] );
	}
}

if ( array_key_exists( 'email', $_POST ) ) {
	$email = sanitize_email( $_POST['email'] );
}

if ( array_key_exists( 'author', $_POST ) ) {
	$author = sanitize_text_field( $_POST['author'] );
}

if ( array_key_exists( 'subject', $_POST ) ) {
	$subject = sanitize_text_field( $_POST['subject'] );
}

if ( array_key_exists( 'body', $_POST ) ) {
	$body = sanitize_textarea_field( $_POST['body'] );
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Diagnostics — Dam Spam', 'dam-spam' ); ?></h1>
	<form method="post" action="">
		<div class="ds-info-box">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
			<div class="mainsection"><?php esc_html_e( 'Option Testing', 'dam-spam' ); ?></div>
			<p><?php esc_html_e( 'Run the settings against an IP address to see the results. IP Address:', 'dam-spam' ); ?></p>
			<input id="dsinput" name="ip" type="text" value="<?php echo esc_attr( $ip ); ?>">
			<?php esc_html_e( '(Your server address is', 'dam-spam' ); ?> <?php echo $hip; ?>)<br><br>
			<?php esc_html_e( 'Email:', 'dam-spam' ); ?><br>
			<input id="dsinput" name="email" type="text" value="<?php echo esc_attr( $email ); ?>"><br><br>
			<?php esc_html_e( 'Author/User:', 'dam-spam' ); ?><br>
			<input id="dsinput" name="author" type="text" value="<?php echo esc_attr( $author ); ?>"><br><br>
			<?php esc_html_e( 'Subject:', 'dam-spam' ); ?><br>
			<input id="dsinput" name="subject" type="text" value="<?php echo esc_attr( $subject ); ?>"><br><br>
			<?php esc_html_e( 'Comment:', 'dam-spam' ); ?><br>
			<textarea name="body"><?php return $body; ?></textarea><br>
			<div style="width:50%;float:left">
				<p class="submit"><input name="testopt" class="button-primary" value="<?php esc_html_e( 'Test Options', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<div style="width:50%;float:right">
				<p class="submit"><input name="testcountry" class="button-primary" value="<?php esc_html_e( 'Test Countries', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<br style="clear:both">
			<?php

			$nonce = '';

			if ( array_key_exists( 'ds_control', $_POST ) ) {
					$nonce = $_POST['ds_control'];
			}

			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
				$post = get_post_variables();
				if ( array_key_exists( 'testopt', $_POST ) ) {
					// do the test
					$optionlist = array(
						'chkaws',
						'chkcloudflare',
						'chkgcache',
						'chkgenallowlist',
						'chkgoogle',
						'chkmiscallowlist',
						'chkpaypal',
						'chkscripts',
						'chkvalidip',
						'chkwlem',
						'chkwluserid',
						'chkwlist',
						'chkwlistemail',
						'chkform',
						'chkyahoomerchant'
					);
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					printf( __( '<br>Memory Used: %1$s Peak: %2$s<br>', 'dam-spam' ), $m1, $m2 );
					_e( '<ul>Allow Checks<br>', 'dam-spam' );
					foreach ( $optionlist as $chk ) {
						$ansa = be_load( $chk, $ip, $stats, $options, $post );
						if ( empty( $ansa ) ) {
							$ansa = 'OK';
						}
						echo "$chk: $ansa<br>";
					}
					echo "</ul>";
					$optionlist = array(
						'chk404',
						'chkaccept',
						'chkadmin',
						'chkadminlog',
						'chkagent',
						'chkamazon',
						'chkbbcode',
						'chkbcache',
						'chkblem',
						'chkbluserid',
						'chkblip',
						'chkbotscout',
						'chkdisp',
						'chkdnsbl',
						'chkexploits',
						'chkgooglesafe',
						'chkhoney',
						'chkhosting',
						'chkinvalidip',
						'chklong',
						'chkmulti',
						'chkperiods',
						'chkreferer',
						'chksession',
						'chksfs',
						'chkshort',
						'chkspamwords',
						'chktld',
						'chkubiquity',
						'chkurlshort',
						'chkurls'
					);
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					printf( __( '<br>Memory Used: %1$s Peak: %2$s<br>', 'dam-spam' ), $m1, $m2 );
					_e( '<ul>Block Checks<br>', 'dam-spam' );
					foreach ( $optionlist as $chk ) {
						$ansa = be_load( $chk, $ip, $stats, $options, $post );
						if ( empty( $ansa ) ) {
							$ansa = 'OK';
						}
						echo "$chk: $ansa<br>";
					}
					echo "</ul>";
					$optionlist = array();
					$a1		    = apply_filters( 'ds_addons_allow', $optionlist );
					$a3		    = apply_filters( 'ds_addons_block', $optionlist );
					$a5		    = apply_filters( 'ds_addons_get', $optionlist );
					$optionlist = array_merge( $a1, $a3, $a5 );
					if ( !empty( $optionlist ) ) {
						echo "<ul>Add-on Checks<br>";
						foreach ( $optionlist as $chk ) {
							$ansa = be_load( $chk, $ip, $stats, $options, $post );
							if ( empty( $ansa ) ) {
								$ansa = 'OK';
							}
							$nm = $chk[1];
							echo "$nm: $ansa<br>";
						}
						echo "</ul>";
					}
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					printf( __( '<br>Memory Used: %1$s Peak: %2$s<br>', 'dam-spam' ), $m1, $m2 );
				}
				if ( array_key_exists( 'testcountry', $_POST ) ) {
					$optionlist = array(
						'chkAD',
						'chkAE',
						'chkAF',
						'chkAL',
						'chkAM',
						'chkAR',
						'chkAT',
						'chkAU',
						'chkAX',
						'chkAZ',
						'chkBA',
						'chkBB',
						'chkBD',
						'chkBE',
						'chkBG',
						'chkBH',
						'chkBN',
						'chkBO',
						'chkBR',
						'chkBS',
						'chkBY',
						'chkBZ',
						'chkCA',
						'chkCD',
						'chkCH',
						'chkCL',
						'chkCN',
						'chkCO',
						'chkCR',
						'chkCU',
						'chkCW',
						'chkCY',
						'chkCZ',
						'chkDE',
						'chkDK',
						'chkDO',
						'chkDZ',
						'chkEC',
						'chkEE',
						'chkES',
						'chkEU',
						'chkFI',
						'chkFJ',
						'chkFR',
						'chkGB',
						'chkGE',
						'chkGF',
						'chkGI',
						'chkGP',
						'chkGR',
						'chkGT',
						'chkGU',
						'chkGY',
						'chkHK',
						'chkHN',
						'chkHR',
						'chkHT',
						'chkHU',
						'chkID',
						'chkIE',
						'chkIL',
						'chkIN',
						'chkIQ',
						'chkIR',
						'chkIS',
						'chkIT',
						'chkJM',
						'chkJO',
						'chkJP',
						'chkKE',
						'chkKG',
						'chkKH',
						'chkKR',
						'chkKW',
						'chkKY',
						'chkKZ',
						'chkLA',
						'chkLB',
						'chkLK',
						'chkLT',
						'chkLU',
						'chkLV',
						'chkMD',
						'chkME',
						'chkMK',
						'chkMM',
						'chkMN',
						'chkMO',
						'chkMP',
						'chkMQ',
						'chkMT',
						'chkMV',
						'chkMX',
						'chkMY',
						'chkNC',
						'chkNI',
						'chkNL',
						'chkNO',
						'chkNP',
						'chkNZ',
						'chkOM',
						'chkPA',
						'chkPE',
						'chkPG',
						'chkPH',
						'chkPK',
						'chkPL',
						'chkPR',
						'chkPS',
						'chkPT',
						'chkPW',
						'chkPY',
						'chkQA',
						'chkRO',
						'chkRS',
						'chkRU',
						'chkSA',
						'chkSC',
						'chkSE',
						'chkSG',
						'chkSI',
						'chkSK',
						'chkSV',
						'chkSX',
						'chkSY',
						'chkTH',
						'chkTJ',
						'chkTM',
						'chkTR',
						'chkTT',
						'chkTW',
						'chkUA',
						'chkUK',
						'chkUS',
						'chkUY',
						'chkUZ',
						'chkVC',
						'chkVE',
						'chkVN',
						'chkYE'
					);
					// KE - Kenya
					// chkMA missing
					// SC - Seychelles
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					printf( __( '<br>Memory Used: %1$s Peak: %2$s<br>', 'dam-spam' ), $m1, $m2 );
					foreach ( $optionlist as $chk ) {
						$ansa = be_load( $chk, $ip, $stats, $options, $post );
						if ( empty( $ansa ) ) {
							$ansa = 'OK';
						}
						echo "$chk: $ansa<br>";
					}
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					printf( __( '<br>Memory Used: %1$s Peak: %2$s<br>', 'dam-spam' ), $m1, $m2 );
				}
			}
			?>
		</div>
		<div class="ds-info-box">
			<div class="mainsection"><?php esc_html_e( 'Information Display', 'dam-spam' ); ?></div>
			<div style="width:50%;float:left">
				<h2><?php esc_html_e( 'Display All Options', 'dam-spam' ); ?></h2>
				<p><?php esc_html_e( 'You can dump all options here (useful for debugging):', 'dam-spam' ); ?></p>
				<p class="submit"><input name="dumpoptions" class="button-primary" value="<?php esc_html_e( 'Dump Options', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<div style="width:50%;float:right">
				<h2><?php esc_html_e( 'Display All Stats', 'dam-spam' ); ?></h2>
				<p><?php esc_html_e( 'You can dump all stats here: ', 'dam-spam' ); ?></p>
				<p class="submit"><input name="dumpstats" class="button-primary" value="<?php esc_html_e( 'Dump Stats', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<br style="clear:both">
			<?php
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = $_POST['ds_control'];
			}
			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
				if ( array_key_exists( 'dumpoptions', $_POST ) ) { ?>
					<?php
					echo '<pre>';
					echo "\r\n";
					$options = ds_get_options();
					foreach ( $options as $key => $val ) {
						if ( is_array( $val ) ) {
							$val = print_r( $val, true );
						}
						echo "<strong>&bull; $key</strong> = $val\r\n";
					}
					echo "\r\n";
					echo '</pre>';
					?>
				<?php }
			}
			?>
			<?php
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = $_POST['ds_control'];
			}
			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
				if ( array_key_exists( 'dumpstats', $_POST ) ) { ?>
					<?php
					$stats = ds_get_stats();
					echo '<pre>';
					echo "\r\n";
					foreach ( $stats as $key => $val ) {
						if ( is_array( $val ) ) {
							$val = print_r( $val, true );
						}
						echo "<strong>&bull; $key</strong> = $val\r\n";
					}
					echo "\r\n";
					echo '</pre>';
					?>
				<?php }
			}
			?>
			<p>&nbsp;</p>
		</div>
	</form>
	<div class="ds-info-box">
		<div class="mainsection"><?php esc_html_e( 'Debugging', 'dam-spam' ); ?></div>
		<?php
		// if there is a log file we can display it here
		$dfile = DS_PLUGIN_DATA . '.sfs_debug_output.txt';
		if ( file_exists( $dfile ) ) {
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = $_POST['ds_control'];
			}
			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
				if ( array_key_exists( 'killdebug', $_POST ) ) {
					$f = unlink( $dfile );
					esc_html_e( 'File Deleted', 'dam-spam' );
				}
			}
		}
		if ( file_exists( $dfile ) ) {
			// we have a file - we can view it or delete it
			$nonce = '';
			$to	   = get_option( 'admin_email' );
			$f	   = file_get_contents( $dfile );
			$ff	   = wordwrap( $f, 70, "\r\n" );
		}
		if ( array_key_exists( 'ds_control', $_POST ) ) {
			$nonce = $_POST['ds_control'];
		}
		if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
			if ( array_key_exists( 'showdebug', $_POST ) ) {
				_e( '<p><strong>Debug Output:</strong></p><pre>$f</pre><p><strong>end of file (if empty, there are no errors to display)</p></strong>', 'dam-spam' );
			}
		}
		$nonce = wp_create_nonce( 'ds_update' );
		?>
		<div style="width:50%;float:left">
			<form method="post" action="">
				<input type="hidden" name="update_options" value="update">
				<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
				<p class="submit"><input class="button-primary" name="showdebug" value="<?php esc_html_e( 'Show Debug File', 'dam-spam' ); ?>" type="submit"></p>
			</form>
		</div>
		<div style="width:50%;float:right">
			<form method="post" action="">
				<input type="hidden" name="update_options" value="update">
				<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
				<p class="submit"><input class="button-primary" name="killdebug" value="<?php esc_html_e( 'Delete Debug File', 'dam-spam' ); ?>" type="submit"></p>
			</form>
		</div>
	<br style="clear:both">
	</div>
	<?php
	$ini  = '';
	$pinf = true;
	$ini  = @ini_get( 'disable_functions' );
	if ( !empty( $ini ) ) {
		$disabled = explode( ',', $ini );
		if ( is_array( $disabled ) && in_array( 'phpinfo', $disabled ) ) {
			$pinf = false;
		}
	}
	if ( $pinf ) { ?>
		<a href="" onclick="document.getElementById('shpinf').style.display='block';return false;" id="php-info" class="button-primary"><?php esc_html_e( 'Show PHP Info', 'dam-spam' ); ?></a>
		<?php
		ob_start();
		phpinfo();
		preg_match( '%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches );
		# $matches [1]; # Style information
		# $matches [2]; # Body information
		echo "<div class='phpinfodisplay' id=\"shpinf\" style=\"display:none;\"><style type='text/css'>\n",
		join( "\n",
			array_map(
				function($i) {
					return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );
				},
				preg_split( '/\n/', $matches[1] )
			)
		),
		"</style>\n",
		$matches[2],
		"\n</div>\n";
	}
	?>
	<?php
	ds_fix_post_vars();
	global $wpdb;
	global $wp_query;
	$pre	 = $wpdb->prefix;
	$runscan = false;
	$nonce   = '';
	if ( array_key_exists( 'ds_control', $_POST ) ) {
		$nonce = $_POST['ds_control'];
	}
	if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
		if ( array_key_exists( 'update_options', $_POST ) ) {
			$runscan = true;
		}
	}
	$nonce = wp_create_nonce( 'ds_update' );
	?>
	<div class="ds-info-box">
		<div id="scan" class="mainsection"><?php esc_html_e( 'Threat Scan', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'A very simple scan that looks for things out of place in the content directory as well as the database.', 'dam-spam' ); ?></p>
		<form method="post" action="#scan">
			<input type="hidden" name="update_options" value="update">
			<input type="hidden" name="ds_control" value="<?php echo $nonce; ?>">
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Run Scan', 'dam-spam' ); ?>" type="submit"></p>
		</form>
	</div>
	<?php if ( $runscan ) { ?>
		<h2><?php esc_html_e( 'A clean scan does not mean you are safe. Please keep regular backups and ensure your installation is up-to-date!', 'dam-spam' ); ?></h2>
		<hr>
		<?php
		$disp = false;
		flush();
		// lets try the posts - looking for script tags in data
		_e( '<br><br>Testing Posts<br>', 'dam-spam' );
		$ptab = $pre . 'posts';
		$sql  = "select ID,post_author,post_title,post_name,guid,post_content,post_mime_type
			from $ptab where 
			INSTR(LCASE(post_author), '<script') +
			INSTR(LCASE(post_title), '<script') +
			INSTR(LCASE(post_name), '<script') +
			INSTR(LCASE(guid), '<script') +
			INSTR(LCASE(post_author), 'eval(') +
			INSTR(LCASE(post_title), 'eval(') +
			INSTR(LCASE(post_name), 'eval(') +
			INSTR(LCASE(guid), 'eval(') +
			INSTR(LCASE(post_content), 'eval(') +
			INSTR(LCASE(post_author), 'eval (') +
			INSTR(LCASE(post_title), 'eval (') +
			INSTR(LCASE(post_name), 'eval (') +
			INSTR(LCASE(guid), 'eval (') +
			INSTR(LCASE(post_content), 'eval (') +
			INSTR(LCASE(post_content), 'document.write(unescape(') +
			INSTR(LCASE(post_content), 'try{window.onload') +
			INSTR(LCASE(post_content), 'setAttribute(\'src\'') +
			INSTR(LCASE(post_mime_type), 'script') > 0
		";
		$sql = str_replace( "\t", '', $sql );
		flush();
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
				$disp   = true;
				$reason = '';
				if ( strpos( strtolower( $myrow->post_author ), '<script' ) !== false ) {
					$reason .= "post_author:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->post_title ), '<script' ) !== false ) {
					$reason .= "post_title:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->post_name ), '<script' ) !== false ) {
					$reason .= "post_name:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->guid ), '<script' ) !== false ) {
					$reason .= "guid:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->post_author ), 'eval(' ) !== false ) {
					$reason .= "post_author:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_title ), 'eval(' ) !== false ) {
					$reason .= "post_title:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_name ), 'eval(' ) !== false ) {
					$reason .= "post_name:eval() ";
				}
				if ( strpos( strtolower( $myrow->guid ), 'eval(' ) !== false ) {
					$reason .= "guid:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_content ), 'eval(' ) !== false ) {
					$reason .= "post_content:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_author ), 'eval (' ) !== false ) {
					$reason .= "post_author:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_title ), 'eval (' ) !== false ) {
					$reason .= "post_title:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_name ), 'eval (' ) !== false ) {
					$reason .= "post_name:eval() ";
				}
				if ( strpos( strtolower( $myrow->guid ), 'eval (' ) !== false ) {
					$reason .= "guid:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_content ), 'eval (' ) !== false ) {
					$reason .= "post_content:eval() ";
				}
				if ( strpos( strtolower( $myrow->post_content ), 'document.write(unescape(' ) !== false ) {
					$reason .= "post_content:document.write(unescape( ";
				}
				if ( strpos( strtolower( $myrow->post_content ), 'try{window.onload' ) !== false ) {
					$reason .= "post_content:try{window.onload ";
				}
				if ( strpos( strtolower( $myrow->post_content ), "setAttribute('src'" ) !== false ) {
					$reason .= "post_content:setAttribute('src' ";
				}
				if ( strpos( strtolower( $myrow->post_mime_type ), 'script' ) !== false ) {
					$reason .= "post_mime_type:script ";
				}
				printf( esc_html__( 'found possible problems in post (%s) ID: ', 'dam-spam' ), $reason ) . $myrow->ID . '<br>';
			}
		} else {
			_e( '<br>Nothing found in posts.<br>', 'dam-spam' );
			$disp = false;
		}
		echo '<hr>';
		// comments: comment_ID: author_url, comment_agent, comment_author, comment_email
		$ptab = $pre . 'comments';
		_e( '<br><br>Testing Comments<br>', 'dam-spam' );
		flush();
		$sql = "select comment_ID,comment_author_url,comment_agent,comment_author,comment_author_email,comment_content
			from $ptab where 
			INSTR(LCASE(comment_author_url), '<script') +
			INSTR(LCASE(comment_agent), '<script') +
			INSTR(LCASE(comment_author), '<script') +
			INSTR(LCASE(comment_author_email), '<script') +
			INSTR(LCASE(comment_author_url), 'eval(') +
			INSTR(LCASE(comment_agent), 'eval(') +
			INSTR(LCASE(comment_author), 'eval(') +
			INSTR(LCASE(comment_author_email), 'eval(') +
			INSTR(LCASE(comment_author_url), 'eval (') +
			INSTR(LCASE(comment_agent), 'eval (') +
			INSTR(LCASE(comment_author), 'eval (') +
			INSTR(LCASE(comment_author_email), 'eval (') +
			INSTR(LCASE(comment_content), '<script') +
			INSTR(LCASE(comment_content), 'eval(') +
			INSTR(LCASE(comment_content), 'eval (') +
			INSTR(LCASE(comment_content), 'document.write(unescape(') +
			INSTR(LCASE(comment_content), 'try{window.onload') +
			INSTR(LCASE(comment_content), 'setAttribute(\'src\'') +
			INSTR(LCASE(comment_author_url), 'javascript:') >0
		";
		$sql = str_replace( "\t", '', $sql );
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
				$disp   = true;
				$reason = '';
				if ( strpos( strtolower( $myrow->comment_author_url ), '<script' ) !== false ) {
					$reason .= "comment_author_url:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->comment_agent ), '<script' ) !== false ) {
					$reason .= "comment_agent:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->comment_author ), '<script' ) !== false ) {
					$reason .= "comment_author:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->comment_author_email ), '<script' ) !== false ) {
					$reason .= "comment_author_email:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), '<script' ) !== false ) {
					$reason .= "comment_content:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->comment_author_url ), 'eval(' ) !== false ) {
					$reason .= "comment_author_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_agent ), 'eval(' ) !== false ) {
					$reason .= "comment_agent:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_author ), 'eval(' ) !== false ) {
					$reason .= "comment_author:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_author_email ), 'eval(' ) !== false ) {
					$reason .= "comment_author_email:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), 'eval(' ) !== false ) {
					$reason .= "comment_content:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_author_url ), 'eval (' ) !== false ) {
					$reason .= "comment_author_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_agent ), 'eval (' ) !== false ) {
					$reason .= "comment_agent:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_author ), 'eval (' ) !== false ) {
					$reason .= "comment_author:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_author_email ), 'eval (' ) !== false ) {
					$reason .= "comment_author_email:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), 'eval (' ) !== false ) {
					$reason .= "comment_content:eval() ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), 'document.write(unescape(' ) !== false ) {
					$reason .= "comment_content:document.write(unescape( ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), 'try{window.onload' ) !== false ) {
					$reason .= "comment_content:try{window.onload ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), "setAttribute('src'" ) !== false ) {
					$reason .= "comment_content:setAttribute('src' ";
				}
				if ( strpos( strtolower( $myrow->comment_content ), 'javascript:' ) !== false ) {
					$reason .= "comment_content:javascript: ";
				}
				printf( esc_html__( 'found possible problems in comment (%s) ID: ', 'dam-spam' ), $reason ) . $myrow->comment_ID . '<br>';
			}
		} else {
			_e( '<br>Nothing found in comments.<br>', 'dam-spam' );
		}
		flush();
		echo '<hr>';
		// links: links_id: link_url, link_image, link_description, link_notes, link_rss,link_rss
		$ptab   = $pre . 'links';
		_e( '<br><br>Testing Links<br>', 'dam-spam' );
		flush();
		$sql = "select link_ID,link_url,link_image,link_description,link_notes
			from $ptab where 
			INSTR(LCASE(link_url), '<script') +
			INSTR(LCASE(link_image), '<script') +
			INSTR(LCASE(link_description), '<script') +
			INSTR(LCASE(link_notes), '<script') +
			INSTR(LCASE(link_rss), '<script') +
			INSTR(LCASE(link_url), 'eval(') +
			INSTR(LCASE(link_image), 'eval(') +
			INSTR(LCASE(link_description), 'eval(') +
			INSTR(LCASE(link_notes), 'eval(') +
			INSTR(LCASE(link_rss), 'eval(') +
			INSTR(LCASE(link_url), 'eval (') +
			INSTR(LCASE(link_image), 'eval (') +
			INSTR(LCASE(link_description), 'eval (') +
			INSTR(LCASE(link_notes), 'eval (') +
			INSTR(LCASE(link_rss), 'eval (') +
			INSTR(LCASE(link_url), 'javascript:') >0
		";
		$sql = str_replace( "\t", '', $sql );
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
				$reason = '';
				if ( strpos( strtolower( $myrow->link_url ), '<script' ) !== false ) {
					$reason .= "link_url:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->link_image ), '<script' ) !== false ) {
					$reason .= "link_image:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->link_description ), '<script' ) !== false ) {
					$reason .= "link_description:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->link_notes ), '<script' ) !== false ) {
					$reason .= "link_notes:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->link_rss ), '<script' ) !== false ) {
					$reason .= "link_rss:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->link_url ), 'eval(' ) !== false ) {
					$reason .= "link_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_image ), 'eval(' ) !== false ) {
					$reason .= "link_image:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_description ), 'eval(' ) !== false ) {
					$reason .= "link_description:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_notes ), 'eval(' ) !== false ) {
					$reason .= "link_notes:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_rss ), 'eval(' ) !== false ) {
					$reason .= "link_rss:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_url ), 'eval (' ) !== false ) {
					$reason .= "link_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_image ), 'eval (' ) !== false ) {
					$reason .= "link_image:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_description ), 'eval (' ) !== false ) {
					$reason .= "link_description:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_notes ), 'eval (' ) !== false ) {
					$reason .= "link_notes:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_rss ), 'eval (' ) !== false ) {
					$reason .= "link_rss:eval() ";
				}
				if ( strpos( strtolower( $myrow->link_url ), 'javascript:' ) !== false ) {
					$reason .= "link_url:javascript: ";
				}
				printf( esc_html__( 'found possible problems in links (%s) ID: ', 'dam-spam' ), $reason ) . $myrow->link_ID . '<br>';
			}
		} else {
			_e( '<br>Nothing found in links.<br>', 'dam-spam' );
		}
		echo '<hr>';
		// users: ID: user_login, user_nicename, user_email, user_url, display_name
		$ptab = $pre . 'users';
		_e( '<br><br>Testing Users<br>', 'dam-spam' );
		flush();
		$sql = "select ID,user_login,user_nicename,user_email,user_url,display_name 
			from $ptab where 
			INSTR(LCASE(user_login), '<script') +
			INSTR(LCASE(user_nicename), '<script') +
			INSTR(LCASE(user_email), '<script') +
			INSTR(LCASE(user_url), '<script') +
			INSTR(LCASE(display_name), '<script') +
			INSTR(user_login, 'eval(') +
			INSTR(user_nicename, 'eval(') +
			INSTR(user_email, 'eval(') +
			INSTR(user_url, 'eval(') +
			INSTR(display_name, 'eval(') +
			INSTR(user_login, 'eval (') +
			INSTR(user_nicename, 'eval (') +
			INSTR(user_email, 'eval (') +
			INSTR(user_url, 'eval (') +
			INSTR(display_name, 'eval (') +
			INSTR(LCASE(user_url), 'javascript:') +
			INSTR(LCASE(user_email), 'javascript:')>0
		";
		$sql = str_replace( "\t", '', $sql );
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
				$disp   = true;
				$reason = '';
				if ( strpos( strtolower( $myrow->user_login ), '<script' ) !== false ) {
					$reason .= "user_login:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->user_nicename ), '<script' ) !== false ) {
					$reason .= "user_nicename:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->user_email ), '<script' ) !== false ) {
					$reason .= "user_email:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->user_url ), '<script' ) !== false ) {
					$reason .= "user_url:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->display_name ), '<script' ) !== false ) {
					$reason .= "display_name:&lt;script ";
				}
				if ( strpos( strtolower( $myrow->user_login ), 'eval(' ) !== false ) {
					$reason .= "user_login:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_nicename ), 'eval(' ) !== false ) {
					$reason .= "user_nicename:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_email ), 'eval(' ) !== false ) {
					$reason .= "user_email:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_url ), 'eval(' ) !== false ) {
					$reason .= "user_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->display_name ), 'eval(' ) !== false ) {
					$reason .= "display_name:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_login ), 'eval (' ) !== false ) {
					$reason .= "user_login:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_nicename ), 'eval (' ) !== false ) {
					$reason .= "user_nicename:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_email ), 'eval (' ) !== false ) {
					$reason .= "user_email:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_url ), 'eval (' ) !== false ) {
					$reason .= "user_url:eval() ";
				}
				if ( strpos( strtolower( $myrow->display_name ), 'eval (' ) !== false ) {
					$reason .= "display_name:eval() ";
				}
				if ( strpos( strtolower( $myrow->user_email ), 'javascript:' ) !== false ) {
					$reason .= "user_email:javascript: ";
				}
				if ( strpos( strtolower( $myrow->user_url ), 'javascript:' ) !== false ) {
					$reason .= "user_url:javascript: ";
				}
				printf( esc_html__( 'found possible problems in users (%s) ID: ', 'dam-spam' ), $reason ) . $myrow->ID . '<br>';
			}
		} else {
			esc_html_e( '<br>Nothing found in users.<br>', 'dam-spam' );
		}
		echo '<hr>';
		// options: option_id option_value, option_name
		// I may have to update this as new websites show up
		$ptab = $pre . 'options';
		_e( '<br><br>Testing Options Table for HTML<br>', 'dam-spam' );
		flush();
		$badguys = array(
			'eval('							     => esc_html__( 'eval function found', 'dam-spam' ),
			'eval ('							 => esc_html__( 'eval function found', 'dam-spam' ),
			'networkads'						 => esc_html__( 'unexpected network ads reference', 'dam-spam' ),
			'document.write(unescape('		     => esc_html__( 'javascript document write unescape', 'dam-spam' ),
			'try{window.onload'				     => esc_html__( 'javascript onload event', 'dam-spam' ),
			'escape(document['				     => esc_html__( 'javascript checking document array', 'dam-spam' ),
			'escape(navigator['				     => esc_html__( 'javascript checking navigator', 'dam-spam' ),
			'document.write(string.fromcharcode' => esc_html__( 'obsfucated javascript write', 'dam-spam' ),
			'(base64' . '_decode'				 => esc_html__( 'base64 decode to hide code', 'dam-spam' ),
			'(gz' . 'inflate'					 => esc_html__( 'gzip inflate often used to hide code', 'dam-spam' ),
			'UA-27917097-1'					     => esc_html__( 'Bogus Google Analytics code', 'dam-spam' ),
			'w.wpquery.o'						 => esc_html__( 'Malicious jquery in bootleg plugin or theme', 'dam-spam' ),
			'<scr\\\'+'						     => esc_html__( 'Obfuscated script tag, usually in bootleg plugin or theme', 'dam-spam' )
		);
		$sql = "select option_id,option_value,option_name from $ptab where";
		foreach ( $badguys as $baddie => $reas ) {
			$sql .= "INSTR(LCASE(option_value), '$baddie') +";
		}
		$sql	= trim( $sql, '+' );
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
				// get the option and then red the string
				$id	    = $myrow->option_id;
				$name   = $myrow->option_name;
				$line   = $myrow->option_value;
				$line   = htmlentities( $line );
				$line   = strtolower( $line );
				$reason = '';
				if ( strpos( $name, '_transient_feed_' ) === false ) {
					$disp = true;
					foreach ( $badguys as $baddie => $reas ) {
						if ( !( strpos( $line, $baddie ) === false ) ) {
							// bad boy
							$line   = ds_make_red( $baddie, $line );
							$reason .= $reas . ' ';
						}
					}
				}
				printf( esc_html__( '<strong>Found possible problems in option %1$s (%2$s)</strong> ID: %3$s, Value: %4$s', 'dam-spam' ),
				$name, $reason, $myrow->option_id, $line ) . '<br><br>';
			}
		} else {
			_e( '<br>Nothing found in options.<br>', 'dam-spam' );
		}
		echo '<hr>';
		_e( '<h2>Scanning Themes and Plugins for eval...</h2>', 'dam-spam' );
		flush();
		if ( ds_scan_for_eval() ) {
			$disp = true;
		}
		if ( $disp ) { ?>
			<h2><?php esc_html_e( 'Possible Problems Found!', 'dam-spam' ); ?></h2>
			<p><?php esc_html_e( 'These are warnings only. Some content and plugins might not be
				malicious, but still contain one or more
				of these indicators. Please investigate all indications of
				problems. The plugin may err on the side of
				caution.', 'dam-spam' ); ?></p>
			<p><?php esc_html_e( 'Although there are legitimate reasons for using the eval
				function, and JavaScript uses it frequently,
				finding eval in PHP code is in the very least bad practice, and
				the worst is used to hide malicious
				code. If eval() comes up in a scan, try to get rid of it.', 'dam-spam' ); ?></p>
			<p><?php esc_html_e( 'Your code could contain "eval", or "document.write(unescape(" or
				"try{window.onload" or
				setAttribute("src". These are markers for problems such as SQL
				injection or crods-browser JavaScript.
				&lt;script&gt; tags should occur in your posts, if you added
				them, but should not be found anywhere
				else, except options. Options often have scripts for displaying
				Facebook, Twitter, etc. Be careful,
				though, if one appears in an option. Most of the time it is OK,
				but make sure.', 'dam-spam' ); ?></p>
		<?php } else { ?>
			<h2><?php esc_html_e( 'No Problems Found', 'dam-spam' ); ?></h2>
			<p><?php esc_html_e( 'It appears that there are no eval or suspicious JavaScript
				functions in the code in your wp-content
				directory. That does not mean that you are safe, only that a
				threat may be well-hidden.', 'dam-spam' ); ?></p>
		<?php }
		flush();
	} // end if runscan
	function ds_scan_for_eval() {
		// scan content completely
		// WP_CONTENT_DIR is supposed to have the content dir
		$phparray = array();
		// use get_home_path()
		// $phparray=ds_scan_for_eval_recurse(WP_CONTENT_DIR.'/..',$phparray);
		$phparray = ds_scan_for_eval_recurse( realpath( get_home_path() ), $phparray );
		// phparray should have a list of all of the PHP files
		$disp = false;
		esc_html_e( 'Files:', 'dam-spam' );
		echo '<ol>';
		for ( $j = 0; $j < count( $phparray ); $j ++ ) {
		// ignore my work on this subject
			if ( strpos( $phparray[$j], 'threat_scan' ) === false && strpos( $phparray[$j], 'threat-scan' ) === false ) {
				$ansa = ds_look_in_file( $phparray[$j] );
				if ( count( $ansa ) > 0 ) {
					$disp = true;
					// echo "Think we got something<br>";
					echo '<li>' . $phparray[$j] . ' <br> ';
					for ( $k = 0; $k < count( $ansa ); $k ++ ) {
						echo $ansa[$k] . ' <br>';
					}
					echo '</li>';
				}
			}
		}
		echo '</ol>';
		return $disp;
	} // end of function
	// recursive walk of directory structure.
	function ds_scan_for_eval_recurse( $dir, $phparray ) {
		if ( !@is_dir( $dir ) ) {
			return $phparray;
		}
		// if (substr($dir,0,1)='.') return $phparray;
		$dh = null;
		// can't protect this - turn off the error capture for a moment.
		sfs_errorsonoff( 'off' );
		try {
			$dh = @opendir( $dir );
		} catch ( Exception $e ) {
			sfs_errorsonoff();
			return $phparray;
		}
		sfs_errorsonoff();
		if ( $dh !== null && $dh !== false ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if ( @is_dir( $dir . '/' . $file ) ) {
					if ( $file != '.' && $file != '..' && $file != ':'
						 && strpos( '/', $file ) === false
					) { // that last one does some symbolics?
						$phparray = ds_scan_for_eval_recurse( $dir . '/' . $file, $phparray );
					}
				} else if ( strpos( $file, '.php' ) > 0 ) {
					$phparray[count( $phparray )] = $dir . '/' . $file;
				} else {
				// echo "can't find .php in $file <br>";
				}
			}
			closedir( $dh );
		}
		return $phparray;
	}
	function ds_look_in_file( $file ) {
		if ( !file_exists( $file ) ) {
			return false;
		}
		// don't look in this plugin because it finds too much stuff
		// only look for .php files - no more javascript
		if ( strpos( $file, '.php' ) === false ) {
			return false;
		}
		$handle = @fopen( $file, 'r' );
		if ( $handle === false ) {
			return array();
		}
		$ansa	 = array();
		$n	     = 0;
		$idx	 = 0;
		$badguys = array(
			'eval(',
			'eval (',
			'document.write(unescape(',
			'try{window.onload',
			'escape(document[',
			'escape(navigator[',
			"setAttribute('src'",
			'document.write(string.fromcharcode',
			'base64' . '_decode',
			'gzun' . 'compress',
			'gz' . 'inflate',
			'if(!isset($GLOBALS[' . "\\'\\a\\e\\0",
			'passssword',
			'Bruteforce protection',
			'w.wpquery.o',
			"<scr'+"
		);
		while ( !@feof( $handle ) ) {
			$line = fgets( $handle );
			$line = htmlentities( $line );
			$n ++;
			foreach ( $badguys as $baddie ) {
				if ( !( strpos( $line, $baddie ) === false ) ) {
					// bad boy
					if ( ds_ok_list( $file, $n ) ) {
						$line		  = ds_make_red( $baddie, $line );
						$ansa[$idx] = $n . ': ' . $line;
						$idx ++;
					}
				}
			}
			// search line for $xxxxx() type things
			$m	    = 0;
			$f	    = false;
			$vchars = '!@#$%^&*),.;:\"[]{}?/+=_- \t\\|~`<>' . "'"; // not part of variable names
			while ( $m < strlen( $line ) - 2 ) {
				$m = strpos( $line, '$', $m );
				if ( $m === false ) {
					break;
				}
				if ( substr( $line, $m, 7 ) != '$class(' ) { // used often and correctly
					$mi = $m;
					$mi ++;
					for ( $mm = $mi; ( $mm < $mi + 8 && $mm < strlen( $line ) ); $mm ++ ) {
						$c = substr( $line, $mm, 1 );
						if ( $c == '(' && $mm > $mi ) { // need at least a character so as not to kill jQuery
							$f = true;
							break;
						}
						if ( strpos( $vchars, $c ) !== false ) {
							break;
						}
					}
				}
				if ( $f ) {
					break;
				}
				$m ++;
			}
			if ( $f ) {
				if ( ds_ok_list( $file, $n ) ) {
					$ll		      = substr( $line, $m, 7 );
					$line		  = ds_make_red( $ll, $line );
					$ansa[$idx] = $n . ': ' . $line;
					$idx ++;
				}
			}
		}
		fclose( $handle );
		return $ansa;
	}
	function ds_make_red( $needle, $haystack ) {
		// turns error red
		$j = strpos( $haystack, $needle );
		$s = substr_replace( $haystack, '</span>', $j + strlen( $needle ), 0 );
		$s = substr_replace( $s, '<span style="color:red;">', $j, 0 );
		return $s;
	}
	function ds_ok_list( $file, $line ) {
		// more advanced excluder file=>array(start,end,start,end,start,end
		// start and end are loose to allow for varuous versions - hope that they don't hide some bad code
		$exclude = array(
			'class-pclzip.php'								   => array(
				3700,
				4300
			),
			'wp-admin/includes/file.php'					   => array(
				450,
				550
			),
			'wp-admin/preds-this.php'						   => array(
				200,
				250,
				400,
				450
			),
			'jetpack/class.jetpack.php'						   => array(
				5000,
				5100
			),
			'jetpack/locales.php'							   => array(
				25,
				75
			),
			'custom-css/preprocessors/lessc.inc.php'		   => array(
				25,
				75,
				1500,
				1600
			),
			'preprocessors/scss.inc.php'					   => array(
				800,
				900,
				1800,
				1900
			),
			'ds_challenge.php'								   => array(
				0,
				300
			),
			'modules/chkexploits.php'						   => array(
				10,
				30
			),
			'wp-includes/class-http.php'					   => array(
				2000,
				2300
			),
			'class-IXR.php'									   => array(
				300,
				350
			),
			'all-in-one-seo-pack/JSON.php'					   => array(
				10,
				30
			),
			'all-in-one-seo-pack/OAuth.php'					   => array(
				240,
				300
			),
			'all-in-one-seo-pack/aioseop_sitemap.php'		   => array(
				500,
				600
			),
			'wp-includes/class-json.php'					   => array(
				10,
				30
			),
			'p-includes/class-smtp.php'						   => array(
				300,
				400
			),
			'wp-includes/class-snoopy.php'					   => array(
				650,
				700
			),
			'wp-includes/class-feed.php'					   => array(
				100,
				150
			),
			'wp-includes/class-wp-customize-widgets.php'	   => array(
				1100,
				1250
			),
			'wp-includes/compat.php'						   => array(
				40,
				60
			),
			'/jsonwrapper/JSON/JSON.php'					   => array(
				10,
				30
			),
			'wp-includes/functions.php'						   => array(
				200,
				250
			),
			'wp-includes/ID3/module.audio-video.quicktime.php' => array(
				450,
				550
			),
			'wp-includes/ID3/module.audio.ogg.php'			   => array(
				550,
				650
			),
			'wp-includes/ID3/module.tag.id3v2.php'			   => array(
				550,
				650
			),
			'wp-includes/pluggable.php'						   => array(
				1750,
				1850
			),
			'wp-includes/session.php'						   => array(
				25,
				75
			),
			'wp-includes/SimplePie/File.php'				   => array(
				200,
				300
			),
			'wp-includes/SimplePie/gzdecode.php'			   => array(
				300,
				350
			),
			'wp-includes/SimplePie/Sanitize.php'			   => array(
				225,
				275,
				300,
				350
			),
			'dam-spam.php'									   => array(
				250,
				400
			)
		);
		foreach ( $exclude as $f => $ln ) {
			if ( stripos( $file, $f ) !== false ) {
				// found a file
				for ( $j = 0; $j < count( $ln ) / 2; $j ++ ) {
					$t1 = $ln[$j * 2];
					$t2 = $ln[( $j * 2 ) + 1];
					// echo "checking $file, $f for $line and '$ln'<br>";
					if ( $line >= $t1 && $line <= $t2 ) {
						return false;
					}
				}
			}
		}
		// if ( strpos( $file, 'dam-spam' ) !== false ) return false;
		return true;
	}
	?>
</div>