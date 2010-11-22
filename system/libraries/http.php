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
 * @license    http://www.opensource.org/licenses/mit-license.php MIT/X11 License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

  namespace Eventing\Library;

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class http extends library {

    protected $options = array(
                'timeout' => 30.0,
                'redirects' => 3,
              ),
              $context = false;

    protected function __construct() {}

    /**
     * Timeout
     *
     * Set the timeout in seconds.
     *
     * @access public
     * @param integer $seconds
     * @return boolean
     */
    public function timeout($seconds) {
      if(is_int($seconds) && $seconds > 0) {
        // stream_socket_client() requires the timeout to be a float.
        $this->options['timeout'] = (float) $seconds;
        return true;
      }
      return false;
    }

    /**
     * Redirects
     *
     * Set the maximum amount of redirects to follow.
     *
     * @access public
     * @param integer $max
     * @return boolean
     */
    public function redirects($max) {
      if(is_int($max) && $max >= 0) {
        $this->options['redirects'] = $max;
        return true;
      }
      return false;
    }

    /**
     * Fetch
     *
     * Perform a HTTP request using the specified RESTful method, sending the
     * appropriate data and additional headers, too.
     *
     * @access protected
     * @param string $url
     * @param string $method
     * @param array|false $data
     * @param array headers
     * @return ???
     */
    protected function fetch($url, $method = 'get', $data = array(), $headers = array()) {
      static $methods = array('get', 'post', 'put', 'delete');
      // Are we trying to request a valid HTTP RESTful method?
      if(!is_string($method) || !in_array($method, $methods)) {
        return false;
      }
      // Prepare all the necessary information and content, ready to be pumped
      // through the sockets.
      $prepared = $this->prepare($url, $data, $headers);
      // If it could not be prepared, return from this method; there is nothing
      // more we can do.
      if(!$prepared) {
        return false;
      }
      list($socket, $port, $request) = $prepared;
      $request = strtoupper($method) . $request;
      
      if(!$this->context) {
        $fp = @stream_socket_client($socket, $errno, $error, $this->timeout);
      }
      else {
        $fp = @stream_socket_client($socket, $errno, $error, $this->timeout, STREAM_CLIENT_CONNECT, $this->context);
      }
      
      if(!$fp) {
        return false;
      }
      fwrite($fp, $request);
      // Fetch response. Due to PHP bugs like http://bugs.php.net/bug.php?id=43782
      // and http://bugs.php.net/bug.php?id=46049 we can't rely on feof(), but
      // instead must invoke stream_get_meta_data() each iteration.
      $info = stream_get_meta_data($fp);
      $alive = !$info['eof'] && !$info['timed_out'];
      $response = '';

      while ($alive) {
        $chunk = fread($fp, 1024);
        $response .= $chunk;
        $info = stream_get_meta_data($fp);
        $alive = !$info['eof'] && !$info['timed_out'] && $chunk;
      }
      fclose($fp);
      
      return $response;
    }

    /**
     * Prepare Request
     *
     * Prepare a request, ready to be sent through the appropriate sockets.
     *
     * @access protected
     * @param string $url
     * @param array|false $data
     * @param array|false $headers
     * @return false|array
     */
    protected function prepare($url, $data, $user_headers) {
      // Filter the URL, making sure we have a valid absolute (HTTP scheme
      // required) URL.
      $url = filter_var(
        $url,
        FILTER_VALIDATE_URL,
        FILTER_FLAG_SCHEME_REQUIRED
      );
      if(!$url) {
        return false;
      }
      // Parse the URL into its separate parts.
      $url = parse_url($url);
      // If the user is specifying a HTTP scheme other than the HyperText
      // Transfer Protocol, return from the method, as these are the only ones
      // we support. Please note that Secure HTTP only works if PHP has been
      // compiled with OpenSSL support.
      if($url['scheme'] != 'http' && $url['scheme'] != 'https') {
        return false;
      }
      $parts = array(
        // Because we are using sockets, we will use the low-level schemas for
        // transferring data.
        'scheme' => $url['scheme'] == 'https' ? 'ssl' : 'tcp',
        'host' => $url['host'],
        'port' => isset($url['port'])
                // If the port has been specified, set it as a string, to that
                // we know to include it in the headers.
                ? (string) $url['port']
                // If the port has not been specified, fall back to the
                // defaults, specifying them as integers, so that we know not to
                // include them in the headers.
                : ($url['scheme'] == 'https' ? 443 : 80),
        // If the path has not been specified, fall back to the web root.
        'path' => isset($url['path']) ? $url['path'] : '/',
      );
      if(isset($url['query'])) {
        $parts['path'] .= '?' . $url['query'];
      }

      $content = is_array($data)
               ? http_build_query($data, '', '&')
               : '';

      // Compile the headers.
      $headers = array();
      // The Host header is very important and is REQUIRED. If a non-standard
      // port is used, make sure that is appended the the host, too.
      $headers['Host'] = $parts['host'];
      if(is_string($parts['port'])) {
        $headers['Host'] .= ':' . $parts['port'];
      }
      if($content) {
        $headers['Content-Length'] = strlen($content);
      }
      $headers['User-Agent'] = 'Eventing HTTP Library '
                             . '(+http://github.com/mynameiszanders/eventing)';
      if(isset($url['user']) && $url['user']) {
        $auth_str = $url['user'];
        if(isset($url['pass']) && $url['pass']) {
          $auth_str .= ':' . $url['pass'];
        }
        $headers['Authorization'] = 'Basic ' . base64_encode($auth_str);
      }
      // Add the additional headers that the user has specified, making sure
      // that they do not overwrite the Host header. That's rather important.
      if(is_array($user_headers)) {
        foreach($user_headers as $uh => $uhdata) {
          if(is_string($uh) && strtolower($uh) != 'host') {
            $headers[$uh] = trim($uhdata);
          }
        }
      }
      // Compile the headers and content into a string ready to be sent.
      $compiled = ' ' . $parts['path'] . " HTTP/1.1\r\n";
      foreach($headers as $header => $header_data) {
        $compiled .= $header . ': ' . $header_data . "\r\n";
      }
      $compiled .= "\r\n" . $content;
      $socket = $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port'];
      // Return the Socket URL, the port to connect to, and the compiled request
      // string.
      return array($socket, (int) $parts['port'], $compiled);
    }

    /**
     * GET Request
     *
     * Perform a GET HTTP request on a URL with the specified data and
     * additional headers.
     *
     * @access public
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return object|false
     */
    public function get($url, $headers = array()) {
      return $this->fetch($url, 'get', false, $headers);
    }

    /**
     * GET Request
     *
     * Perform a GET HTTP request on a URL with the specified data and
     * additional headers.
     *
     * @access public
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return object|false
     */
    public function post($url, $data = array(), $headers = array()) {
      return $this->fetch($url, 'post', $data, $headers);
    }

    /**
     * GET Request
     *
     * Perform a GET HTTP request on a URL with the specified data and
     * additional headers.
     *
     * @access public
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return object|false
     */
    public function put($url, $data = array(), $headers = array()) {
      return $this->fetch($url, 'put', $data, $headers);
    }

    /**
     * GET Request
     *
     * Perform a GET HTTP request on a URL with the specified data and
     * additional headers.
     *
     * @access public
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return object|false
     */
    public function delete($url, $headers = array()) {
      return $this->fetch($url, 'delete', false, $headers);
    }

    /**
     * Parse Result
     *
     */
    protected function result() {}

  }










