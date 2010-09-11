<?php

/**
 * Eventing Framework Common Functions
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to
 * rapidly build applications.
 * Common functions used throughout the framework.
 *
 * @category   Eventing
 * @package    Core
 * @subpackage common
 * @copyright  2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

if (!defined('E_FRAMEWORK')) {
  headers_sent() || header('HTTP/1.1 404 Not Found',true,404);
  exit('Direct script access is disallowed.');
}

// Some older versions of PHP don't define the E_STRICT constant, so for the
// convinience of the next function:
defined('E_STRICT') || define('E_STRICT', 2048);

if (!function_exists('E_Error_Handler')) {
  /**
   * Eventing Error Handler
   */
  function E_Error_Handler($num, $msg, $file, $line) {
    $types = array(
    2 => 'Warning',
    // 8 => 'Notice',
    // 32 => 'Core Warning',
    // 128 => 'Compile Warning',
    512 => 'User Generated Warning',
    1024 => 'User Generated Notice',
    // 2048 => 'Strict',
    4096 => 'Recoverable Error',
    // 8192 => 'Depreciated',
    16384 => 'User Define Depreciated'
    );
    if (isset($types[$num])) {
      ?>
<div class="error"
  style="display: block; border: 2px solid #900; padding: 10px; margin: 10px; background-color: #FFF;">
<h2 style="margin: 0 0 0.4em; color: #600;">Error <?php echo $num . ' (' . $types[$num]; ?>)</h2>
<p style="margin: 0 0 0.4em; font-family: monospace; color: #000;"><strong>File:</strong>
      <?php echo $file; ?><br />
<strong>Line:</strong> <?php echo $line; ?></p>
<p style="margin: 0 0 0 1em; color: #000;"><?php echo $msg; ?></p>
</div>
      <?php
    }
  }
}

/**
 * Set PHP's error handler to the Eventing error handler.
 */
set_error_handler('E_Error_Handler');

if (!function_exists('load_class')) {
  /**
   * Load Class
   *
   * For loading classes/libraries. Objects in PHP are returned by reference
   * anyway, so we don't need to declare that part. This also saves on the
   * amount of strict (2048) warnings when returning a boolean (eg. the
   * library doesn't exist).
   *
   * @param string $lib
   * @param bool $return
   * @return boolean|object
   */
  function load_class($lib, $return = true) {
    static $objects = array();
    if (!is_string($lib)) {
      return false;
    }
    $lib = trim(filter_path(strtolower($lib)), '/');
    $class = 'E_' . end(explode('/', $lib));
    // Check that we haven't already loaded this class. That would be pretty
    // stupid.
    if (isset($objects[$lib]) && $objects[$lib] !== false) {
      return $objects[$lib];
    }
    // Default. If we can't load it the first time, there is no point trying
    // again!
    $objects[$lib] = false;
    // Let's go get it!
    $file = SYS . 'libraries/' . $lib . EXT;
    if (!file_exists($file)) {
      return false;
    }
    require_once $file;
    if (!class_exists($class)) {
      return false;
    }
    // Do we want to initiate the class, or just load it?
    if (bool($return)) {
      // Initiate.
      $objects[$lib] = new $class;
    }
    else {
      // Load, but do not initiate.
      $objects[$lib] = true;
    }
    return $objects[$lib];
  }
}

if(!function_exists('get_called_class'))
{
  /**
   * Get Called Class Object
   *
   * To use the native get_called_class(), PHP 5.3 or greater must be installed.
   * Use this implementation by Chris Webb.
   * http://www.septuro.com/2009/07/php-5-2-late-static-binding-get_called_class-and-self-new-self/
   */
  class _get_called_class_object
  {
    static $i = 0;
    static $fl = null;

    static function get_called_class()
    {
      $bt = debug_backtrace();
      if(self::$fl == $bt[2]['file'].$bt[2]['line'])
      {
        self::$i++;
      }
      else
      {
        self::$i = 0;
        self::$fl = $bt[2]['file'].$bt[2]['line'];
      }
      $lines = file($bt[2]['file']);
      preg_match_all('
                /([a-zA-Z0-9\_]+)::'.$bt[2]['function'].'/',
      $lines[$bt[2]['line']-1],
      $matches
      );
      return $matches[1][self::$i];
    }
  }
  /**
   * Get Called Class
   *
   * @return string
   */
  function get_called_class()
  {
    return _get_called_class_object::get_called_class();
  }
}

if(!function_exists('get_instance'))
{
  /**
   * Get Instance
   *
   * Get an instance of the super (core) object.
   *
   * @return object
   */
  function &get_instance()
  {
    return E_core::get_instance();
  }
}

