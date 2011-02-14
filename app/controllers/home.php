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

    public function debug() {
      $this->load->view('welcome_message', array());
      $this->load->library('template', 'page');
      $this->page->create(array(
        'shell' => 'html5shell'
      ));
      $this->load->model('example', 'dumdum', true);
      // Adding data via the model super method.
      $this->page->section('shell')->add(
        'text',
        $this->model('dumdum')->dummy()
      );
      // Adding data via the model super property.
      $this->page->section('shell')->add(
        'text',
        $this->dumdum->dummy()
      );
    }

    public function index() {
      $this->load->library('template');
      $this->template->create(array(
        'shell' => 'welcome_message',
        'head' => 'head',
      ));
      $this->template->section('shell')->add('title', 'Eventing PHP Framework');
      $this->template->load('shell');
    }

  }