function drupal_http_request($url, array $options = array()) {
  $result = new stdClass();

  // Parse the URL and make sure we can handle the schema.
  $uri = @parse_url($url);

  if ($uri == FALSE) {
    $result->error = 'unable to parse URL';
    $result->code = -1001;
    return $result;
  }

  if (!isset($uri['scheme'])) {
    $result->error = 'missing schema';
    $result->code = -1002;
    return $result;
  }

  timer_start(__FUNCTION__);

  // Merge the default options.
  $options += array(
    'headers' => array(), 
    'method' => 'GET', 
    'data' => NULL, 
    'max_redirects' => 3, 
    'timeout' => 30.0, 
    'context' => NULL,
  );
  // stream_socket_client() requires timeout to be a float.
  $options['timeout'] = (float) $options['timeout'];

  switch ($uri['scheme']) {
    case 'http':
    case 'feed':
      $port = isset($uri['port']) ? $uri['port'] : 80;
      $socket = 'tcp://' . $uri['host'] . ':' . $port;
      // RFC 2616: "non-standard ports MUST, default ports MAY be included".
      // We don't add the standard port to prevent from breaking rewrite rules
      // checking the host that do not take into account the port number.
      $options['headers']['Host'] = $uri['host'] . ($port != 80 ? ':' . $port : '');
      break;
    case 'https':
      // Note: Only works when PHP is compiled with OpenSSL support.
      $port = isset($uri['port']) ? $uri['port'] : 443;
      $socket = 'ssl://' . $uri['host'] . ':' . $port;
      $options['headers']['Host'] = $uri['host'] . ($port != 443 ? ':' . $port : '');
      break;
    default:
      $result->error = 'invalid schema ' . $uri['scheme'];
      $result->code = -1003;
      return $result;
  }

  if (empty($options['context'])) {
    $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout']);
  }
  else {
    // Create a stream with context. Allows verification of a SSL certificate.
    $fp = @stream_socket_client($socket, $errno, $errstr, $options['timeout'], STREAM_CLIENT_CONNECT, $options['context']);
  }

  // Make sure the socket opened properly.
  if (!$fp) {
    // When a network error occurs, we use a negative number so it does not
    // clash with the HTTP status codes.
    $result->code = -$errno;
    $result->error = trim($errstr) ? trim($errstr) : t('Error opening socket @socket', array('@socket' => $socket));

    // Mark that this request failed. This will trigger a check of the web
    // server's ability to make outgoing HTTP requests the next time that
    // requirements checking is performed.
    // See system_requirements()
    variable_set('drupal_http_request_fails', TRUE);

    return $result;
  }

  // Construct the path to act on.
  $path = isset($uri['path']) ? $uri['path'] : '/';
  if (isset($uri['query'])) {
    $path .= '?' . $uri['query'];
  }

  // Merge the default headers.
  $options['headers'] += array(
    'User-Agent' => 'Drupal (+http://drupal.org/)',
  );

  // Only add Content-Length if we actually have any content or if it is a POST
  // or PUT request. Some non-standard servers get confused by Content-Length in
  // at least HEAD/GET requests, and Squid always requires Content-Length in
  // POST/PUT requests.
  $content_length = strlen($options['data']);
  if ($content_length > 0 || $options['method'] == 'POST' || $options['method'] == 'PUT') {
    $options['headers']['Content-Length'] = $content_length;
  }

  // If the server URL has a user then attempt to use basic authentication.
  if (isset($uri['user'])) {
    $options['headers']['Authorization'] = 'Basic ' . base64_encode($uri['user'] . (!empty($uri['pass']) ? ":" . $uri['pass'] : ''));
  }

  // If the database prefix is being used by SimpleTest to run the tests in a copied
  // database then set the user-agent header to the database prefix so that any
  // calls to other Drupal pages will run the SimpleTest prefixed database. The
  // user-agent is used to ensure that multiple testing sessions running at the
  // same time won't interfere with each other as they would if the database
  // prefix were stored statically in a file or database variable.
  $test_info = &$GLOBALS['drupal_test_info'];
  if (!empty($test_info['test_run_id'])) {
    $options['headers']['User-Agent'] = drupal_generate_test_ua($test_info['test_run_id']);
  }

  $request = $options['method'] . ' ' . $path . " HTTP/1.0\r\n";
  foreach ($options['headers'] as $name => $value) {
    $request .= $name . ': ' . trim($value) . "\r\n";
  }
  $request .= "\r\n" . $options['data'];
  $result->request = $request;
  // Calculate how much time is left of the original timeout value.
  $timeout = $options['timeout'] - timer_read(__FUNCTION__) / 1000;
  if ($timeout > 0) {
    stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
    fwrite($fp, $request);
  }

  // Fetch response. Due to PHP bugs like http://bugs.php.net/bug.php?id=43782
  // and http://bugs.php.net/bug.php?id=46049 we can't rely on feof(), but
  // instead must invoke stream_get_meta_data() each iteration.
  $info = stream_get_meta_data($fp);
  $alive = !$info['eof'] && !$info['timed_out'];
  $response = '';

  while ($alive) {
    // Calculate how much time is left of the original timeout value.
    $timeout = $options['timeout'] - timer_read(__FUNCTION__) / 1000;
    if ($timeout <= 0) {
      $info['timed_out'] = TRUE;
      break;
    }
    stream_set_timeout($fp, floor($timeout), floor(1000000 * fmod($timeout, 1)));
    $chunk = fread($fp, 1024);
    $response .= $chunk;
    $info = stream_get_meta_data($fp);
    $alive = !$info['eof'] && !$info['timed_out'] && $chunk;
  }
  fclose($fp);

  if ($info['timed_out']) {
    $result->code = HTTP_REQUEST_TIMEOUT;
    $result->error = 'request timed out';
    return $result;
  }
  // Parse response headers from the response body.
  list($response, $result->data) = explode("\r\n\r\n", $response, 2);
  $response = preg_split("/\r\n|\n|\r/", $response);

  // Parse the response status line.
  list($protocol, $code, $status_message) = explode(' ', trim(array_shift($response)), 3);
  $result->protocol = $protocol;
  $result->status_message = $status_message;

  $result->headers = array();

  // Parse the response headers.
  while ($line = trim(array_shift($response))) {
    list($name, $value) = explode(':', $line, 2);
    $name = strtolower($name);
    if (isset($result->headers[$name]) && $name == 'set-cookie') {
      // RFC 2109: the Set-Cookie response header comprises the token Set-
      // Cookie:, followed by a comma-separated list of one or more cookies.
      $result->headers[$name] .= ',' . trim($value);
    }
    else {
      $result->headers[$name] = trim($value);
    }
  }

  $responses = array(
    100 => 'Continue', 
    101 => 'Switching Protocols', 
    200 => 'OK', 
    201 => 'Created', 
    202 => 'Accepted', 
    203 => 'Non-Authoritative Information', 
    204 => 'No Content', 
    205 => 'Reset Content', 
    206 => 'Partial Content', 
    300 => 'Multiple Choices', 
    301 => 'Moved Permanently', 
    302 => 'Found', 
    303 => 'See Other', 
    304 => 'Not Modified', 
    305 => 'Use Proxy', 
    307 => 'Temporary Redirect', 
    400 => 'Bad Request', 
    401 => 'Unauthorized', 
    402 => 'Payment Required', 
    403 => 'Forbidden', 
    404 => 'Not Found', 
    405 => 'Method Not Allowed', 
    406 => 'Not Acceptable', 
    407 => 'Proxy Authentication Required', 
    408 => 'Request Time-out', 
    409 => 'Conflict', 
    410 => 'Gone', 
    411 => 'Length Required', 
    412 => 'Precondition Failed', 
    413 => 'Request Entity Too Large', 
    414 => 'Request-URI Too Large', 
    415 => 'Unsupported Media Type', 
    416 => 'Requested range not satisfiable', 
    417 => 'Expectation Failed', 
    500 => 'Internal Server Error', 
    501 => 'Not Implemented', 
    502 => 'Bad Gateway', 
    503 => 'Service Unavailable', 
    504 => 'Gateway Time-out', 
    505 => 'HTTP Version not supported',
  );
  // RFC 2616 states that all unknown HTTP codes must be treated the same as the
  // base code in their class.
  if (!isset($responses[$code])) {
    $code = floor($code / 100) * 100;
  }
  $result->code = $code;

  switch ($code) {
    case 200: // OK
    case 304: // Not modified
      break;
    case 301: // Moved permanently
    case 302: // Moved temporarily
    case 307: // Moved temporarily
      $location = $result->headers['location'];
      $options['timeout'] -= timer_read(__FUNCTION__) / 1000;
      if ($options['timeout'] <= 0) {
        $result->code = HTTP_REQUEST_TIMEOUT;
        $result->error = 'request timed out';
      }
      elseif ($options['max_redirects']) {
        // Redirect to the new location.
        $options['max_redirects']--;
        $result = drupal_http_request($location, $options);
        $result->redirect_code = $code;
      }
      $result->redirect_url = $location;
      break;
    default:
      $result->error = $status_message;
  }

  return $result;
}






































class http0 extends library
{
  private $requests = array();

  protected function __construct() {
  }
  
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