if(!function_exists('bool'))
{
  /**
   * Check Boolean
   *
   * Returns boolean equivelant of value passed to function.
   *
   * @param mixed $var
   * @return boolean
   */
  function bool($var)
  {
    return $var === true ? true : false;
  }
}

if(!function_exists('bl'))
{
  /**
   * Make Boolean
   *
   * Takes a variable, and makes into a boolean by reference.
   *
   * @param mixed $var
   * @return boolean
   */
  function bl(&$var)
  {
    $var = bool($var);
    return $var;
  }
}

if(!function_exists('get_config'))
{
  /**
   * Get Config
   *
   * Fetch a config array from a file.
   *
   * @param string $file
   * @return array|false
   */
  function get_config($config_file)
  {
    static $main_config = array();
    if(isset($main_config[$config_file]))
    {
      return $main_config[$config_file];
    }
    $file = APP . 'config/' . $config_file;
    if(CONFIG == 'ini')
    {
      function_exists('parse_ini_file')
      || show_error('Cannot retrieve config settings. INI file parser does not exist.', 500);
      $file .= '.ini';
      if(!file_exists($file))
      {
        return false;
      }
      $config = parse_ini_file($file, false);
    }
    else
    {
      $file .= EXT;
      if(!file_exists($file))
      {
        return false;
      }
      require_once $file;
    }
    if(!is_array($config))
    {
      return false;
    }
    $main_config[$config_file] =& $config;
    return $main_config[$config_file];
  }
}

if(!function_exists('c'))
{
  /**
   * Config Item
   *
   * Fetches an item from the config files.
   *
   * @param string $item
   * @param string $file
   * @return mixed|false
   */
  function c($item, $file = 'config')
  {
    static $config_items = array();
    $file = is_string($file) && $file != '' ? $file : 'config';
    if(!isset($config_items[$file]))
    {
      $config_items[$file] = get_config($file);
    }
    if(!is_array($config_items[$file]))
    {
      return false;
    }
    return isset($config_items[$file][$item]) ? $config_items[$file][$item] : null;
  }
}

if(!function_exists('filter_path'))
{
  /**
   * Filter Path
   *
   * Converts all backslashes to forward slashes, for Unix style consistency, and removes unnecessary slashes.
   *
   * @param string $path
   * @return string|null
   */
  function filter_path($path)
  {
    return preg_replace('|/+|', '/', str_replace('\\', '/', $path));
  }
}

if(!function_exists('show_error'))
{
  /**
   * Show Error
   *
   * Explain the function...
   *
   * @return exit
   */
  function show_error($msg, $header = '500 Framework Application Error', $format = '') {
    if(!headers_sent()) {
    	if(preg_match('|^[0-9]{3} |', $header, $matches)) {
    		$code = (int) $matches[1];
    		if($code < 600 && $code >= 100) {
    			header($header, true, $code);
    		}
    	}
    }
    // Just just the message right now, don't have time to write anything. Those
    // shirts aren't going to iron themselves!
    exit($msg);
  }
}

if(!function_exists('show_404'))
{
  /**
   * Show 404
   *
   * Calls show_doc(404), trying to find a user error document. If this fails, default to the not-so-pretty
   * show_error().
   *
   * @return void
   */
  function show_404()
  {
    show_doc(404) || show_error('The page you requested does not exist.', '404 Not Found');
  }
}

if(!function_exists('show_deny'))
{
  /**
   * Show Deny
   *
   * Calls show_doc(403), trying to find a user error document. If this fails, default to the not-so-pretty
   * show_error().
   */
  function show_deny()
  {
    show_doc(403) || show_error('You do not have sufficient clearance to view this page.', '403 Forbidden');
  }
}

if(!function_exists('show_teapot_error')) {
	/**
	 * Show Teapot Error
	 * 
	 * Show the HTTP Teapot error according to RFC2324.
	 * 
	 * @access public
	 * @return exit
	 */
	function show_teapot_error() {
		show_doc(418) || show_error(
		  'The '
		. a(
		    'http://en.wikipedia.org/wiki/Hyper_Text_Coffee_Pot_Control_Protocol',
		    'HTCPCP'
		  )
		. ' server you requested a page from is a teapot, the entity may be short '
		. 'or stout. Please '
		. a(
		    'coffee://' . $_SERVER['SERVER_NAME'] . '/brew/',
		    'brew yourself a coffee'
		  ) . '!',
		  '418 I\'m a teapot'
		);
	}
}

