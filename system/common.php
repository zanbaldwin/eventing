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
   * @license    http://www.opensource.org/licenses/mit-license.php MIT/X11 License
   * @version    v0.4
   * @link       http://github.com/mynameiszanders/eventing
   * @since      v0.1
   */

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  // This framework now requires PHP5.3 for quite a lot of functionality. If we
  // are running anything less, terminate.
  if(PHP_VERSION_ID < 50300) {
    show_error(
      'This installation of PHP is running version ' . PHP_VERSION
    . ', but this framework requires version 5.3.0 or greater.'
    );
  }

  // This is against standard practice, to set error reporting to full, especially
  // for production, but in truth, if you don't want errors coming up in your
  // applications, start writing better code!
  error_reporting(-1);
  ini_set('display_errors', 1);

  if (!function_exists('eventing_error_handler')) {
    /**
     * Eventing Error Handler
     * 
     * Custom error handler for error's thrown by PHP or ones that have been
     * triggered with trigger_error().
     * 
     * @access public
     * @param integer $err
     * @param string $msg
     * @param string $file
     * @param integer $line
     * @return false|exit
     */
    function eventing_error_handler($err, $msg, $file, $line) {
      // Define the different error types.
      $types = array(
        E_ERROR              => 'Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parse Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Strict',
        E_RECOVERABLE_ERROR  => 'Recoverable Error',
        E_DEPRECATED         => 'Deprecated',
        E_USER_DEPRECATED    => 'User Deprecated',
      );
      // Define which error types we will account for.
      $trigger = c('error_types_trigger');
      if(!is_int($trigger)) {
        // The default.
        $trigger = 17237;
      }
      $triggers = binary_parts($trigger);
      if(!in_array($err, $triggers)) {
        return false;
      }
      // Build the error overwrite data array.
      $error = array(
        'title'    => "Error {$err} ({$types[$err]})",
        'file'     => $file,
        'line'     => $line
      );
      // Show an error!
      show_error($msg, '500 Internal Server Error', $error);
    }
  }

  // Set PHP's error handler to the Eventing error handler.
  set_error_handler('eventing_error_handler');

  if(!function_exists('binary_parts')) {
    /**
     * Binary Parts
     * 
     * Returns an array of integers. Each integer is a power of 2 that adds up
     * to the number passed to the function.
     * 
     * @access public
     * @param integer $int
     * @return array|false
     */
    function binary_parts($int) {
      if(!is_int($int)
         || (!is_numeric($int)
             || !preg_match('|^[0-9]+$|', $int))
         || $int < 0
      ) {
        return false;
      }
      $arr = str_split(decbin((int) $int));
      $arr = array_reverse($arr);
      $count = count($arr);
      $parts = array();
      for($i = 0; $i < $count; $i++) {
        if($arr[$i] == '1') {
          $parts[] = pow(2, $i);
        }
      }
      return $parts;
    }
  }

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
    function load_class($identifier, $return = true) {
      // Create a static array to house our loaded files in - we don't want to
      // include a class definition twice now, do we?
      static $files = array();
      // No point continuing with the function if no library has been specified.
      if(!is_string($identifier)) {
        return false;
      }
      $return = bool($return);
      // Check if the specified library is an empty string and that it adheres
      // to the "module:path/to/library" syntax.
      $regex = '#^(([a-zA-Z_][a-zA-Z0-9_]*)?@)?([a-zA-Z_][a-zA-Z0-9_/]*)$#';
      if(!$identifier || !preg_match($regex, $identifier, $parts)) {
        return false;
      }
      // Assign our parts to something a little more human-readable.
      $module = $parts[2] ? strtolower($parts[2]) : false;
      // Filter our library string to all lowercase, no separators on the ends
      // of the string or doubled up.
      $lib = trim(filter_path(strtolower($parts[3])), '/');
      // Since the identifier string check out? Let's rebuild it so that the
      // string will be identical to other calls to this function.
      $identifier = $module ? $module . ':' . $lib : $lib;
      // Now that we have a usable identifier string, the only useful
      // extractable information in the library string is the class name. Make
      // sure xplode() and end() are called as two separate statements as to
      // avoid an E_STRICT error.
      $class = xplode('/', $lib);
      $class = end($class);
      // Transform the library name to an absolute namespace and class
      // reference.
      $class = $module
             ? ns(NS, NSMODULE, $module, NSLIBRARY) . $class
             : ns(NS, NSLIBRARY)                    . $class;
      // If we have already loaded the file in question, then return the
      // appropriate - we do not want to load the file again.
      if(isset($files[$identifier])) {
        // If load_class() failed the first time, then it's not going to work a
        // second time, is it?
        if(!$files[$identifier]) {
          return false;
        }
        // load_class() was successful last time! Now, did the function callee
        // want an instance returned?
        return $return ? $class::getInstance() : true;
      }
      // This must be the first call this function specifying this library.
      // Determine the file path, and include the file.
      $file = $module
            ? MOD . $module . '/libraries/' . $lib . EXT
            : SYS . 'libraries/' . $lib . EXT;
      // If the file exists, return false, remembering to set the $files array
      // first, so that we can save ourselves the trouble of querying the
      // filesystem for every call to this library.
      if(!file_exists($file)) {
        $files[$identifier] = false;
        return false;
      }
      // Whack it in, baby!
      // This is where we hope the file actually doesn't contain anything to
      // screw up the framework, like inline HTML, or worse, 
      require_once $file;
      // Check that the library class exists.
      if(!class_exists($class)) {
        $files[$identifier] = false;
        return false;
      }
      // If the function callee has specified that they do not want an instance
      // returned, just return true here.
      // Do NOT add anything to the $files array, because the library they
      // specified may not implement the getInstance() method!
      if(!$return) {
        return true;
      }
      if(!method_exists($class, 'getInstance')) {
        $files[$identifier] = false;
        return false;
      }
      // Well done! We have determined the library, the file it is contained in,
      // that the class exists, and that it implements a method called
      // getInstance()!
      //Add a true boolean to the $files array, and return an instance.
      $files[$identifier] = true;
      return $class::getInstance();
    }
  }

  if(!function_exists('getInstance')) {
    /**
     * Get Instance
     *
     * Global version of the getInstance method in libraries, returns an
     * instance of the super (core) object.
     *
     * @access public
     * @return object
     */
    function &getInstance() {
      $core = ns(NS, NSLIBRARY) . 'core';
      return $core::getInstance();
    }
  }

  if(!function_exists('bool')) {
    /**
     * Check Boolean
     *
     * Returns boolean equivelant of value passed to function.
     *
     * @param mixed $var
     * @return boolean
     */
    function bool($var) {
      return $var === true ? true : false;
    }
  }

  if(!function_exists('bl')) {
    /**
     * Make Boolean
     *
     * Takes a variable, and makes into a boolean by reference.
     *
     * @param mixed $var
     * @return boolean
     */
    function bl(&$var) {
      $var = bool($var);
      return $var;
    }
  }

  if(!function_exists('get_config')) {
    /**
     * Get Config
     *
     * Fetch a config array from a file.
     *
     * @param string $file
     * @return array|false
     */
    function get_config($config_file) {
      static $main_config = array();
      if(isset($main_config[$config_file])) {
        return $main_config[$config_file];
      }
      $file = APP . 'config/' . $config_file;
      if(CONFIG == 'ini') {
        function_exists('parse_ini_file') || show_error(
          'Cannot retrieve config settings. INI file parser does not exist.',
          500
        );
        $file .= '.ini';
        if(!file_exists($file)) {
          return false;
        }
        $config = parse_ini_file($file, false);
      }
      else {
        $file .= EXT;
        if(!file_exists($file)) {
          return false;
        }
        require_once $file;
      }
      if(!isset($config) || !is_array($config)) {
        return false;
      }
      $main_config[$config_file] =& $config;
      return $main_config[$config_file];
    }
  }

  if(!function_exists('c')) {
    /**
     * Config Item
     *
     * Fetches an item from the config files.
     *
     * @param string $item
     * @param string $file
     * @return mixed|false
     */
    function c($item, $file = 'config') {
      static $config_items = array();
      $file = is_string($file) && $file != '' ? $file : 'config';
      if(!isset($config_items[$file])) {
        $config_items[$file] = get_config($file);
      }
      if(!is_array($config_items[$file])) {
        return false;
      }
      return isset($config_items[$file][$item])
           ? $config_items[$file][$item]
           : null;
    }
  }

  if(!function_exists('filter_path')) {
    /**
     * Filter Path
     *
     * Converts all backslashes to forward slashes, for Unix style consistency,
     * and removes unnecessary slashes.
     *
     * @param string $path
     * @return string|null
     */
    function filter_path($path) {
      return preg_replace('|/+|', '/', str_replace('\\', '/', $path));
    }
  }

  if(!function_exists('show_error')) {
    /**
     * Show Error
     *
     * Explain the function...
     *
     * @return exit
     */
    function show_error($msg, $header = '500 Framework Application Error', $user_error = false) {
      if(is_string($header)
         && preg_match('|^([0-9]{3}) |', $header, $matches)
         && (int) $matches[1] < 600
         && (int) $matches[1] >= 100
      ) {
        if(!headers_sent()) {
            header($header, true, (int) $matches[1]);
        }
      }
      else {
        $header = '500 Framework Application Error';
      }
      // Grab some information from the backtrace. It might be useful.
      $trace = debug_backtrace();
      $error = array(
        'message' => $msg,
        'title' => $header,
        'status' => (int) $matches[1],
        'file' => $trace[0]['file'],
        'line' => $trace[0]['line']
      );
      if(is_array($user_error)) {
        foreach($user_error as $overwrite => $value) {
          if(isset($error[$overwrite])) {
            $error[$overwrite] = $value;
          }
        }
      }
      if(file_exists(theme_path('errors') . 'error' . EXT)) {
        extract($error);
        // Unset any variables that we don't want included in the error document.
        unset($msg, $header, $matches, $trace, $error);
        // We are writing about the path twice because we don't want to set
        // anymore variables.
        require theme_path('errors') . 'error' . EXT;
      }
      else {
        // No HTML document to show? Dump out the data in an XML document
        // instead.
        echo '<?xml version="1.0" encoding="utf-8" ?>' . "\n<error>\n";
        foreach($error as $element => $value) {
          echo "  <{$element}>\n    {$value}\n  </{$element}>\n";
        }
        echo '</error>';
      }
      exit;
    }
  }

  if(!function_exists('show_404')) {
    /**
     * Show 404
     *
     * Calls show_doc(404), trying to find a user error document. If this fails,
     * default to the not-so-pretty show_error().
     *
     * @return void
     */
    function show_404() {
      show_doc(404) || show_error(
        'The page you requested does not exist.',
        '404 Not Found'
      );
    }
  }

  if(!function_exists('show_deny')) {
    /**
     * Show Deny
     *
     * Calls show_doc(403), trying to find a user error document. If this fails,
     * default to the not-so-pretty show_error().
     */
    function show_deny() {
      show_doc(403) || show_error(
        'You do not have sufficient clearance to view this page.',
        '403 Forbidden'
      );
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

  if(!function_exists('show_doc')) {
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
    function show_doc($error_number) {
      $file = theme_path('errors') . (string) $error_number . EXT;
      if(headers_sent() || !file_exists($file)) {
        return false;
      }
      require $file;
      exit;
    }
  }

  if(!function_exists('uri')) {
    /**
     * URI Parser
     *
     * Parse a string, in the format designed for Eventing URIs and return an
     * object containing data about the different parts.
     *
     * @access public
     * @param string $uri
     * @param boolean $object
     * @return object|array|false
     */
    function uri($uri, $object = true) {
      static $r = false;
      if(!is_string($uri)) {
        return false;
      }
      if(!$r) {
        $regex = array();
        // The module name must also be a valid PHP class name.
        $regex['module']    = '[a-zA-Z_][a-zA-Z0-9_]*\\s*@';
        // Segments must be alphanumeric, hyphens or underscores, separated by
        // *nix-style directory separators.
        $regex['segments']  = '[a-zA-Z0-9/_-]+';
        // The suffix is just like a regular file extension, except it can only
        // contain alphanumeric characters.
        $regex['suffix']    = '?:\.?([a-zA-Z0-9]+)/?';
        // The query string can only contain certain characters, but according
        // to RFC 3986, a query string is a URL part starting from the first
        // question mark, and terminating at the end of the URL, or at the '#'
        // character, if one is present.
        $regex['query']     = '\?(?:(?:'.VALIDLABEL.')?\?|[^#? ]*)';
        // The URL fragment can basically be anything from the first occurance
        // of the '#' character to the end of the URL. It is only used
        // client-side, so we don't need to bother with it... It's just here so
        // we can pump it into our anchors
        $regex['fragment']  = '#.*';
        // Wrap each part so we can assign them as matches, and combine the PCRE
        // RegEx string.
        foreach($regex as &$part) {
          $part = '(' . $part . ')?';
        }
        $r = '~^' . implode('\\s*', $regex) . '$~';
      }
      if(!preg_match($r, $uri, $matches)) {
        return false;
      }
      // Start constructing the data array.
      $u = array();
      // We want the name of the module, if no module passed, use boolean false.
      $u['module']    = isset($matches[1]) && $matches[1]
                      ? trim(substr($matches[1], 0, -1))
                      : false;
      // Do the segments specify an absolute or relative URI?
      $u['absolute']  = isset($matches[2]) && substr($matches[2], 0, 1) == '/';
      // A trailing slash on the segments indicates the URI points to a
      // directory. Before filtering the segments, make a note of this.
      $trailing       = isset($matches[2]) && substr($matches[2], -1) == '/';
      // Clean up the segments
      $u['segments']  = isset($matches[2])
                     && ($u['segments'] = trim(filter_path($matches[2]), '/'))
                      ? $u['segments']
                      : false;
      // A trailing slash on the segments indicates the URI points to a
      // directory. Before filtering the segments, make a note of this.
      $trailing       = isset($matches[2]) && substr($matches[2], -1) == '/';
      // If we have a suffix, prepend it with a full stop, else false.
      $u['suffix'] = '/';
      if($u['segments']) {
        $u['suffix'] = isset($matches[3]) && $matches[3]
                     ? '.' . $matches[3]
                     : ($trailing ? '/' : DEFAULTSUFFIX);
      }
      // If we have a query string placeholder, just return the placeholder
      // name. If we have an actual query string, parse it and return the query
      // array.
      if(!isset($matches[4])) {
        $u['query']   = false;
      }
      elseif(substr($matches[4], -1) == '?') {
        $u['query']   = ($query_varname = trim($matches[4], '?'))
                      ? $query_varname
                      : 'query';
      }
      else {
        parse_str(trim(substr($matches[4], 1)), $u['query']);
      }
      // If we have a fragment, great! Else false, as per usual.
      $u['fragment']  = isset($matches[5]) && $matches[5]
                      ? substr($matches[5], 1)
                      : false;
      // Return the data we've collected in the format the function callee
      // requested.
      return bool($object) ? (object) $u : $u;
    }
  }

  if(!function_exists('a')) {
    /**
     * Anchor
     * 
     * Function relating to all things Framework URL, plus a bit more! Segments
     * to URL, with file extension and query string support, plain old
     * wrap-link-in-html-tag, and fetch link from config file. If the second
     * parameter is a non-empty string, it will wrap the link in the HTML 'a'
     * tag with text.
     * If the URL has already been used by this function, it will add the
     * attribute rel="nofollow" to prevent search engines thinking you are
     * trying to spam them.
     * 
     * @access public
     * @param string $path
     * @param string $title
     * @param array $options
     * @return string|false
     */
    
    function a($path, $title = false, $options = array()) {
      static $used_urls = array();
      if(!is_string($path)) {
        return false;
      }
      // Set the optional parameters to safe values.
      $options = (array) $options;
      $title = !is_string($title) || !$title ? false : $title;
      // If $path is a reference to a link inside the links configuration file,
      // then grab the shortcut name, and fetch it from the config array.
      $shortcut_regex = '#^~('.VALIDLABEL.')$#';
      if(preg_match($shortcut_regex, $path, $matches)) {
        $link = c($matches[1], 'links');
        if(!is_string($link)) {
          return false;
        }
        $path = $link;
      }
      // Check that the $path is not an absolute URL. If not, then treat $path
      // as an Eventing-style URI.
      if(!filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
        $data = uri($path);
        if(!is_object($data)) {
          return false;
        }
        // Determine how our URL should reference the domain and application
        // directory.
        $server = ($data->absolute    ? BASEURL             : URL)
                . (!c('mod_rewrite')
                  && ($data->module || $data->segments)
                  ? SELF . '/'                              : '');
        // Build the URL part that gets used by the application for routing.
        $application = ltrim(
          ($data->module              ? $data->module . '@' : '')
        . $data->segments
        . $data->suffix,
          '/'
        );
        // Build the query string depending on what source we are going to use.
        $query = '';
        if($data->query) {
          // If the query data returned is a string, it means that it's a
          // placeholder for a query array held in the $options array.
          if(is_string($data->query)
             && isset($options[$data->query])
             && is_array($options[$data->query])
          ) {
            $query_identifier = $data->query;
            $data->query = $options[$data->query];
            unset($options[$query_identifier], $query_identifier);
          }
          // Now we have satisfied the query placeholder, check that the query
          // data is an array ready to be made into a query string.
          if(is_array($data->query)) {
            $query = '?' . http_build_query($data->query, null, '&');
          }
        }
        // Include a URL fragment if one has been set.
        $fragment = $data->fragment ? '#' . $data->fragment : '';
        // Rebuild our path from the URL parts we just created from the string
        // that was originally passed to the function. If only the fragment was
        // passed, then only use the fragment part, as passing more than that
        // will make the page reload. Fragment is usually intended for internal
        // page navigation or Javascript, neither of which want the page to
        // reload.
        $uri = $application . $query . $fragment;
        if($path != '#') {
          $path = substr($uri, 0, 1) == '#' && !$data->absolute
                ? $fragment
                : $server . $uri;
        }
      }
      // The path is now a valid URL!
      // Check that we haven't already used the URL already. If we have, add a
      // rel="nofollow" to stop search engine crawlers thinking we're trying to
      // spam them.
      if(in_array($path, $used_urls)) {
        if(isset($options['rel'])) {
          if(!is_array($options['rel'])) {
            $rels = xplode(' ', (string) $options['rel']);
          }
          if(!in_array('nofollow', $rels)) {
            $rels[] = 'nofollow';
          }
          $options['rel'] = implode(' ', $rels);
        }
        else {
          $options['rel'] = 'nofollow';
        }
      }
      elseif($title) {
        $used_urls[] = $path;
      }
      // If the title evaluates to true, it means we have a valid string (we
      // have already done checks on it). We want to wrap the URL in an anchor
      // tag, and add a title and attributes.
      if($title) {
        // Compile the options string from the array passed in the third
        // parameter.
        $o = '';
        foreach($options as $attr => $value) {
          // We do not want our href attribute being overwritten, else there
          // would be no point in having the first parameter!
          if($attr == 'href') {
            continue;
          }
          // If the attribute isn't a string, then it can't go in the HTML tag.
          if(is_string($attr)) {
            // If the value is an array (such as an array of rel tags or class
            // names), implode them into a space-separated string.
            if(is_array($value)) {
              $value = implode(' ', $value);
            }
            $o .= ' ' . $attr . '="' . htmlentities((string) $value) . '"';
          }
        }
        // Build our HTML tag.
        $path = '<a href="' . htmlentities($path) . '"' . $o . '>'
              . $title
              . '</a>';
      }
      return $path;
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
    function theme_path($theme = true) {
      if($theme === true) {
        $theme = '';
      }
      if(!is_string($theme)) {
        return false;
      }
      $path = realpath(APP . 'themes/' . $theme);
      return is_string($path) ? $path . '/' : false;
    }
  }

  if(!function_exists('get_themes')) {
    /**
     * Get Themes
     * 
     * Returns an array of folders that are inside the themes directory.
     * 
     * @access public
     * @return array
     */
    function get_themes() {
      static $themes = false;
      if(is_array($themes)) {
        return $themes;
      }
      $path = theme_path();
      if(!is_string($path)) {
        return array();
      }
      $handler = opendir($path);
      while($file = readdir($handler)) {
        if($file != '.' && $file != '..' && is_dir($path . $file)) {
          $themes[] = $file;
        }
      }
      return $themes;
    }
  }

  if(!function_exists('content')) {
    /**
     * Content URL
     *
     * Takes a file path, relative to the content folder, checks that it exists,
     * and returns the absolute URL. If $force is set to true, it will return
     * the path regardless of whether the file exists (content path still needs
     * to be set).
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
      $url = CONTENT . $file;
      if($force) {
        return $url;
      }
      $path = CONTENTPATH . $file;
      return file_exists($path) ? $url : false;
    }
  }

  if(!function_exists('redirect')) {
    /**
     * Redirect
     *
     * Redirects the client/browser to another page. The parameter accepts the
     * same as the first parameter for the a() function.
     *
     * @param string $segments
     * @return false|void
     */
    function redirect($segments, $location = true) {
      $url = a($segments);
      if(!is_string($url) || headers_sent()) {
        return false;
      }
      $header = bool($location) ? 'Location: ' : 'Refresh: 0; url=';
      $header .= $url;
      header('HTTP/1.1 307 Temporary Redirect', true, 307);
      header($header);
      exit;
    }
  }

  if(!function_exists('vardump')) {
    /**
     * Vardump
     *
     * Same as the PHP var_dump() function, except it returns the value, instead
     * of dumping it to the output.
     *
     * @param mixed $var
     * @return string
     */
    function vardump($var) {
      ob_start();
      var_dump($var);
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
  }

  if(!function_exists('xplode')) {
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
    function xplode($delimiter, $string) {
      $string = trim($string, $delimiter);
      if($string === '') {
        return array();
      }
      $string = preg_replace(
        '#' . preg_quote($delimiter . $delimiter, '#') . '+#',
        $delimiter,
        $string
      );
      $array = explode($delimiter, $string);
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

  if(!function_exists('copyright')) {
    /**
     * Copyright Notice
     *
     * Echo out a copyright notice, which automatically updates the copyright
     * years.
     *
     * @access public
     * @param string $holder
     * @param integer $since
     * @return string
     */
    function copyright($holder = 'Copyright Holder', $since = false) {
      $since = is_numeric($since) ? (int) $since : (int) strftime('%Y');
      $year = (int) strftime('%Y');
      $year = $year > $since ? '-'.$year : '';
      return 'Copyright &#169; ' . $holder . ' ' . $since . $year;
    }
  }

  // Define the default suffix, so that we know what to use incase one isn't
  // given in the application URI or any eURI's.
  defined('DEFAULTSUFFIX') || define(
    'DEFAULTSUFFIX',
    is_string($s = c('default_suffix'))
   && preg_match('/^\.[a-zA-Z0-9]+$/', $s)
    ? strtolower($s)
    : '/'
  );

  // If we don't do this, PHP (we use versions above 5.2 remember?) will throw a
  // little tantrum. Let's keep it happy :)
  // You can change this in your controller, or a future library (hopefully!)
  $default_timezone = c('default_timezone')
                    ? c('default_timezone')
                    : 'Europe/London';
  date_default_timezone_set($default_timezone);
