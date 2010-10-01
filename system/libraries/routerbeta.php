<?php

/**
 * Router Library
 *
 * Takes the URI string (segments and suffix). Checks to see if it should re-route the request to a different one.
 * Then finds the appropriate controller and method, and determines which folder the controller class is in.
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage router
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

/**
 * Router Class
 */
class E_routerbeta extends E_library {
  
	protected $uri_string = false,
	          $valid = true;
	          
	
  /**
   * Constructor Method
   *
   * @return void
   */
  protected function __construct($uri_string = false) {
  	// If a URI string was not passed (most likely called from init), grab the
  	// current URI request.
    if(!is_string($uri_string)) {
    	$uri_string = $this->get_uri();
    }
    // Check that we have a valid request.
    if(!$this->check($uri_string)) {
    	// Invalid characters? That's not good!
    	$this->valid = false;
    	// This class is always going to be called from load_class() so we can't
    	// return anything from the constructor function.
    	return;
    }
    list($this->uri_string, $this->suffix) = $this->split($uri_string);
    $this->segments = xplode('/', $this->uri_string);
    // Define for rest of application, in case in some weird event that they do
    // not have access to SuperObj -> Router.
    // The way that defining is set out in the framework means we don't need to
    // worry about calling this method several times.
    defined('REQUEST') || define('REQUEST', $this->uri_string);
    defined('SUFFIX') || define('SUFFIX', $this->suffix);
  }

  /**
   * Get URI
   * Fetch the URI string from server variables.
   * 
   * @access protected
   * @return string
   */
  protected function get_uri() {
  	// Get the URI String from the following methods: PATH_INFO, ORIG_PATH_INFO and REQUEST_URI.
    foreach(array('PATH_INFO', 'ORIG_PATH_INFO', 'REQUEST_URI') as $method) {
      $uri_string = isset($_SERVER[$method])
                  ? $_SERVER[$method]
                  : @getenv($method);
      if(trim(filter_path($uri_string), '/') != ''
         && trim(filter_path($uri_string), '/') != SELF
      ) {
        break;
      }
    }
    // Remove the query string from the URI. It can't help up determine
    // controllers and methods!
    $uri_string = ($pos = strpos($uri_string, '?')) !== false
                ? substr($uri_string, 0, $pos)
                : $uri_string;
    // If the URI string contains either the root folder the application is
    // located in, or the application file, remove them. They have nothing to do
    // with the application now.
    foreach(array(URL, SELF) as $method) {
      if($uri_string == $method) {
        $uri_string = '';
        break;
      }
      if(strlen($uri_string) > strlen($method)
         && substr($uri_string, 0, strlen($method)) == $method
      ) {
        $uri_string = substr($uri_string, strlen($method));
      }
    }
    $uri_string = trim(filter_path($uri_string), '/');
    return $uri_string;
  	
  }
  
  /**
   * Check URI
   * Check that the URI string passed is valid for use with this framework.
   * 
   * @access protected
   * @param string $uri_string
   * @return boolean
   */
  protected function check($uri_string) {
  	$regex = '|^[a-zA-Z0-9\/_-]*(\.[a-zA-Z0-9]+)?(\?.*)?$|';
  	$regex = '#^([a-zA-Z0-9\/_-]+(\.[a-zA-Z0-9]+)?$#';
    if(preg_match($regex, $uri_string))
    {
      if(strstr($uri_string, '/.') === false)
      {
        return true;
      }
    }
    return false;
  }
  protected function split() {}
  protected function routes() {}
  protected function determine() {}
  
}