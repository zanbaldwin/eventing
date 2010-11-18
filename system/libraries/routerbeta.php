<?php

/**
 * Router Library
 *
 * Takes the URI string (module, segments and suffix). Checks to see if it
 * should re-route the request to a different one. Then finds the appropriate
 * controller and method, and determines which folder the controller class is
 * in.
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

  namespace Eventing\Library;

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class routerbeta extends library {

    protected static $_instance = false;
    public    $valid = false,
              $uri_string = false,
              $ruri_string = false;
    protected $segments = array(),
              $rsegments = array(),
              $module = false,
              $rmodule = false,
              $segment_string = false,
              $rsegment_string = false,
              $suffix = false,
              $rsuffix = false,
              $p = false,
              $c = false,
              $m = false;

    /**
     * Constructor Method
     *
     * This method will get called everytime a new route is initiated.
     * The first time, however, will be the route of the main application, when
     * called from the init.php file.
     *
     * @access protected
     * @param object|string|false $data
     * @return void
     */
    protected function __construct($data = false) {
      // Store the original instance of this class, it will be the application
      // default.
      if(!self::$_instance) {
        self::$_instance =& $this;
      }
      // If the data is not an object, the user must have passed a string to be
      // parsed by the uri() function.
      if(!is_object($data)) {
        // If the data is not a string or an object, it means this constructor
        // method was called via the getInstance() Singleton library method.
        // Unless something has gone horribly wrong, this is the call from the
        // initialisation script asking for the route of the main application.
        if(!is_string($data)) {
          // Grab the URI string for the application.
          $data = $this->get_uri();
          if(preg_match('#\\s#', $data)) {
            // If the application URI string contains whitespace, then do not
            // continue. We cannot return a value from the constructor function,
            // but we're specifying false here just to emphasise that it is a
            // failure for quick development reference.
            return false;
          }
        }
        // Now we know we have a string, parse it with the uri() function.
        $data = uri($data);
      }

      // If after all this the data is not in object form, then we obviously got
      // an invalid URI string.
      $this->valid = is_object($data);
      if(!$this->valid) {
        return false;
      }
      // Create a temporary URI string variable for now, we don't want to
      // overwrite the default value for the class property just yet.
      $uri_string = '';
      // Check that module, segments and suffix exist in the data, and set them
      // to class properties and the computed URI string.
      if(isset($data->module) && $data->module) {
        $this->module = $data->module;
        $uri_string .= $this->module . '@';
      }
      if(isset($data->segments) && $data->segments) {
        $this->segment_string = $data->segments;
        $uri_string .= $this->segment_string;
        // The URL suffix will only get set if segments are present. You can't
        // have a file extension if you are specifying the root directory.
        // Additionally, it would be unwise to allow *nix hidden files.
        if(isset($data->suffix) && $data->suffix) {
          $this->suffix = $data->suffix;
          $uri_string .= $this->suffix;
        }
      }
      // If the URI string is not empty, set the URI string to what was
      // extracted from the passed data, instead of the default (false).
      if($uri_string) {
        $this->uri_string = $uri_string;
      }
      // Now we have data for the URI half of the Library, move onto the other
      // half of the library, and reroute any requests if necessary.
      $this->reroute();
      // We have done everything we need to do in terms of data collection and
      // parsing, publicise all our just publicise the data in an accessible way
      // for our user to use. This will allow the data to be accessed using the
      // public methods (which are used by the determine() method).
      $this->publicise();
      // Now we need to determine the path, controller and method from the given
      // URI string now.
      // However, if the route is not valid, due to the re-route not being
      // correct, then don't bother.
      $this->valid && $this->determine();
    }

    /**
     * New Route
     *
     *
     *
     * @access public
     * @param string $uri_string
     * @return object|false
     */
    public function route($uri_string) {
      $data = uri($uri_string);
      if(!is_object($data)) {
        return false;
      }
      return new $this($data);
    }

    /**
     * Get URI
     *
     * Get the raw URI string from the server, using preset PHP Global
     * variables. Filter out all unwanted information that comes with the URI
     * string from the server.
     *
     * @access protected
     * @return string
     */
    protected function get_uri() {
      $uri_string = '';
      // Get the URI string from the following methods: PATH_INFO,
      // ORIG_PATH_INFO and REQUEST_URI. If none of those provide a URI, just
      // continue with an empty string.
      $server_methods = array('PATH_INFO', 'ORIGIN_PATH_INFO', 'REQUEST_URI');
      foreach($server_methods as $method) {
        $uri_string = isset($_SERVER[$method])
                    ? $_SERVER[$method]
                    : @getenv($method);
        $uri_string = trim(filter_path($uri_string), '/');
        if($uri_string != '' && $uri_string != SELF) {
          break;
        }
      }
      // Remove the query string from the URI. It can't help us determine
      // modules, controllers or methods!
      $uri_string = ($pos = strpos($uri_string, '?')) !== false
                  ? substr($uri_string, 0, $pos)
                  : $uri_string;
      // If the URI string contains either the root folder the application is
      // located in, or the application file, remove them. They have nothing to
      // do with the flow of the application now.
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
      return trim(filter_path($uri_string), '/');
    }

    /**
     * Re-Route Request
     */
    protected function reroute() {}

    /**
     * Publicise Data
     *
     * Make all the data we collected in the constructor method into usable,
     * accessible data for the user.
     *
     * @access protected
     * @return void
     */
    protected function publicise() {
      $this->segments = xplode('/', $this->segment_string);
      $this->rsegments = xplode('/', $this->rsegment_string);
      array_unshift($this->segments, null);
      array_unshift($this->rsegments, null);
      unset($this->segments[0], $this->rsegments[0]);
    }

    /**
     * Determine Route
     *
     * Determine the path to the controller, the controller itself, and the
     * method from the re-routed URI.
     *
     * @access protected
     * @return void
     */
    public function determine() {}

    /**
     * Get Route Path
     *
     * Return the path to the controller file. If the request is not valid, or
     * no controller could be found, return false.
     *
     * @access public
     * @return string|false
     */
    public function path() {
      return $this->valid ? $this->p : false;
    }

    /**
     * Get Route Controller
     *
     * Return the name of the controller class, including its namespace. If the
     * request is not valid, or no controller could be found, return false.
     *
     * @access public
     * @return string|false
     */
    public function controller() {
      return $this->valid ? $this->c : false;
    }

    /**
     * Get Route Method
     *
     * Return the name of the controller method. If the request is not valid, or
     * no controller could be found, return false. The Router library does not
     * check if the method exists, as we do not want to include the controller
     * file from withing this class.
     *
     * @access public
     * @return string|false
     */
    public function method() {
      return $this->valid ? $this->m : false;
    }

    /**
     * Get Segment
     *
     * Return a specified segment from the URI string.
     *
     * @access public
     * @param integer $n
     * @param mixed $return
     * @return string|false|mixed
     */
    public function segment($n, $return  = false) {
      if(!$this->valid || !is_numeric($n)) {
        return false;
      }
      $n = (int) $n;
      return isset($this->segments[$n]) ? $this->segments[$n] : $return;
    }

    /**
     * Get Re-Routed Segment
     *
     * Return a specified segment from the re-routed URI string.
     *
     * @access public
     * @param integer $n
     * @param mixed $return
     * @return string|false|mixed
     */
    public function rsegment($n, $return  = false) {
      if(!$this->valid || !is_numeric($n)) {
        return false;
      }
      $n = (int) $n;
      return isset($this->rsegments[$n]) ? $this->segments[$n] : $return;
    }

    /**
     * Get Segments
     *
     * Return all the segments of the URI string as an array.
     *
     * @access public
     * @return array|false
     */
    public function segments() {
      return $this->valid ? $this->segments : false;
    }

    /**
     * Get Re-Routed Segments
     *
     * Return all the segments of the re-routed URI string as an array.
     *
     * @access public
     * @return array|false
     */
    public function rsegments() {
      return $this->valid ? $this->rsegments : false;
    }

  }














