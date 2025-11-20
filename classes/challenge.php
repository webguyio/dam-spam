<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class challenge extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$ip = ds_get_ip();
		$stats = ds_get_stats();
		$options = ds_get_options();
		if ( isset( $options['redir'] ) && $options['redir'] === 'Y' && !empty( $options['redirect_url'] ) ) {
			if ( isset( $_POST['_wpcf7'] ) ) {
				return wp_json_encode( $_POST );
			} else {
				wp_redirect( $options['redirect_url'], 307 );
				exit();
			}
		}
		$check_captcha = isset( $options['check_captcha'] ) ? $options['check_captcha'] : 'N';
		$allow_list_request = isset( $options['allow_list_request'] ) ? $options['allow_list_request'] : 'N';
		$reject_message = isset( $options['reject_message'] ) ? $options['reject_message'] : '';
		$ke = '';
		$km = '';
		$kr = '';
		$ka = '';
		$kp = '';
		$nonce = '';
		$msg = '';
		if ( !empty( $_POST ) && array_key_exists( 'kn', $_POST ) ) {
			if ( isset( $_POST['ke'] ) ) {
				$ke = sanitize_email( wp_unslash( $_POST['ke'] ) );
			}
			if ( array_key_exists( 'km', $_POST ) ) {
				$km = sanitize_text_field( wp_unslash( $_POST['km'] ) );
			}
			if ( strlen( $km ) > 80 ) {
				$km = substr( $km, 0, 77 ) . '...';
			}
			if ( array_key_exists( 'kr', $_POST ) ) {
				$kr = sanitize_text_field( wp_unslash( $_POST['kr'] ) );
			}
			if ( array_key_exists( 'ka', $_POST ) ) {
				$ka = sanitize_text_field( wp_unslash( $_POST['ka'] ) );
			}
			if ( array_key_exists( 'kp', $_POST ) ) {
				$kp = sanitize_textarea_field( wp_unslash( $_POST['kp'] ) );
			}
			if ( !empty( $_POST['kn'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kn'] ) ), 'ds_block' ) ) {
				$emailsent = $this->ds_send_email( $options );
				$allowset = false;
				if ( $allow_list_request === 'Y' ) {
					$allowset = $this->ds_add_allow( $ip, $options, $stats, $post, $post );
				}
				$msg = esc_html__( 'Thank you,', 'dam-spam' ) . '<br>';
				if ( $emailsent ) {
					$msg .= esc_html__( 'The webmaster has been notified.', 'dam-spam' ) . '<br>';
				}
				if ( $allowset ) {
					$msg .= esc_html__( 'Your request has been recorded.', 'dam-spam' ) . '<br>';
				}
				if ( empty( $check_captcha ) || $check_captcha === 'N' ) {
					wp_die( wp_kses_post( $msg ), 'Dam Spam', array( 'response' => 200 ) );
					exit();
				}
				switch ( $check_captcha ) {
					case 'G':
						if ( array_key_exists( 'recaptcha', $_POST ) && !empty( $_POST['recaptcha'] ) && array_key_exists( 'g-recaptcha-response', $_POST ) ) {
							$recaptchaapisecret = isset( $options['recaptchaapisecret'] ) ? $options['recaptchaapisecret'] : '';
							$recaptchaapisite = isset( $options['recaptchaapisite'] ) ? $options['recaptchaapisite'] : '';
							if ( empty( $recaptchaapisecret ) || empty( $recaptchaapisite ) ) {
								$msg = esc_html__( 'reCAPTCHA keys are not set.', 'dam-spam' );
							} else {
								$g = isset( $_REQUEST['g-recaptcha-response'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['g-recaptcha-response'] ) ) : '';
								$url = add_query_arg(
									array(
										'secret'   => $recaptchaapisecret,
										'response' => $g,
										'remoteip' => $ip,
									),
									'https://www.google.com/recaptcha/api/siteverify'
								);
								$resp = ds_read_file( $url );
								if ( strpos( $resp, '"success": true' ) !== false ) {
									ds_log_good( $ip, esc_html__( 'Passed reCAPTCHA', 'dam-spam' ), 'pass' );
									do_action( 'ds_ok', $ip, $post );
									return false;
								} else {
									$msg = esc_html__( 'Google reCAPTCHA entry does not match. Try again.', 'dam-spam' );
								}
							}
						}
						break;
					case 'H':
						if ( array_key_exists( 'h-captcha', $_POST ) && !empty( $_POST['h-captcha'] ) && array_key_exists( 'h-captcha-response', $_POST ) ) {
							$hcaptchaapisecret = isset( $options['hcaptchaapisecret'] ) ? $options['hcaptchaapisecret'] : '';
							$hcaptchaapisite = isset( $options['hcaptchaapisite'] ) ? $options['hcaptchaapisite'] : '';
							if ( empty( $hcaptchaapisecret ) || empty( $hcaptchaapisite ) ) {
								$msg = esc_html__( 'hCaptcha keys are not set.', 'dam-spam' );
							} else {
								$h = isset( $_REQUEST['h-captcha-response'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['h-captcha-response'] ) ) : '';
								$url = add_query_arg(
									array(
										'secret'   => $hcaptchaapisecret,
										'response' => $h,
										'remoteip' => $ip,
									),
									'https://hcaptcha.com/siteverify'
								);
								$resp = ds_read_file( $url );
								$response = json_decode( $resp );
								if ( isset( $response->success ) && $response->success === true ) {
									ds_log_good( $ip, esc_html__( 'Passed hCaptcha', 'dam-spam' ), 'pass' );
									do_action( 'ds_ok', $ip, $post );
									return false;
								} else {
									$msg = esc_html__( 'hCaptcha entry does not match. Try again.', 'dam-spam' );
								}
							}
						}
						break;
					case 'A':
					case 'Y':
						if ( array_key_exists( 'nums', $_POST ) && !empty( $_POST['nums'] ) ) {
							$seed = 5;
							$spam_date = isset( $stats['spam_date'] ) ? $stats['spam_date'] : '';
							if ( !empty( $spam_date ) ) {
								$seed = strtotime( $spam_date );
							}
							$nums = isset( $_POST['nums'] ) ? intval( wp_unslash( $_POST['nums'] ) ) : 0;
							$nums += $seed;
							$sum = isset( $_POST['sum'] ) ? intval( wp_unslash( $_POST['sum'] ) ) : 0;
							if ( $sum === $nums ) {
								ds_log_good( $ip, esc_html__( 'Passed Simple Arithmetic CAPTCHA', 'dam-spam' ), 'pass' );
								do_action( 'ds_ok', $ip, $post );
								return false;
							} else {
								$msg = esc_html__( 'Incorrect. Try again.', 'dam-spam' );
							}
						}
						break;
					case 'F':
						break;
				}
			}
		} else {
			$ke = isset( $post['email'] ) ? $post['email'] : '';
			$km = '';
			$kr = '';
			if ( array_key_exists( 'reason', $post ) ) {
				$kr = $post['reason'];
			}
			$ka = isset( $post['author'] ) ? sanitize_user( $post['author'] ) : '';
			$validated_post = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
			$kp = base64_encode( wp_json_encode( $validated_post ) );
		}
		$knonce = wp_create_nonce( 'ds_block' );
		if ( !empty( $msg ) ) {
			$msg = "\r\n<br><span style='color:red'> " . esc_html( $msg ) . " </span><hr>\r\n";
		}
		$formtop = '
			<form action="" method="post">
				<input type="hidden" name="kn" value="' . esc_attr( $knonce ) . '">
				<input type="hidden" name="ds_block" value="' . esc_attr( $check_captcha ) . '">
				<input type="hidden" name="kp" value="' . esc_attr( $kp ) . '">
				<input type="hidden" name="kr" value="' . esc_attr( $kr ) . '">
				<input type="hidden" name="ka" value="' . esc_attr( $ka ) . '">
		';
		$formbot = '<p><input class="button button-large" type="submit" value="' . esc_attr__( 'Submit Request', 'dam-spam' ) . '"></p>
			</form>';
		$not = '';
		if ( $allow_list_request === 'Y' ) {
			$not = '
				<h1>' . esc_html__( 'Allow Request', 'dam-spam' ) . '</h1>
				<p>' . esc_html__( 'You have been blocked from entering information on this site.', 'dam-spam' ) . '</p>
				<p>' . esc_html__( 'Email Address (required):', 'dam-spam' ) . '</p>
				<p><input type="text" name="ke" value=""></p>
				<p>' . esc_html__( 'Message:', 'dam-spam' ) . '</p>
				<textarea name="km" cols="80" rows="6" style="width:100%;box-sizing:border-box" placeholder="' . esc_attr__( 'Explain what you were trying to do or re-enter your message.', 'dam-spam' ) . '"></textarea>
			';
		}
		$captop = '<h1>' . esc_html__( 'Are you human?', 'dam-spam' ) . '</h1>';
		$capbot = '';
		$cap = '';
		switch ( $check_captcha ) {
			case 'G':
				$recaptchaapisite = isset( $options['recaptchaapisite'] ) ? $options['recaptchaapisite'] : '';
				$cap = "
					<script src='https://www.google.com/recaptcha/api.js' async defer></script>
					<input type='hidden' name='recaptcha' value='recaptcha'>
					<div class='g-recaptcha' data-sitekey='" . esc_attr( $recaptchaapisite ) . "'></div>
				";
				break;
			case 'H':
				$hcaptchaapisite = isset( $options['hcaptchaapisite'] ) ? $options['hcaptchaapisite'] : '';
				$cap = "
					<script src='https://hcaptcha.com/1/api.js' async defer></script>
					<input type='hidden' name='h-captcha' value='h-captcha'>
					<div class='h-captcha' data-sitekey='" . esc_attr( $hcaptchaapisite ) . "'></div>
				";
				break;
			case 'A':
			case 'Y':
				$n1 = wp_rand( 1, 9 );
				$n2 = wp_rand( 1, 9 );
				$seed = 5;
				$spam_date = isset( $stats['spam_date'] ) ? $stats['spam_date'] : '';
				if ( !empty( $spam_date ) ) {
					$seed = strtotime( $spam_date );
				}
				$math = $n1 + $n2 - $seed;
				$cap = '<br>' . esc_html__( 'Enter the SUM of these two numbers: ', 'dam-spam' ) .
					'<strong>' . esc_html( $n1 ) . ' + ' . esc_html( $n2 ) . '</strong><br>
					<input name="sum" value="" type="text">
					<input type="hidden" name="nums" value="' . esc_attr( $math ) . '"><br>';
				break;
			case 'F':
			default:
				$captop = '';
				$capbot = '';
				$cap = '';
				break;
		}
		if ( empty( $msg ) ) {
			$msg = html_entity_decode( $reject_message );
			$msg = str_replace( '[ip]', $ip, $msg );
			$msg = str_replace( '[reason]', isset( $post['reason'] ) ? $post['reason'] : '', $msg );
		}
		$answer = "
			$msg
			$formtop
			$not
			$captop
			$cap
			$capbot
			$formbot
		";
		$allowed_html = array(
			'form' => array(
				'action' => array(),
				'method' => array(),
				'style' => array(),
			),
			'input' => array(
				'type' => array(),
				'name' => array(),
				'value' => array(),
				'class' => array(),
				'style' => array(),
			),
			'textarea' => array(
				'name' => array(),
				'rows' => array(),
				'cols' => array(),
				'placeholder' => array(),
				'class' => array(),
				'style' => array(),
			),
			'div' => array(
				'class' => array(),
				'data-sitekey' => array(),
			),
			'script' => array(
				'src' => array(),
				'async' => array(),
				'defer' => array(),
			),
			'h1' => array(),
			'p' => array(),
			'span' => array(
				'style' => array(),
			),
			'strong' => array(),
			'br' => array(),
			'hr' => array(),
			'noscript' => array(),
			'iframe' => array(
				'src' => array(),
				'height' => array(),
				'width' => array(),
				'frameborder' => array(),
			),
		);
		wp_die( wp_kses( $answer, $allowed_html ), 'Dam Spam', array( 'response' => 200 ) );
		exit();
	}

	public function ds_send_email( $options = array() ) {
		if ( !array_key_exists( 'notify', $options ) ) {
			return false;
		}
		$notify = $options['notify'];
		$allow_list_request_email = isset( $options['allow_list_request_email'] ) ? $options['allow_list_request_email'] : '';
		if ( $notify === 'N' ) {
			return false;
		}
		if ( array_key_exists( 'ke', $_POST ) && !empty( $_POST['ke'] ) ) {
			$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
			$ke = sanitize_email( wp_unslash( $_POST['ke'] ) );
			if ( !is_email( $ke ) || empty( $ke ) ) {
				return false;
			}
			$km = isset( $_POST['km'] ) ? sanitize_text_field( wp_unslash( $_POST['km'] ) ) : '';
			if ( strlen( $km ) > 200 ) {
				$km = substr( $km, 0, 197 ) . '...';
			}
			$kr = isset( $_POST['kr'] ) ? sanitize_text_field( wp_unslash( $_POST['kr'] ) ) : '';
			$to = get_option( 'admin_email' );
			if ( !empty( $allow_list_request_email ) && is_email( $allow_list_request_email ) ) {
				$to = sanitize_email( $allow_list_request_email );
			}
			// translators: %s is the website name
			$subject = sprintf( esc_html__( 'Allow List Request from %s', 'dam-spam' ), get_bloginfo( 'name' ) );
			$ip = ds_get_ip();
			$web = esc_html__( 'Approve or Block Request: ', 'dam-spam' ) . admin_url( 'admin.php?page=ds-allowed' );
			// translators: 1: time, 2: IP address, 3: email, 4: reason, 5: message, 6: approval URL
			$message = wp_specialchars_decode( sprintf( esc_html__( '
				Someone was blocked from registering or commenting and has requested access.

				You are being notified because this option is enabled.

				Information from the request:

				Time: %1$s
				IP: %2$s
				Email: %3$s
				Reason: %4$s
				Message: %5$s

				%6$s

				Do not grant them access unless you\'re sure it\'s a legitimate user.

				— Dam Spam
			', 'dam-spam' ),
			$now, $ip, $ke, $kr, $km, $web ), ENT_QUOTES );
			$message = str_replace( "\t", '', $message );
			$headers = 'From: ' . sanitize_email( get_option( 'admin_email' ) ) . "\r\n";
			wp_mail( $to, $subject, $message, $headers );
			return true;
		}
		return false;
	}

	public function ds_add_allow( $ip, $options = array(), $stats = array(), $post = array(), $post1 = array() ) {
		$sname = $this->getSname();
		$now = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
		$ke = '';
		if ( array_key_exists( 'ke', $_POST ) ) {
			$ke = sanitize_email( wp_unslash( $_POST['ke'] ) );
		}
		if ( empty( $ke ) ) {
			return false;
		}
		if ( !is_email( $ke ) ) {
			return false;
		}
		$km = '';
		if ( isset( $_POST['km'] ) ) {
			$km = sanitize_text_field( wp_unslash( $_POST['km'] ) );
			if ( strlen( $km ) > 80 ) {
				$km = substr( $km, 0, 77 ) . '...';
			}
		}
		$kr = isset( $_POST['kr'] ) ? sanitize_text_field( wp_unslash( $_POST['kr'] ) ) : '';
		$ka = isset( $_POST['ka'] ) ? sanitize_user( wp_unslash( $_POST['ka'] ) ) : '';
		$req = array( $ip, $ke, $ka, $kr, $km, $sname );
		$allow_list_requests = isset( $stats['allow_list_requests'] ) ? $stats['allow_list_requests'] : array();
		if ( empty( $allow_list_requests ) || !is_array( $allow_list_requests ) ) {
			$allow_list_requests = array();
		}
		$allow_list_requests[ $now ] = $req;
		$stats['allow_list_requests'] = $allow_list_requests;
		ds_set_stats( $stats );
		return true;
	}
}

?>