<?php

  namespace Eventing\Application;

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  /**
   * Eventing Home Controller Class (framework default)
   */
  final class home extends \Eventing\Library\controller {

    protected function __construct() {
      parent::__construct();
    }

    public function index() {
      echo "<h2>Viewing default controller/action.</h2><br />\n";
      echo '<tt>\\' . __METHOD__ . "()</tt><br />\n";
      $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
      echo $indent . "<strong>extends</strong> <tt>\\Eventing\\Library\\controller</tt><br />\n";
      echo $indent . "<strong>extends</strong> <tt>\\Eventing\\Library\\core</tt><br />\n";
      echo $indent . '<strong>extends</strong> <tt>\\Eventing\\Library\\library</tt>';
      return;
    }

  }
