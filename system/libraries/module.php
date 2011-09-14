<?php

/**
 * Module Library
 *
 * @category	Eventing
 * @package		Libraries
 * @subpackage	Module
 * @see			/index.php
 */

	namespace Eventing\Library;

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	/**
	 * Module Library
	 * This library does NOT extend the library class, as multiple instances are
	 * required.
	 */
	class module extends library {

		protected function __construct() {
			parent::__construct();
			// Load the libraries that need separate instances for separate modules.
			$libs = array('input');
			foreach($libs as $lib) {
				if(!isset($this->$lib)) {
					$obj = load_class($lib);
					if(is_object($obj)) {
						$this->$lib = load_class($lib);
					}
				}
			}
		}

	}
