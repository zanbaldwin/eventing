<?php

  namespace Eventing\Model;

  class example extends \Eventing\Library\model {

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
      // This is the string we are going to use as the document title.
      return 'Eventing PHP Application Framework';
    }

  }