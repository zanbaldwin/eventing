<?php

/**
 * Eventing Framework Template Library
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to
 * rapidly build applications. Template Library. Organises multiple views as
 * sections, provides each one with data, and links them together to create a
 * page.
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage Template
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
 * Eventing Template Library
 *
 * Provides simple templating methods, such as adding variables, and combining
 * multiple views together into complex, nested sections.
 *
 * @package     Eventing
 * @subpackage  Libraries
 * @category    template
 * @author      Alexander Baldwin
 * @link        http://github.com/mynameiszanders/eventing
 */
class template extends library {

  protected $links = array(),
  $sections = array(),
  $theme = false,
  $subdir = '',
  $prefix = '',
  $active = false,
  // The following is hard-coded, this should not be changed.
  $section_class = 'E_template_section',
  $valid_name = '[a-zA-Z_][a-zA-Z0-9_]*';

  protected function __construct() {
    $this->section_class = ns(NS, NSLIBRARY) . $this->section_class;
    $theme = c('default_theme');
    $this->theme = is_string($theme)
    && $theme != ''
    && is_dir(APP . 'themes/' . $theme)
    ? $theme . '/'
    : false;
  }

  /**
   * View Exists
   *
   * @access public
   * @param  string  $view
   * @return boolean
   */
  public function view_exists($view) {
    if(!is_string($view) || !is_string($this->theme)) {
      return false;
    }
    $file = APP
    . 'themes/'
    . $this->theme
    . $this->subdir
    . $this->prefix
    . $view
    . EXT;
    return file_exists($file);
  }

  /**
   * Section (or Group) Exists
   *
   * @access public
   * @param  string  $section
   * @return boolean
   */
  public function section_exists($section) {
    if(!is_string($section)) {
      return false;
    }
    return isset($this->sections[$section]);
  }

  /**
   * Section Name
   *
   * Return the section name as a string.
   *
   * @access protected
   * @param  object|string $section
   * @return string|false
   */
  protected function section_name($section) {
    // If the section is already passed as a string, return it straight away.
    if(is_string($section)) {
      return $section;
    }
    if(!is_object($section)
    || !($section instanceof $this->section_class)
    || get_class($section) != $this->section_class) {
      return false;
    }
    return $section->name();
  }

  /**
   * View Path
   *
   * Return the full path to the view, including theme folder, sub directory
   * and file prefix. Return false if the does not exist.
   *
   * @access public
   * @param  string $view
   * @return string|false
   */
  public function view_path($view) {
    if(!$this->view_exists($view)) {
      return false;
    }
    $file = APP
    . 'themes/'
    . $this->theme
    . $this->subdir
    . $this->prefix
    . $view
    . EXT;
    return $file;
  }

  /**
   * Is Varname
   *
   * @access protected
   * @param  string    $varname
   * @return boolean
   */
  protected function is_varname($varname) {
    if(!is_string($varname)) {
      return false;
    }
    $regex = '/^' . $this->valid_name . '$/';
    $match = preg_match($regex, $varname);
    return $match ? true : false;
  }

  /**
   * Set Theme
   *
   * @access public
   * @param  string $theme
   * @return boolean
   */
  public function set_theme($theme) {
    if(!is_string($theme) || strpos($theme, '/') !== false) {
      return false;
    }
    $path = APP . 'themes/' . $theme;
    if(!is_dir($path)) {
      return false;
    }
    $this->theme = $theme . '/';
  }

  /**
   * Set Subdirectory
   *
   * @access public
   * @param  string  $dir
   * @return boolean
   */
  public function set_dir($dir) {
    if(!is_string($dir) || !is_string($this->theme)) {
      return false;
    }
    $dir = trim($dir, '/');
    $path = APP . 'themes/' . $this->theme . $dir;
    if(!is_dir($path)) {
      return false;
    }
    $this->subdir = $dir;
    return true;
  }

  /**
   * Set File Prefix
   *
   * Just to be arsey, we're only going to allow valid variable names to be
   * prefixes!
   *
   * @access public
   * @param  string  $prefix
   * @return boolean
   */
  public function set_prefix($prefix) {
    if(!$this->is_varname($prefix)) {
      return false;
    }
    $this->prefix = $prefix;
    return true;
  }

