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
          <div class="error" style="display:block;border:2px solid #900;padding:10px;margin:10px;background-color:#FFF;">
            <h2 style="margin:0 0 0.4em;color:#600;">Error <?php echo $num . ' (' . $types[$num]; ?>)</h2>
            <p style="margin:0 0 0.4em;font-family:monospace;color:#000;">
              <strong>File:</strong> <?php echo $file; ?><br />
              <strong>Line:</strong> <?php echo $line; ?>
            </p>
            <p style="margin:0 0 0 1em;color:#000;"><?php echo $msg; ?></p>
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
         * @return void
         */
        function show_error($msg)
        {
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

    if(!function_exists('show_doc'))
    {
        /**
         * Show Error Document
         *
         * Supply it with a HTTP Status Code integer, and it will go check if the user has defined a special error
         * document for that status code.
         *
         * @param int $error_number
         * @return void
         */
        function show_doc($error_number)
        {
            $file = APP . 'themes/errors/' . (string) $error_number . EXT;
            file_exists($file) || show_error('Error Document for ' . $error_number . ' status code does not exist.',
                                        '500 Internal Application Error');
            require_once $file;
            exit;
        }
    }

    if(!function_exists('a'))
    {
        /**
         * Anchor
         *
         * Function relating to all things Framework URL, plus a bit more! Segments to URL, with file extension and
         * query string support, plain old wrap-link-in-html-tag, and fetch link from config file. If the second
         * parameter is a non-empty string, it will wrap the link in the HTML 'a' tag with text.
         *
         * @param string $segments
         * @param string $title
         * @param array $options
         * @return string|boolean
         */
        function a($segments, $title = null, $options = null)
        {
            $regex = '|^([a-zA-Z0-9]*\:)?([a-zA-Z0-9\/_-]*)(\?.*)?$|';
            if(!is_string($segments) || !preg_match($regex, $segments, $matches))
            {
                return false;
            }
            // Start creating the final URL. Server and App Folder + Segments and Suffix
            $segments = $matches[2];
            switch($matches[1])
            {
                case '':
                    $suffix = '.' . c('url_default_suffix'); 
                    break;
                case ':':
                    $suffix = '/';
                    break;
                default:
                    $suffix = '.' . substr($matches[1], 0, strlen($matches[1]) - 1);
                    break;
            }
            if($matches[2] == '')
            {
                $suffix = '';
            }
            $url = c('url_mod_rewrite') ? BASEURL : BASEURL . SELF . '/';
            $url .= $segments . $suffix;
            // Sort out the Query String. Add the variables that should be saved from one page to the other.
            $E =& get_instance();
            $query = array_merge($E->uri->get_saves(), $E->uri->split_query(substr($matches[3], 1)));
            $query = $E->uri->create_query($query);
            $url .= $query != '' ? '?' . $query : '';
            // Check for a title, and if so, wrap the URL in an HTML Anchor tag.
            if(is_string($title))
            {
                $option_text = '';
                if(is_array($options))
                {
                    foreach($options as $key => $value)
                    {
                        if(is_string($key) && is_string($value))
                        {
                            $option_text .= ' ' . $key . '="' . $value . '"';
                        }
                    }
                }
                $url = '<a href="'.$url.'"'.$option_text.'>'.$title.'</a>';
            }
            return $url;
        }
    }

    /**
     * Content URL
     *
     * Takes a file path, relative to the content folder, checks that it exists, and returns the absolute URL.
     *
     * @param string $file
     * @return false|string
     */
    if(!function_exists('content'))
    {
        function content($file)
        {
            if(is_null(CONTENTPATH) || is_null(CONTENT))
            {
                return false;
            }
            $file = trim(preg_replace('|//+|', '/', str_replace('\\', '/', $file)), '/');
            $filepath = realpath(CONTENTPATH . $file);
            if(!file_exists($filepath))
            {
                return false;
            }
            return CONTENT . $file;
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
