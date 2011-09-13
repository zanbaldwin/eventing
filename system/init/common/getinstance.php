<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	GetInstance
 * @see			/index.php
 */

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