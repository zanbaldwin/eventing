<?php

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    class M_users extends E_model
    {

        private $users = array();

        public function __construct()
        {
            parent::model();
            $this->populate();
        }

        /**
         * Populate Users
         *
         * Populate the user array with dummy data.
         *
         * @return void
         */
        private function populate()
        {
            $this->users['demo'] = 'demo';
            $this->users['eventing'] = 'framework';
            $this->users['test'] = 'pass';
        }

        /**
         * Username and Password
         *
         * Returns a users password if the user exists, false if they don't.
         *
         * @param string $username
         * @return string|false
         */
        public function user_pass($username)
        {
            if(!is_string($username))
            {
                return false;
            }
            $password = isset($this->users[$username]) && is_string($this->users[$username]) ? $this->users[$username] : false;
            return $password;
        }

    }