if(!function_exists('show_doc'))
{
  /**
   * Show Error Document
   *
   * Supply it with a HTTP Status Code integer, and it will go check if the user
   * has defined a special error document for that status code.
   * The function will return false if the document does not exist or headers
   * have already been sent (the document will get mixed up with parts of the
   * page that have already been served).
   *
   * @param int $error_number
   * @return exit|false
   */
  function show_doc($error_number)
  {
    $file = theme_path('errors') . (string) $error_number . EXT;
    if(headers_sent() || !file_exists($file)) {
    	return false;
    }
    require $file;
    exit;
  }
}

if(!function_exists('a')) {
  /**
   * Anchor
   * 
   * Function relating to all things Framework URL, plus a bit more! Segments to
   * URL, with file extension and query string support, plain old
   * wrap-link-in-html-tag, and fetch link from config file. If the second
   * parameter is a non-empty string, it will wrap the link in the HTML 'a' tag
   * with text.
   * If the URL has already been used by this function, it will add the
   * attribute rel="nofollow" to prevent search engines thinking you are trying
   * to spam them.
   * 
   * @access public
   * @param string $path
   * @param string $title
   * @param array $options
   * @return string|false
   */
  function a($path, $title = false, $options = array()) {
    static $used_urls = array();
    if(!is_array($options)) {
      $options = array();
    }
    // If the path is a config reference, load it up so we can perform the check
    // on it as if it had been passed to the function directly.
    if(preg_match('|^~([a-zA-Z0-9_-]+)$|', $path, $matches)) {
      $link = c($matches[1], 'links');
      if(!is_string($link)) {
        return false;
      }
      $path = $link;
    }
    // The segment regular expression is not easy to read, so we'll break it
    // down here.
    $segment_regex['suffix'] = '[a-zA-Z0-9]*\:';
    // Segments can only contain alphanumeric characters, underscores, hyphens
    // and segment separators.
    $segment_regex['segments'] = '[a-zA-Z0-9/_-]+';
    // The query string is a bit different, because there are so many ways of
    // including it.
    $segment_regex['query'] = array(
      '\?(?:[a-zA-Z][a-zA-Z\:]*)?\?',
      '\?[^\?#]*',
    );
    $segment_regex['query'] = '(?:' . implode('|', $segment_regex['query']) . ')';
    // The fragment is easy. Pretty much any characters are allowed after a hash
    // symbol.
    $segment_regex['fragment'] = '#.*';
    // Now combine all the part regular expressions together to form an AWESOME
    // ALLIANCE!
    foreach($segment_regex as &$regex) {
      $regex = '(' . $regex . ')?';
    }
    $segment_regex = '~^' . implode('', $segment_regex) . '$~';
    // Filter $path.
    // Depending on what format the path is in, is how we grab the URL from it.
    switch(true) {
      // Valid URL.
      case filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED):
        $url = $path;
        break;
      // Segments.
      case preg_match($segment_regex, $path, $matches):
        if(strlen($matches[1]) == 0) {
          $matches[1] = c('url_default_suffix') . ':';
        }
        $suffix = strlen($matches[1]) > 1
                ? '.' . substr($matches[1], 0, -1)
                : '/';
        $segments = trim($matches[2], '/');
        $query = '';
        if(strlen($matches[3]) > 1) {
          if(substr($matches[3], -1) == '?') {
            $matches[3] = trim($matches[3], '?');
            if($matches[3] == '') {
              $matches[3] = 'query_string';
            }
            if(isset($options[$matches[3]])) {
              $query = is_array($options[$matches[3]])
                     ? http_build_query($options[$matches[3]])
                     : $options[$matches[3]];
              unset($options[$matches[3]]);
              if(substr($query, 0, 1) != '?') {
                $query = '?' . $query;
              }
            }
          }
          else {
            $query = $matches[3];
          }
        }
        $fragment = '';
        if(strlen($matches[4]) > 1) {
          $fragment = $matches[4];
        }
        $url = c('url_mod_rewrite') ? BASEURL : BASEURL . SELF . '/';
        $url .= $segments . $suffix . $query . $fragment;
        break;
      // Anthing else.
      default:
        return false;
        break;
    }
    // Rel Nofollow
    if(in_array($url, $used_urls)) {
      $options['rel'] = isset($options['rel'])
                      ? trim($options['rel']) . ' nofollow'
                      : 'nofollow';
    }
    elseif(is_string($title)) {
      $used_urls[] = $url;
    }
    // Title
    if(is_string($title)) {
      // Compile the options string
      $attributes = '';
      if(is_array($options) && count($options)) {
        // We do not want the href attribute being overwritten.
        if(isset($options['href'])) {
          unset($options['href']);
        }
        // Loop through and add them all as a single string.
        foreach($options as $attr => $value) {
          if (is_string($attr)
           && is_string($value)
           && preg_match('|^[a-zA-Z][a-zA-Z\:]*$|', $attr)
           && strpos($value, '"') === false
          ) {
            $attributes .= ' ' . $attr . '="' . $value . '"';
          }
        }
      }
      $url = '<a href="' . $url . '"' . $attributes . '>' . $title . '</a>';
    }
    return $url;
  }
}

