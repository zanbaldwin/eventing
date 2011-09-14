<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	URI
 * @see			/index.php
 */

	if (!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

	if(!function_exists('uri')) {
		/**
		 * URI Parser
		 *
		 * Parse a string, in the format designed for Eventing URIs and return an
		 * object containing data about the different parts.
		 *
		 * @access public
		 * @param string $uri
		 * @param boolean $object
		 * @return object|array|false
		 */
		function uri($uri, $object = true) {
			static $r = false;
			if(!is_string($uri)) {
				return false;
			}
			if(!$r) {
				$regex = array();
				// The module name must also be a valid PHP class name.
				$regex['module']    = VALIDLABEL . '\\s*@';
				// Segments must be alphanumeric, hyphens or underscores, separated by
				// *nix-style directory separators.
				$regex['segments']  = '[a-zA-Z0-9/_-]+';
				// The suffix is just like a regular file extension, except it can only
				// contain alphanumeric characters.
				$regex['suffix']    = '?:\.?([a-zA-Z0-9]+)/?';
				// The query string can only contain certain characters, but according
				// to RFC 3986, a query string is a URL part starting from the first
				// question mark, and terminating at the end of the URL, or at the '#'
				// character, if one is present.
				$regex['query']     = '\?(?:(?:'.VALIDLABEL.')?\?|[^#? ]*)';
				// The URL fragment can basically be anything from the first occurance
				// of the '#' character to the end of the URL. It is only used
				// client-side, so we don't need to bother with it... It's just here so
				// we can pump it into our anchors
				$regex['fragment']  = '#.*';
				// Wrap each part so we can assign them as matches, and combine the PCRE
				// RegEx string.
				foreach($regex as &$part) {
					$part = '(' . $part . ')?';
				}
				$r = '~^' . implode('\\s*', $regex) . '$~';
			}
			if(!preg_match($r, $uri, $matches)) {
				return false;
			}
			// Start constructing the data array.
			$u = array();
			// We want the name of the module, if no module passed, use boolean false.
			$u['module']	= isset($matches[1]) && $matches[1]
				? trim(substr($matches[1], 0, -1))
				: false;
			// Do the segments specify an absolute or relative URI?
			$u['absolute']	= isset($matches[2]) && substr($matches[2], 0, 1) == '/';
			// A trailing slash on the segments indicates the URI points to a
			// directory. Before filtering the segments, make a note of this.
			$trailing		= isset($matches[2]) && substr($matches[2], -1) == '/';
			// Clean up the segments
			$u['segments']	= isset($matches[2])
			&& ($u['segments'] = trim(filter_path($matches[2]), '/'))
				? $u['segments']
				: false;
			// A trailing slash on the segments indicates the URI points to a
			// directory. Before filtering the segments, make a note of this.
			$trailing		= isset($matches[2]) && substr($matches[2], -1) == '/';
			// If we have a suffix, prepend it with a full stop, else false.
			$u['suffix'] = '/';
			if($u['segments']) {
				$u['suffix'] = isset($matches[3]) && $matches[3]
					? '.' . $matches[3]
				: ($trailing ? '/' : DEFAULTSUFFIX);
			}
			// If we have a query string placeholder, just return the placeholder
			// name. If we have an actual query string, parse it and return the query
			// array.
			if(!isset($matches[4])) {
				$u['query']   = false;
			}
			elseif(substr($matches[4], -1) == '?') {
				$u['query']   = ($query_varname = trim($matches[4], '?'))
					? $query_varname
					: 'query';
			}
			else {
				parse_str(trim(substr($matches[4], 1)), $u['query']);
			}
			// If we have a fragment, great! Else false, as per usual.
			$u['fragment']  = isset($matches[5]) && $matches[5]
				? substr($matches[5], 1)
				: false;
			// Return the data we've collected in the format the function callee
			// requested.
			return $object ? (object) $u : $u;
		}
	}