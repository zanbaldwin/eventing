<?php

/**
 * Eventing Framework Singleton Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage core
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.opensource.org/licenses/mit-license.php MIT/X11 License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  namespace Eventing\Library;

  /**
   * Library Base Class
   *
   * This is the Eventing Framework implementation of the Singleton pattern,
   * instances of classes can be returned with the getInstance() method, whilst
   * new instances are forbidden.
   */
  abstract class library
  {

    /**
     * Constructor Function
     *
     * Every library class must have a constructor function that is defined
     * using the protected access availability.
     *
     * @abstract
     * @access protected
     */
    abstract protected function __construct();

    /**
     * Disallow Cloning
     *
     * No point using the singleton pattern if the object can be cloned into a
     * new instance.
     *
     * @access public
     * @return fatalerror
     */
    final public function __clone() {
      trigger_error('Cannot clone framework library.', E_USER_ERROR);
    }

    /**
     * Get Instance
     *
     * @final
     * @static
     * @access public
     * @return object|false
     */
    final public static function &getInstance($super = false) {
      static $objects = array();
      $class = get_called_class();
      if(isset($objects[$class]) && is_object($objects[$class])) {
        return $objects[$class];
      }
      if(!class_exists($class)) {
        return false;
      }
      $objects[$class] = isset($class::$_instance)
                      && is_object($class::$_instance)
                       ? $class::$_instance
                       : new $class;
      $super = bool($super);
      if($super) {
        // Find some way of merging objects, maintaining both properties and
        // methods? That would be nice. KTHXBAI!
      }
      return $objects[$class];
    }

  }
