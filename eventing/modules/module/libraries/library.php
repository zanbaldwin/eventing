<?php

  namespace \Eventing\Module\module\Library;

  if(!defined('E_FRAMEWORK')) {
    if(!headers_sent()) {
      header('HTTP/1.1 404 Not Found', true, 404);
    }
    exit('Direct script access disallowed.');
  }

  class library extends \Eventing\Library\library {

    protected function __construct() {}

  }