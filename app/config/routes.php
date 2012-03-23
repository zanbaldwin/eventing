<?php

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	$config = array(

		'admin'						=> 'cms_admin',
		'*'							=> 'cms',

		/* Example  routes.
		'module@segments.suffix'  => 'home/index',
		'user/*'                 => 'user/profile',
		'bands'                   => 'bands@list/bands',
		'feed/'                   => 'rss@main.rss',
		'item/#'                  => 'products/view_item_$1',
		*/

	);