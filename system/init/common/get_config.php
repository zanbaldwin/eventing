<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	GetConfig
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('get_config')) {
		/**
		 * Get Config
		 *
		 * Fetch a config array from a file.
		 *
		 * @param string $file
		 * @return array|false
		 */
		function get_config($config_file) {
			static $main_config = array();
			if(isset($main_config[$config_file])) {
				return $main_config[$config_file];
			}
			$file = APP . 'config/' . $config_file;
			if(CONFIG == 'ini') {
				function_exists('parse_ini_file') || show_error(
					'Cannot retrieve config settings. INI file parser does not exist.',
					500
				);
				$file .= '.ini';
				if(!file_exists($file)) {
					return false;
				}
				$config = parse_ini_file($file, false);
			}
			else {
				$file .= EXT;
				if(!file_exists($file)) {
					return false;
				}
				require_once $file;
			}
			if(!isset($config) || !is_array($config)) {
				return false;
			}
			$main_config[$config_file] =& $config;
			return $main_config[$config_file];
		}
	}