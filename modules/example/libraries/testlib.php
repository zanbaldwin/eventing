<?php

  namespace Eventing\Module\example;

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class testlib extends \Eventing\Library\library {

    protected function __construct() {}

    public function test() {
      return 'test string';
    }

  }