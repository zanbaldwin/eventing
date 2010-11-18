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

  class router extends library {

    protected static $_instance = false;

    public    $valid            = false,
              $uri_string       = false,
              $ruri_string      = false;

    protected $segments         = array(),
              $rsegments        = array(),
              $default_suffix   = false,
              $module           = false,
              $rmodule          = false,
              $segment_string   = false,
              $rsegment_string  = false,
              $suffix           = false,
              $rsuffix          = false,
              $p                = false,
              $c                = false,
              $m                = false;

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
      // Instead of worrying all the time about the confusion of URI suffixes,
      // like we have in the past, define the default suffix here and be done
      // with it. It must either be a file extension containing only
      // alphanumeric characters, preceded with a full stop, or a directory
      // separator.
      $ds = c('default_suffix');
      $this->default_suffix = is_string($ds)
                           && preg_match('/^\.[a-zA-Z0-9]+$/', $ds)
                            ? $ds
                            : '/';
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
        // We don't want a suffix of false, so set the default suffix as a
        // directory separator.
        $this->suffix = '/';
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
     * Create a new instance of the Router library, specifying a request passed
     * by the URI string in the first parameter.
     *
     * @access public
     * @param string $uri_string
     * @return object|false
     */
    public function route($uri_string) {
      $data = uri($uri_string);
      // If the URI string wasn't formatted correctly, or it wasn't a string at
      // all, the uri() function will not return a data object. If this is the
      // case, don't bother creating a new instance of the Router library.
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
      // Explode the segment strings, both the original and the re-routed
      // versions.
      $this->segments = xplode('/', $this->segment_string);
      $this->rsegments = xplode('/', $this->rsegment_string);
      // Unshift the arrays, so that the array indexes match human numbering.
      array_unshift($this->segments, null);
      array_unshift($this->rsegments, null);
      // Removed the zero-indexed values from the arrays.
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
