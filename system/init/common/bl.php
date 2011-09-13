<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Bl
 * @see			/index.php
 */

	if(!function_exists('bl')) {
		/**
		 * Check Boolean
		 *
		 * Returns strict boolean equivelant of value passed to function.
		 *
		 * @param mixed $var
		 * @return boolean
		 */
		function bl($var) {
			return $var === true ? true : false;
		}
	}