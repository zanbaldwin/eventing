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
      $data = array(
        'title' => 'Eventing PHP Framework',
      );
      $view = $this->load->view('welcome_message', $data);
      $this->output->append($view);
    }

    public function template() {

      $this->output->header('Content-Type', 'text/plain');

      // Example Template library usage.
      $this->load->library('template');
      $this->template->create('shell', 'welcome_message');
      $this->template->section('shell')->add('title', 'Eventing: Template Usage Example');
      $this->template->load('shell');
    }

  }
