<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ThemePath
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('theme_path')) {
		/**
		 * Theme Path
		 *
		 * Specify a theme and will return the absolute path to the theme directory.
		 * Will return false if the theme directory does not exist.
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