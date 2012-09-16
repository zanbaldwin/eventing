<?php

  namespace \Eventing\Module\module\Model;

  if(!defined('E_FRAMEWORK')) {
    if(!headers_sent()) {
      header('HTTP/1.1 404 Not Found', true, 404);
    }
    exit('Direct script access disallowed.');
  }

  class model extends \Eventing\Library\model {

    protected function __construct() {}

  }