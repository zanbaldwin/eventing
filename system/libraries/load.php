<?php

/**
 * Eventing Framework Loader Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage load
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

namespace Eventing\Library;

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class load extends library
{

  private $E;

  protected function __construct()
  {
    $this->E =& getInstance();
  }

  /**
   * Autoload
   *
   * Load resources as defined in the "autoload" config file.
   *
   * @return boolean
   */
  public function autoload()
  {
    if(!is_array($load = get_config('autoload')))
    {
      return false;
    }
    foreach($load as $type => $resource)
    {
      if(!is_array($resource) || !method_exists($this, $type))
      {
        continue;
      }
      foreach($resource as $call)
      {
        if(!is_string($call))
        {
          continue;
        }
        $this->$type($call);
      }
    }
    return true;
  }

  /**
   * Load Library
   *
   * @param string $library
   * @return boolean
   */
  public function library($library)
  {
    if(isset($this->E->$library))
    {
      return true;
    }
    $lib = load_class($library);
    if(is_object($lib))
    {
      $library = xplode('/', $library);
      $library = end($library);
      $this->E->$library = $lib;
      return true;
    }
    return false;
  }

  /**
   * Load model.
   * @param string $model
   * @return boolean
   */
  public function model($model)
  {
    if(isset($this->E->models[$model]))
    {
      return true;
    }
    if(!file_exists($file = APP . 'models/' . $model . EXT))
    {
      return false;
    }
    require_once $file;
    $m = xplode('/', $model);
    $m = 'M_' . end($m);
    if(!class_exists($m))
    {
      return false;
    }
    $this->E->models[$model] = new $m;
    return true;
  }

  /**
   * Load View
   *
   * @param string $view
   * @param array $data
   * @return string|false
   */
  public function view($view, $_E_Load_View_data, $theme = false) {
    $theme = is_string($theme) ? $theme : c('default_theme');
    $_E_Load_View_file = file_exists($view)
    ? $view
    : APP . 'themes/' . $theme . '/' . $view . EXT;
    unset($view, $theme);
    if (!file_exists($_E_Load_View_file)) {
      return false;
    }
    ob_start();
    if (is_array($_E_Load_View_data)) {
      // Remove as many variables as possible.
      unset($theme, $view);
      extract($_E_Load_View_data, EXTR_SKIP);
    }
    require $_E_Load_View_file;
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  /**
   * Include File
   *
   * Used to include misc files in your controllers, methods and views.
   * There are three options available: flush, return and path.
   * Flush will get the output of the file (executing any PHP inside it) and add it to the output class.
   * Return will grab the file contents, and return it as a string to whatever call it.
   * Path will calculate the path of the file, and return it as a string so it can be used in an include().
   *
   * If you want to pass variables to the file, then you can pass an array of them as the second parameter
   * instead. Flush is the default option, and therefore will be the option chosen if you pass variables.
   *
   * @param string $file
   * @param string $type
   * @param array $vars
   * @return boolean|string
   */
  public function file($_E_load_file, $type = array())
  {
    if(is_array($type))
    {
      // Because of the way extract() works, if the user specifies 'flush' directly it
      // won't become a variable later on because it will have a numeric index.
      $vars = (array) $type;
      $type = 'flush';
    }
    if(!is_string($type) || !in_array($type, array('flush', 'return', 'path')))
    {
      return false;
    }
    $_E_load_file = APP . 'files/' . $_E_load_file . EXT;
    if(!file_exists($_E_load_file))
    {
      return false;
    }
    switch($type)
    {
      case 'path':
        return $_E_load_file;
        break;
      case 'return':
        return file_get_contents($_E_load_file);
        break;
      case 'flush':
        // Remove any variables we don't need or want overwriting, and extract the variables from $vars.
        unset($type, $vars['_E_load_file']);
        extract($vars);
        // Start output buffering, and get the output of the file.
        ob_start();
        require $_E_load_file;
        $file_contents = ob_get_contents();
        ob_end_clean();
        // Add whatever we got from the file and add it to the output class.
        $this->E->output->append_output($file_contents);
        return true;
        break;
    }
  }

  public function plugin($plugin)
  {
  }

  public function vars($vars)
  {
  }

  public function database($db)
  {
  }

}
