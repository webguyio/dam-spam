<?php

if ( !defined( 'ABSPATH' ) ) {
	status_header( 404 );
	exit;
}

// last updated from https://support.authorize.net/knowledgebase/Knowledgearticle/?code=000001158 on 2/29/24
class dam_spam_check_authorize_net extends dam_spam_module {
	public $searchname = 'Authorize.net';
	public $searchlist = array(
		'198.241.206.38',
		'198.241.206.88',
		'198.241.206.93',
		'198.241.206.95',
		'198.241.206.96',
		'198.241.207.38',
		'198.241.207.97',
		'198.241.207.102',
		'198.241.207.104',
		'198.241.207.105',
		'198.241.206.22',
		'198.241.206.25',
		'198.241.206.38',
		'198.241.207.38',
		'198.241.207.84',
		'198.241.207.86'
	);
}

?>