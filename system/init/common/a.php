<?php

/**
 * Common function
 *
 * @category	Eventing
 * @package		Common
 * @subpackage	A
 * @see			/index.php
 */

	if(!defined('E_FRAMEWORK')) {
		headers_sent() || header('HTTP/1.1 404 Not Found', true, 404);
		exit('Direct script access is disallowed.');
	}

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
			// Create a container for URL's that have already been wrapped in anchor tags.
			static $used_urls = array();
			if(!is_string($path)) {
				// We return null in case the return value does not get checked before being outputed as a string. Null evaluates to an empty string, whereas false evaluates to "0".
				return null;
			}
			// Make sure the options array passed is actually an array. Force it's hand.
			$options = (array) $options;
			/* *****
			 * Being Section: Shortcut Expanding
			 */
			$shortcut_regex = '/^~(' . VALIDLABEL . ')$/';
			if(preg_match($shortcut_regex, $path, $matches)) {
				$path = c($matches[1], 'links');
				if(!is_string($path)) {
					return null;
				}
			}
			/* *****
			 * Begin Section: Not an absolute URL, parse as an eURI.
			 */
			if(!filter_var($path, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
				// Convert the $path string into an eURI object to work on.
				$uri = uri($path);
				if(!is_object($uri)) {
					return null;
				}
				// Determine how our URL shoudl reference the domain and application directory.
				$domain = $uri->absolute
					? BASEURL
					: URL;
				if(!c('mod_rewrite') && ($uri->module || $uri->segments)) {
					$domain .= SELF . '/';
				}
				// Build the URL segment part that gets used by the application for routing.
				$application = ltrim($uri->segments . $uri->suffix, '/');
				if($uri->module) {
					$application = $uri->module . '@' . $application;
				}
				// Build the query string from saved vars from the current request, mashed up with the query string
				// passed to this function, either in the options array, or directly in the $path string.
				$query = array();
				// Compile a list of query string variables that should be persisted, as set in the configuration file.
				if(is_array($save_gets = c('save_gets'))) {
					foreach($save_gets as $save_var) {
						if(isset($_GET[$save_var])) {
							$query[$save_var] = $_GET[$save_var];
						}
					}
				}
				if($uri->query) {
					// If the query string data from the URI function is a string, it is referencing that we should grab
					// the query data from the options array.
					if(is_string($uri->query) && isset($options[$uri->query]) && is_array($options[$uri->query])) {
						$query = array_merge($query, $options[$uri->query]);
					}
					// Unset the query data in the options array, regardless of what data type it is, to prevent it from
					// appearing in the HTML tag as an attribute.
					if(is_string($uri->query) && isset($options[$uri->query])) {
						unset($options[$uri->query]);
					}
				}
				$query = count($query)
					? '?' . http_build_query($query, null, '&')
					: null;
				// After the query goes the fragment. Build it if it has been set.
				$fragment = $uri->fragment
					? '#' . $uri->fragment
					: null;
				// Rebuild our URL. If the $path specified that it was not an absolute URL, and the module and segments
				// are not present, disregard the $domain part. Using just a query string and/or fragment references the
				// current request segments again anyway. Else, build the whole thing.
				$path = !$uri->absolute && !$application && ($query || $fragment)
					? $query . $fragment
					: $domain . $application . $query . $fragment;
				#$path = $domain . $application . $query . $fragment;
			}
			/* *****
			 * Begin Section: If there's a title, wrap it in an HTML anchor tag.
			 */
			if(is_string($title)) {
				// If we have already used this URL in another anchor tag on the same request, add a rel="nofollow"
				// attribute to it, to stop search engine crawlers thinking we're spamming their sexy asses. I'm looking
				// at you, Google. Please note that we are removing the fragment from the URL, because the same URL with
				// a different fragment IS STILL THE SAME URL.
				$hard_url = ($pos = strpos($path, '#')) !== false
					? substr($path, 0, $pos)
					: $path;
				if($hard_url && in_array($hard_url, $used_urls)) {
					if(isset($options['rel'])) {
						if(!is_array($options['rel'])) {
							$rels = xplode(' ', (string) $options['rel']);
						}
						if(!in_array('nofollow')) {
							$rels[] = 'nofollow';
						}
						$options['rel'] = implode(' ', $rels);
					}
					else {
						$options['rel'] = 'nofollow';
					}
				}
				else {
					$used_urls[] = $hard_url;
				}
				// Every anchor tag should have a title attribute. If one is not specified in the options array, use the
				// title as the title. Simple, eh? True, we could get image tags as the title, but hey, if that happens
				// just grab your handbag, whack in a brick, and hit your front-end developer with it :)
				if(!isset($options['title'])) {
					$options['title'] = $title;
				}
				// Now we want to build our attributes from reading each entry in the options array.
				$attributes = '';
				foreach($options as $attr => $value) {
					// If the attribute name is not a string, then we can use it. More importantly we DO NOT want to
					// overwrite our href attribute. There would be no point in the first parameter of this function
					// otherwise!
					if(!is_string($attr) || $attr == 'href') {
						continue;
					}
					// Force the value to be a string. Bit pointless in an HTML tag otherwise?
					$value = is_array($value)
						? implode(' ', $value)
						: (string) $value;
					$attributes .= ' ' . $attr . '="' . htmlentities($value) . '"';
				}
				// Build our final HTML anchor tag... Come on, altogether now!
				$path = '<a href="' . htmlentities($path) . '"' . $attributes . '>' . $title . '</a>';
			}
			/* *****
			 * End Function: Everything has been calculated, compiled and cuddled. Return the $path (which may now be an
			 * HTML anchor tag, so not technically a path anymore, but we love it exactly the same. Don't we
			 * snugglebuttons?)
			 */
			 return $path;
		}
	}