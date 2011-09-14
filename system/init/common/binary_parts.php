<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	BinaryParts
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('binary_parts')) {
		/**
		 * Binary Parts
		 *
		 * Returns an array of integers. Each integer is a power of 2 that adds up
		 * to the number passed to the function.
		 *
		 * @access public
		 * @param integer $int
		 * @return array|false
		 */
		function binary_parts($int) {
			if(!is_int($int)
				|| (!is_numeric($int)
				|| !preg_match('|^[0-9]+$|', $int))
				|| $int < 0
			) {
				return false;
			}
			$arr = str_split(decbin((int) $int));
			$arr = array_reverse($arr);
			$count = count($arr);
			$parts = array();
			for($i = 0; $i < $count; $i++) {
				if($arr[$i] == '1') {
					$parts[] = pow(2, $i);
				}
			}
			return $parts;
		}
	}