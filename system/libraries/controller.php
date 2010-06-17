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
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://eventing.zafr.net/source/system/libraries/controller.php
  * @since      v0.1
 */

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     * Eventing Controller Class
     */
    class E_controller extends E_core
    {

        /**
         * Controller Construct Function
         *
         * We won't use the PHP5 __construct() function, because each controller class must call "parent::controller();"
         * We could use "parent::__construct();" - but it makes the code a lot more readable!
         *
         * @return void
         */
        function controller()
        {
            parent::core();
            $this->_initialise();
        }

        /**
         * Controller Initialise Function
         *
         * Load the libraries into the super object.
         *
         * @access private
         * @return void
         */
        private function _initialise()
        {
            $load_classes = array('uri', 'router', 'load', 'input', 'output', 'template');
            foreach($load_classes as $class)
            {
                // We want to load the libraries to be stored in variables of the Core object, not the controller
                // ($this) object.
                $E =& get_instance();
                $E->$class =& load_class($class);
            }
            // Load the resources that the user wants for their application.
            $this->load->autoload();
        }

    }
