<?php

/**
 * Initialisation Script
 *
 * This is where we start all our settings, libraries and other odd-jobs to get
 * the ball rolling...
 *
 * @category   Eventing
 * @package    Core
 * @subpackage init
 * @copyright  2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  defined('E_FRAMEWORK') || trigger_error(
    'E_FRAMEWORK has not been defined.',
    E_USER_ERROR
  );
  isset($main_file) || trigger_error(
    'Main file is not specified.',
    E_USER_ERROR
  );
  file_exists($main_file) || trigger_error(
    'The main file does not exist.',
    E_USER_ERROR
  );

  $main_config = array(
    'system_folder'   => 'system',
    'default_app'     => 'app',
    'content_folder'  => 'public',
    'config_type'     => 'array'
  );

  // Incorporate the user's settings into the default settings. We trust the
  // user to have all the right stuff there... Well, almost...
  if (is_array($user_config)) {
    foreach ($user_config as $key => $value) {
      if (array_key_exists($key, $main_config)) {
        $main_config[$key] = is_string($value) ? strtolower($value) : $value;
      }
    }
  }

  // Bring them... ALIVE!!!
  @extract($main_config);
  $c = array();

  // We have a dependant on $_SERVER['DOCUMENT_ROOT']. Unfortunately, some OS's
  // don't set this *cough* Windows *cough*
  if(!isset($_SERVER['DOCUMENT_ROOT'])) {
    if(isset($_SERVER['SERVER_SOFTWARE'])
       && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === 0
    ) {
      $path_length = strlen($_SERVER['PATH_TRANSLATED'])
                   - strlen($_SERVER['SCRIPT_NAME']);
      $_SERVER['DOCUMENT_ROOT'] = rtrim(
        substr($_SERVER['PATH_TRANSLATED'], 0, $path_length),
        '\\'
      );
    }
  }

  // File and System Constants.
  $c['config'] = strtolower($config_type) == 'ini' ? 'ini' : 'array';
  $c['self'] = basename($main_file);
  $c['ext'] = explode('.', $c['self']);
  $c['ext'] = '.' . end($c['ext']);

  // URL Constants.
  $c['server'] = (isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT'] == 443)
               ? 'https://'.$_SERVER['SERVER_NAME']
               : 'http://'.$_SERVER['SERVER_NAME'];
  $c['url'] = preg_replace(
    '|/+|',
    '/',
    '/' . trim(
      str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])),
    '/'
    ) . '/'
  );
  $c['baseurl']  = $c['server'].$c['url'];

  // Directory Constants.
  $c['basepath'] = rtrim(
    str_replace('\\', '/', realpath(dirname($main_file))),
    '/'
  ) . '/';
  $c['sys'] = rtrim(
    str_replace('\\', '/', realpath($system_folder)),
    '/'
  ) . '/';
  $c['app'] = rtrim(str_replace('\\', '/', realpath($default_app)), '/') . '/';
  $c['modbase'] = realpath($modules_folder);
  $c['modbase'] = is_string($c['modbase'])
                ? rtrim(str_replace('\\', '/', $c['modbase']), '/') . '/'
                : null;
  $c['contentpath'] = rtrim(
    str_replace('\\', '/', realpath($content_folder)),
    '/'
  ) . '/';
  // Check that the content directory is a sub-directory of the web root. If it
  // is not, set it as null.
  $c['content'] = null;
  if(is_string($_SERVER['DOCUMENT_ROOT'])) {
    $len = strlen($_SERVER['DOCUMENT_ROOT']);
    if(substr($c['contentpath'], 0, $len) == $_SERVER['DOCUMENT_ROOT']) {
      $c['content'] = trim(substr($c['contentpath'], $len), '/');
      $c['content'] = $c['content']
                    ? '/' . $c['content'] . '/'
                    : '/';
      $c['content'] = $c['server'] . $c['content'];
    }
  }
  // If the contenturl cannot be established, or it is outside the web root,
  // there is no point having the content path.
  if(is_null($c['content'])) {
    $c['contentpath'] = null;
  }

  // All our constants are really great, but they're a little soft at the
  // moment... Shall we make them hardcore?
  foreach ($c as $name => $const) {
    $name = strtoupper($name);
    defined($name) || define($name, $const);
  }
  
  // You know what? I've had enough of you lot... Yeah you heard me! Get lost!
  unset(
    $main_config, $user_config, $key, $value, $system_folder, $default_app,
    $content_folder, $skeleton_mode, $config_type, $c, $name, $const,
    $modules_folder
  );

  // Right, we have all out constants defined, with no loose variables floating
  // about... I think we're doing pretty well! Shall we load some common
  // functions? Let's!

  $common = SYS . 'common' . EXT;
  file_exists($common) || trigger_error(
    'Common functions could not be loaded.',
    E_USER_ERROR
  );
  require_once $common;

  // This framework now requires PHP5.3 for quite a lot of functionality. If we
  // are running anything less, terminate.
  if(PHP_VERSION_ID < 50300) {
    show_error(
      'This installation of PHP is running version ' . PHP_VERSION
    . ', but this framework requires version 5.3.0 or greater.'
    );
  }

  // Cool. We have functions. Now we want libraries! Big, fat juicy ones first,
  // for functionality. Then we can have the lean, mean, big-boss libraries! To
  // make it simple: URI, Router, Core, Controller and Model libraries...
  load_class('library', false);
  $u = load_class('uri');

  // Let's determine whether or not we need to load a module before we call the
  // router.
  $modules = get_config('modules');
  $mod = null;
  // Remember to check MODBASE, there will be no module without a module folder!
  if(MODBASE) {
    // Check the $modules config array for a match.
    if(is_array($modules)) {
      $uri_string = $u->uri_string();
      // Do a time saving check first.
      if(isset($modules[$uri_string])
         && is_string($modules[$uri_string])
         // Make sure that the string isn't empty.
         && $modules[$uri_string]
         && is_dir(MODBASE . $modules[$uri_string])
      ) {
        $mod = $modules[$uri_string];
      }
      // If it isn't that easy, iterate through the modules array, checking each
      // one for a match.
      else {
        foreach($modules as $uri => $module) {
          // If the module name is not a string, an empty string, or contains
          // characters other than alphanumeric, underscores or hyphens, then it
          // is not valid. Skip it.
          if(!is_string($module) || preg_match('^[^a-zA-Z0-9_-]*$', $module)) {
            continue;
          }
          // Modules are not quite as flexible as routes, no wildcards, and no
          // heirachy.
          if(substr($uri_string, 0, strlen($uri)) == $module
             && is_dir(MODBASE . $module)
          ) {
            $mod = $module;
            break;
          }
        }
      }
    }
    // If $modules is not an array, then use the fallback of loading the module if
    // the first segment is the same as the module slug.
    else {
      $segment = $u->segment(1);
      if($segment && is_dir(MODBASE . $segment)) {
        $mod = $segment;
      }
    }
  }
  defined('MOD') || define('MOD', $mod);
  $modpath = MOD
           ? rtrim(str_replace('\\', '/', MODBASE . MOD), '/') . '/'
           : null;
  defined('MODPATH') || define('MODPATH', $modpath);

  $r = load_class('router');
  load_class('core', false);
  load_class('controller', false);
  load_class('model', false);

  // We want to know what request this application is meant to serve!
  if(!is_array($r->dcm())) {
    show_404();
  }
  list($controller_path, $controller, $method) = $r->dcm();

  // Directory should come in format 'path/to/controller/', with a trailing
  // slash. Make an absolute path to the controller file, and include it.
  $controller_file = APP . 'controllers/' . $controller_path . EXT;
  // Check that the file provided by router::dcm() exists.
  file_exists($controller_file) || show_404();
  require_once $controller_file;

  // Make sure the controller class exists.
  class_exists($controller) || show_404();
  $controller = new $controller;
  // Make sure the method function exists and is public.
  if(!class_exists('ReflectionMethod')) {
    show_error(
      'ReflectionMethod class does not exist. Method publicity status cannot '
    . 'be determined.'
    );
  }
  
  method_exists($controller, $method)
    || in_array($method, get_class_methods($controller), true)
    || show_404();
  $method_reflection = new ReflectionMethod($controller, $method);
  $method_reflection->isPublic() || show_404();

  // Unset all unecessary variables before we call action.
  unset(
    $common, $u, $modules, $mod, $uri_string, $uri, $segment, $r,
    $controller_path, $controller_file
  );

  $controller->$method();

  // Right, that's everything done! Just dump the output to the client end
  // finish the script!
  $E =& get_instance();
  $E->output->display();
