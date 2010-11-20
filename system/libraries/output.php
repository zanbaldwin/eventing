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

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class output extends library
{

  private $output = '', $headers = array();

  protected function __construct()
  {
    // Do nothing!
  }

  public function set_output($output)
  {
    if(!is_string($output))
    {
      return false;
    }
    $this->output = $output;
    return true;
  }

  /**
   * Append Output
   *
   * @param  $output
   * @return void
   */
  public function append_output($output)
  {
    if(!is_string($output))
    {
      return false;
    }
    $this->output .= $output;
    return true;
  }

  /**
   * Get Output
   *
   * @return void
   */
  public function get_output()
  {
    return $this->output;
  }

  /**
   * Add Header
   *
   * Add a header to the output buffer.
   *
   * @param string $header
   * @param boolean $status_code
   * @return boolean
   */
  public function add_header($header, $status_code = false)
  {
    if(!is_string($header))
    {
      return false;
    }
    if(is_int($status_code))
    {
      if($status_code >= 100 && $status_code < 600)
      {
        $this->headers[] = array($header, $status_code);
        return true;
      }
      else
      {
        return false;
      }
    }
    $this->headers[] = $header;
    return true;
  }

  /**
   * Parse Headers
   *
   * Parse headers from buffer and flush them to the client if the output hasn't started already.
   *
   * @access private
   * @return void
   */
  private function _parse_headers()
  {
    if(headers_sent())
    {
      return false;
    }
    foreach($this->headers as $header)
    {
      if(is_array($header))
      {
        header($header[0], true, $header[1]);
        continue;
      }
      header($header);
    }
    return true;
  }

  /**
   * Parse Output
   *
   * Parse output and return what is generated.
   *
   * @access private
   * @return void
   */
  private function _parse_output($output)
  {
    $benchmark = (string) round(
    preg_replace('/^0\.([0-9]+) ([0-9]+)$/', '$2.$1', microtime()) -
    preg_replace('/^0\.([0-9]+) ([0-9]+)$/', '$2.$1', E_FRAMEWORK),
    4);
    $pseudo = array(
                '{elapsed_time}' => $benchmark,
                '{memory_usage}' => round((memory_get_usage() - E_MEMORY) / 1024 / 1024, 2) . ' Mb'
                );
                foreach($pseudo as $match => $replace)
                {
                  if(strpos($output, $match) !== false)
                  {
                    $output = str_replace($match, $replace, $output);
                  }
                }
                return $output;
  }

  /**
   * Display
   *
   * @return void
   */
  public function display($continue = false, $set_output = false, $use_headers = false)
  {
    $continue = bool($continue);
    if(is_string($set_output))
    {
      if(bool($use_headers))
      {
        $this->_parse_headers();
      }
      echo $this->_parse_output($set_output);
      if($continue)
      {
        flush();
        return true;
      }
      exit;
    }
    // No need to put this in an else statement. The above if statement results in a return or exit.
    $this->_parse_headers();
    echo $this->_parse_output($this->output);
    if($continue)
    {
      flush();
      // We've already sent the buffered output, get rid of it!
      $this->output = '';
      return true;
    }
    exit;
  }


}
