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
     $this->template->create(array('s' => 'html5shell'));
     $this->template->load('s');
    
      echo '<h1>' . __METHOD__ . '(' . $this->router->suffix() . ')</h1>';

      // Compile a list of routes to test.
      $routes = array(
        'example@mycontroller',
        '',
        'home',

        'controller',
        '/controller',

        'controller/',
        '/controller/',
        
        'controller/method',
        '/controller/method',
        
        'controller.suffix',
        '/controller.suffix',
        
        'controller/method.suffix',
        '/controller/method.suffix',
        
        'controller/method.suffix/',
        '/controller/method.suffix/',
        
        'module@controller/method.suffix',
        'module@/controller/method.suffix',
        
        'module@',
        'module@/',
        
        'module@.suffix',
        'module@/.suffix',
        
        '?query=parse',
        '/?query=parse',
        
        '?query?',
        '/?query?',
        
        '#fragment',
        '/#fragment',
        
        'module@/controller/method/param.suffix?query=param#fragment',
        'module @ /controller/method/param .suffix ?query=param #fragment',
      );

      // Echo out all the route tests.
      if(is_array($routes) && $routes) {
        echo '<table>';
        foreach($routes as $route) {
          $a = a($route);
          if($a) {
            echo "<tr><td><a href=\"{$a}\">{$route}</a></td><td>{$a}</td></tr>\n";
          }
          else {
            echo "<tr><td><span style=\"color:#d00;\">{$route}</span></td><td><span style=\"color:#d00;\">{$route}</span></td></tr>\n";
          }
        }
        echo '</table>';
      }
    }

  }
