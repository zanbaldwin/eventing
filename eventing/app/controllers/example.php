<?php

	namespace Eventing\Application;

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	final class yourController extends \Eventing\Library\controller {

		protected function __construct() {
			parent::__construct();
		}

		public function yourAction() {
			// Your default action goes here.
		}

	}