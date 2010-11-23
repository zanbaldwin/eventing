<?php

/**
 * Eventing Framework Controller Library
 *
 *
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage controller
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.opensource.org/licenses/mit-license.php MIT/X11 License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
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

    // We don't want to create an extra instance when extending classes, so
    // store an instance of this class in the following variable.
    protected static $_instance;

    /**
     * Controller Construct Function
     *
     * @access protected
     * @return void
     */
    protected function __construct() {
      // Save the instance, in case another one is created by another module
      // extending the Core library.
      self::$_instance =& $this;
      // Load the libraries that need separate instances for separate modules.
      $libs = array('router', 'input', 'output', 'load');
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
