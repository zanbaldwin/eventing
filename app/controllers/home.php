<?php

  namespace Eventing\Application;

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  /**
   * Eventing Home Controller Class (framework default)
   */
  final class home extends controller {

    public function __construct() {
      parent::__construct();
    }

    public function index() {
      // This library should already be loaded, but just in case.
      $this->load->library('template');

      $this->load->model('default');
      $data = $this->model('default')->dummy();
      
      $this->template->create(array(
        'shell' => 'html5shell'
      ));
      $this->template->section('shell')->add('title', $data);

      // Fire up the template.
      $this->template->load('shell');
      // Notice we can echo after we have loaded the template, but it still gets
      // outputted first?
      echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
    }

  }
