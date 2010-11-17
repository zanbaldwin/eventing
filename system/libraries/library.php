<?php

/**
 * Eventing Framework Core Library
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
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  namespace Eventing\Library;

  /**
   * Library base class
   */
  abstract class library
  {

    /**
     * Constructor Function
     *
     * Every library class must have a constructor function that is defined using the protected
     * access availability.
     *
     * @access protected
     */
    abstract protected function __construct();

    final public function __clone() {
      trigger_error('Cannot clone Singleton library.', E_USER_ERROR);
    }

    /**
     * Get Instance
     *
     * @access public
     * @return object|false
     */
    final public static function &getInstance() {
      static $objects = array();
      $class = get_called_class();
      if(isset($objects[$class]) && is_object($objects[$class])) {
        return $objects[$class];
      }
      if(!class_exists($class)) {
        return false;
      }
      $objects[$class] = new $class;
      return $objects[$class];
    }

  }
