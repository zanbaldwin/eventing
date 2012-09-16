<?php

  namespace \Eventing\Module\module\Application;

  if(!defined('E_FRAMEWORK')) {
    if(!headers_sent()) {
      header('HTTP/1.1 404 Not Found', true, 404);
    }
    exit('Direct script access disallowed.');
  }

  class controller extends \Eventing\Library\module {

    protected function __construct() {
      parent::__construct();
    }

    public function method() {
    }

  }