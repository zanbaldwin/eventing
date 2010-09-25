<?php

/**
 * Eventing Framework Annotate Library
 *
 * Eventing PHP Framework by Alexander Baldwin <zanders@zafr.net>.
 * http://eventing.zafr.net/
 * The Eventing Framework is an object-orientated PHP Framework, designed to rapidly build applications.
 * This library is essentially for extracting the Doc Comments from specific class methods. This can be used to
 * write inline configuration for controllers and actions inside the Doc Comments.
 * For example, @route, @suffix and @method.
 *
 * @category   Eventing
 * @package    Libraries
 * @subpackage tokenizer
 * @author     Alexander Baldwin
 * @copyright  (c) 2009 Alexander Baldwin
 * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
 * @version    v0.4
 * @link       http://github.com/mynameiszanders/eventing
 * @since      v0.1
 */

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

/**
 * Eventing Annotate Library
 *
 * Description. It's a bit weird to think that this library is designed to access and interpret
 * the very comments that I'm writing now... Hmm...
 * May I also add dedication to the var_dump() function. During the creation of this library, nothing
 * has been so useful as that little function!
 *
 * @author Alexander Baldwin
 */
class E_annotate
{

  private $compiled = false, $path = false, $tokens = false, $classes = array();

  /**
   * Load
   *
   * @param string $path
   * @return boolean
   */
  public function load($path)
  {
    $path = realpath($path);
    if(!file_exists($path) || !function_exists('token_get_all'))
    {
      return false;
    }
    $this->compiled = false;
    $this->classes = array();
    $this->path = $path;
    $this->tokens = false;

    $this->get_tokens();
    $this->get_classes();
    $this->class_blocks();
    $this->class_functions();
    return true;
  }

  /**
   * Get Tokens
   *
   * @access protected
   * @return void
   */
  protected function get_tokens()
  {
    // Grab all the tokens that PHP returns as an arrray, we want it as a searchable string.
    $tokens = token_get_all(file_get_contents($this->path));
    $compiled = '';
    foreach($tokens as $k => $t)
    {
      // Whitespace just gets in the way, as there could be any number of these floating around.
      // Because the tokens are already separated, we don't need to bother with whitespace at all!
      if(is_array($t) && $t[0] != T_WHITESPACE)
      {
        $compiled .= $k . ':' . $t[0] . ',';
      }
      else
      {
        if($t == '{' || $t == '}')
        {
          $compiled .= $t . ',';
        }
      }
    }
    $this->tokens = $tokens;
    $this->compiled = trim($compiled, ',');
  }

  /**
   * Parse Classes
   *
   * @access protected
   * @return void
   */
  protected function get_classes()
  {
    if(!$this->compiled)
    {
      return false;
    }
    // The following regular expression searches through the tokens generated for the given file for class definitions:
    // "[/** Doc Comment */] [abstract|final|interface] class <className> [extends <className> [implements <className>]] {"
    $regex = '%(?:(\\d+)\\:366,)?(?:\\d+\\:(?:345|344|353),)?\\d+\\:352,(\\d+)\\:307,(?:\\d+\\:(?:354|355),\\d+\\:307,)*{%';
    preg_match_all($regex, $this->compiled, $classes, PREG_SET_ORDER);
    if(is_array($classes))
    {
      foreach($classes as $class)
      {
        $this->classes[$this->tokens[$class[2]][1]] = array('token' => $class[2]);
        $this->classes[$this->tokens[$class[2]][1]]['doc'] = isset($this->tokens[$class[1]][1]) ? $this->tokens[$class[1]][1] : false;
      }
    }
  }

  /**
   * Class Blocks
   *
   * Fetch all the class definitions in the file.
   *
   * @return void
   */
  protected function class_blocks()
  {
    if(!$this->compiled)
    {
      return false;
    }
    foreach($this->classes as $class_name => $class)
    {
      $this->classes[$class_name]['block'] = $this->get_block($class['token']);
    }
  }

