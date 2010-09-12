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
  $c['ext'] = '.' . end(explode('.', $c['self']));

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
      $c['content'] = $c['server']
                    . '/'
                    . trim(substr($c['contentpath'], $len), '/')
                    . '/';
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
    $content_folder, $skeleton_mode, $config_type, $c, $name, $const
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

  // Cool. We have functions. Now we want libraries! Big, fat juicy ones first,
  // for functionality. Then we can have the lean, mean, big-boss libraries! To
  // make it simple: URI, Router, Core, Controller and Model libraries...
  $URI =& load_class('uri');
  $RTR =& load_class('router');
  load_class('core', false);
  load_class('controller', false);
  load_class('model', false);

  // We want to know what request this application is meant to serve!
  if(!is_array($dcm = $RTR->dcm())) {
    show_404();
  }

  // Directory should come in format 'path/to/controller/', with a trailing
  // slash. Make an absolute path to the controller file, and include it.
  $controller_file = APP . 'controllers/' . $dcm[0] . $dcm[1] . EXT;
  // Check that the file provided by $dcm exists, because the router library may
  // not of used the recursive method.
  file_exists($controller_file) || show_404();
  require_once $controller_file;

  // Strip all extensions. We only want the name of the controller class now!
  if(($pos = strpos($dcm[1], '.')) !== false) {
    $dcm[1] = reset(explode('.', $dcm[1]));
  }

  // Make sure the controller class exists.
  class_exists($dcm[1]) || show_404();
  $controller = new $dcm[1];
  // Make sure the method function exists.
  method_exists($controller, $dcm[2])
    || in_array($dcm[2], get_class_methods($dcm[1]), true)
    || show_404();
  $controller->$dcm[2]();

  // Right, that's everything done! Just dump the output to the client end
  // finish the script!
  $E =& get_instance();
  $E->output->display();
