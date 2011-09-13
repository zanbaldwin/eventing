<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	FilterPath
 * @see			/index.php
 */

	if(!function_exists('filter_path')) {
		/**
		 * Filter Path
		 *
		 * Converts all backslashes to forward slashes, for Unix style consistency,
		 * and removes unnecessary slashes.
		 *
		 * @param string $path
		 * @return string|null
		 */
		function filter_path($path) {
			return preg_replace('|/+|', '/', str_replace('\\', '/', $path));
		}
	}