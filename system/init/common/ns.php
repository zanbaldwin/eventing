<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	NS
 * @see			/index.php
 */

	if(!function_exists('ns')) {
		/**
		 * Namespace String
		 *
		 * @access public
		 * @params strings
		 * @return string
		 */
		function ns() {
			return '\\' . implode('\\', func_get_args()) . '\\';
		}
	}