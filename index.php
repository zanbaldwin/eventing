<?php

 /**
  * Eventing Framework
  *
  * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
  * http://eventing.zafr.net/
  * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
  * This file is the file called by the server when a request is made to your application.
  * Many concepts and ideas of this framework are inspired from CodeIgniter.
  * All code is rewritten from scratch, but please see their license:
  * http://codeigniter.com/user_guide/license.html
  *
  * @category   Eventing
  * @package    Core
  * @subpackage index
  * @author     Alexander Baldwin
  * @copyright  (c) 2009 Alexander Baldwin
  * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
  * @version    v0.4
  * @link       http://eventing.zafr.net/source/index.php
  * @since      v0.1
  *
  * I seem to have my Git settings completely wrong (fresh install of Linux).
  * Let's hope that pushing this file won't completely overwrite everything!
 */

 # =================================================================================================================== #
 # Please edit the following settings depending on your folder structure and preferences.                              #
 # If you are going to keep the default values here, you might as well remove this array.                              #
 # All other editable system settings can be found in "<system_folder>/config/".                                       #
 # =================================================================================================================== #

    //*
    $user_config = array(
        'system_folder'  => 'system',
        'default_app'    => 'app',
        'content_folder' => 'public',
        # Can be "ini" or "array". Defaults to "array".
        'config_type'    => 'array'
    );
    /**/

 # =================================================================================================================== #
 # End of user configurable settings.                                                                                  #
 # =================================================================================================================== #

    // Killing two birds with one stone. Define a constant for other files to check that they're not being called
    // independently, and to set the core benchmark. The E_FRAMEWORK constant is REQUIRED!
    defined('E_FRAMEWORK') || define('E_FRAMEWORK', microtime());
    // However, the version number doesn't really have any use in the framework at all. You may discard it if you want.
    defined('E_VERSION') || define('E_VERSION', '0.2b');

    // Right! Let's get this party started!
    $main_file = __FILE__;
    $init = isset($user_config['system_folder'])
          ? $user_config['system_folder'] . '/init.php'
          : 'system/init.php';
    $init = realpath($init);

    // For obvious reasons, let's check if the initialisation script exists... If it doesn't, we'll give the user a big
    // slap in the face of epic disappointment by calling the E_Core_Error() function. SAD TIMES!
    file_exists($init) || exit('Initialisation script not found.');
    // Great... Everything working so far. What a boring life we live!
    require_once $init;
