<?php

/**
 * Eventing Framework URI Library
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * Simple library for providing HTTP Digest Authentication.
 * Disclaimer/Note: Digest authentication is NO substitute for SSL. People can still log in as you by sniffing HTTP traffic. Be warned!
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage uri
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class E_digest
{

	protected $user = false, $hash = false, $data = false, $valid = false;
	// Whatever happens, we need a realm declared. Put this as default.
	protected $realm = 'Eventing';

	/**
	 * Digest Constructor
	 *
	 * Checks to see if the end-user has already provided login details.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// First things first, if the framework user has specified a default realm to use,
		// we should use it.
		if(is_string(c('default_realm')))
		{
			$this->realm = c('default_realm');
		}
		// Grab all the data we can from PHP_AUTH_DIGEST.
		// Two things could be happening here; the user might or might not be logged in right now.
		if(empty($_SERVER['PHP_AUTH_DIGEST']))
		{
			// Nope, the user has not logged in yet. Do nothing.
			return;
		}
		$this->data = $this->_parse($_SERVER['PHP_AUTH_DIGEST']);
		if(is_array($this->data) && isset($this->data['username']))
		{
			$this->user = $this->data['username'];
		}
	}

	/**
	 * Parse HTTP Digest data
	 */
	protected function _parse($str)
	{
		// Protect against missing data.
		$required = array('username', 'realm', 'nonce', 'uri', 'response', 'opaque', 'qop', 'nc', 'cnonce');
		$data = array();
		$keys = implode('|', $required);
		$regex = '#(' . $keys . ')=(?:([\'"])([^\2]*?)\2|([^\s,]+))#';
		//$regex = '#(' . $keys . ')=([\'"])([^\2]*)\2,?#';
		preg_match_all($regex, $str, $matches, PREG_SET_ORDER);
		foreach($matches as $match)
		{
			$data[$match[1]] = $match[3] ? $match[3] : $match[4];
			$key = array_search($match[1], $required);
			if($key !== false)
			{
				unset($required[$key]);
			}
		}
		return $required ? false : $data;
	}

	/**
	 * Set Realm
	 */
	public function realm($realm)
	{
		if(!is_string($realm))
		{
			return false;
		}
		$this->realm = $realm;
		return true;
	}

	/**
	 * Get Username
	 *
	 * Return the supplied username, or false if nothing has been submitted yet.
	 *
	 * @return string|false;
	 */
	public function user()
	{
		return $this->user;
	}

	/**
	 * Set Password
	 *
	 * If you know the password of the user who has just entered their username, you can set it here.
	 * If you call this function after you call E_digest::hash(), it will overwrite the hash you set.
	 *
	 * @param string $password
	 * @return boolean
	 */
	public function password($password)
	{
		if(!is_string($password))
		{
			return false;
		}
		return $this->hash(md5($this->username . ':' . $this->realm . ':' . $password));
	}

	/**
	 * Set Hash
	 *
	 * If you don't save your password in plain text (well done you!), you can pass an MD5 hash
	 * consisting of md5("username:realm:password"). The downside to this is that if you change
	 * your realm, or the user changes their username, all your hashes will become obsolete!
	 *
	 * @param string $hash
	 * @return boolean
	 */
	public function hash($hash)
	{
		if(!is_string($hash))
		{
			return false;
		}
		$this->hash = $hash;
		return true;
	}

	/**
	 * Validate Username and Password
	 */
	public function validate()
	{
		if(!is_string($this->hash))
		{
			return false;
		}
		$part1 = $this->hash;
		$part2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $this->data['uri']);
		$valid = md5(
		$part1
		. ':' . $this->data['nonce']
		. ':' . $this->data['nc']
		. ':' . $this->data['cnonce']
		. ':' . $this->data['qop']
		. ':'
		. $part2
		);
		if($this->data['response'] != $valid)
		{
			return false;
		}
		$this->valid = true;
		return true;
	}

	/**
	 * Initialize
	 *
	 * @return boolean
	 */
	public function init()
	{
		if(headers_sent())
		{
			return false;
		}
		// If the user isn't currently authenticated yet, sent the appropriate headers.
		$digest_header = 'WWW-Authenticate: Digest '
		. 'realm="' . $this->realm . '",'
		. 'qop="auth",'
		. 'nouce="' . uniqid() . '",'
		. 'opaque="' . md5($this->realm) . '"';
		header('HTTP/1.1 401 Unauthorized', true, 401);
		header($digest_header);
		return true;
	}

	/**
	 * Is User Valid
	 *
	 * Returns a boolean on whether or not the user has been successfully validated.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->valid;
	}

}
