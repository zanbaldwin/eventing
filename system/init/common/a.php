<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	A
 * @see			/index.php
 */

	if(!function_exists('a')) {
		/**
		 * Anchor
		 *
		 * Function relating to all things Framework URL, plus a bit more! Segments
		 * to URL, with file extension and query string support, plain old
		 * wrap-link-in-html-tag, and fetch link from config file. If the second
		 * parameter is a non-empty string, it will wrap the link in the HTML 'a'
		 * tag with text.
		 * If the URL has already been used by this function, it will add the
		 * attribute rel="nofollow" to prevent search engines thinking you are
		 * trying to spam them.
		 *
		 * @access public
		 * @param string $path
		 * @param string $title
		 * @param array $options
		 * @return string|false
		 */
		function a($path, $title = false, $options = array()) {
			static $used_urls = array();
			if(!is_string($path)) {
				return false;
			}
			// Set the optional parameters to safe values.
			$options = (array) $options;
			$title = !is_string($title) || !$title ? false : $title;
			// If $path is a reference to a link inside the links configuration file,
			// then grab the shortcut name, and fetch it from the config array.
			$shortcut_regex = '#^~('.VALIDLABEL.')$#';
			if(preg_match($shortcut_regex, $path, $matches)) {
				$link = c($matches[1], 'links');
				if(!is_string($link)) {
					return false;
				}
				$path = $link;
			}
			// Check that the $path is not an absolute URL. If not, then treat $path
			// as an Eventing-style URI.
			if(!filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
				$data = uri($path);
				if(!is_object($data)) {
					return false;
				}
				// Determine how our URL should reference the domain and application
				// directory.
				$server = ($data->absolute ? BASEURL : URL)
					. (!c('mod_rewrite')
					&& ($data->module || $data->segments)
						? SELF . '/'
						: '');
				// Build the URL part that gets used by the application for routing.
				$application = ltrim(
					($data->module              ? $data->module . '@' : '')
					. $data->segments
					. $data->suffix,
					'/'
				);
				// Build the query string depending on what source we are going to use.
				$query = '';
				if($data->query) {
					// If the query data returned is a string, it means that it's a
					// placeholder for a query array held in the $options array.
					if(is_string($data->query)
						&& isset($options[$data->query])
						&& is_array($options[$data->query])
					) {
						$query_identifier = $data->query;
						$data->query = $options[$data->query];
						unset($options[$query_identifier], $query_identifier);
					}
					// Now we have satisfied the query placeholder, check that the query
					// data is an array ready to be made into a query string.
					if(is_array($data->query)) {
						$query = '?' . http_build_query($data->query, null, '&');
					}
				}
				// Include a URL fragment if one has been set.
				$fragment = $data->fragment ? '#' . $data->fragment : '';
				// Rebuild our path from the URL parts we just created from the string
				// that was originally passed to the function. If only the fragment was
				// passed, then only use the fragment part, as passing more than that
				// will make the page reload. Fragment is usually intended for internal
				// page navigation or Javascript, neither of which want the page to
				// reload.
				$uri = $application . $query . $fragment;
				if($path != '#') {
					$path = substr($uri, 0, 1) == '#' && !$data->absolute
						? $fragment
						: $server . $uri;
				}
			}
			// The path is now a valid URL!
			// Check that we haven't already used the URL already. If we have, add a
			// rel="nofollow" to stop search engine crawlers thinking we're trying to
			// spam them.
			if(in_array($path, $used_urls)) {
				if(isset($options['rel'])) {
					if(!is_array($options['rel'])) {
						$rels = xplode(' ', (string) $options['rel']);
					}
					if(!in_array('nofollow', $rels)) {
						$rels[] = 'nofollow';
					}
					$options['rel'] = implode(' ', $rels);
				}
				else {
					$options['rel'] = 'nofollow';
				}
			}
			elseif($title) {
				$used_urls[] = $path;
			}
			// If the title evaluates to true, it means we have a valid string (we
			// have already done checks on it). We want to wrap the URL in an anchor
			// tag, and add a title and attributes.
			if($title) {
				// Compile the options string from the array passed in the third
				// parameter.
				$o = '';
				foreach($options as $attr => $value) {
					// We do not want our href attribute being overwritten, else there
					// would be no point in having the first parameter!
					if($attr == 'href') {
						continue;
					}
					// If the attribute isn't a string, then it can't go in the HTML tag.
					if(is_string($attr)) {
						// If the value is an array (such as an array of rel tags or class
						// names), implode them into a space-separated string.
						if(is_array($value)) {
							$value = implode(' ', $value);
						}
						$o .= ' ' . $attr . '="' . htmlentities((string) $value) . '"';
					}
				}
				// Build our HTML tag.
				$path = '<a href="' . htmlentities($path) . '"' . $o . '>'
				. $title
				. '</a>';
			}
			return $path;
		}
	}