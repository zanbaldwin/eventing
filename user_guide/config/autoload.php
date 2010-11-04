<?php

if (!defined('E_FRAMEWORK')) {
  headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
  exit('Direct script access is disallowed.');
}

$config = array(
    'library' => array(),
    'model'   => array(),
    'plugin'  => array()
);
