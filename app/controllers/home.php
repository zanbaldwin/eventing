<?php

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     * Eventing Home Controller Class (framework default)
     */
    final class home extends E_controller
    {

        /**
         * Token Action
         *
         * This is a description...
         *
         * @return void
         */
        public function token()
        {
            $this->load->library('annotate');
            $this->annotate->load(__FILE__);
            $doc = $this->annotate->get_doc(__CLASS__, 'image');
            var_dump($doc);
            if(!headers_sent())
            {
                header('Content-Type: text/plain');
            }
            echo 'The Doc Comment for this controller ("' . __CLASS__ . '::' . __FUNCTION__ . '") is:' . "\n\n" . $doc;
        }

        function  __construct()
        {
            parent::controller();
        }

        /**
         ** Doc Comment Comments have had '//' prepended to them, as most people are used
         ** to this comment notation and makes it easier to read.

         ** // Doc Comment Title
         *  Output the main Logo of the Application.

         ** // Doc Comment Description
         *  This action is for generating and outputting the image for the main site Logo.
         
         *  All Doc Comment lines must begin with an asterix. They may have any whitespace
         *  character before and after the asterix, any text after another asterix will be
         *  discarded. Any lines not starting with an asterix will be discarded. You may
         *  escape an asterix with the backslash character, eg. "SELECT \* FROM db.table".

         ** Doc Comment Variables
         *  @suffix png|jpg|gif|:|~
         *  @method get
         *  @route "png:home/image"
         */
        public function image()
        {
            // Example usage.
        }

        /**
         * Start the default controller example page.
         * "/" | "home.htm" | "/home/index.htm" | "home/index/*.htm"
         */
        public function index()
        {
            // Load the view data from the default model.
            include $this->load->file('data', 'path');
            // List the example pages in this controller.
            $examples = array(
                a('home/digest', 'Digest Library'),
                a('home/zend', 'Zend Library'),
                a('png:home/image', 'Multiple suffixes'),
                a('home/token', 'Tokenizer Library')
            );
            sort($examples);
            // Create sections and populate them with data.
            $this->template->create(array(
                'welcome_message',
                'xhtmlshell',
                'style'
            ));
            $this->template->section('welcome_message')->add('text', $data);
            $this->template->section('welcome_message')->add('examples', $examples);
            $this->template->section('xhtmlshell')->add(array(
                'favicon' => content('leaf.png'),
                'title' => 'Eventing Framework :: Default Controller'
            ));
            // Link the different sections together.
            $links = array(
                'xhtmlshell' => array(
                    'welcome_message',
                    'style'
                )
            );
            $this->template->link($links);
            // Load the parent section.
            $this->template->load('xhtmlshell');
        }

        /**
         * "home/digest.htm"
         */
        public function digest()
        {            
            $logged_in = false;
            $this->load->library('digest');
            // We do not need to set the realm, but it would be nice to change it to
            // something more appropriate that the generic "Eventing".
            $this->digest->realm('zafrOpenID');
            // Get the username the user has provided. If they have not logged 
            $username = $this->digest->user();
            // If $username is a string, it means that they have already *attempted* Digest Authentication.
            // If $username is bool(false), it means that they have not, and the appropriate headers must be sent.
            if($username)
            {
                // Grab the password, the model is set up to return false is the user does not exist, or the
                // password is not a string.
                $password = $this->load->model('users') ? $this->model('users')->user_pass($username) : false;
                // User has entered their details. Check if the username exists.
                if($password)
                {
                    // The username does exist! Let's validate the password they supplied against the correct one.
                    $this->digest->password($password);
                    if(!($logged_in = $this->digest->validate()))
                    {
                        // The username exists, but the password they supplied was incorrect. Show the login box
                        // until they get it right. You could tell them, but it requires stopping Authentication.
                        # exit('The password you entered is incorrect.');
                    }
                }
                else
                {
                    // The username does not exist! We could tell the user, but nah, let's just bug them
                    // with another login box :D
                    # exit('The username you entered does not exist.');
                }
            }
            if(!isset($logged_in) || !$logged_in)
            {
                // They either have not logged in yet, or supplied the wrong login details.
                // Initiate HTTP Digest Authenticaion (send appropriate headers).
                // WARNING! If you call this before you check that the user is logged in, you will end
                //          up with an infinate loop of HTTP Auth boxes in the browser!
                if($this->digest->init())
                {
                    // E_digest::init() will return true if the appropriate headers were sent.
                    // If this part of the script is still running it means that the user cancelled
                    // the login box in the browser.
                    exit('Please enter your credentials to access this page.');
                }
                else
                {
                    // However, if E_digest::init() returns false, it means that the appropriate headers
                    // could not be sent. Report/log the correct error messages.
                    exit('HTTP Digest Authentication headers could not be sent. Application terminated.');
                }
            }
            
            // Congratulations, you have your user logged in!
            echo "Welcome back, {$username}!\n";
        }

        /**
         * Home Controller's zend Method
         *
         * Page for the example usage of Eventing's Zend Library.
         * Loads the Zend Libraries (if you have them), and enables autoloading of class, so you don't have to.
         */
        public function zend()
        {
            $this->load->library('zend');
            // In case the settings weren't in our config, we can load them now.
            $this->zend->set_path(BASEPATH);
            $this->zend->autoload(true);
            // Load the Zend Libraries. If you have the settings in your config file, they will be loaded straight
            // away. No need to mess with E_zend::set_path() and E_zend::autoload(). Call E_zend::load(), and check
            // the boolean return value as to whether they loaded correctly.
            if($this->zend->load())
            {
                // We have the Zend Libraries! Let's have a little fun with one of the extensive number of libraries
                // available to us courtesy of Zend!
                $figlet = new Zend_Text_Figlet();
                $text = "Eventing\n+ Zend\n\n= Sorted";
                $ascii_image = $figlet->render($text);
                echo '<pre>' . $ascii_image . '</pre>';
            }
            else
            {
                // The Zend Libraries could not be loaded, it's probably best not to reference them then!
                echo 'Zend Libraries could not be loaded. Bad luck, old chum!';
            }
        }

    }
