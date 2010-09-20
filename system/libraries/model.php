<?php

if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

class E_model extends E_core
{

	public function __construct()
	{
		// Do nothing for the moment?
		// This is just so all models have access to the Eventing super object instead of having to use the
		// get_instance() method. Happy days!
	}

}
