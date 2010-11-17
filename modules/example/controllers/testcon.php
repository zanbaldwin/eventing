<?php

  namespace Eventing\Module\example\Application;

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class testcon extends someclassthatihaventdecidedyet {

    protected function __construct() {}

    public function index() {
      // Your module controller, default action.
    }

  }