  /**
   * Get Code Block
   *
   * Return a string of text (a sub-string of the compiled tokens) between the matching closing code brace
   * and the opening code brace directly after a specified token identifier.
   *
   * @param int|string $name_token
   * @return string|false
   */
  protected function get_block($name_token)
  {
    if(!$this->compiled || ($pos = strpos($this->compiled, $name_token . ':')) === false)
    {
      return false;
    }
    $section= substr($this->compiled, $pos);
    $len = strlen($section);
    $block = '';
    $opening = 1;
    $closing = 0;
    for($i = 0; $i < $len; $i++)
    {
      if($section[$i] == '{')
      {
        $opening++;
      }
      elseif($section[$i] == '}')
      {
        $closing++;
        if($closing == $opening)
        {
          break;
        }
      }
      if($opening > 0)
      {
        $block .= $section[$i];
      }
    }
    return trim($block, ',');
  }

  /**
   * Class Functions
   *
   * @return void
   */
  protected function class_functions()
  {
    if(!$this->compiled)
    {
      return false;
    }
    foreach($this->classes as $class_name => $class)
    {
      $regex = '%(?:(\d+)\:366,)?(?:\d+\:(?:344|345),)?(?:\d+\:(?:341|342|343),)?\d+\:333,(\d+)\:307,\{%';
      preg_match_all($regex, $class['block'], $functions, PREG_SET_ORDER);
      foreach($functions as $function)
      {
        $function_name = $this->tokens[$function[2]][1];
        $this->classes[$class_name]['functions'][$function_name] = array('token' => $function[2]);
        $this->classes[$class_name]['functions'][$function_name]['doc'] = isset($this->tokens[$function[1]][1]) ? $this->tokens[$function[1]][1] : false;
        $this->classes[$class_name]['functions'][$function_name]['block'] = $this->get_block($function[2]);
      }
    }
  }

  /**
   * Get Doc Comment
   *
   * @param string $class
   * @param string|false $function
   */
  public function get_doc($class, $function = false)
  {
    if(!is_string($class) || !isset($this->classes[$class]))
    {
      return false;
    }
    if(!is_string($function))
    {
      return $this->classes[$class]['doc'];
    }
    else
    {
      if(!isset($this->classes[$class]['functions'][$function]))
      {
        return false;
      }
      return $this->classes[$class]['functions'][$function]['doc'];
    }
  }

  /**
   * Parse Docs
   *
   * @param string $doc
   * @return array|string
   */
  public function parse_doc($doc)
  {
    if(!is_string($doc) || substr($doc, 0, 3) != '/**' || substr($doc, -2) !== '*/')
    {
      return false;
    }
    $doc_array = array();
    $params = false;
    // Split the string into lines, after removing the Doc Comment markers.
    foreach(xplode("\n", substr($doc, 3, -2)) as $num => $line)
    {
      $line = trim($line);
      if(substr($line, 0, 2) == '**' || substr($line, 0, 1) != '*')
      {
        continue;
      }
      $line = trim(substr($line, 1));
      $line = preg_replace('%(?<!\\\\)\\*.*$%', '', $line);
      $line = str_replace('\\*', '*', $line);
      if($line == '')
      {
        continue;
      }
      if(substr($line, 0, 1) == '@')
      {
        $params = true;
      }
      if(!$params)
      {
        if(!isset($doc_array['title']))
        {
          $doc_array['title'] = $line;
        }
        else
        {
          $doc_array['description'] .= $line . ' ';
        }
      }
      else
      {
        // Add the param to the $doc['params'] variable.
        if(substr($line, 0, 1) != '@' || !isset($line[1]))
        {
          continue;
        }
        $pos = strpos($line, ' ');
        if($pos === false)
        {
          $var = substr($line, 1);
          $doc_array['params'][$var] = null;
        }
        else
        {
          $var = substr($line, 1, $pos - 1);
          $val = substr($line, $pos + 1);
          $doc_array['params'][$var] = $val;
        }
      }
    }
    return $doc_array;
  }

}
