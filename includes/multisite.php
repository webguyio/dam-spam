<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

$dam_spam_semaphore = 0;
function dam_spam_global_setup() {
	global $blog_id;
	if ( $blog_id == 1 ) {
		return;
	}
	$ops = array( 'dam_spam_stats', 'dam_spam_options' );
	foreach ( $ops as $value ) {
		add_filter( 'pre_update_option_' . $value, 'dam_spam_global_set', 10, 2 );
		add_filter( 'add_option_' . $value, 'dam_spam_global_add', 1, 2 );
		add_filter( 'delete_option_' . $value, 'dam_spam_global_delete' );
		add_filter( 'pre_option_' . $value, 'dam_spam_global_get', 1 );
	}
}

function dam_spam_global_set( $newvalue, $oldvalue ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return $newvalue;
	}
	global $dam_spam_semaphore;
	if ( $dam_spam_semaphore ) {
		return $newvalue;
	}
	$dam_spam_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'pre_update_option_' ) );
	switch_to_blog( 1 );
	$answer = update_option( $f, $newvalue );
	restore_current_blog();
	$dam_spam_semaphore --;
	return $oldvalue;
}

function dam_spam_global_add( $option, $value ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $dam_spam_semaphore;
	if ( $dam_spam_semaphore ) {
		return false;
	}
	$dam_spam_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'add_option_' ) );
	switch_to_blog( 1 );
	$answer = update_option( $f, $value );
	restore_current_blog();
	$dam_spam_semaphore --;
	return true;
}

function dam_spam_global_get( $option ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $dam_spam_semaphore;
	if ( $dam_spam_semaphore ) {
		return false;
	}
	$dam_spam_semaphore ++;
	$filt = current_filter();
	$f	= substr( $filt, strlen( 'pre_option_' ) );
	switch_to_blog( 1 );
	$answer = get_option( $f );
	restore_current_blog();
	$dam_spam_semaphore --;
	return $answer;
}

function dam_spam_global_Delete( $ops ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $dam_spam_semaphore;
	if ( $dam_spam_semaphore ) {
		return false;
	}
	$dam_spam_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'delete_option_' ) );
	switch_to_blog( 1 );
	$answer = delete_option( $ops );
	restore_current_blog();
	$dam_spam_semaphore --;
	return $answer;
}

function dam_spam_global_unsetup() {
	$ops = array( 'dam_spam_stats', 'dam_spam_options' );
	foreach ( $ops as $value ) {
		remove_filter( 'pre_update_option_' . $value, 'dam_spam_pf_global_set', 10, 2 );
		remove_filter( 'add_option_' . $value, 'dam_spam_pf_global_add', 1, 2 );
		remove_filter( 'delete_option_' . $value, 'dam_spam_pf_global_delete' );
		remove_filter( 'pre_option_' . $value, 'dam_spam_pf_global_get', 1 );
	}
	return;
}

?>