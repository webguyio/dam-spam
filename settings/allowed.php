<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

if ( !current_user_can( 'manage_options' ) ) {
	die( esc_html__( 'Access Blocked', 'dam-spam' ) );
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Settings template file with local scope variables

dam_spam_fix_post_vars();
$stats   = dam_spam_get_stats();
extract( $stats );
$now	 = gmdate( 'Y/m/d H:i:s', time() + ( get_option( 'gmt_offset' ) * 3600 ) );
$options = dam_spam_get_options();
extract( $options );
$stats   = dam_spam_get_stats();
extract( $stats );
$icons = dam_spam_get_icon_urls();
extract( $icons );
$check_cloudflare = 'Y';
$nonce   = '';
$ajaxurl = admin_url( 'admin-ajax.php' );

if ( array_key_exists( 'dam_spam_control', $_POST ) ) {
	$nonce = isset( $_POST['dam_spam_control'] ) ? sanitize_text_field( wp_unslash( $_POST['dam_spam_control'] ) ) : '';
}

if ( !empty( $nonce ) && wp_verify_nonce( $nonce, 'dam_spam_update' ) ) {
	if ( array_key_exists( 'dam_spam_clear_allow_list_request', $_POST ) ) {
		$allow_list_requests = array();
		$stats['allow_list_requests'] = $allow_list_requests;
		dam_spam_set_stats( $stats );
	}
	if ( array_key_exists( 'allow_list', $_POST ) and !array_key_exists( 'dam_spam_clear_allow_list_request', $_POST ) ) {
		$allow_list = isset( $_POST['allow_list'] ) ? sanitize_textarea_field( wp_unslash( $_POST['allow_list'] ) ) : '';
		$allow_list = explode( "\n", $allow_list );
		$tblock_list = array();
		foreach ( $allow_list as $bl ) {
			$bl = trim( $bl );
			if ( !empty( $bl ) ) {
				$tblock_list[] = $bl;
			}
		}
		$options['allow_list'] = $tblock_list;
		$allow_list	= $tblock_list;
	}
	if ( !array_key_exists( 'dam_spam_clear_allow_list_request', $_POST ) ) {
		$optionlist = array(
			'check_google',
			'check_aws',
			'check_allowed_user_id',
			'check_paypal',
			'check_stripe',
			'check_authorize_net',
			'check_braintree',
			'check_recurly',
			'check_square',
			'check_misc_allow_list'
		);
		foreach ( $optionlist as $check ) {
			$v = 'N';
			if ( array_key_exists( $check, $_POST ) ) {
				$v = isset( $_POST[$check] ) ? sanitize_text_field( wp_unslash( $_POST[$check] ) ) : '';
				if ( $v != 'Y' ) {
					$v = 'N';
				}
			}
			$options[$check] = $v;
		}
		dam_spam_set_options( $options );
	}
	extract( $options );
	$msg = '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options Updated', 'dam-spam' ) . '</p></div>';
}

$nonce = wp_create_nonce( 'dam_spam_update' );

?>

