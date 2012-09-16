<?php

/**
 * Input Library
 *
 * Normalise all input; GET, POST, STDIN, etc.
 *
 * @category	Eventing
 * @package		Libraries
 * @subpackage	Input
 * @see			/index.php
 */

	namespace Eventing\Library;

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	class input extends library {

		private $globals;

		protected function __construct() {
			// Grab the global variables, add them to this library, then get rid of the originals.
			$this->collect();
			// Get rid of some preset cookies, as these can sometimes cause a problem.
			$this->preset_cookies();
		}

		/**
		 * Collect
		 */
		private function collect() {
			$globals = array(
				'get'    => $_GET,
				'post'   => $_POST,
				'cookie' => $_COOKIE,
				'env'    => $_ENV,
			);
			if(isset($_SESSION) && is_array($_SESSION)) {
				$globals['session'] = $_SESSION;
			}
			foreach($globals as $name => &$global) {
				$this->globals[$name] = array();
				foreach($global as $var => $val) {
					$this->globals[$name][$var] = $val;
				}
			}
		}

		private function preset_cookies() {
			// Get rid of specially treated cookies that might be set by a server or application.
			foreach(array('$Version', '$Path', '$Domain') as $preset) {
				unset($this->globals['cookie'][$preset]);
			}
		}

		public function __call($name, $args) {
			if(!isset($args[0]) || isset($this->globals[$args[0]])) {
				return false;
			}
			$args[1] = isset($args[1]) ? $args[1] : false;
			return isset($this->globals[$name][$args[0]])
				? $this->globals[$name][$args[0]]
				: $args[1];
		}

	}