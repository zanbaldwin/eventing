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
  * @subpackage template
  * @author     Alexander Baldwin
  * @copyright  (c) 2009 Alexander Baldwin
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://eventing.zafr.net/source/system/libraries/template.php
  * @since      v0.1
  * 
  * TODO: Implement <!--{link[]}-->
  *       This may take quite a lot of rewriting!
  * TODO: Decide whether we implement <!--{link[$n]}-->, where $n is max entries
 */

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  /**
   * Template Class
   *
   * A simple library for Eventing, building over views to create links to load
   * multiple view with one call.
   */
  class E_Template {

    private $links = array(),
            $sections = array(),
            $folder = '',
            $prefix = '',
            $_last_created = '', $E;

    /**
     * E_Template Constructor Function
     *
     * @return void
     */
    public function __construct() {
      // Load the Eventing super object.
      $this->E =& get_instance();
    }

    /**
     * View Exists
     *
     * Determines whether a particular view exists or not.
     * You can state whether you want the preceding separators present or not.
     *
     * @access public
     * @param  string $view
     * @param  boolean $global
     * @return boolean
     */
    private function view_exists($view, $global = false) {
      $theme = is_string($theme) ? $theme : c('default_theme');
      $view = $global ? $view : $this->_view_path($view);
      return file_exists(APP . 'themes/' . $theme . '/' . $view . EXT);
    }

    /**
     * Section Exists
     *
     * Determines whether a particular section exists or not.
     *
     * @access private
     * @param  string $section_name
     * @return boolean
     */
    private function _section_exists($section_name) {
      if (!is_string($section_name)) {
        return false;
      }
      return isset($this->sections[$section_name]);
    }

    /**
     * Section Name
     *
     * Returns the section name if a CI_Template_Section is passed.
     *
     * @access private
     * @param  string|object $section
     * @return false|string
     */
    private function _section_name($section)
    {
      if (is_string($section)) {
        return $section;
      }
      if (is_object($section) && get_class($section) == 'E_Template_Section') {
        return $section->view;
      }
      else {
        return false;
      }
    }

    /**
     * View Path
     *
     * Takes a view and prepends the folder and suffix to it to create the path
     * for CI to load.
     *
     * @access private
     * @param  string $view
     * @return string
     */
    private function _view_path($view) {
      return $this->folder . $this->prefix . $view;
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
    private function _check_varname($varname) {
      return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $varname);
    }

    /**
     * Set Folder
     *
     * Sets the sub-directory in which to look for views. Use to split your
     * views into themes. Returns true on success, and false on failure (eg. the
     * folder you specify does not exist).
     *
     * @param string $folder
     * @return boolean
     */
    public function set_folder($folder, $theme = false) {
      $theme = is_string($theme) ? $theme : c('default_theme');
      $folder = trim($folder, '/') . '/';
      if (is_dir(APP . 'themes/' . $theme . '/' . $folder)) {
        $this->folder = $folder;
        return true;
      }
      return false;
    }

    /**
     * Set Prefix
     *
     * Set the prefix in which to prepend to view paths. Use to split your views
     * into themes. Always returns true.
     *
     * @param  string $prefix
     * @return true
     */
    public function set_prefix($prefix) {
      $this->prefix = $prefix;
      return true;
    }

    /**
     * Create Section
     *
     * Takes any amount of arguments
     *
     * @param array $views
     * @return CI_Template_Section|void
     */
    public function create($views)
    {
      if (is_string($views)) {
        $views = array($views);
      }
      foreach ($views as $name => $view) {
        // If the section already exists, there is no point creating a new one;
        // you'd lose all your data!
        if ($this->_section_exists($view)) {
          continue;
        }
        // Shortcut for those lazy people, if no array key is given, use the
        // view as the name.
        if (is_int($name) && $name >= 0) {
          $name = $view;
        }
        // You can't make a section if the view doesn't exist!
        if (!$this->_check_varname($name) || !$this->view_exists($view)) {
          continue;
        }
        // All checks have passed, let's create that section!
        $path = $this->folder . $this->prefix;
        $this->sections[$name] = new E_Template_Section($view, $path);
        $this->_last_created = $name;
        if (count($views) == 1) {
          return $this->sections[$name];
        }
      }
    }

    /**
     * Return Section
     *
     * Long descrip...
     *
     * @access public
     * @param  string $section_name
     * @return CI_Template_Section|void
     */
    public function section($section_name = '')
    {
      if ($this->_section_exists($section_name)) {
        return $this->sections[$section_name];
      }
      elseif ($section_name == '' && $this->_section_exists($this->_last_created)) {
        return $this->sections[$this->_last_created];
      }
    }

    /**
     * Link Sections
     *
     * Create symbolic links between two sections, so when the parent section is loaded,  all sections linked will
     * be included in the final output.
     *
     * @access public
     * @params strings|array
     * @return void|boolean
     */
    public function link()
    {
      $args = func_get_args();
      switch (func_num_args())
      {
        case 1:
          if (!is_array($args[0])) {
            return false;
          }
          $args = $args[0];
          break;
        case 2:
          if (!is_string($args[0]) || !is_string($args[1])) {
            return false;
          }
          $args = array($args[0] => array($args[1]));
          break;
        default:
          return false;
          break;
      }
      foreach ($args as $section => $imports) {
        // Make sure we have a section name and not a section object.
        $section = $this->_section_name($section);
        // We can't use a foreach loop if it's not an array!
        $imports = is_array($imports) ? $imports : array($imports);
        // If the parent section does not exist, we can't link it!
        if (!$this->_section_exists($section)) {
          continue;
        }
        // Make sure the parent section has a link array.
        if (!is_array($this->links[$section])) {
          $this->links[$section] = array();
        }
        // Right, lets loop through the imports array we created.
        foreach ($imports as $import) {
          $import = $this->_section_name($import);
          if ($this->_section_exists($import)) {
            // Make sure we haven't linked the two together already.
            if (!in_array($import, $this->links[$section])) {
              // Create a symbolic link between the two.
              $this->links[$section][] = $import;
            }
          }
        }
      }
    }

    /**
     * Combine Sections
     *
     * Combines all the sections which have symbolic links between them by the link() function.
     *
     * @access private
     * @param string $start_section
     * @return string
     */
    private function _link($start_section)
    {
      if (!$this->_section_exists($start_section)) {
        return false;
      }
      $content = $this->section($start_section)->content();
      if (isset($this->links[$start_section])) {
        foreach ($this->links[$start_section] as $link) {
          $content = str_replace('<!--{'.$link.'}-->', $this->_link($link), $content);
        }
      }
      return $content;
    }

    /**
     * Swap Active Section
     *
     * Long descrip...
     *
     * @param string $section_name
     * @return boolean
     */
    public function swap($section_name)
    {
      if ($this->_section_exists($section_name)) {
        $this->_last_created = $section_name;
        return true;
      }
      return false;
    }

    /**
     * Load Section
     *
     * Long descrip...
     *
     * @param  string $section_name
     * @return void
     */
    public function load($section_name)
    {
      if (!$this->_section_exists($section_name)) {
        return false;
      }
      $content = $this->_link($section_name);
      $this->E->output->append_output($content);
      return true;
    }


  }

