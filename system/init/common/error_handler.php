<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ErrorHandler
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('eventing_error_handler')) {
		/**
		 * Eventing Error Handler
		 *
		 * Custom error handler for error's thrown by PHP or ones that have been triggered with trigger_error().
		 *
		 * @access	public
		 * @param	integer $err
		 * @param	string $msg
		 * @param	string $file
		 * @param	integer $line
		 * @return	false|exit
		 */
		function eventing_error_handler($err, $msg, $file, $line) {
			// Define the different error types.
			$types = array(
				E_ERROR              => 'Error',
				E_WARNING            => 'Warning',
				E_PARSE              => 'Parse Error',
				E_NOTICE             => 'Notice',
				E_CORE_ERROR         => 'Core Error',
				E_CORE_WARNING       => 'Core Warning',
				E_COMPILE_ERROR      => 'Compile Error',
				E_COMPILE_WARNING    => 'Compile Warning',
				E_USER_ERROR         => 'User Error',
				E_USER_WARNING       => 'User Warning',
				E_USER_NOTICE        => 'User Notice',
				E_STRICT             => 'Strict',
				E_RECOVERABLE_ERROR  => 'Recoverable Error',
				E_DEPRECATED         => 'Deprecated',
				E_USER_DEPRECATED    => 'User Deprecated',
			);
			// Define which error types we will account for.
			$trigger = c('error_types_trigger');
			if(!is_int($trigger)) {
				// If a value has not been set, default to the following errors:
				// Error, Warning, User Error, User Warning, Deprecated and User Deprecated.
				$trigger = 25347;
			}
			$triggers = binary_parts($trigger);
			if(!in_array($err, $triggers)) {
				return false;
			}
			// Build the error overwrite data array.
			$error = array(
				'title'	=> 'Error ' . $err . ' (' . $types[$err] . ').',
				'file'	=> $file,
				'line'	=> $line,
			);
			// Show an error!
			show_error($msg, '500 Internal Server Error', $error);
		}
	}