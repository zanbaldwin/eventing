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

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		// This library should already be loaded, but just in case.
		$this->load->library('template');
		$this->load->model('default');

		$data = $this->model('default')->dummy();
		
		$this->template->create(array(
		  'shell' => 'html5shell'
		));
		$this->template->section('shell')->add('title', $data);
		$this->template->load('shell');
	}

}
