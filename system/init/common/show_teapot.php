<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowTeapot
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('show_teapot')) {
		/**
		 * Show Teapot Error
		 *
		 * Show the HTTP Teapot error according to RFC2324.
		 *
		 * @access public
		 * @return exit
		 */
		function show_teapot() {
			show_doc(418) || show_error(
				'The '
				. a(
					'http://en.wikipedia.org/wiki/Hyper_Text_Coffee_Pot_Control_Protocol',
					'HTCPCP'
				)
				. ' server you requested a page from is a teapot, the entity may be short '
				. 'or stout. Please '
				. a(
					'coffee://' . $_SERVER['SERVER_NAME'] . '/brew/',
					'brew yourself a coffee'
				) . '!',
				'418 I\'m a teapot'
			);
		}
	}