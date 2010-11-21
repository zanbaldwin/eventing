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
   * Prowl Class
   */
  class prowl extends library {

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
