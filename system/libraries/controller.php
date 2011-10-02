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

		// We don't want to create an extra instance when extending classes, so store an instance of this class in the
		// following variable.
		protected static $_instance;
		private $loader = 'load';

		private static $models = array(),
			$modules = array();

		/**
		 * Controller Construct Function
		 *
		 * @access protected
		 * @return void
		 */
		protected function __construct() {
			// Save the instance, in case another one is created by another module extending the Core library.
			if(!is_object(self::$_instance)) {
				self::$_instance =& $this;
			}
			// Define the core minimal required libraries that the application needs to run.
			$libs = array('router', 'input', 'output');
			// Initialise the Framework's core loading class.
			$loadobj = load_class($this->loader);
			if(!is_object($loadobj)) {
				show_error(
					'Framework application dependancy "Loader" class does not exist.'
				);
			}
			// Set the Framework core loader to a property of the super object. If the
			// loader property has already been set by something else that is
			// unusable, overwrite it.
			if(!isset($this->{$this->loader}) || !method_exists($this->{$this->loader}, 'library')) {
				$this->{$this->loader} = $loadobj;
			}
			// Run one final check on the loader class itself, before attempting to use
			// it.
			if(!method_exists($this->{$this->loader}, 'library')) {
				show_error(
					'Framework application dependancy "Loader" class missing library '
					. 'method.'
				);
			}
			// Loop through all the core required libraries and load them using the
			// Framework Loader we just set.
			foreach($libs as $name => $library) {
				$this->{$this->loader}->library($library, $name);
			}
		}

		/**
		 * Set Model
		 * Set a model into the super models array, ready to be accessed via
		 * controllers, views, etc.
		 *
		 * @static
		 * @access public
		 * @param string $name
		 * @param object $model
		 * @param boolean $overwrite
		 * @return boolean
		 */
		public static function setModel($name, $model, $overwrite = false) {
			if(!is_string($name)
				|| !preg_match('#^' . VALIDLABEL . '$#', $name)
				|| !is_a($model, ns(NS, NSLIBRARY) . 'model')
			) {
				return false;
			}
			if(isset(self::$models[$name]) && !$overwrite) {
				return false;
			}
			self::$models[$name] = $model;
			return true;
		}

		/**
		 * Model
		 * Return an instance of a model.
		 *
		 * @access protected
		 * @param string $model
		 * @return false|object
		 */
		protected function model($model) {
			return isset(self::$models[$model])
				? self::$models[$model]
				: false;
		}

		/**
		 * Set Module
		 * Set a model into the super modules array, ready to be accessed via
		 * controllers, models, etc.
		 *
		 * @static
		 * @access public
		 * @param string $name
		 * @param object $module
		 * @param boolean $overwrite
		 * @return boolean
		 */
		public static function setModule($name, $module, $overwrite = false) {
			if(!is_string($name)
				|| !preg_match('#^' . VALIDLABEL . '$#', $name)
				|| !is_a($module, ns(NS, NSLIBRARY) . 'module')
			) {
				return false;
			}
			if(isset(self::$modules[$name]) && !$overwrite) {
				return false;
			}
			self::$modules[$name] = $module;
			return true;
		}

		/**
		 * Module
		 * Return an instance of a module
		 *
		 * @access protected
		 * @param string $module
		 * @return false|object
		 */
		protected function module($module) {
			return isset(self::$modules[$module])
				? self::$modules[$module]
				: false;
		}

	}