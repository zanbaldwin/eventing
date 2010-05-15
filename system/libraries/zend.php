<?php

 /**
  * Eventing Framework URI Library
  *
  * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
  * http://eventing.zafr.net/
  * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
  * Enable libraries from the Zend Framework to be included with the Eventing Framework.
  * Because of the way Zend uses namespaces in its classes (not to be confused with real namespaces,
  * supported in PHP since 5.3), you don't need to worry about any libraries conflicting with any from
  * Eventing.
  *
  * @category   Eventing
  * @package    Libraries
  * @subpackage zend
  * @author     Alexander Baldwin
  * @copyright  (c) 2009 Alexander Baldwin
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://eventing.zafr.net/source/system/libraries/zend.php
  * @since      v0.1
 */

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    class E_zend
    {

        protected $path = false, $autoload = true, $loaded = false;

        /**
         * Constructor Function
         *
         * Due to the way the load_class() function works, you will not be able to get a boolean
         * value as to whether the Zend Libraries have successfully loaded.
         *
         * @return void
         */
        public function __construct()
        {
            // Grab the path to the Zend Framework libraries from the config file.
            $this->set_path(c('zend_container_path'));
            // Decide whether the user wants the libraries to load automatically.
            $this->autoload(c('zend_autoload'));
            // Attempt to load the Zend Libraries straight away. After all, they wouldn't of loaded
            // this library if they didn't want them! If it fails, they can always specify the
            // requirements after this constructor function.
            $this->load();
        }

        /**
         * Set Zend Path
         *
         * @param string $path
         * @return boolean
         */
        public function set_path($path)
        {
            // The path to the Zend Libraries must be an existing directory, which must also have the
            // "Zend" folder as a direct descendant.
            $path = realpath($path);
            $valid = is_string($path) && is_dir($path . '/Zend');
            if($valid)
            {
                $this->path = $path;
            }
            return $valid;
        }
        
        /**
         * Autoload Setting
         *
         * @param boolean $autoload
         * @return void
         */
        public function autoload($autoload)
        {
            $this->autoload = bool($autoload);
        }

        /**
         * Load Zend Libraries
         *
         * @return boolean
         */
        public function load()
        {
            if($this->loaded)
            {
                return true;
            }
            if(!is_string($this->path))
            {
                return false;
            }
            $set = set_include_path(
                implode(PATH_SEPARATOR, array(
                    $this->path,
                    get_include_path()
                ))
            );
            if(!$set || !file_exists($this->path . '/Zend/Loader/Autoloader' . EXT))
            {
                return false;
            }
            if($this->autoload)
            {
                require_once 'Zend/Loader/Autoloader' . EXT;
                if(!class_exists('Zend_Loader_Autoloader'))
                {
                    return false;
                }
                Zend_Loader_Autoloader::getInstance();
            }
            $this->loaded = true;
            return true;
        }

    }