if(!function_exists('theme_path')) {
	/**
	 * Theme Path
	 * 
	 * Specify a theme and will return the absolute path to the theme directory.
	 * Will return false if the theme directory does not exist.
	 * 
	 * @access public
	 * @param string $theme
	 * @return string|false
	 */
	function theme_path($theme) {
		if(!is_string($theme)) {
			return false;
		}
		$path = realpath(APP . 'themes/' . $theme);
		return is_string($path) ? $path . '/' : false;
	}
}

if(!function_exists('content'))
{
	/**
   * Content URL
   *
   * Takes a file path, relative to the content folder, checks that it exists,
   * and returns the absolute URL. If $force is set to true, it will return the
   * path regardless of whether the file exists (content path still needs to be
   * set).
   *
   * @access public
   * @param string $file
   * @param boolean $force
   * @return false|string
   */
  function content($file, $force = false) {
  	$force = bool($force);
  	if(is_null(CONTENTPATH) || is_null(CONTENT)) {
  		return false;
  	}
  	$file = trim(preg_replace('|/+|', '/', str_replace('\\', '/', $file)), '/');
  	$path = CONTENTPATH . $file;
  	if($force) {
  		return $path;
  	}
  	return file_exists($path) ? realpath($path) : false;
  }
}

if(!function_exists('redirect'))
{
  /**
   * Redirect
   *
   * Redirects the client/browser to another page. The parameter accepts the same as the first parameter for the
   * a() function.
   *
   * @param string $segments
   * @return false|void
   */
  function redirect($segments, $location = true)
  {
    $url = a($segments);
    if(!is_string($url) || headers_sent())
    {
      return false;
    }
    $header = bool($location) ? 'Location: ' : 'Refresh: 0; url=';
    $header .= $url;
    header('HTTP/1.1 307 Temporary Redirect', true, 307);
    header($header);
    exit;
  }
}

if(!function_exists('vardump'))
{
  /**
   * Vardump
   *
   * Same as the PHP var_dump() function, except it returns the value, instead of dumping it to the output.
   *
   * @param mixed $var
   * @return string
   */
  function vardump($var)
  {
    ob_start();
    var_dump($var);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
}

if(!function_exists('xplode'))
{
  /**
   * Xplode
   *
   * Same as the PHP explode() function, except if the second paramter is
   * an empty string it will return an empty
   * array, instead of an array containing an empty string.
   *
   * @param string $delimiter
   * @param string $string
   * @return array|false
   */
  function xplode($delimiter, $string)
  {
    if($string === '') return array();
    $array = explode($delimiter, $string);
    array_unshift($array, null);
    unset($array[0]);
    return $array;
  }
}

if(!function_exists('elapsed_time')) {
  /**
   * Elapsed Time
   *
   * Return the elapsed time in seconds, between the time specified time
   * passed to the function (must be the return of the function microtime)
   * and now.
   *
   * @param  string|float $start
   * @return false|float
   */
  function elapsed_time($start) {
    // Grab the time now, so we can compare.
    $end = microtime(true);
    // The user probably passed the microtime as a string.
    $regex = '/^0\\.([0-9]+) ([0-9]+)$/';
    if (is_string($start)) {
      $start = preg_match($regex, $start)
      ? (float) preg_replace($regex, '$2.$1', $start)
      : false;
    }
    // We should also check the end time, because microtime(true) will
    // return a string is PHP is less than 5.
    if (is_string($end)) {
      $end = preg_match($regex, $end)
      ? (float) preg_replace($regex, '$2.$1', $end)
      : false;
    }
    if (!is_float($start) || !is_float($end)) {
      return false;
    }
    $elapsed_time = round($end - $start, 3);
    return $elapsed_time;
  }
}

// That's it for common functions, now just a couple of hard coded settings / configurations:

set_magic_quotes_runtime(0);
error_reporting(E_ALL & ~E_NOTICE);
// If we don't do this, PHP 5.2+ will throw a little tantrum. Let's keep it happy :)
// You can change this in your controller, or a future library (hopefully!)
if(function_exists('date_default_timezone_set'))
{
  date_default_timezone_set(c('default_timezone'));
}
// Versions of PHP less than 5 do not have these constants, let's add them in for backwards
// compatibility with the PHP Tokenizer.
$tokens = defined('T_ML_COMMENT') ? array('T_DOC_COMMENT', T_ML_COMMENT) : array('T_ML_COMMENT', T_COMMENT);
define($tokens[0], $tokens[1]);
