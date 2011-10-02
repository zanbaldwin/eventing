<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	GetInstance
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('getInstance')) {
		/**
		 * Get Instance
		 *
		 * Global version of the getInstance method in libraries, returns an
		 * instance of the super (core) object.
		 *
		 * @access public
		 * @return object
		 */
		function &getInstance() {
			$core = ns(NS, NSLIBRARY) . 'controller';
			return $core::getInstance();
		}
	}