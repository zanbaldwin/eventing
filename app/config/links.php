<?php

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	$config = array(

		'eventingsource'      => 'http://github.com/mynameiszanders/eventing',
		'eventingtwitter'     => 'http://twitter.com/eventingphp',
		'ci'                  => 'http://codeigniter.com/',
		'profile'             => 'http://github.com/mynameiszanders',
		'licensemit'          => 'http://www.opensource.org/licenses/mit-license.php',
		'php53'               => 'http://php.net/releases/5_3_0.php',

	);