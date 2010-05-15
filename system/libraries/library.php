<?php

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
        
        /**
         * Get Instance
         *
         * @access public
         * @return object|false
         */
        public static function &getInstance()
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
