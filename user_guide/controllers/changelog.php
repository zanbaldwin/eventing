<?php

	if (!defined('E_FRAMEWORK')) {
	  headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
	  exit('Direct script access is disallowed.');
	}
	
	final class changelog extends E_controller {
	
	  public function __construct() {
	    parent::__construct();
	  }
	
	  public function index() {
	    $this->template->create(array('shell', 'nav', 'content' => 'home/changelog'));
	    $this->template->link(array('shell' => array('nav', 'content')));
	    $this->template->load('shell');
	  }
	
	}