<div id="dam-spam" class="wrap">
	<h1 id="dam-spam-head"><?php esc_html_e( 'Allowed — Dam Spam', 'dam-spam' ); ?></h1>
	<?php if ( !empty( $msg ) ) {
		echo wp_kses_post( $msg );
	} ?>
	<br>
	<br>
	<div class="mainsection"><?php esc_html_e( 'Requests', 'dam-spam' ); ?></div>
	<?php
	if ( count( $allow_list_requests ) == 0 ) {
		esc_html_e( 'There are currently no pending requests.', 'dam-spam' );
	} else { ?>
	<form method="post" action="">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="dam_spam_clear_allow_list_request" value="true">
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Clear the Requests', 'dam-spam' ); ?>" type="submit"></p>
	</form>
	<table id="dam-spam-table" name="dam-spam-table" cellspacing="2">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Time', 'dam-spam' ); ?></th>
				<th><?php esc_html_e( 'IP', 'dam-spam' ); ?></th>
				<th><?php esc_html_e( 'Email', 'dam-spam' ); ?></th>
				<th><?php esc_html_e( 'Reason', 'dam-spam' ); ?></th>
				<th><?php esc_html_e( 'Message', 'dam-spam' ); ?></th>
			</tr>
		</thead>
		<tbody id="allow_list_request">
			<?php
			$show = '';
			$cont = 'allow_list_requests';
			$options = dam_spam_get_options();
			$stats   = dam_spam_get_stats();
			$show	 = dam_spam_load( 'get_allow_requests', 'x', $stats, $options );
			$allowed_html = array(
				'table' => array(
					'id' => array(),
					'name' => array(),
					'cellspacing' => array(),
				),
				'thead' => array(),
				'tr' => array(
					'class' => array(),
				),
				'th' => array(),
				'tbody' => array(
					'id' => array(),
				),
				'td' => array(
					'class' => array(),
				),
				'a' => array(
					'href' => array(),
					'onclick' => array(),
					'title' => array(),
					'alt' => array(),
				),
				'img' => array(
					'class' => array(),
					'src' => array(),
					'alt' => array(),
				),
			);
			echo wp_kses( $show, $allowed_html );
			?>
		</tbody>
	</table>
	<?php } ?>
	<form method="post" action="">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="dam_spam_control" value="<?php echo esc_attr( $nonce ); ?>">
		<div class="mainsection"><?php esc_html_e( 'Allow List', 'dam-spam' ); ?></div>
		<p><?php esc_html_e( 'One email or IP per line. You can use wild cards here for emails.', 'dam-spam' ); ?></p>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_allowed_user_id">
				<input class="dam_spam_toggle" type="checkbox" id="check_allowed_user_id" name="check_allowed_user_id" value="Y" <?php if ( $check_allowed_user_id == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Allow Usernames', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<textarea name="allow_list" cols="40" rows="8" class="ipbox"><?php
			for ( $k = 0; $k < count( $allow_list ); $k ++ ) {
				echo esc_html( $allow_list[$k] ) . "\r\n";
			}
		?></textarea>
		<br>
		<div class="mainsection"><?php esc_html_e( 'Allow Options', 'dam-spam' ); ?></div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-subhead" for="check_google">
				<input class="dam_spam_toggle" type="checkbox" id="check_google" name="check_google" value="Y" <?php if ( $check_google == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Google (keep enabled under most circumstances)', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_misc_allow_list">
				<input class="dam_spam_toggle" type="checkbox" id="check_misc_allow_list" name="check_misc_allow_list" value="Y" <?php if ( $check_misc_allow_list == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Other Allow Lists', 'dam-spam' ); ?></small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_paypal">
				<input class="dam_spam_toggle" type="checkbox" id="check_paypal" name="check_paypal" value="Y" <?php if ( $check_paypal == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> PayPal</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_stripe">
				<input class="dam_spam_toggle" type="checkbox" id="check_stripe" name="check_stripe" value="Y" <?php if ( $check_stripe == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Stripe</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_authorize_net">
				<input class="dam_spam_toggle" type="checkbox" id="check_authorize_net" name="check_authorize_net" value="Y" <?php if ( $check_authorize_net == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Authorize.Net</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_braintree">
				<input class="dam_spam_toggle" type="checkbox" id="check_braintree" name="check_braintree" value="Y" <?php if ( $check_braintree == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Braintree</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_recurly">
				<input class="dam_spam_toggle" type="checkbox" id="check_recurly" name="check_recurly" value="Y" <?php if ( $check_recurly == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Recurly</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
	  		<label class="dam-spam-subhead" for="check_square">
				<input class="dam_spam_toggle" type="checkbox" id="check_square" name="check_square" value="Y" <?php if ( $check_square == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
		  		<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Square</small>
			</label>
		</div>
		<br>
		<div class="checkbox switcher">
			<label class="dam-spam-subhead" for="check_aws">
				<input class="dam_spam_toggle" type="checkbox" id="check_aws" name="check_aws" value="Y" <?php if ( $check_aws == 'Y' ) { echo 'checked="checked"'; } ?>><span><small></small></span>
				<small><?php esc_html_e( 'Allow', 'dam-spam' ); ?> Amazon Cloud</small>
			</label>
		</div>
		<br>
		<p class="submit"><input class="button-primary" value="<?php esc_html_e( 'Save Changes', 'dam-spam' ); ?>" type="submit"></p>
	</form>
</div>