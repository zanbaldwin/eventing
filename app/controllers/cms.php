<?php

    namespace Eventing\Application;

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	final class cms extends \Eventing\Library\controller {

		protected function __construct() {
			parent::__construct();
		}

		public function index() {
			// For a static page to be display from the database, make the last entry in the routes config re-route all
            // remaining requests to "cms/index".
            
            // Load the required models.
            $this->load->model('pages');
            $this->load->model('users');
            
            // Grab the route the end-user is currently requesting.
            $route = implode('/', $this->router->segments());
            // Does the route exist as a page?
            $page_id = $this->model('pages')->page_exists($route);
            if(!$page_id) {
                show_404();
            }
            
            // Can the current user view the page in question?
            $user = $this->model('users')->get_current();
            if(!$user->can_view($page_id)) {
                show_404();
            }
            
            // Grab the page object containing all its data.
            $page_object = $this->model('pages')->load($page_id);
            
            // Now we want to start templating the final HTML output.
            // Add the page object to a variable called "page" which will be available in the final template view.
            $this->template->add('page', $page_object);
            $this->template->load($page_object->layout);
		}

	}