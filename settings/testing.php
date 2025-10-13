<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

$stats   = ds_get_stats();
$options = ds_get_options();

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

ds_fix_post_vars();
$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );

$ip  = ds_get_ip();
$hip = 'unknown';

if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) && !empty( $_SERVER['SERVER_ADDR'] ) ) {
	$hip = filter_var( wp_unslash( $_SERVER['SERVER_ADDR'] ), FILTER_VALIDATE_IP );
	if ( !$hip ) {
		$hip = 'unknown';
	}
}

$email   = '';
$author  = '';
$subject = '';
$body	 = '';

if ( array_key_exists( 'ip', $_POST ) ) {
	if ( filter_var( wp_unslash( $_POST['ip'] ), FILTER_VALIDATE_IP ) ) {
		$ip = sanitize_text_field( wp_unslash( $_POST['ip'] ) );
	}
}

if ( array_key_exists( 'email', $_POST ) ) {
	$email = sanitize_email( wp_unslash( $_POST['email'] ) );
}

if ( array_key_exists( 'author', $_POST ) ) {
	$author = sanitize_text_field( wp_unslash( $_POST['author'] ) );
}

if ( array_key_exists( 'subject', $_POST ) ) {
	$subject = sanitize_text_field( wp_unslash( $_POST['subject'] ) );
}

if ( array_key_exists( 'body', $_POST ) ) {
	$body = sanitize_textarea_field( wp_unslash( $_POST['body'] ) );
}

$nonce = wp_create_nonce( 'ds_update' );

?>

