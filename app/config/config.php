<?php

/**
 * Eventing Framework
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to
 * rapidly build applications.
 * All about the config file...
 *
 * @category   Eventing
 * @package    Application
 * @subpackage config
 * @author     Alexander Baldwin <zanders@zafr.net>
 * @copyright  2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  $config = array(

    // Generic System Properties:
    'default_charset' => 'utf-8',
    'encryption_key' => 'lolomgwtf',
    'default_timezone' => 'GMT',
    'error_types_trigger' => 17234,

    // Date Preferences:
    'time_short' => 'G:i',
    'date_short' => 'j/n/y',
    'time_long' => 'g:i a',
    'date_long' => 'l jS, F Y',

    // Load::View and Template Class Settings:
    'default_theme' => 'default',

    // Database Preferences:
    'db_host' => 'localhost',
    'db_user' => 'eventing_app',
    'db_pass' => 'eventing_iscoollike',
    'db_name' => 'eventing_db',

    // HTTP Digest Authentication
    'default_realm' => 'Eventing',

    // For the anchor - a() - function.
    'redirect_refresh' => false,
    'save_gets' => array('l'),
    'cookie_name' => 'E_cookie',
    'mod_rewrite' => false,

    // Input Class Settings:
    // '^(get|post):[a-zA-Z0-9_-]+$'
    'xss_filter_bypass' => 'post:_some_super-duper_unobvious_key',
    'xss_replace_text' => '[xss-removed]',
    'xss_allowed_html' => array(
      'strong', 'b', 'em', 'i', 'h2', 'h3', 'h4', 'p', 'span', 'img', 'code',
      'blockquote', 'q', 'hr'
    ),

    // Router Class Settings:
    'default_suffix' => '/',
    'file_over_folder' => false,
    'default_controller' => 'home',
    'default_method' => 'index',
    'remap_method' => '_remap',

    // To enable usage of Zend Libraries within the framework, set this to where
    // the Zend Library folder is located. For example, if the Zend Loader class
    // is located at "/home/username/public_html/Zend/Loader.php", set this to
    // "/home/username/public_html". This does not need to be absolute, just set
    // relative to the $main_file. By default, this is set to the SYS path.
    'zend_container_path' => SYS,
    'zend_autoload' => true,

  );
