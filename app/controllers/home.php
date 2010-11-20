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
        
        '?query',
        '/?query',
        
        '#fragment',
        '/#fragment',
      
        '#',
        '/#',
        
        'module@/controller/method/param.suffix?query=param#fragment',
        'module @ /controller/method/param .suffix ?query=param #fragment',
      );

      // Echo out all the route tests.
      $data = array();
      $invalid = content('images/slash.png');
      $invalid = $invalid
               ? '<img src="' . $invalid . '" alt="Invalid" width="16" height="16" />'
               . ' <span style="color:#900;">Invalid</span>'
               : '';
      $valid = content('images/tick.png');
      $valid = $valid
             ? '<img src="' . $valid . '" alt="Invalid" width="16" height="16" />'
             : '';
      if(is_array($routes) && $routes) {
        foreach($routes as $route) {
          $r = $this->router->route($route);
          $rvalid = is_object($r) && $r->valid;
          $temp = array(
            'euri'  => $route,
            'url'   => ($a = a($route, htmlentities(a($route)))) ? $valid . ' ' . $a : $invalid,
            'route' => $rvalid
                     ? $valid . ' ' . $r->controller() . '::' . $r->method()
                     . '(<span>' . $r->rsuffix() . '</span>)'
                     : $invalid,
          );
          $data[] = (object) $temp;
        }
      }
      $this->template->create(array('s' => 'html5shell'));
      $this->template->section('s')->add(array(
        'title'   => 'Eventing PHP Application Framework',
        'heading' => __METHOD__ . '(<span>' . $this->router->suffix() . '</span>)',
        'routes'  => $data,
      ));
      $this->template->load('s');
    }

  }
