<?php

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     * Eventing Home Controller Class (framework default)
     */
    class home extends E_controller
    {

        public function __construct()
        {
            parent::controller();
        }
        
        public function image()
        {
            echo "This page is being called from the <code>home::image()</code> method, found in the file <code>/app/controllers/home.png.php</code>.<br />\n";
            echo "Using multiple file extensions is an easy way to generate content that is not default HTML documents, just don't forget to send the appropriate headers!<br />\n";
            echo "For example, the header that should be sent with this suffix would be <code>Content-Type: image/png</code>.";
        }

    }
