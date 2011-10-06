<?php

/**
 * Controller Library
 *
 * This is the base object for the Eventing super-object.
 *
 * @category	Eventing
 * @package		Libraries
 * @subpackage	Controller
 * @see			/index.php
 */

	namespace Eventing\Library;

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	/**
	 * Eventing Controller Class
	 */
	class controller extends library {
		// We don't want to create an extra instance when extending classes, so store the first instance of this class in the following variable.
		protected static $_instance;
		// Create containers for
		private static $_modules = array(),
			$_models = array();
		
		/**
		 * Controller constructor method
		 *
		 * @access protected
		 * @return void
		 */
		protected function __construct() {
			// Save the instance, in case another one is created by another module extending the Core library.
			if(!is_object(self::$_instance)) {
				self::$_instance =& $this;
			}
			// Define the Loader class name here, so we can reduce the amount of hard-coded referencing.
			$loader = 'load';
			// Initialise the frameworks core loading class.
			$loadobj = load_class($loader);
			if(!is_object($loadobj)) {
				show_error('', 'Framework application dependacny "Load" class does not exist.');
			}
			// Set the Framework core loader to a property of the super object. If the
			// loader property has already been set by something else that is
			// unusable, overwrite it.
			if(!isset($this->$loader) || !is_a($this->$loader, ns(NS, NSLIBRARY) . $loader)) {
				$this->$loader = $loadobj;
			}
			// Run one final check on the loader class itself before attempting to use it.
			if(!method_exists($this->$loader, 'library')) {
				show_error('Framework application dependancy "Load" class missing required "library()" method.');
			}
			// Define the core minimal required libraries that the application needs to run.
			$libs = array('router', 'input', 'output');
			// Loop through all the core required libraries and load them using the
			// Framework Loader we just set.
			foreach($libs as $library) {
				$this->$loader->library($library);
			}
			SKELETON || $this->_autoload();
		}

		/**
		 * Autoloader
		 *
		 * Take the user setting from the autoload config file and load them into the super-object.
		 *
		 * @access private
		 * @return void
		 */
		private function _autoload() {
			$autoload = get_config('autoload');
			if(!is_array($autoload)) {
				return false;
			}
			foreach($autoload as $type => $resource) {
				if(method_exists($this->load, $type)) {
					$resource = (array) $resource;
					foreach($resource as $parameter) {
						if(is_string($parameter)) {
							$this->load->$type($parameter);
						}
					}
				}
			}
		}

		/**
		 * Get or Set Model
		 *
		 * Get or set a model with the given name. Saves to a private static array to be accessed via controllers,
		 * views, etc.
		 *
		 * @access public
		 * @param string $name
		 * @param object $model
		 * @param boolean $overwrite
		 * @return object|boolean
		 */
		public function model($name, $model = false, $overwrite = false) {
			// If we do not have a valid model name, there is no point continuing, regardless of whether a model is
			// being fetched or saved.
			if(!is_string($name) || !preg_match('/^' . VALIDLABEL . '$/', $name)) {
				return false;
			}
			// Set a model
			if(is_a($model, ns(NS, NSLIBRARY) . 'model')) {
				if(is_a($model, ns(NS, NSLIBRARY) . 'model') && (!isset(self::$_models[$name]) || $overwrite)) {
					self::$_models[$name] = $model;
				}
			}
			// Get a model.
			else {
				if(isset(self::$_models[$name])) {
					return self:: $_models[$name];
				}
			}
			// Something failed, return bool(false).
			return false;
		}

		/**
		 * Get or Set Model
		 *
		 * Get or set a model with the given name. Saves to a private static array to be accessed via controllers,
		 * models, etc.
		 *
		 * @access public
		 * @param string $name
		 * @param object $model
		 * @param boolean $overwrite
		 * @return object|boolean
		 */
		public function module($name, $module = false, $overwrite = false) {
			// If we do not have a valid module name, there is no point continuing, regardless of whether a module is
			// being fetched or saved.
			if(!is_string($name) || !preg_match('/^' . VALIDLABEL . '$/', $name)) {
				return false;
			}
			// Set a module
			if(is_a($module, ns(NS, NSLIBRARY) . 'module')) {
				if(is_a($module, ns(NS, NSLIBRARY) . 'module') && (!isset(self::$_modules[$name]) || $overwrite)) {
					self::$_modules[$name] = $module;
				}
			}
			// Get a module.
			else {
				if(isset(self::$_modules[$name])) {
					return self:: $_modules[$name];
				}
			}
			// Something failed, return bool(false).
			return false;
		}

	}