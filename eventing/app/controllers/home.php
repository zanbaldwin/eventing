<?php

	namespace Eventing\Application;

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	final class home extends \Eventing\Library\controller {
		
		protected function __construct() {
			parent::__construct();
		}

		public function index() {
			headers_sent() || header('Content-Type: text/plain');
			echo 'Eventing PHP Framework.' . "\n";
			echo '=======================' . "\n\n";
			echo 'URI String: "' . $this->router->uri_string() . "\"\n";
			echo 'Controller: Home; Method: Index;' . "\n";
			echo 'Calling home::index() from initialisation script.' . "\n\n";
		}

	}