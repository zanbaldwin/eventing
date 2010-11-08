<?php

/**
 * Eventing Framework URI Library
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage uri
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

namespace Eventing\Library;

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class uri extends library
{

  private $uri_string = '',
  $segments = array(),
  $suffix = '';

  /**
   * Eventing URI Library Construct Function
   *
   * @return void
   */
  protected function __construct()
  {
    $this->_get();
    if(!$this->check($this->uri_string))
    {
      show_error('The URL you specified contains invalid characters. Page does not exist.');
    }
    list($this->uri_string, $this->suffix) = $this->_split($this->uri_string);
    $this->segments = xplode('/', $this->uri_string);
    // Define the important bits, so the Router Library can access them.
    defined('REQUEST') || define('REQUEST', $this->uri_string);
    defined('SUFFIX') || define('SUFFIX', $this->suffix);
  }

  /**
   * Get URI
   *
   * Get the raw URI string from the server, using preset PHP global variables.
   * Filter out all unwanted information that comes with the URI string from the server.
   *
   * @access private
   * @return void
   */
  private function _get()
  {
    // Get the URI String from the following methods: PATH_INFO, ORIG_PATH_INFO and REQUEST_URI.
    // If none provide a URI, just continue with an empty string.
    $uri_string = '';

    foreach(array('PATH_INFO', 'ORIG_PATH_INFO', 'REQUEST_URI') as $method)
    {
      $uri_string = isset($_SERVER[$method]) ? $_SERVER[$method] : @getenv($method);
      if(trim(filter_path($uri_string), '/') != '' && trim(filter_path($uri_string), '/') != SELF)
      {
        break;
      }
    }
    // Remove the query string from the URI. It can't help up determine controllers and methods!
    $uri_string = ($pos = strpos($uri_string, '?')) !== false ? substr($uri_string, 0, $pos) : $uri_string;

    // If the URI string contains either the root folder the application is located in, or the application file,
    // remove them. They have nothing to do with the application now.
    foreach(array(URL, SELF) as $method)
    {
      if($uri_string == $method)
      {
        $uri_string = '';
        break;
      }
      if(strlen($uri_string) > strlen($method) && substr($uri_string, 0, strlen($method)) == $method)
      {
        $uri_string = substr($uri_string, strlen($method));
      }
    }
    $uri_string = trim(filter_path($uri_string), '/');
    $this->uri_string = $uri_string;
    return $uri_string;
  }

  /**
   * Check URI String
   *
   * Check that the URI string only contains permitted characters. Segments + Suffix + Query.
   *
   * @param string $uri_string
   * @return boolean
   */
  public function check($uri_string)
  {
    $regex = '|^[a-zA-Z0-9\/_-]*(\.[a-zA-Z0-9]+)?(\?.*)?$|';
    if(preg_match($regex, $uri_string))
    {
      if(strstr($uri_string, '/.') === false)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * Split URI
   *
   * Split URI into parts: Segments and Suffix.
   *
   * @access private
   * @return array
   */
  private function _split($uri_string)
  {
    $parts = xplode('.', $this->uri_string);
    switch(count($parts))
    {
      case 0:
        return array('', '');
        break;
      case 1:
        return array(rtrim($uri_string, '/'), '');
        break;
      case 2:
        return array(rtrim($parts[0], '/'), '.' . $parts[1]);
        break;
      default:
        show_error('Invalid URI passed check. Oh noes!');
        break;
    }
  }

  /**
   * Convert URI to Segments
   *
   * @access private
   * @param string $uri_string
   * @return array
   */
  private function _segment_array($uri_string)
  {
    $segments = xplode('/', $uri_string);
    array_unshift($segments, null);
    unset($segments[0]);
    return $segments;
  }

  /**
   * Segment
   *
   * Return a specific segment from the URI.
   *
   * @param int $n
   * @param mixed $return
   * @return string|mixed
   */
  public function segment($n, $return = false)
  {
    if(isset($this->segments[$n]))
    {
      return $this->segments[$n];
    }
    return $return;
  }

  /**
   * Segments
   *
   * Return an array containing all segments of the URI.
   *
   * @return array
   */
  public function segments()
  {
    return $this->segments;
  }

  /**
   * Total Segments
   *
   * Return the total number of segments in the URI.
   *
   * @return int
   */
  public function total_segments()
  {
    return count($this->segments);
  }

  /**
   * URI String
   *
   * Return the processed string of the URI.
   *
   * @return string
   */
  public function uri_string()
  {
    return $this->uri_string;
  }

  public function suffix()
  {
    return $this->suffix;
  }

  /**
   * Array to Query String
   *
   * @param array $query
   * @return string
   */
  public function create_query($query)
  {
    return http_build_query($query);
  }

  /**
   * Query String to Array
   *
   * @param string $query
   * @return array
   */
  public function split_query($query)
  {
    parse_str($query, $return);
    return $return;
  }

  public function get_saves()
  {
    $save = c('save_gets');
    if(!is_array($save) || count($save) == 0)
    {
      return array();
    }
    $saved = array();
    $E =& get_instance();
    foreach($save as $get_key)
    {
      $get_value = $E->input->get($get_key);
      if(!is_string($get_value))
      {
        continue;
      }
      $saved[$get_key] = $get_value;
    }
    return $saved;
  }

}
