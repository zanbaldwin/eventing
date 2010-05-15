<?php

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    class M_default extends E_model
    {

        public function __construct()
        {
            parent::model();
        }

        public function dummy_data()
        {
            return 'This is some dummy data from the default model.';
        }
    }
