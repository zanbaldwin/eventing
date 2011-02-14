<?php

/**
 * Eventing Framework Output Library
 *
 * Eventing PHP Framework by Alexander Baldwin (zanders [at] zafr [dot] net).
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This is where we start all our settings, libraries and other odd-jobs to get the ball rolling...
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage output
 * @copyright  (c) 2009 Alexander Baldwin
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

  class output extends library {

    protected $output   = '',
              $headers  = array(),
              $status   = 200,
              $message  = 'OK',
              // The following status codes were taken from the list found at
              // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
              $status_codes = array(
                // Informational
                100 => 'Continue',
                101 => 'Switching Protocols',
                102 => 'Processing',
                // Success
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                207 => 'Multi-Status',
                // Redirection
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => 'Switch Proxy',
                307 => 'Temporary Redirect',
                // Client Error
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                418 => 'I\'m a teapot',
                422 => 'Unprocessable Entity',
                423 => 'Locked',
                424 => 'Failed Dependency',
                425 => 'Unordered Collection',
                426 => 'Upgrade Required',
                449 => 'Retry With',
                450 => 'Blocked by Windows Parental Controls',
                // Server Error
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                506 => 'Variant Also Negotiates',
                507 => 'Insufficient Storage',
                509 => 'Bandwidth Limit Exceeded',
                510 => 'Not Extended',
              );

    protected function __construct() {}

    /**
     * Set Output
     *
     * Set the output, replacing what has already been set.
     *
     * @access public
     * @param string $output
     * @return boolean
     */
    public function set($output) {
      if(!is_string($output)) {
        return false;
      }
      $this->output = $output;
      return true;
    }

    /**
     * Append Output
     *
     * Append a string onto what the output that has already been set.
     *
     * @access public
     * @param string $output
     * @return boolean
     */
    public function append($output) {
      if(!is_string($output)) {
        return false;
      }
      $this->output .= $output;
      return true;
    }

    /**
     * Get Output
     *
     * Return a string containing what has been set as the output.
     *
     * @access public
     * @return string
     */
    public function get_output() {
      return $this->output;
    }

    /**
     * Set Header
     *
     * Set a header to be sent along with the output.
     *
     * @access public
     * @param string $header
     * @param string $value
     * @return boolean
     */
    public function header($header, $value) {
      if(!is_string($header) || !is_string($value)) {
        return false;
      }
      $header = trim($header, '-');
      if(!preg_match('/^[a-zA-Z-]+$/', $header)) {
        return false;
      }
      $value = preg_replace('/\\s+/', ' ', $value);
      $this->headers[$header] = trim($value);
      return true;
    }

    /**
     * Status Code
     *
     * Set the HTTP/1.1 status code, and an optional message, if a message is
     * not set, it will be determined from the status code.
     *
     * @access public
     * @param integer $code
     * @param string $message
     * @return boolean
     */
    public function status($code, $message = false) {
      if(!is_numeric($code)) {
        return false;
      }
      $code = (int) $code;
      // We have a pretty exhaustive list of status codes. You shouldn't be
      // passing any others, if you really REALLY need to, add them to the array
      // above.
      if(!isset($this->status_codes[$code])) {
        return false;
      }
      // Get the message, either by filtering 
      if(!is_string($message)) {
        $message = $this->status_codes[$code];
      }
      // Filter user input and assign to class property.
      $this->status = $code;
      $this->message = trim(preg_replace('/\\s+/', ' ', $message));
      return true;
    }

    protected function render_vars($output, $precision = 4) {
      if(!is_string($output)) {
        return false;
      }
      if(!is_int($precision)) {
        $precision = 4;
      }
      $elapsed_time = round(microtime(true) - E_FRAMEWORK, $precision);
      $output = str_replace('{elapsed_time}', $elapsed_time, $output);
      $memory_usage = round(
                        (memory_get_usage() - E_MEMORY) / pow(1024, 2),
                        $precision
                      )
                    . ' Mb';
      $output = str_replace('{memory_usage}', $memory_usage, $output);
      return $output;
    }

    /**
     * Display Output
     *
     * Display the buffered output, sending the appropriate headers.
     *
     * @access public
     * @param boolean $continue
     * @return boolean|exit
     */
    public function display($continue = false) {
      // Can we sent the headers? Or has output already started?
      if(!headers_sent()) {
        // Send the HTTP Status Code and Message.
        header(
          'HTTP/1.1 ' . $this->status . ' ' . $this->message,
          true,
          $this->status
        );
        // Send each header that the user specified.
        foreach($this->headers as $header => $value) {
          header($header . ': ' . $value);
        }
      }
      // Echo the buffered output to the client.
      echo $this->render_vars($this->output);
      // Do we want to continue running the application, or should we terminate
      // now the client has got what they requested?
      if($continue) {
        // Reset the buffer. No point keeping the output if it has already been
        // sent to the client.
        $this->output = '';
        return true;
      }
      // YOU ARE TERMINATED, BABY.
      exit;
    }

  }
