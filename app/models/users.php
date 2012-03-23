<?php

    namespace Eventing\Model;

    class users extends \Eventing\Library\model {

        protected $E, $db, $user_exists;

		public function __construct() {
			parent::__construct();
			$this->E =& getInstance();
            // Load the database settings because we require database access. It's a model after all.
            $this->db = $this->E->load->database();
            $sql = "SELECT id FROM `users` WHERE `id` = ':userstring' OR `email` = ':userstring' LIMIT 1;";
            $this->user_exists = $this->db->prepare($sql);
		}

        /**
         * User Exists
         *
         * Check that the specified string exists as a user; either ID or email. Returns the user ID if it does exist,
         * else false.
         *
         * @access public
         * @param string $user
         * @return string|false
         */
        public function user_exists($user) {
            $result = $this->user_exists->execute(array(':userstring' => $user));
            if($result->rowCount === 1) {
                return $result->id;
            }
            else {
                return false;
            }
        }

        /**
         * Get
         *
         * Get a user object for a specified username or user ID. If one does not exist, no object will be returned;
         * bool(false) will be returned instead.
         *
         * @access public
         * @param string $user
         * @return object|false
         */
        public function get($user) {
            $user_id = $this->user_exists($user);
            return $user_id
                ? new users_userobject($user_id)
                : false;
        }

        /**
         * Get Current
         *
         * Get a user object for the currently logged in user. If no user is logged in then the guest/public user will be used.
         *
         * @access public
         * @return object
         */
        public function get_current() {
        }

	}

    class users_userobject {

        /**
         * Constructor function.
         *
         * Tries to load a user object from the ID provided. If it does not exist, it means get the guest/public user object.
         */
        public function __construct($user_id) {
            // Get database values.
            // Get groups.

        }

        public function can_view($page_id) {
            // Can the user view the page in question?
        }

    }