<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Vardump
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('vardump')) {
		/**
		 * Vardump
		 *
		 * Same as the PHP var_dump() function, except it returns the value, instead
		 * of dumping it to the output.
		 *
		 * @param mixed $var
		 * @return string
		 */
		function vardump($var) {
			ob_start();
			var_dump($var);
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}