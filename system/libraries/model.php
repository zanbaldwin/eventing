<?php

  namespace Eventing\Library;

  if(!defined('E_FRAMEWORK')) {
    headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
    exit('Direct script access is disallowed.');
  }

  class model extends core
  {

    public function __construct()
    {
      // Do nothing for the moment?
      // This is just so all models have access to the Eventing super object instead of having to use the
      // getInstance() method. Happy days!
    }

  }
