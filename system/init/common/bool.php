<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Bool
 * @see			/index.php
 */

	if(!function_exists('bool')) {
		/**
		 * Check Strict Boolean
		 *
		 * Returns boolean equivelant of value passed to function.
		 *
		 * @param mixed $var
		 * @return boolean
		 */
		function bool($var) {
			return $var ? true : false;
		}
	}