class E_routerbeta {
  
  protected $uri_string = false,
            $valid = true;
            
  
  /**
   * Constructor Method
   *
   * @return void
   */
  protected function __construct($uri_string = false, $module = false) {
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
    list($uri_string, $suffix) = $this->routes($this->uri_string, $this->suffix);
    list($this->ruri_string, $this->rsuffix) = $this->determine($uri_string, $suffix);
  }

  public function route($uri_string, $module) {
    
  }

  /**
   * Get URI
   * Fetch the URI string from server variables.
   * 
   * @access protected
   * @return string
   */
  protected function get_uri() {
    // Get the URI String from the following methods: PATH_INFO, ORIG_PATH_INFO
    // and REQUEST_URI.
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
    if(!is_string($uri_string)) {
      return false;
    }
    $regex = '#^(([a-zA-Z0-9/_-]+)((?<!/)\.[a-zA-Z0-9]+)?)?$#';
    // Does the string contain the correct characters, and in the right places?
    return preg_match($regex, $uri_string);
  }

  /**
   * Split URI
   * Split the URI into segments and suffix.
   */
  protected function split($uri_string) {
    if(!is_string($uri_string)) {
      return false;
    }
    $parts = xplode('.', $uri_string);
    return array(
      isset($parts[0]) ? trim($parts[0], '/') : '',
      isset($parts[1]) ? '.' . $parts[1] : '',
    );
  }
  
  /**
   * Routes
   * Match the URI with routes inside the routes config file.
   * 
   * @access protected
   * @param string $uri_string
   * @param string $suffix
   * @return string
   */
  protected function routes($uri_string, $suffix) {}
  protected function determine($uri_string, $suffix) {}
  
}
