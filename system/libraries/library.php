<?php

namespace Eventing\Libraries;

/**
 * Library base class
 */
abstract class E_library
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
  final public static function &getInstance()
  {
    static $objects = array();
    $class = get_called_class();
    if(isset($objects[$class]) && is_object($objects[$class]))
    {
      return $objects[$class];
    }
    if(!class_exists($class))
    {
      return false;
    }
    $objects[$class] = new $class;
    return $objects[$class];
  }

}
