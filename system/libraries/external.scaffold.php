<?php

 /**
  * Eventing Framework Scaffold Library
  *
  * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
  * http://eventing.zafr.net/
  * The Scaffold CSS Library for Eventing is a rewrite of CSScaffold by Anthony Short.
  * Much has been taken out, as they will be written as separate libraries (for example, Benchmark and Cache).
  *
  * @category   Eventing
  * @package    Libraries
  * @subpackage scaffold
  * @author     Alexander Baldwin; Anthony Short
  * @copyright  (c) 2009 Alexander Baldwin
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://eventing.zafr.net/source/system/libraries/scaffold.php
  * @since      v0.1
 */

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     * Scaffold Utility Library
     */
    class E_scaffold_utils{}

    /**
     * Scaffold CSS Library
     */
    class E_scaffold_css
    {

        private $file = null, $raw = null, $output= null;

        public function __construct()
        {
            // I'm not really a big fan of using construct methods much. It makes things too transparent; if you use
            // other methods to do what you could do automatically in this function, you know more of what is going on.
        }

        public function load_file($file)
        {
            if(!is_string($file))
            {
                return false;
            }
            $file = realpath($file);
            if(!file_exists($file))
            {
                return false;
            }
            $this->raw = file_get_contents($file);
            $this->file = $file;
            return true;
        }

        public function load_css($css)
        {
            if(!is_string($css))
            {
                return false;
            }
            $this->raw = $css;
            return true;
        }

    }

    /**
     * Scaffold Events Library
     */
    class E_scaffold_events{}

    /**
     * Scaffold Library
     */
    class E_scaffold
    {
        private $last_modified, $expires = 3600;

        public function __construct()
        {
            // Nothing to do yet.
        }

        public function load($file)
        {

        }

        public function display($name)
        {
            
        }

    }