  /**
   * Create Sections from Views
   *
   * @access public
   * @param  array  $views
   * @return void
   */
  public function create($views) {
    if(!is_array($views) || !count($views)) {
      return;
    }
    foreach($views as $name => $view) {
      // If the section already exists, there is no point creating a new one;
      // you'd lose all your data!
      if($this->section_exists($name)) {
        continue;
      }
      // Shortcut for lazy people, if no array key is given, use the view as
      // the name. If something other than a valid string is passed as the key
      // just continue.
      $name = is_int($name) ? $view : $name;
      if(!$this->is_varname($name)) {
        continue;
      }
      // You can't makea section if the view doesn't exist!
      if(!$this->view_exists($view)) {
        continue;
      }
      // All checks have passed, let's create that section!
      $path = APP
      . 'themes/'
      . $this->theme
      . $this->subdir
      . $this->prefix
      . $view
      . EXT;
      $this->sections[$name] = new $this->section_class($name, $path);
      $this->active = $name;
    }
  }

  /**
   * Set Active
   *
   * @access public
   * @param  string|object $section
   * @return boolean
   */
  public function active($section) {
    $section = $this->section_name($section);
    if(!$this->section_exists($section)) {
      return false;
    }
    $this->active = $section;
    return true;
  }

  /**
   * Get Section
   *
   * Returns the section specified, else returns false. If you stick with the
   * default value, it will return the last activated section.
   *
   * @access public
   * @param  string|object $section
   * @return object
   */
  public function section($section = true) {
    if(isset($this->sections[$section])) {
      return $this->sections[$section];
    }
    if($section === true && isset($this->sections[$this->active])) {
      return $this->sections[$this->active];
    }
    // If we can't find either, return nothing (void).
    return;
  }

  /**
   * Link Sections
   *
   * @access public
   * @param  array $links
   * @return void
   */
  public function link($links) {

    if(!is_array($links)) {
      return;
    }
    foreach($links as $section => $imports) {
      $section = $this->section_name($section);
      if(!$this->section_exists($section)) {
        continue;
      }
      // Make sure that it is an array!
      $imports = (array) $imports;
      if(!isset($this->links[$section]) || !is_array($this->links[$section])) {
        $this->links[$section] = array();
      }
      // Loop through the imports, making sure each one exists.
      foreach($imports as $import) {
        if(!$this->section_exists($import)
        || in_array($import, $this->links[$section])) {
          continue;
        }
        $this->links[$section][] = $import;
      }
    }
  }

  /**
   * Group Sections
   *
   * @access public
   * @param  string  $name
   * @param  array   $sections
   * @return boolean
   */
  public function group($name, $sections) {
    if($this->section_exists($name)
    || !$this->is_varname($name)
    || !is_array($sections)) {
      return false;
    }
    $this->sections[$name] = array();
    foreach($sections as $section) {
      $section = $this->section_name($section);
      if($this->section_exists($section) && is_object($this->section($section))) {
        $this->sections[$name][] = $section;
      }
    }
    return true;
  }

  /**
   * Join Group
   *
   * Join the passed sections with the already existing group
   *
   * @access public
   * @param string $group
   * @param string|array $sections
   * @return boolean
   */
  public function join($group, $sections) {
    if(is_string($sections)) {
      $sections = array($sections);
    }
    if(!is_string($group)
    || !$this->is_varname($group)
    || !$this->section_exists($group)
    || !is_array($this->sections[$group])
    || !is_array($sections)
    ) {
      return false;
    }
    foreach($sections as $section) {
      if($this->section_exists($section)) {
        $this->sections[$group][] = $section;
      }
    }
    return true;
  }

  /**
   * Combine Sections
   *
   * @access protected
   * @return boolean
   */
  protected function combine($section, $limit = 1) {

    // Need to go away and think about this method. Rushing into it ends up
    // with me thinking of something that I should of done differently 5
    // minutes ago.

    if(!$this->section_exists($section)
    || !is_int($limit)) {
      return false;
    }

    if(is_array($this->sections[$section])) {
      $sections = $this->sections[$section];
    }
    elseif(is_object($this->section($section))) {
      $sections = array($this->section($section)->name());
    }
    else {
      return false;
    }

    $content = $this->concat_sections($sections, $limit);

    if(!isset($this->links[$section]) || !is_array($this->links[$section])) {
      return $content;
    }

    foreach($this->links[$section] as $link) {
      $link = $this->section_name($link);
      if(!$this->section_exists($link)
      || !preg_match('/' . $this->valid_name . '/', $link)) {
        continue;
      }

      // Some fancy PCRE to find the pseudo-link tag.
      $regex = '/'
      . preg_quote('<!--{', '/')
      . '(' . preg_quote($link, '/') . ')'
      . '(\[([0-9]+)?\])?'
      . preg_quote('}-->', '/')
      . '/';
      if($preg = preg_match_all($regex, $content, $matches, PREG_SET_ORDER)) {
        foreach($matches as $match) {
          // Determine how many interations are needed if the next section is
          // actually a group.
          // "" = 1, "[]" = 0 (unlimited), "[n]" = n.
          if(isset($match[2])) {
            if(isset($match[3])) {
              $n = (int) $match[3];
            }
            else {
              $n = 0;
            }
          }
          else {
            $n = 1;
          }

          $content = str_replace(
          $match[0],
          $this->combine($link, $n),
          $content
          );
        }
      }

    }

    return $content;
  }

