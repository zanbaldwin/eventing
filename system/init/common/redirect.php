<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Redirect
 * @see			/index.php
 */

	if(!function_exists('redirect')) {
		/**
		 * Redirect
		 *
		 * Redirects the client/browser to another page. The parameter accepts the
		 * same as the first parameter for the a() function.
		 *
		 * @param string $segments
		 * @return false|void
		 */
		function redirect($segments, $location = true) {
			$url = a($segments);
			if(!is_string($url) || headers_sent()) {
				return false;
			}
			$header = bool($location) ? 'Location: ' : 'Refresh: 0; url=';
			$header .= $url;
			header('HTTP/1.1 307 Temporary Redirect', true, 307);
			header($header);
			exit;
		}
	}