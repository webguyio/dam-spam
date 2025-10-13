<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

class check_post extends ds_module {
	public function process( $ip, &$stats = array(), &$options = array(), &$post = array() ) {
		$reason = ds_load( 'check_good_cache', ds_get_ip(), $stats, $options, $post );
		if ( $reason !== false ) {
			return;
		}
		$addons = array();
		$addons = apply_filters( 'ds_addons_block', $addons );
		if ( !empty( $addons ) && is_array( $addons ) ) {
			foreach ( $addons as $add ) {
				if ( !empty( $add ) && is_array( $add ) ) {
					$reason = ds_load( $add, ds_get_ip(), $stats, $options,
						$post );
					if ( $reason !== false ) {
						ds_log_bad( ds_get_ip(), $reason, $add[1], $add );
						exit();
					}
				}
			}
		}
		$noipactions = array(
			'check_agent',
			'check_bbcode',
			'check_blocked_email',
			'check_blocked_user_id',
			'check_disposable',
			'check_exploits',
			'check_long',
			'check_short',
			'check_referer',
			'check_session',
			'check_spam_words',
			'check_periods',
			'check_url_short',
			'check_tld',
			'check_accept',
			'check_admin',
			'check_urls'
		);
		$actions = array(
			'check_amazon',
			'check_bad_cache',
			'check_blocked_ip',
			'check_disposable',
			'check_hosting',
			'check_invalid_ip',
			'check_ubiquity',
			'check_multi',
			'check_google_safe',
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
			'check_ye',
			'check_sfs',
			'check_honeypot',
			'check_botscout',
			'check_dnsbl'
		);
		$check = '';
		foreach ( $noipactions as $check ) {
			if ( $options[$check] == 'Y' ) {
				$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					break;
				}
			}
		}
		if ( $reason === false ) {
			$actionvalid = array( 'check_valid_ip' );
			foreach ( $actionvalid as $check ) {
				$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
				if ( $reason !== false ) {
					break;
				}
			}
			if ( $reason !== false ) {
				return false;
			}
		}
		if ( $reason === false ) {
			foreach ( $actions as $check ) {
				if ( $options[$check] == 'Y' ) {
					$reason = ds_load( $check, ds_get_ip(), $stats, $options, $post );
					if ( $reason !== false ) {
						break;
					}
				}
			}
		}
		if ( array_key_exists( 'email', $post ) && $post['email'] == 'email@example.com' ) {
			$post['reason'] = esc_html__( 'Testing Email (will always be blocked)', 'dam-spam' );
			ds_load( 'challenge', ds_get_ip(), $stats, $options, $post );
			return;
		}
		if ( $reason === false ) {
			return false;
		}
		ds_log_bad( ds_get_ip(), $reason, $check );
		exit;
	}
}

?>