  /**
   * Concatenate Sections
   *
   * @access protected
   * @param  array        $sections
   * @param  integer      $max
   * @return string|false
   */
  protected function concat_sections($sections, $max = 0) {
    // If they have provided us with just one section object, turn it into an
    // array.
    if(is_object($sections) && $sections instanceof $this->section_class) {
      $sections = array($sections);
    }
    if(!is_int($max) || !is_array($sections) || !count($sections)) {
      return false;
    }
    if($max === 0) {
      $max = count($sections);
    }
    $content = '';
    reset($sections);
    for($i = 0; $i < $max; $i++) {
      // Grab the array element the pointer is currently at.
      $section = $this->section_name(current($sections));
      if(!$this->section_exists($section) || !is_object($this->section($section))) {
        continue;
      }
      $content .= $this->section($section)->content();
      // Move the array pointer along one.
      next($sections);
    }
    return $content;
  }

  /**
   * Load Section Tree
   *
   * @access public
   * @param  string|object $section
   * @return boolean
   */
  public function load($section) {
    $section = $this->section_name($section);
    // You are required to pass a valid section, groups are not allowed.
    if(!$this->section_exists($section)
    || !is_object($this->section($section))) {
      return false;
    }
    $rendered = $this->combine($section);
    if(!is_string($rendered) || $rendered == '') {
      return false;
    }
    $E =& getInstance();
    if(!isset($E->output)) {
      echo $rendered;
    }
    else {
      $E->output->append($rendered);
    }
    return true;
  }

  /**
   * Load Config
   *
   * Load the template settings from a pre-defined config file.
   *
   * @access public
   * @param string $config_file
   * @return boolean
   */
  public function load_config($config) {
    // If the config file is not a string, we won't be able to load it anyway.
    if(!is_string($config)) {
      return false;
    }
    // Grab the config array.
    $config = c($config, 'template');
    if(!is_array($config)) {
      // Config file doesn't exist...
      return false;
    }
    // Set the simple strings.
    isset($config['theme'])  && $this->set_theme($config['theme']);
    isset($config['dir'])    && $this->set_dir($config['dir']);
    isset($config['prefix']) && $this->set_prefix($config['prefix']);
    // Now to iterate over the rest...
  }

}

//------------------------------------------------------------------------------

/**
 * Eventing Template Library Section
 *
 * A class for creating section (not group) objects for the Template library.
 *
 * @package     Eventing
 * @subpackage  Libraries
 * @category    template
 * @author      Alexander Baldwin
 * @link        http://github.com/mynameiszanders/eventing
 */
class E_template_section {

  public    $name,
  $path;
  protected $data = array(),
  $E;

  // TODO: Rewrite the class to use the format:
  //       new E_template_section($name, $path);
  /**
   * Constructor Function
   *
   * Defines $view and $path, and links to Eventing's super object.
   *
   * @param string $view
   * @param  $path
   * @return void
   */
  public function __construct($name, $path) {
    $this->name = $name;
    $this->path = is_string($path) ? $path : '';
    $this->E =& getInstance();
  }

  /**
   * Variable Name Checker
   *
   * Checks a string to see if it can be used as a valid variable name.
   *
   * @access private
   * @param string $varname
   * @return boolean
   */
  protected function is_varname($varname) {
    return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $varname);
  }

  /**
   * Section Name
   *
   * Returns the name of the section.
   *
   * @access public
   * @return string
   */
  public function name() {
    return $this->name;
  }

  /**
   * Section Content
   *
   * Returns a views content, with data passed to it.
   *
   * @access public
   * @return string
   */
  public function content() {
    return $this->E->load->view($this->path, $this->data);
  }

  /**
   * Add Data
   *
   * Add data to be included in the view.
   *
   * @access public
   * @return boolean
   */
  public function add() {
    $args = func_get_args();
    array_unshift($args, null);
    unset($args[0]);
    switch(count($args)) {
      case 1:
        if(!is_array($args[1])) {
          return false;
        }
        break;
      case 2:
        $args[1] = array($args[1] => $args[2]);
        break;
      default:
        // Incorrect number of arguments!
        return false;
        break;
    }
    foreach($args[1] as $varname => $vardata)
    {
      if(!is_string($varname) || !$this->is_varname($varname)) {
        continue;
      }
      $this->data[$varname] = $vardata;
    }
  }

}
