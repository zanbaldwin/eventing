<?php

  namespace Eventing\Module\example\Application;

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class mycontroller extends \Eventing\Library\module {

    protected function __construct() {}

    public function index() {
      echo 'Viewing a module controller.<br />';
      echo '<tt>' . __METHOD__ . '()</tt>.<br />';
    }

  }