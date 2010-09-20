<?php

  class M_default extends E_model {

    public function __construct() {
    	parent::__construct();
    }
    
    /**
     * Dummy
     * 
     * Return some dummy data, just so we can demonstrate models.
     * 
     * @access public
     * @return array
     */
    public function dummy() {
    	// We could be pulling a list of users from the database.
    	return array('User01', 'User02', 'User03', 'User04', 'User05');
    }

  }