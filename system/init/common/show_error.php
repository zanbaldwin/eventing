<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowError
 * @see			/index.php
 */

	if(!function_exists('show_error')) {
		/**
		 * Show Error
		 *
		 * Explain the function...
		 *
		 * @return exit
		 */
		function show_error($msg, $header = '500 Framework Application Error', $user_error = false) {
			if(is_string($header)
				&& preg_match('|^([0-9]{3}) |', $header, $matches)
				&& (int) $matches[1] < 600
				&& (int) $matches[1] >= 100
			) {
				if(!headers_sent()) {
					header($header, true, (int) $matches[1]);
				}
			}
			else {
				$header = '500 Framework Application Error';
			}
			// Grab some information from the backtrace. It might be useful.
			$trace = debug_backtrace();
			$error = array(
				'message'	=> $msg,
				'title'		=> $header,
				'status'	=> (int) $matches[1],
				'file'		=> $trace[0]['file'],
				'line'		=> $trace[0]['line']
			);
			if(is_array($user_error)) {
				foreach($user_error as $overwrite => $value) {
					if(isset($error[$overwrite])) {
						$error[$overwrite] = $value;
					}
				}
			}
			if(file_exists(theme_path('errors') . 'error' . EXT)) {
				extract($error);
				// Unset any variables that we don't want included in the error document.
				unset($msg, $header, $matches, $trace, $error);
				// We are writing about the path twice because we don't want to set
				// anymore variables.
				require theme_path('errors') . 'error' . EXT;
			}
			else {
				// No HTML document to show? Dump out the data in an XML document
				// instead.
				echo '<?xml version="1.0" encoding="utf-8" ?>' . "\n<error>\n";
				foreach($error as $element => $value) {
					echo "  <{$element}>\n    {$value}\n  </{$element}>\n";
				}
				echo '</error>';
			}
			exit;
		}
	}