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

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     *
     */
    class E_core
    {

        public $models = array();

        private static $instance;

        /**
         * Core Construct Function
         *
         * Don't use __construct method so controller and model classes can use "parent::core();".
         *
         * @return void
         */
        public function core()
        {
            self::$instance =& $this;
        }

        /**
         * Core Get-Instance Function
         *
         * blah blah blah...
         *
         * @static
         * @return object
         */
        public static function &get_instance()
        {
            return self::$instance;
        }

        /**
         * Use Model
         *
         * Models must be loaded through the Loader Library before they can be used here.
         * eg. "$this->load->model($model_name);"
         * They can they be called here.
         * eg. "$this->model($model_name)->userMethod();"
         *
         * @param string $model_name
         * @return object|void
         */
        public function model($model_name)
        {
            // So we can use models that have been loaded through "$this->load->model($model_name);"
            // Use like: "$this->model($model_name)->get_user_data();"
            if(isset($this->models[$model_name]))
            {
                return $this->models[$model_name];
            }
        }
  }
