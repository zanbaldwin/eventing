<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	Xplode
 * @see			/index.php
 */

	if(!function_exists('xplode')) {
		/**
		 * Xplode
		 *
		 * Same as the PHP explode() function, except if the second paramter is
		 * an empty string it will return an empty
		 * array, instead of an array containing an empty string.
		 *
		 * @param string $delimiter
		 * @param string $string
		 * @return array|false
		 */
		function xplode($delimiter, $string) {
			$string = trim($string, $delimiter);
			if($string === '') {
				return array();
			}
			$string = preg_replace(
				'#' . preg_quote($delimiter . $delimiter, '#') . '+#',
				$delimiter,
				$string
			);
			$array = explode($delimiter, $string);
			return $array;
		}
	}