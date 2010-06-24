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
  * @copyright  (c) 2010 Alexander Baldwin
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://github.com/mynameiszanders/eventing
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

  class E_template {
  	
  	protected $links = array(),
  	          $sections = array(),
  	          $theme = false,
  	          $subdir = '',
  	          $prefix = '',
  	          $active = false,
  	          // The following is hard-coded, this should not be changed.
  	          $section_class = 'E_template_section',
  	          $valid_name = '[a-zA-Z_][a-zA-Z0-9_]*';

    public function __construct() {
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
    	if (!is_string($view) || !is_string($this->theme)) {
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
     * Section Exists
     * 
     * @access public
     * @param  string  $section
     * @return boolean
     */
    public function section_exists($section) {
    	if (!is_string($section)) {
    		return false;
    	}
    	return isset($this->sections[$section]);
    }
    
    /**
     * Section Name
     * 
     * @access protected
     * @param  object    $section
     * @return string|false
     */
    protected function section_name($section) {
    	if (!is_object($section)
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
    	if (!$this->view_exists($view)) {
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
    	if (!is_string($varname)) {
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
    	if (!is_string($theme) || strpos($theme, '/') !== false) {
    		return false;
    	}
    	$path = APP . 'themes/' . $theme;
    	if (!is_dir($path)) {
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
    	if (!is_string($dir) || !is_string($this->theme)) {
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
    	if (!$this->is_varname($prefix)) {
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
    		if($this->section_exists($view)) {
    			continue;
    		}
    		// Shortcut for lazy people, if no array key is given, use the view as
    		// the name. If something other than a valid string is passed as the key
    		// just continue.
    		if(!is_string($name) || !is_int($name) || !$this->is_varname($name)) {
    			continue;
    		}
    		$name = is_int($name) ? $view : $name;
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
        $this->sections[$name] = new $this->section_name($name, $path);
        $this->active = $name;
    	}
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
    	if (isset($this->sections[$section])) {
    		return $this->sections[$section];
    	}
    	if ($section === true && isset($this->sections[$this->active])) {
    		return $this->sections[$this->active];
    	}
    	return false;
    }
    
    /**
     * Link Sections
     * 
     * @access public
     * @param  array $links
     * @return void
     */
    public function link($links) {

    	// The following is the old methods code.
    	// TODO: Rewrite function! It isn't effiencient! Too many parameters!
    	
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
        if (!$this->section_exists($section)) {
          continue;
        }
        // Make sure the parent section has a link array.
        if (!is_array($this->links[$section])) {
          $this->links[$section] = array();
        }
        // Right, lets loop through the imports array we created.
        foreach ($imports as $import) {
          $import = $this->_section_name($import);
          if ($this->section_exists($import)) {
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
     * @access protected
     * @return boolean
     */
    protected function combine($section) {

    	// The following is the old method.
    	// Need to rewrite for <!--{link[n]}--> groups.
    	
    	      if (!$this->section_exists($start_section)) {
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
     * Group Sections
     * 
     * @access public
     * @param  string  $name
     * @param  array   $sections
     * @return boolean
     */
    public function group($name, $sections) {
    	
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
     * Load Section Tree
     * 
     * @access public
     * @param  string|object $section
     * @return boolean
     */
    public function load($section) {
      if(!$this->section_exists($section)) {
        return false;
      }
      $rendered = $this->combine($section);
      if(!is_string($rendered)) {
        return false;
      }
    	$E =& get_instance();
    	if(!isset($E->output)) {
    	  echo $rendered;
    	}
    	else {
    	  $E->output->append($rendered);
    	}
    	return true;
    }
    
  }
  
  /** --------------------------------------------------------------------------
   * Template Class
   *
   * A simple library for Eventing, building over views to create links to load
   * multiple view with one call.
   */
  class E_template1 {

    private $links = array(),
            $sections = array(),
            $folder = '',
            $prefix = '',
            $active = '',
            $theme = false,
            $E;

    /**
     * Link Sections
     *
     * Create symbolic links between two sections, so when the parent section is
     * loaded,  all sections linked will
     * be included in the final output.
     *
     * @access public
     * @params strings|array
     * @return void|boolean
     */


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
   * @link        http://github.com/mynameiszanders/eventing
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
    protected function is_varname($varname) {
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
        if (!is_string($varname) || !$this->is_varname($varname)) {
          continue;
        }
        $this->data[$varname] = $vardata;
      }
    }

  }

  /**
   * Eventing Template Group
   */
  class E_template_group {

    protected $sections = array(),
              $name = false;

  }
