<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowError
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('show_error')) {
		/**
		 * Show Error
		 *
		 * Terminate the application, and display an optional error or error document.
		 * This function takes two parameters, both optional.
		 * The first is the header, the combination of a status code and a status. If this string is not formatted
		 * properly, it will default to "500 Framework Application Error". The second is the error message; if this
		 * value is not a string, it will simply terminate the application after sending the header in the first
		 * parameter; if it is a string, it will attempt to load an error document from
		 * "<theme_directory>/errors/<statuscode>.php" or simply print out an XML document if one cannot be found in
		 * that location.
		 *
		 * @access public
		 * @param string|non-string $message
		 * @param string $header
		 * @return exit
		 */
		function show_error($header = '', $message = false) {
			// First parse the header from the string provided. If it is invalid, send a standard 500
			// "the-server-screwed-up" status.
			if(!is_string($header) || !preg_match('/^([0-9]{3}) ([a-zA-Z -]+)$/', $header, $matches) || $matches[1] >= 600 || $matches[1] < 100) {
				$matches[1] = '500';
				$matches[2] = 'Framework Application Error';
				$header = $matches[1] . ' ' . $matches[2];
			}
			$code = (int) $matches[1];
			$status = trim($matches[2]);
			// If headers have not already been sent, send the header.
			headers_sent() || header('HTTP/1.1 ' . $header, true, $code);
			// If the message is not a string, terminate immediately. This implies that the user does not want to send any message or error document.
			if(!is_string($message) || !$message) {
				exit;
			}
			// If the message was a non-empty string, carry on, starting with an array compilation of important
			// information.
			$trace = debug_backtrace();
			$information = array(
				'title'			=> $status,
				'statuscode'	=> $code,
				'message'		=> $message,
				'file'			=> $trace[0]['file'],
				'line'			=> $trace[0]['line'],
			);
			// Try to load an error document view. This will be located in the "errors" theme directory.
			$file = filter_path(theme_path('errors') . $code . EXT);
			if(file_exists($file)) {
				headers_sent() || header('Content-Type: text/html');
				// Before loading the error document view, unset any variable we do not want in there.
				${'1file'} = $file;
				unset($header, $matches, $code, $status, $message, $trace, $file);
				// Extract the error information.
				extract($information);
				// Load in the error document view, like a regular view, except we don't use any output buffering.
				require ${'1file'};
			}
			// Else just print out the error in XML format, with a text/xml header.
			else {
				headers_sent() || header('Content-Type: text/xml');
				echo '<?xml version="1.0" encoding="utf-8" ?>' . "\n<error>\n";
				foreach($information as $element => $value) {
					echo "\t<{$element}>{$value}</{$element}>\n";
				}
				echo '</error>';
			}
			exit;
		}
	}