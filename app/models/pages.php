<?php

    namespace Eventing\Model;

    /**
     * Uses tables: pages, page_revisions, heirachy.
     */
	class pages extends \Eventing\Library\model {

        protected $db;

		public function __construct() {
			parent::__construct();
            // Load the database settings because we require database access. It's a model after all.
            $this->db = $this->load->database();
		}
        
        public function page_exists($page) {
        }

        public function load($page) {
        }

	}
    
    class pages_pageobject {
    }