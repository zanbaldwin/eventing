<?php

/**
 * Eventing Framework Core Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage core
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  namespace Eventing\Library;

  if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

  /**
   *
   */
  class core extends library {

    protected $models = array(),
              $modules = array();

    protected function __construct() {}

    /**
     * Use Model
     *
     * Return an instance of a model object.
     *
     * @access public
     * @param string $model
     * @return object|void
     */
    public function model($model) {
      // So we can use models that have been loaded through "$this->load->model($model_name);"
      // Use like: "$this->model($model_name)->get_user_data();"
      if(isset($this->models[$model])) {
        return $this->models[$model];
      }
    }

    /**
     * Use Module
     *
     * Return an instance of a module object.
     *
     * @access public
     * @param string $module
     * @return object|void
     */
    public function module($module) {
      if(isset($this->modules[$module])) {
        return $this->modules[$module];
      }
    }

  }