<div id="ds-plugin" class="wrap">
	<h1 id="ds-head"><?php esc_html_e( 'Testing — Dam Spam', 'dam-spam' ); ?></h1>
	<form method="post" action="">
		<div class="ds-info-box">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
			<div class="mainsection"><?php esc_html_e( 'Option Testing', 'dam-spam' ); ?></div>
			<p><?php esc_html_e( 'Run the settings against an IP address to test.', 'dam-spam' ); ?></p>
			<?php esc_html_e( 'IP Address:', 'dam-spam' ); ?><br>
			<input id="ds-input" name="ip" type="text" value="<?php echo esc_attr( $ip ); ?>">
			(<?php esc_html_e( 'Server IP:', 'dam-spam' ); ?> <?php echo esc_html( $hip ); ?>)<br><br>
			<?php esc_html_e( 'Email:', 'dam-spam' ); ?><br>
			<input id="ds-input" name="email" type="text" value="<?php echo esc_attr( $email ); ?>"><br><br>
			<?php esc_html_e( 'Username:', 'dam-spam' ); ?><br>
			<input id="ds-input" name="author" type="text" value="<?php echo esc_attr( $author ); ?>"><br><br>
			<?php esc_html_e( 'Subject:', 'dam-spam' ); ?><br>
			<input id="ds-input" name="subject" type="text" value="<?php echo esc_attr( $subject ); ?>"><br><br>
			<?php esc_html_e( 'Comment:', 'dam-spam' ); ?><br>
			<textarea name="body"><?php echo esc_html( $body ); ?></textarea><br>
			<div class="half">
				<p class="submit"><input name="testopt" class="button-primary" value="<?php esc_html_e( 'Test Options', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<div class="half">
				<p class="submit"><input name="testcountry" class="button-primary" value="<?php esc_html_e( 'Test Countries', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<br style="clear:both">
			<?php
			$nonce = '';
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['ds_control'] ) );
			}
			if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'ds_update' ) ) {
				$post = get_post_variables();
				if ( array_key_exists( 'testopt', $_POST ) ) {
					$optionlist = array(
						'check_aws',
						'check_cloudflare',
						'check_good_cache',
						'check_general_allow_list',
						'check_google',
						'check_misc_allow_list',
						'check_paypal',
						'check_scripts',
						'check_valid_ip',
						'check_allowed_email',
						'check_allowed_user_id',
						'check_allow_list',
						'check_allow_list_email',
						'check_form',
						'check_yahoo_merchant'
					);
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					// translators: %1$s is memory used, %2$s is peak memory
					printf( esc_html__( 'Memory Used: %1$s Peak: %2$s', 'dam-spam' ), esc_html( $m1 ), esc_html( $m2 ) );
					echo '<br><br>';
					esc_html_e( 'Allow Checks', 'dam-spam' );
					echo '<ul>';
					foreach ( $optionlist as $check ) {
						$answer = ds_load( $check, $ip, $stats, $options, $post );
						if ( empty( $answer ) ) {
							$answer = 'OK';
						}
						echo esc_html( $check ) . ': ' . esc_html( $answer ) . '<br>';
					}
					echo '</ul>';
					$optionlist = array(
						'check_404',
						'check_accept',
						'check_admin',
						'check_admin_log',
						'check_agent',
						'check_amazon',
						'check_bbcode',
						'check_bad_cache',
						'check_blocked_email',
						'check_blocked_user_id',
						'check_blocked_ip',
						'check_botscout',
						'check_disposable',
						'check_dnsbl',
						'check_exploits',
						'check_google_safe',
						'check_honeypot',
						'check_hosting',
						'check_invalid_ip',
						'check_long',
						'check_multi',
						'check_periods',
						'check_referer',
						'check_session',
						'check_sfs',
						'check_short',
						'check_spam_words',
						'check_tld',
						'check_ubiquity',
						'check_url_short',
						'check_urls'
					);
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					// translators: %1$s is memory used, %2$s is peak memory
					printf( esc_html__( 'Memory Used: %1$s Peak: %2$s', 'dam-spam' ), esc_html( $m1 ), esc_html( $m2 ) );
					echo '<br><br>';
					esc_html_e( 'Block Checks', 'dam-spam' );
					echo '<ul>';
					foreach ( $optionlist as $check ) {
						$answer = ds_load( $check, $ip, $stats, $options, $post );
						if ( empty( $answer ) ) {
							$answer = 'OK';
						}
						echo esc_html( $check ) . ': ' . esc_html( $answer ) . '<br>';
					}
					echo '</ul>';
					$optionlist = array();
					$a1		    = apply_filters( 'ds_addons_allow', $optionlist );
					$a3		    = apply_filters( 'ds_addons_block', $optionlist );
					$a5		    = apply_filters( 'ds_addons_get', $optionlist );
					$optionlist = array_merge( $a1, $a3, $a5 );
					if ( !empty( $optionlist ) ) {
						echo 'Add-on Checks';
						echo '<ul>';
						foreach ( $optionlist as $check ) {
							$answer = ds_load( $check, $ip, $stats, $options, $post );
							if ( empty( $answer ) ) {
								$answer = 'OK';
							}
							$nm = $check[1];
							echo esc_html( $nm ) . ': ' . esc_html( $answer ) . '<br>';
						}
						echo '</ul>';
					}
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					// translators: %1$s is memory used, %2$s is peak memory
					printf( esc_html__( 'Memory Used: %1$s Peak: %2$s', 'dam-spam' ), esc_html( $m1 ), esc_html( $m2 ) );
					echo '<br><br>';
				}
				if ( array_key_exists( 'testcountry', $_POST ) ) {
					$optionlist = array(
						'check_ad',
						'check_ae',
						'check_af',
						'check_al',
						'check_am',
						'check_ar',
						'check_at',
						'check_au',
						'check_ax',
						'check_az',
						'check_ba',
						'check_bb',
						'check_bd',
						'check_be',
						'check_bg',
						'check_bh',
						'check_bn',
						'check_bo',
						'check_br',
						'check_bs',
						'check_by',
						'check_bz',
						'check_ca',
						'check_cd',
						'check_ch',
						'check_cl',
						'check_cn',
						'check_co',
						'check_cr',
						'check_cu',
						'check_cw',
						'check_cy',
						'check_cz',
						'check_de',
						'check_dk',
						'check_do',
						'check_dz',
						'check_ec',
						'check_ee',
						'check_es',
						'check_eu',
						'check_fi',
						'check_fj',
						'check_fr',
						'check_gb',
						'check_ge',
						'check_gf',
						'check_gi',
						'check_gp',
						'check_gr',
						'check_gt',
						'check_gu',
						'check_gy',
						'check_hk',
						'check_hn',
						'check_hr',
						'check_ht',
						'check_hu',
						'check_id',
						'check_ie',
						'check_il',
						'check_in',
						'check_iq',
						'check_ir',
						'check_is',
						'check_it',
						'check_jm',
						'check_jo',
						'check_jp',
						'check_ke',
						'check_kg',
						'check_kh',
						'check_kr',
						'check_kw',
						'check_ky',
						'check_kz',
						'check_la',
						'check_lb',
						'check_lk',
						'check_lt',
						'check_lu',
						'check_lv',
						'check_md',
						'check_me',
						'check_mk',
						'check_mm',
						'check_mn',
						'check_mo',
						'check_mp',
						'check_mq',
						'check_mt',
						'check_mv',
						'check_mx',
						'check_my',
						'check_nc',
						'check_ni',
						'check_nl',
						'check_no',
						'check_np',
						'check_nz',
						'check_om',
						'check_pa',
						'check_pe',
						'check_pg',
						'check_ph',
						'check_pk',
						'check_pl',
						'check_pr',
						'check_ps',
						'check_pt',
						'check_pw',
						'check_py',
						'check_qa',
						'check_ro',
						'check_rs',
						'check_ru',
						'check_sa',
						'check_sc',
						'check_se',
						'check_sg',
						'check_si',
						'check_sk',
						'check_sv',
						'check_sx',
						'check_sy',
						'check_th',
						'check_tj',
						'check_tm',
						'check_tr',
						'check_tt',
						'check_tw',
						'check_ua',
						'check_uk',
						'check_us',
						'check_uy',
						'check_uz',
						'check_vc',
						'check_ve',
						'check_vn',
						'check_ye'
					);
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					// translators: %1$s is memory used, %2$s is peak memory
					printf( esc_html__( 'Memory Used: %1$s Peak: %2$s', 'dam-spam' ), esc_html( $m1 ), esc_html( $m2 ) );
					echo '<br><br>';
					foreach ( $optionlist as $check ) {
						$answer = ds_load( $check, $ip, $stats, $options, $post );
						if ( empty( $answer ) ) {
							$answer = 'OK';
						}
						echo esc_html( $check ) . ': ' . esc_html( $answer ) . '<br>';
					}
					$m1 = memory_get_usage( true );
					$m2 = memory_get_peak_usage( true );
					echo '<br><br>';
					// translators: %1$s is memory used, %2$s is peak memory
					printf( esc_html__( 'Memory Used: %1$s Peak: %2$s', 'dam-spam' ), esc_html( $m1 ), esc_html( $m2 ) );
				}
			}
			?>
		</div>
		<div class="ds-info-box">
			<div class="half">
				<h2><?php esc_html_e( 'Display All Options', 'dam-spam' ); ?></h2>
				<p><?php esc_html_e( 'You can dump all options here (useful for debugging).', 'dam-spam' ); ?></p>
				<p class="submit"><input name="dumpoptions" class="button-primary" value="<?php esc_attr_e( 'Dump Options', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<div class="half">
				<h2><?php esc_html_e( 'Display All Stats', 'dam-spam' ); ?></h2>
				<p><?php esc_html_e( 'You can dump all stats here.', 'dam-spam' ); ?></p>
				<p class="submit"><input name="dumpstats" class="button-primary" value="<?php esc_attr_e( 'Dump Stats', 'dam-spam' ); ?>" type="submit"></p>
			</div>
			<br style="clear:both">
			<?php
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['ds_control'] ) );
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
						echo '<strong>&bull; ' . esc_html( $key ) . '</strong> = ' . esc_html( $val ) . "\r\n";
					}
					echo "\r\n";
					echo '</pre>';
					?>
				<?php }
			}
			?>
			<?php
			if ( array_key_exists( 'ds_control', $_POST ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_POST['ds_control'] ) );
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
						echo '<strong>&bull; ' . esc_html( $key ) . '</strong> = ' . esc_html( $val ) . "\r\n";
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
		<a href="" onclick="var el=document.getElementById('shpinf');el.style.display=(el.style.display==='none'?'block':'none');this.textContent=(el.style.display==='none'?'<?php echo esc_js( __( 'Show PHP Info', 'dam-spam' ) ); ?>':'<?php echo esc_js( __( 'Hide PHP Info', 'dam-spam' ) ); ?>');return false;" id="php-info" class="button-primary"><?php esc_html_e( 'Show PHP Info', 'dam-spam' ); ?></a>
		<?php
		ob_start();
		phpinfo();
		preg_match( '%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches );
		$allowed_tags = array(
			'div' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'table' => array( 'class' => array(), 'id' => array(), 'style' => array(), 'border' => array(), 'cellpadding' => array(), 'cellspacing' => array() ),
			'tbody' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'thead' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'tr' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'td' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'th' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h1' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'h2' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
			'a' => array( 'class' => array(), 'id' => array(), 'style' => array(), 'name' => array(), 'href' => array() ),
			'img' => array( 'class' => array(), 'id' => array(), 'style' => array(), 'src' => array(), 'alt' => array(), 'border' => array() ),
			'svg' => array( 'class' => array(), 'id' => array(), 'style' => array(), 'xmlns' => array(), 'viewbox' => array(), 'width' => array(), 'height' => array() ),
			'path' => array( 'class' => array(), 'id' => array(), 'style' => array(), 'd' => array(), 'fill' => array(), 'stroke' => array() ),
			'hr' => array( 'class' => array(), 'id' => array(), 'style' => array() ),
		);
		echo "<div class='phpinfodisplay' id=\"shpinf\" style=\"display:none\"><style>\n",
		esc_html(
			join( "\n",
				array_map(
					function( $i ) {
						return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );
					},
					preg_split( '/\n/', $matches[1] )
				)
			)
		),
		"</style>\n",
		wp_kses( $matches[2], $allowed_tags ),
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
		$nonce = sanitize_text_field( wp_unslash( $_POST['ds_control'] ) );
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
		<p><?php esc_html_e( 'Simple scan that looks for odd thing in /wp-content and the database.', 'dam-spam' ); ?></p>
		<form method="post" action="#scan">
			<input type="hidden" name="update_options" value="update">
			<input type="hidden" name="ds_control" value="<?php echo esc_attr( $nonce ); ?>">
			<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Run Scan', 'dam-spam' ); ?>" type="submit"></p>
		</form>
	</div>
	<?php if ( $runscan ) { ?>
		<h2><?php esc_html_e( 'A clean scan does not mean you\'re safe.', 'dam-spam' ); ?></h2>
		<hr>
		<?php
		$disp = false;
		flush();
		echo '<br><br>' . esc_html__( 'Testing Posts', 'dam-spam' ) . '<br>';
		$ptab = $pre . 'posts';
		$sql = $wpdb->prepare(
			"SELECT ID,post_author,post_title,post_name,guid,post_content,post_mime_type
			FROM {$wpdb->posts} WHERE 
			INSTR(LCASE(post_author), %s) +
			INSTR(LCASE(post_title), %s) +
			INSTR(LCASE(post_name), %s) +
			INSTR(LCASE(guid), %s) +
			INSTR(LCASE(post_author), %s) +
			INSTR(LCASE(post_title), %s) +
			INSTR(LCASE(post_name), %s) +
			INSTR(LCASE(guid), %s) +
			INSTR(LCASE(post_content), %s) +
			INSTR(LCASE(post_author), %s) +
			INSTR(LCASE(post_title), %s) +
			INSTR(LCASE(post_name), %s) +
			INSTR(LCASE(guid), %s) +
			INSTR(LCASE(post_content), %s) +
			INSTR(LCASE(post_content), %s) +
			INSTR(LCASE(post_content), %s) +
			INSTR(LCASE(post_content), %s) +
			INSTR(LCASE(post_mime_type), %s) > 0",
			'<script', '<script', '<script', '<script',
			'eval(', 'eval(', 'eval(', 'eval(', 'eval(',
			'eval (', 'eval (', 'eval (', 'eval (', 'eval (',
			'document.write(unescape(', 'try{window.onload', "setAttribute('src'", 'script'
		);
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
				// translators: %1$s is the reason for the problem, %2$s is the post ID
				printf( esc_html__( 'found possible problems in post (%1$s) ID: %2$s', 'dam-spam' ), esc_html( $reason ), esc_html( $myrow->ID ) );
				echo '<br>';
			}
		} else {
			echo '<br>' . esc_html__( 'Nothing found in posts.', 'dam-spam' ) . '<br>';
			$disp = false;
		}
		echo '<hr>';
		$ptab = $pre . 'comments';
		echo '<br><br>' . esc_html__( 'Testing Comments<br>', 'dam-spam' ) . '<br>';
		flush();
		$sql = $wpdb->prepare(
			"SELECT comment_ID,comment_author_url,comment_agent,comment_author,comment_author_email,comment_content
			FROM {$wpdb->comments} WHERE 
			INSTR(LCASE(comment_author_url), %s) +
			INSTR(LCASE(comment_agent), %s) +
			INSTR(LCASE(comment_author), %s) +
			INSTR(LCASE(comment_author_email), %s) +
			INSTR(LCASE(comment_author_url), %s) +
			INSTR(LCASE(comment_agent), %s) +
			INSTR(LCASE(comment_author), %s) +
			INSTR(LCASE(comment_author_email), %s) +
			INSTR(LCASE(comment_author_url), %s) +
			INSTR(LCASE(comment_agent), %s) +
			INSTR(LCASE(comment_author), %s) +
			INSTR(LCASE(comment_author_email), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_content), %s) +
			INSTR(LCASE(comment_author_url), %s) > 0",
			'<script', '<script', '<script', '<script',
			'eval(', 'eval(', 'eval(', 'eval(',
			'eval (', 'eval (', 'eval (', 'eval (',
			'<script', 'eval(', 'eval (', 'document.write(unescape(', 'try{window.onload', "setAttribute('src'", 'javascript:'
		);
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
				// translators: %1$s is the reason for the problem, %2$s is the comment ID
				printf( esc_html__( 'found possible problems in comment (%1$s) ID: %2$s', 'dam-spam' ), esc_html( $reason ), esc_html( $myrow->comment_ID ) );
				echo '<br>';
			}
		} else {
			echo '<br>' . esc_html__( 'Nothing found in comments.', 'dam-spam' ) . '<br>';
		}
		flush();
		echo '<hr>';
		$ptab   = $pre . 'links';
		echo '<br><br>' . esc_html__( 'Testing Links', 'dam-spam' ) . '<br>';
		flush();
		$sql = $wpdb->prepare(
			"SELECT link_ID,link_url,link_image,link_description,link_notes
			FROM {$wpdb->links} WHERE 
			INSTR(LCASE(link_url), %s) +
			INSTR(LCASE(link_image), %s) +
			INSTR(LCASE(link_description), %s) +
			INSTR(LCASE(link_notes), %s) +
			INSTR(LCASE(link_rss), %s) +
			INSTR(LCASE(link_url), %s) +
			INSTR(LCASE(link_image), %s) +
			INSTR(LCASE(link_description), %s) +
			INSTR(LCASE(link_notes), %s) +
			INSTR(LCASE(link_rss), %s) +
			INSTR(LCASE(link_url), %s) +
			INSTR(LCASE(link_image), %s) +
			INSTR(LCASE(link_description), %s) +
			INSTR(LCASE(link_notes), %s) +
			INSTR(LCASE(link_rss), %s) +
			INSTR(LCASE(link_url), %s) > 0",
			'<script', '<script', '<script', '<script', '<script',
			'eval(', 'eval(', 'eval(', 'eval(', 'eval(',
			'eval (', 'eval (', 'eval (', 'eval (', 'eval (',
			'javascript:'
		);
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
				// translators: %1$s is the reason for the problem, %2$s is the link ID
				printf( esc_html__( 'found possible problems in links (%1$s) ID: %2$s', 'dam-spam' ), esc_html( $reason ), esc_html( $myrow->link_ID ) );
				echo '<br>';
			}
		} else {
			echo '<br>' . esc_html__( 'Nothing found in links.', 'dam-spam' ) . '<br>';
		}
		echo '<hr>';
		$ptab = $pre . 'users';
		echo '<br><br>' . esc_html__( 'Testing Users', 'dam-spam' ) . '<br>';
		flush();
		$sql = $wpdb->prepare(
			"SELECT ID,user_login,user_nicename,user_email,user_url,display_name 
			FROM {$wpdb->users} WHERE 
			INSTR(LCASE(user_login), %s) +
			INSTR(LCASE(user_nicename), %s) +
			INSTR(LCASE(user_email), %s) +
			INSTR(LCASE(user_url), %s) +
			INSTR(LCASE(display_name), %s) +
			INSTR(user_login, %s) +
			INSTR(user_nicename, %s) +
			INSTR(user_email, %s) +
			INSTR(user_url, %s) +
			INSTR(display_name, %s) +
			INSTR(user_login, %s) +
			INSTR(user_nicename, %s) +
			INSTR(user_email, %s) +
			INSTR(user_url, %s) +
			INSTR(user_nicename, %s) +
			INSTR(LCASE(user_url), %s) +
			INSTR(LCASE(user_email), %s) > 0",
			'<script', '<script', '<script', '<script', '<script',
			'eval(', 'eval(', 'eval(', 'eval(', 'eval(',
			'eval (', 'eval (', 'eval (', 'eval (', 'eval (',
			'javascript:', 'javascript:'
		);
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
				// translators: %1$s is the reason for the problem, %2$s is the user ID
				printf( esc_html__( 'found possible problems in users (%1$s) ID: %2$s', 'dam-spam' ), esc_html( $reason ), esc_html( $myrow->ID ) );
				echo '<br>';
			}
		} else {
			echo '<br><br>' . esc_html__( 'Nothing found in users.', 'dam-spam' ) . '<br>';
		}
		echo '<hr>';
		$ptab = $pre . 'options';
		echo '<br><br>' . esc_html__( 'Testing Options Table for HTML', 'dam-spam' ) . '<br>';
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
		$sql = "SELECT option_id,option_value,option_name FROM {$wpdb->options} WHERE ";
		$placeholders = array();
		$values = array();
		foreach ( $badguys as $baddie => $reas ) {
			$placeholders[] = "INSTR(LCASE(option_value), %s)";
			$values[] = $baddie;
		}
		$sql .= implode( ' + ', $placeholders ) . ' > 0';
		$sql = $wpdb->prepare( $sql, $values );
		$myrows = $wpdb->get_results( $sql );
		if ( $myrows ) {
			foreach ( $myrows as $myrow ) {
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
							$line   = ds_make_red( $baddie, $line );
							$reason .= $reas . ' ';
						}
					}
				}
				// translators: %1$s is the option name, %2$s is the reason, %3$s is the option ID, %4$s is the option value
				printf( esc_html__( 'Found possible problems in option %1$s (%2$s) ID: %3$s, Value: %4$s', 'dam-spam' ),
				esc_html( $name ), esc_html( $reason ), esc_html( $myrow->option_id ), wp_kses( $line, array() ) );
				echo '<br><br>';
			}
		} else {
			echo '<br>' . esc_html__( 'Nothing found in options.', 'dam-spam' ) . '<br>';
		}
		echo '<hr>';
		echo '<h2>' . esc_html__( 'Scanning Themes and Plugins for eval...', 'dam-spam' ) . '</h2>';
		flush();
		if ( ds_scan_for_eval() ) {
			$disp = true;
		}
		if ( $disp ) { ?>
			<h2><?php esc_html_e( 'Possible Problems Found!', 'dam-spam' ); ?></h2>
			<p><?php esc_html_e( 'These are warnings only, which may contain false positives.', 'dam-spam' ); ?></p>
			<p><?php esc_html_e( 'While there can be legitimate uses for eval(), its appearance in themes and plugins can be suspicious.', 'dam-spam' ); ?></p>
			<p><?php esc_html_e( '"eval", "document.write(unescape(", "try{window.onload", or setAttribute("src" could be indication of a possible SQL injection attack.', 'dam-spam' ); ?></p>
		<?php } else { ?>
			<h2><?php esc_html_e( 'No Problems Found', 'dam-spam' ); ?></h2>
			<p><?php esc_html_e( 'No eval or suspicious JavaScript found in /wp-content.', 'dam-spam' ); ?></p>
		<?php }
		flush();
	}
	function ds_scan_for_eval() {
		$phparray = array();
		$phparray = ds_scan_for_eval_recurse( realpath( get_home_path() ), $phparray );
		$disp = false;
		esc_html_e( 'Files:', 'dam-spam' );
		echo '<ol>';
		for ( $j = 0; $j < count( $phparray ); $j ++ ) {
			if ( strpos( $phparray[$j], 'threat_scan' ) === false && strpos( $phparray[$j], 'threat-scan' ) === false ) {
				$answer = ds_look_in_file( $phparray[$j] );
				if ( count( $answer ) > 0 ) {
					$disp = true;
					echo '<li>' . esc_html( $phparray[$j] ) . ' <br> ';
					for ( $k = 0; $k < count( $answer ); $k ++ ) {
						echo wp_kses( $answer[$k], array( 'span' => array( 'style' => array() ) ) ) . ' <br>';
					}
					echo '</li>';
				}
			}
		}
		echo '</ol>';
		return $disp;
	}
	function ds_scan_for_eval_recurse( $dir, $phparray ) {
		if ( !@is_dir( $dir ) ) {
			return $phparray;
		}
		$dh = null;
		try {
			$dh = @opendir( $dir );
		} catch ( Exception $e ) {
			return $phparray;
		}
		if ( $dh !== null && $dh !== false ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if ( @is_dir( $dir . '/' . $file ) ) {
					if ( $file != '.' && $file != '..' && $file != ':' && strpos( '/', $file ) === false ) {
						$phparray = ds_scan_for_eval_recurse( $dir . '/' . $file, $phparray );
					}
				} else if ( strpos( $file, '.php' ) > 0 ) {
					$phparray[count( $phparray )] = $dir . '/' . $file;
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
		if ( strpos( $file, '.php' ) === false ) {
			return false;
		}
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}
		$contents = $wp_filesystem->get_contents( $file );
		if ( $contents === false ) {
			return array();
		}
		$lines = explode( "\n", $contents );
		$answer	 = array();
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
		foreach ( $lines as $n => $line ) {
			$line = htmlentities( $line );
			$line_num = $n + 1;
			foreach ( $badguys as $baddie ) {
				if ( !( strpos( $line, $baddie ) === false ) ) {
					if ( ds_ok_list( $file, $line_num ) ) {
						$line		  = ds_make_red( $baddie, $line );
						$answer[$idx] = $line_num . ': ' . $line;
						$idx ++;
					}
				}
			}
			$m	    = 0;
			$f	    = false;
			$vchars = '!@#$%^&*),.;:\"[]{}?/+=_- \t\\|~`<>' . "'";
			while ( $m < strlen( $line ) - 2 ) {
				$m = strpos( $line, '$', $m );
				if ( $m === false ) {
					break;
				}
				if ( substr( $line, $m, 7 ) != '$class(' ) {
					$mi = $m;
					$mi ++;
					for ( $mm = $mi; ( $mm < $mi + 8 && $mm < strlen( $line ) ); $mm ++ ) {
						$c = substr( $line, $mm, 1 );
						if ( $c == '(' && $mm > $mi ) {
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
				if ( ds_ok_list( $file, $line_num ) ) {
					$ll		    = substr( $line, $m, 7 );
					$line		= ds_make_red( $ll, $line );
					$answer[$idx] = $line_num . ': ' . $line;
					$idx ++;
				}
			}
		}
		return $answer;
	}
	function ds_make_red( $needle, $haystack ) {
		$j = strpos( $haystack, $needle );
		$s = substr_replace( $haystack, '</span>', $j + strlen( $needle ), 0 );
		$s = substr_replace( $s, '<span style="color:red">', $j, 0 );
		return $s;
	}
	function ds_ok_list( $file, $line ) {
		$exclude = array(
			'class-pclzip.php'								   => array( 3700, 4300 ),
			'wp-admin/includes/file.php'					   => array( 450, 550 ),
			'wp-admin/preds-this.php'						   => array( 200, 250, 400, 450 ),
			'jetpack/class.jetpack.php'						   => array( 5000, 5100 ),
			'jetpack/locales.php'							   => array( 25, 75 ),
			'custom-css/preprocessors/lessc.inc.php'		   => array( 25, 75, 1500, 1600 ),
			'preprocessors/scss.inc.php'					   => array( 800, 900, 1800, 1900 ),
			'ds_challenge.php'								   => array( 0, 300 ),
			'modules/check-exploits.php'					   => array( 10, 30 ),
			'wp-includes/class-http.php'					   => array( 2000, 2300 ),
			'class-IXR.php'									   => array( 300, 350 ),
			'all-in-one-seo-pack/JSON.php'					   => array( 10, 30 ),
			'all-in-one-seo-pack/OAuth.php'					   => array( 240, 300 ),
			'all-in-one-seo-pack/aioseop_sitemap.php'		   => array( 500, 600 ),
			'wp-includes/class-json.php'					   => array( 10, 30 ),
			'p-includes/class-smtp.php'						   => array( 300, 400 ),
			'wp-includes/class-snoopy.php'					   => array( 650, 700 ),
			'wp-includes/class-feed.php'					   => array( 100, 150 ),
			'wp-includes/class-wp-customize-widgets.php'	   => array( 1100, 1250 ),
			'wp-includes/compat.php'						   => array( 40, 60 ),
			'/jsonwrapper/JSON/JSON.php'					   => array( 10, 30 ),
			'wp-includes/functions.php'						   => array( 200, 250 ),
			'wp-includes/ID3/module.audio-video.quicktime.php' => array( 450, 550 ),
			'wp-includes/ID3/module.audio.ogg.php'			   => array( 550, 650 ),
			'wp-includes/ID3/module.tag.id3v2.php'			   => array( 550, 650 ),
			'wp-includes/pluggable.php'						   => array( 1750, 1850 ),
			'wp-includes/session.php'						   => array( 25, 75 ),
			'wp-includes/SimplePie/File.php'				   => array( 200, 300 ),
			'wp-includes/SimplePie/gzdecode.php'			   => array( 300, 350 ),
			'wp-includes/SimplePie/Sanitize.php'			   => array( 225, 275, 300, 350 ),
			'dam-spam.php'									   => array( 250, 400 )
		);
		foreach ( $exclude as $f => $ln ) {
			if ( stripos( $file, $f ) !== false ) {
				for ( $j = 0; $j < count( $ln ) / 2; $j ++ ) {
					$t1 = $ln[$j * 2];
					$t2 = $ln[( $j * 2 ) + 1];
					if ( $line >= $t1 && $line <= $t2 ) {
						return false;
					}
				}
			}
		}
		return true;
	}
	?>
</div>