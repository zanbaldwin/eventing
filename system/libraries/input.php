<?php

/**
 * Eventing Framework Input Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage input
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class E_input
{

	private $globals;

	public function __construct()
	{
		// Grab the global variables, add them to this library, then get rid of the originals.
		$this->collect();
		// Get rid of some preset cookies, as these can sometimes cause a problem.
		$this->preset_cookies();
	}

	/**
	 *
	 */
	private function collect()
	{
		$globals = array(
                'get'    => $_GET,
                'post'   => $_POST,
                'cookie' => $_COOKIE,
                'env'    => $_ENV
		);
		if(isset($_SESSION) && is_array($_SESSION))
		{
			$globals['session'] = $_SESSION;
		}
		foreach($globals as $name => &$global)
		{
			$this->globals[$name] = array();
			foreach($global as $var => $val)
			{
				$this->globals[$name][$var] = $val;
			}
		}
		// Set all the global variables to empty arrays, this stops users accessing unfiltered input data.
		$_GET = $_POST = $_COOKIE = $_ENV = $_SESSION = array();
	}

	private function preset_cookies()
	{
		// Get rid of specially treated cookies that might be set by a server or application.
		foreach(array('$Version', '$Path', '$Domain') as $preset)
		{
			unset($this->globals['cookie'][$preset]);

		}
	}

	public function __call($name, $args)
	{
		if(!isset($args[0]) || isset($this->globals[$args[0]]))
		{
			return false;
		}
		$args[1] = isset($args[1]) ? $args[1] : false;
		return isset($this->globals[$name][$args[0]]) ? $this->globals[$name][$args[0]] : $args[1];
	}

}
