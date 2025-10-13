<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// last updated from https://www.paypal.com/us/cshelp/article/what-are-the-ip-addresses-for-live-paypal-servers-ts1056 on 2/29/24
class check_paypal extends ds_module {
	public $searchname = 'PayPal';
	public $searchlist = array(
		'64.4.240.0/21',
		'64.4.248.0/22',
		'66.211.168.0/22',
		'91.243.72.0/23',
		'173.0.80.0/20',
		'185.177.52.0/22',
		'192.160.215.0/24',
		'198.54.216.0/23'
	);
}

?>