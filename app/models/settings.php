<?php

    namespace Eventing\Model;

    class settings extends \Eventing\Library\model {

        protected $db, $prepared;

		public function __construct() {
			parent::__construct();
            // Load the database settings because we require database access. It's a model after all.
            $this->db = $this->load->database();
            $sql = "SELECT value FROM `settings` WHERE `setting` = ':setting' LIMIT 1;";
            $this->prepared = $this->db->prepare($sql);
		}

		/**
		 * Get
		 *
		 * Return a setting value from the General Settings Table in the database.
         * If the setting does not exist return what the user specifies in the second parameter.
		 *
		 * @access public
		 * @return string|mixed
		 */
		public function get($setting, $return = false) {
			// Grab the specified setting from the database.
            // Clean the input for XSS.
            $sql = 
            // Perform database query.
            $result = $this->prepared->execute(array(':setting' => $setting));
            if($result->rowCount === 1) {
                return $row->value;
            }
            else {
                return $return;
            }
		}

	}