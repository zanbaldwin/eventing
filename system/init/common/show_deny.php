<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	ShowDeny
 * @see			/index.php
 */

	if(!function_exists('show_deny')) {
		/**
		 * Show Deny
		 *
		 * Calls show_doc(403), trying to find a user error document. If this fails,
		 * default to the not-so-pretty show_error().
		 */
		function show_deny() {
			show_doc(403) || show_error(
				'You do not have sufficient clearance to view this page.',
				'403 Forbidden'
			);
		}
	}