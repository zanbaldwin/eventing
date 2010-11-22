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
  class controller extends core {

    /**
     * Controller Construct Function
     *
     * @access protected
     * @return void
     */
    protected function __construct() {
      parent::__construct();
      // Load the libraries that need separate instances for separate modules.
      $libs = array('load', 'template');
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
