<?php

/**
 * Eventing Framework Module Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to
 * rapidly build applications. This is where we start all our settings,
 * libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage module
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
   * Module Library
   * This library does NOT extend the library class, as multiple instances are
   * required.
   */
  class module extends core {

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
