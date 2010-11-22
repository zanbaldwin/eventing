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

      if(isset($_GET['npm'])) {
        $this->load->library('http');
        $json = file_get_contents('/var/www/npm/registry.json');
        $url = 'http://registry.npmjs.org/';
        if($this->http->fetch('npm', $url)) {
          $request = $this->http->request('npm');
          if($request->code() == 200) {
            $json = $request->body();
          }
        }
        if(!$json) {
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_TIMEOUT, 30);
          $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          if($code == 200) {
            $json = curl_exec($ch);
            curl_close($ch);
          }
        }
        $json = (array) json_decode($json);
        $this->template->create(array('content' => 'registry'));
        $this->template->section('content')->add(array('packages' => $json));
      }
      else {
        $this->template->create(array('content'));
      }

      // Prowl Testing
      $api = '7acd7cb102e50d2d16e44e3bd98375519ea6e365';
      $this->load->library('prowl');
      //if($this->prowl->verify($api)) {
        $this->prowl->create('eventing', 'LessHub');
        $prowler = $this->prowl->app('eventing');
        if($prowler) {
          $prowler->keys($api);
          $prowler->notify('New User', 'A new user has just registered under the name of "Alexander Baldwin" (mynameiszanders).');
        }
      //}

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
      ));
      $this->template->section('content')->add(array(
        'routes'  => $data,
      ));
      $this->template->link(array('s' => array('content')));
      $this->template->load('s');
    }

  }
