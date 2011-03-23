<?php

/**
 * Prowl Library
 *
 * Created instances of the Prowl_Application class.
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage router
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 - 2011 Alexander Baldwin
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
   * Prowl Class
   */
  class prowl1 extends library {

    const INVALID       = 400,
          UNAUTHORISED  = 401,
          CONNECTERROR  = 405,
          APILIMIT      = 406,
          SERVERERROR   = 500;

    protected $api_keys = array(),
              $application = 'Eventing',
              $priority = 0;

    protected function __construct() {}

    /**
     * Set API Keys
     */
    public function api_keys($keys) {
      if(is_string($keys)) {
        $keys = (array) $keys;
      }
      if(!is_array($keys)) {
        return false;
      }
      foreach($keys as $key) {
        $key = strtolower($key);
        if(!preg_match('/[a-f0-9]{40}/', $key) || in_array($key, $this->api_keys)) {
          continue;
        }
        $this->api_keys[] = $key;
      }
      return true;
    }

    /**
     * Priority
     */
    public function priority($priority) {
      if(!is_int($priority) || $priority < -2 || $priority > 2) {
        return false;
      }
      $this->priority = $priority;
      return true;
    }

    /**
     * Set Application
     */
    public function application($application) {
      if(is_string($application) && $application) {
        $this->application = substr($application, 0, 256);
        return true;
      }
      return false;
    }


    public function send($event, $description = '') {
      if(!is_string($event)) {
        return false;
      }
      $data = array(
        'apikey' => implode(',', $this->api_keys),
        'application' => $this->application,
        'priority' => $this->priority,
        'event' => $event,
      );
      if(is_string($description)) {
        $data['description'] = $description;
      }
      $url = 'https://prowl.weks.net/publicapi/add';
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data, '', '&'));
      $return = curl_exec($ch);
      $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      switch($code) {
        case self::INVALID:
          return 'The application was passed incorrect parameters.';
          break;
        case self::UNAUTHORISED:
          return 'The application was passed invalid API keys.';
          break;
        case self::CONNECTERROR:
          return 'Could not connect to the Prowl server.';
          break;
        case self::APILIMIT:
          return 'The application has reached it\' API limit for Prowl requests.';
          break;
        case self::SERVERERROR:
          return 'An error occured on the Prowl API server.';
          break;
        default:
          return true;
          break;
      }
    }

  }

  class prowl extends singleton {

    const SUCCESS       = 200,
          INVALID       = 400,
          UNAUTHORISED  = 401,
          CONNECTERROR  = 405,
          APILIMIT      = 406,
          SERVERERROR   = 500;

    // The provider API key will be common across all instances of the Prowl
    // library.
    protected static $provider = false;
    protected $apps = array(),
              $public_api = 'https://prowl.weks.net/publicapi/',
              $app_class = 'Prowl_Application',
              $limit_left = 1000,
              $limit_until = 0,
              $valid_methods = array(
                'add', 'verify',
              ),
              $valid_params = array(
                'apikey', 'providerkey', 'priority', 'application', 'event',
                'description',
              );

    protected function __construct() {}

    public function create($app, $name = false) {
      // No point trying to create something that already exists.
      if(isset($this->apps[$app])) {
        return true;
      }
      // The application identifier must be a valid PHP label, the usual style
      // within Eventing.
      if(!is_string($app)
         || !preg_match('~^' . VALIDLABEL . '$~', $app)
      ) {
        return false;
      }
      // The name of the Application must be a non-empty string, if not, fall
      // back to using the Prowl app identifier.
      $name = is_string($name) && $name ? $name : $app;
      $this->apps[$app] = new Prowl_Application($name);
      return true;
    }

    /**
     * Application
     *
     * Return an instance of a Prowl Application.
     *
     * @access public
     * @param string $app
     * @return object|false
     */
    public function app($app) {
      if(isset($this->apps[$app])) {
        return $this->apps[$app];
      }
      return false;
    }

    /**
     * Provider Key
     *
     * Set the provider API key for whitelisting (for alternative API limits).
     *
     * @access public
     * @param string $api_key
     * @return boolean
     */
    public function provider($api_key) {
      if(!$this->valid($api_key)) {
        return false;
      }
      self::$provider = $api_key;
      return true;
    }

    /**
     * Valid API Key
     *
     * Checks that the API key passed is a valid 40-byte hexadecimal string.
     *
     * @access protected
     * @param string $api_key
     * @return boolean
     */
    protected function valid($api_key) {
      return is_string($api_key) && preg_match('/^[a-f0-9]{40}$/', $api_key);
    }

    /**
     * API Call
     *
     * Perform an API call on the Prowl Server with the given data.
     *
     * @access protected
     * @param string $method
     * @param array $data
     * @return integer
     */
    protected function api($method, $data = array()) {
      // Do we have a valid API method?
      if(!is_string($method) || !in_array($method, $this->valid_methods)) {
        return self::INVALID;
      }
      // Build our parameter array.
      $params = array();
      foreach((array) $data as $param => $value) {
        if(!is_string($param) || !in_array($param, $this->valid_params)) {
          continue;
        }
        $params[$param] = $value;
      }
      // If a provider API key has been set, make sure that is in the parameter
      // array.
      if(self::$provider) {
        $params['providerkey'] = self::$provider;
      }
      // GO FETCH!

      $url = $this->public_api . $method;
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($params, '', '&'));
      $response = curl_exec($ch);
      $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      return $code == 200 ? $this->result($response) : $code;
    }

    /**
     * Result
     *
     * Take the content sent back from the HTTP request, set the API limits for
     * subsequent calls and return an integer depending on the outcome of the
     * server request.
     *
     * @access protected
     * @param string $result_body
     * @return integer
     */
    protected function result($result_body) {
    }

    /**
     * Verify API Key
     *
     * Verify a users API key. This does not verify provider API keys.
     *
     * @access public
     * @param string $api_key
     * @return boolean
     */
    public function verify($api_key) {
      if(!$this->valid($api_key)) {
        return false;
      }
      $result = $this->api('verify', array('apikey' => $api_key));
      return $result == self::SUCCESS;
    }

  }

  class Prowl_Application extends prowl {

    protected $name = 'Eventing Prowl',
              $keys = array(),
              $priority = 0;

    public function __construct($name) {
      if(!is_string($name) || !$name) {
        return false;
      }
      $this->name = $name;
      return true;
    }

    /**
     * API Keys
     *
     * Add multiple user API keys.
     *
     * @access public
     * @param string|array $keys
     * @return boolean
     */
    public function keys($keys) {
      if(is_string($keys)) {
        $keys = (array) $keys;
      }
      if(!is_array($keys)) {
        return false;
      }
      foreach($keys as $key) {
        if(!$this->valid($key) || in_array($key, $this->keys)) {
          continue;
        }
        $this->keys[] = $key;
      }
      return false;
    }

    /**
     * Notify
     *
     * Send a notification to the Prowl server, to be pushed to iPhones
     * specified by the user API keys.
     *
     * @access public
     * @param string $event
     * @param string $description
     * @return boolean
     */
    public function notify($event, $description = false) {
      if(!is_string($event) || !$event) {
        return false;
      }
      $data = array(
        'apikey' => implode(',', $this->keys),
        'priority' => $this->priority,
        'application' => $this->name,
        'event' => $event,
      );
      if(is_string($description)) {
        $data['description'] = $description;
      }
      if(self::$provider) {
        $data['providerkey'] = self::$provider;
      }
      $url = $this->public_api . 'add';
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 8);
      curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($data, '', '&'));
      $response = curl_exec($ch);
      $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      return $response == 200;
    }

  }
