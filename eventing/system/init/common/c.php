<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ErrorHandler
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('c')) {
		/**
		 * Config Item
		 *
		 * Fetches an item from the config files.
		 *
		 * @param string $item
		 * @param string $file
		 * @return mixed|false
		 */
		function c($item, $file = 'config') {
			static $config_items = array();
			$file = is_string($file) && $file != '' ? $file : 'config';
			if(!isset($config_items[$file])) {
				$config_items[$file] = get_config($file);
			}
			if(!is_array($config_items[$file])) {
				return false;
			}
			return isset($config_items[$file][$item])
				? $config_items[$file][$item]
				: null;
		}
	}