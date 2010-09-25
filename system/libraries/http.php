<?php

/**
 * Eventing Framework HTTP Library
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * This library was created after a need to fetch webpages on a PHP application
 * where cURL was not available on the server it resided on. Simple, but
 * effective.
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage HTTP
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

if (!defined('E_FRAMEWORK')) {
	headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
	exit('Direct script access is disallowed.');
}

class E_http
{

	private $requests = array();

	public function fetch($name, $url, $post_data = false, $headers = false) {
		if (isset($this->requests[$name])) {
			// That one already exists. Think of another name... It can't be that
			// hard, surely?
			return false;
		}
		if (!is_string($name) || !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)) {
			// Invalid name. We don't trust you. Go somewhere else with your
			// witchcraft names!
			return false;
		}
		$this->requests[$name] = new E_http_request($url, $post_data, $headers);
		return $this->requests[$name]->valid();
	}

	/**
	 * Return Request Object
	 *
	 * @access public
	 * @return false|E_http_request
	 */
	public function request($request) {
		if (isset($this->requests[$request])) {
			return $this->requests[$request];
		}
		return false;
	}

}

class E_http_request
{

	private $url_host = false,
	$url_path = false,
	$headers = array(),
	$post_data = false,
	$response_headers = false,
	$response_body = false,
	$status_code = 0,
	$status_msg = false,
	$valid = false;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $url
	 * @param string|array $headers
	 * @return void
	 */
	public function __construct($url, $post_data = false, $headers = false) {
		if (!is_string($url)) {
			return false;
		}
		$this->url_host = parse_url($url, PHP_URL_HOST);
		$this->url_path = parse_url($url, PHP_URL_PATH);
		$this->headers = (array) $headers;
		if (is_array($post_data)) {
			$this->post_data = $post_data;
		}
		$this->_fetch();
	}

	/**
	 * Is Valid
	 *
	 * Returns a boolean value of whether the request completed successfully or
	 * not.
	 *
	 * @access public
	 * @return boolean
	 */
	public function valid() {
		return $this->valid;
	}

	/**
	 * Fetch URL
	 *
	 * @access private
	 * @return boolean
	 */
	private function _fetch() {
		// Compile the headers into a string.
		$method = is_array($this->post_data) ? 'POST' : 'GET';
		$headers = "{$method} {$this->url_path} HTTP/1.0\r\n";
		$headers .= implode("\r\n", $this->headers) . "\r\n\r\n";
		$headers .= $this->post_string();
		// Open up a connection to the domain. Timeout of 30 seconds anyone?
		$socket = fsockopen($this->url_host, 80, $error_no, $error_msg, 30);
		if (!$socket) {
			// Technical Error!!!
			return false;
		}
		// Let's send the request headers.
		fwrite($socket, $headers);
		// Read the response the site gives us.
		while (!feof($socket)) {
			$response .= fgets($socket, 4096);
		}
		// Finished getting the response? Great. Get rid of the connection.
		fclose($socket);
		// Right. We have the complete response, but we want to separate the
		// headers from the body.
		$bottleneck = strpos($response, "\r\n\r\n");
		$this->response_headers = xplode("\r\n", substr($response, 0, $bottleneck));
		// The Server always sends the status code and message in the first
		// header, but we'll still check it's there just in case. We like good
		// practice.
		if (substr($this->response_headers[0], 0, 7) == 'HTTP/1.') {
			// Status is there. Split the code and message.
			$this->status_code = (int) substr($this->response_headers[0], 9, 12);
			$this->status_msg = substr($this->response_headers[0], 13);
			// Get rid of the raw header, it is of no use to us.
			// Well, unless you want to extend this library to check which 1.x
			// protocol it used... Let's not go
			// there just yet...
			unset($this->response_headers[0]);
		}
		// Save the HTML code itself.
		$this->response_body = substr($response, $bottleneck + 4);
		$this->valid = true;
		return true;
	}

	protected function post_string() {
		$post_str = '';
		if (!is_array($this->post_data)) {
			return $post_str;
		}
		foreach ($this->post_data as $var => $value) {
			if (!is_string($var)) {
				continue;
			}
			$post_str .= urlencode($var) . '=' . urlencode($value) . '&';
		}
		return substr($post_str, 0, -1);
	}

	/**
	 * Get Header
	 *
	 * Get the nth header, as specified by $num. If the header does not exist,
	 * return the value specified by
	 * $return; defaults to false.
	 *
	 * @access public
	 * @param int $num
	 * @param mixed $return
	 */
	public function header($num, $return = false) {
		return isset($this->response_headers[$num])
		? $this->response_headers[$num]
		: $return;
	}

	/**
	 * Get Headers
	 *
	 * @access public
	 * @return array
	 */
	public function headers() {
		return $this->response_headers;
	}

	/**
	 * Get Body
	 *
	 * @access public
	 * @return string
	 */
	public function body() {
		return $this->response_body;
	}

	/**
	 * Get Status Message
	 *
	 * @access public
	 * @return string
	 */
	public function status() {
		return $this->status_msg;
	}

	/**
	 * Get Status Code
	 *
	 * @access public
	 * @return integer
	 */
	public function code() {
		return $this->status_code;
	}

}
