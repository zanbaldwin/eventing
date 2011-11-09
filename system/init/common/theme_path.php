<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ThemePath
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('theme_path')) {
		/**
		 * Theme Path
		 *
		 * Returns an absolute path to the specified theme, providing it exists, or the theme container directory if
		 * boolean(true) is passed. All other instances return false.
		 *
		 * @access public
		 * @param string $theme
		 * @return string|false
		 */
		function theme_path($theme = true) {
			if($theme === true) {
				$theme = '';
			}
			if(!is_string($theme)) {
				return false;
			}
			$path = realpath(APP . 'themes/' . $theme);
			return is_string($path) ? $path . '/' : false;
		}
	}