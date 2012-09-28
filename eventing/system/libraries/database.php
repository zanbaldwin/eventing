<?php

/**
 * Database Library
 *
 * Normalise all input; GET, POST, STDIN, etc.
 *
 * @category	Eventing
 * @package		Libraries
 * @subpackage	Input
 * @see			/index.php
 */

	namespace Eventing\Library;

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	abstract class database extends library {
	}