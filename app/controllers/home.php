<?php

  if (!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  /**
   * Eventing Home Controller Class (framework default)
   */
  final class home extends E_controller
  {

    public function  __construct() {
      parent::controller();
    }

    public function index() {
      // This library should already be loaded, but just in case.
      $this->load->library('template');
      $this->template->create(array(
        'welcome',
        'shell' => 'xhtmlshell',
        'style'
      ));
      $this->template->section('shell')->add('title', 'Eventing Framework');
      $this->template->link(array(
        'shell' => array('welcome', 'style')
      ));
      $this->template->load('shell');
    }

  }
