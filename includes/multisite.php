<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

$ds_semaphore = 0;
function ds_global_setup() {
	global $blog_id;
	if ( $blog_id == 1 ) {
		return;
	}
	$ops = array( 'ds_stats', 'ds_options' );
	foreach ( $ops as $value ) {
		add_filter( 'pre_update_option_' . $value, 'ds_global_set', 10, 2 );
		add_filter( 'add_option_' . $value, 'ds_global_add', 1, 2 );
		add_filter( 'delete_option_' . $value, 'ds_global_delete' );
		add_filter( 'pre_option_' . $value, 'ds_global_get', 1 );
	}
}

function ds_global_set( $newvalue, $oldvalue ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return $newvalue;
	}
	global $ds_semaphore;
	if ( $ds_semaphore ) {
		return $newvalue;
	}
	$ds_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'pre_update_option_' ) );
	switch_to_blog( 1 );
	$answer = update_option( $f, $newvalue );
	restore_current_blog();
	$ds_semaphore --;
	return $oldvalue;
}

function ds_global_add( $option, $value ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $ds_semaphore;
	if ( $ds_semaphore ) {
		return false;
	}
	$ds_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'add_option_' ) );
	switch_to_blog( 1 );
	$answer = update_option( $f, $value );
	restore_current_blog();
	$ds_semaphore --;
	return true;
}

function ds_global_get( $option ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $ds_semaphore;
	if ( $ds_semaphore ) {
		return false;
	}
	$ds_semaphore ++;
	$filt = current_filter();
	$f	= substr( $filt, strlen( 'pre_option_' ) );
	switch_to_blog( 1 );
	$answer = get_option( $f );
	restore_current_blog();
	$ds_semaphore --;
	return $answer;
}

function ds_global_Delete( $ops ) {
	if ( !function_exists( 'switch_to_blog' ) ) {
		return false;
	}
	global $ds_semaphore;
	if ( $ds_semaphore ) {
		return false;
	}
	$ds_semaphore ++;
	$filt = current_filter();
	$f	  = substr( $filt, strlen( 'delete_option_' ) );
	switch_to_blog( 1 );
	$answer = delete_option( $ops );
	restore_current_blog();
	$ds_semaphore --;
	return $answer;
}

function ds_global_unsetup() {
	$ops = array( 'ds_stats', 'ds_options' );
	foreach ( $ops as $value ) {
		remove_filter( 'pre_update_option_' . $value, 'ds_pf_global_set', 10, 2 );
		remove_filter( 'add_option_' . $value, 'ds_pf_global_add', 1, 2 );
		remove_filter( 'delete_option_' . $value, 'ds_pf_global_delete' );
		remove_filter( 'pre_option_' . $value, 'ds_pf_global_get', 1 );
	}
	return;
}

?>