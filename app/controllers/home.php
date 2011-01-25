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
      $this->load->library('template');
    }

    public function debug() {
      if(!headers_sent()) {
        header('Content-Type: text/plain');
      }
      echo 'Debug page.';
    }

    public function index() {
    
      // Compile a list of routes to test.
      $routes = array(
        'example@mycontroller',
        '',
        '/',
        'home',
        'home/',
        'home/index/',

        'controller',
        '/controller',

        'welcome/home',
        'welcome/home/',

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
        
        '/.suffix',
        '.suffix',
        
        '?inline=http_q_array',
        '/?inline=http_q_array',
        
        '?query?',
        '/?query?',
        
        '?query',
        '/?query',
      
        '??',
        '/??',
        
        '#fragment',
        '/#fragment',
      
        '#',
        '/#',
        
        'module@/controller/method/param.suffix?query=param#fragment',
        'module @ /controller/method/param .suffix ?query=param #fragment',
        
        '/?prowl',
      );

      // Success Rates
      $anchor_success = 0;
      $router_success = 0;
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
          );
          $example_options = array('query' => array('action' => 'do'));
          if($a = a($route, htmlentities(a($route, false, $example_options)), $example_options)) {
            $temp['url'] = $valid . ' ' . $a;
            $anchor_success++;
          }
          else {
            $temp['url'] = $invalid;
          }
          if($rvalid) {
            $temp['controller'] = $valid . ' ' . $r->controller() . '::' . $r->method();
            if($r->suffix() != '/') {
              $temp['controller'] .= '(<span>'.$r->suffix().'</span>)';
            }
            $temp['file'] = $valid . ' ' . str_replace(BASEPATH, '', $r->path());
            $router_success++;
          }
          else {
            $temp['controller'] = $invalid;
            $temp['file'] = $invalid;
          }
          $data[] = (object) $temp;
        }
      }
      $this->template->create(array('s' => 'html5shell', 'content'));
      $this->template->section('s')->add(array(
        'title'   => 'Eventing PHP Application Framework',
        'heading' => __METHOD__ . '(<span>' . $this->router->suffix() . '</span>)',
      ));
      $this->template->section('content')->add(array(
        'routes'  => $data,
      ));
      $this->template->link(array('s' => array('content')));
      $this->template->load('s');
    }

  }