//------------------------------------------------------------------------------

  /**
   * Template Sections Class
   *
   * A class for creating section objects for the Template library.
   *
   * @package     Eventing
   * @subpackage  Libraries
   * @category    template
   * @author      Alexander Baldwin
   * @link        http://eventing.zafr.net/source/system/libraries/template.php
   */
  class E_Template_Section {

    public  $view,
            $path;
    private $data = array(),
            $E;

    /**
     * Constructor Function
     *
     * Defines $view and $path, and links to Eventing's super object.
     *
     * @param string $view
     * @param  $path
     * @return void
     */
    public function __construct($view, $path) {
      $this->view = $view;
      $this->path = is_string($path) ? $path : '';
      $this->E =& get_instance();
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
    private function _check_varname($varname) {
      return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $varname);
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
      return $this->E->load->view($this->path . $this->view, $this->data);
    }

    /**
     * Add Data
     *
     * Add data to be included in the view.
     *
     * @return boolean
     */
    public function add() {
      $args = func_get_args();
      array_unshift($args, null);
      unset($args[0]);
      switch (count($args)) {
        case 1:
          if (!is_array($args[1])) {
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
      foreach ($args[1] as $varname => $vardata)
      {
        if (!is_string($varname) || !$this->_check_varname($varname)) {
          continue;
        }
        $this->data[$varname] = $vardata;
      }
    }

  }
