<?php

    /**
     * Router Library
     *
     * Takes the URI string (segments and suffix). Checks to see if it should re-route the request to a different one.
     * Then finds the appropriate controller and method, and determines which folder the controller class is in.
     *
     * @category   Eventing
     * @package    Libraries
     * @subpackage router
     * @author     Alexander Baldwin
     * @copyright  (c) 2009 Alexander Baldwin
     * @license    http://www.gnu.org/licenses/gpl.txt - GNU General Public License
     * @version    v0.4
     * @link       http://eventing.zafr.net/source/system/libraries/router.php
     * @since      v0.1
     */

    if(!defined('E_FRAMEWORK')){headers_sent()||header('HTTP/1.1 404 Not Found',true,404);exit('Direct script access is disallowed.');}

    /**
     * Router Class
     */
    class E_router
    {

        private $uri_string,
                $ruri_string,
                $suffix,
                $rsuffix,
                $rsegments = array(),
                $d = '',
                $c = '',
                $m = '',
                $controllers;

        /**
         * Constructor Method
         *  
         * @return void
         */
        public function __construct()
        {
            $this->controllers = APP . 'controllers/';
            $this->uri_string = REQUEST;
            $this->suffix = SUFFIX;
            list($this->ruri_string, $this->rsuffix) = $this->_routes($this->uri_string, $this->suffix);
            $this->rsegments = xplode('/', $this->ruri_string);
            if(is_array($dcm = $this->_determine($this->ruri_string, $this->rsuffix)))
            {
                list($this->d, $this->c, $this->m) = $dcm;
            }
            defined('ROUTE') || define('ROUTE', $this->ruri_string);
            defined('RSUFFIX') || define('RSUFFIX', $this->rsuffix);
        }

        /**
         * Routes
         *
         * Re-routes the URI string according to the rules in the routes config file.
         * This method does use up memory a bit, and it extremely fiddly. 'Twas a ***** to get right!
         * EDIT: Wasn't so bad the second time round :D
         *
         * @access private
         * @param string $uri_string
         * @param string $suffix
         * @return array
         */
        private function _routes($uri_string, $suffix)
        {
            $routes = get_config('routes');
            if(!is_array($routes))
            {
                return array($uri_string, $suffix);
            }
            if(isset($routes[$uri_string]) && $suffix == '.' . c('url_default_suffix'))
            {
                return array($routes[$uri_string], $suffix);
            }
            // Define those pseudo-wildcards!
            $wildcards = array(
                'match' => array(
                    '**', '*', '##', '#', '@@', '@'
                ),
                'replace' => array(
                    '([a-zA-Z0-9\/_-]+)',
                    '([a-zA-Z0-9_-]+)',
                    '([0-9\/]+)',
                    '([0-9]+)',
                    '([a-zA-Z\/]+)',
                    '([a-zA-Z]+)'
                )
            );
            // Now, I know looping through every route could potentially be very memory hungry, but it's the only way to
            // do it!
            foreach($routes as $match => $route)
            {
                // We could clean up the $match, but to save on memory, forget it! If it's not formatted
                // properly, then it's the users own fault and won't work anyway!
                // Add a couple of useful helpers to the pseudo-wildcards we have already.
                $helper_names = array('{controller}', '{method}');
                $helper_values = array(c('default_controller'), c('default_method'));
                $match = str_replace($helper_names, $helper_values, $match);
                $route = str_replace($helper_names, $helper_values, $route);
                // If the route isn't formatted properly, then skip it and go onto the next!
                $regex = '#^(([a-zA-Z0-9]*|\~)\:)?['.preg_quote('*#@/', '#').'a-zA-Z0-9_-]*$#';
                if(!preg_match($regex, $match))
                {
                    continue;
                }
                // If the suffix of the $match is not the same as the current document, skip this route and try the
                // next.
                if(substr($match, 0, 1) == '~:')
                {
                    $match = c('url_default_suffix') . substr($match, 1);
                }
                $tsuffix = ($pos = strpos($match, ':')) !== false
                         ? '.' . substr($match, 0, $pos)
                         : $suffix;
                if($tsuffix == '.')
                {
                    $tsuffix = '';
                }
                if($tsuffix != $suffix)
                {
                    continue;
                }
                // Remove the suffix from $match. We only want to segments now.
                if($pos !== false)
                {
                    $match = substr($match, $pos + 1);
                }
                // Now we get to use those magic pseudo-wildcards! Mmm... pseudo-wildcards...
                $match = str_replace($wildcards['match'], $wildcards['replace'], $match);
                // Let's check if this route ($match) equals our current URI string.
                $regex = '|^' . $match . '$|';
                if(!preg_match($regex, $uri_string))
                {
                    continue;
                }
                // Woohoo! We found a route that is also of the same file type! Congrats!
                // We haven't finished though! Let's check that the route is formatted properly.
                $regex = '#^(([a-zA-Z0-9]*|\~)\:)?[/\$a-zA-Z0-9_-]*$#';
                if(!preg_match($regex, $route))
                {
                    // There is no point looping again, we have already found our route; it just isn't formatted
                    // properly! Return the default.
                    return array($uri_string, $suffix);
                }
                // Check if there is a change in suffix. If there isn't the original document suffix is kept.
                if(($pos = strpos($route, ':')) !== false)
                {
                    $rsuffix = '.' . substr($route, 0, $pos);
                    $rsuffix = $rsuffix == '.' ? '' : $rsuffix;
                    $rsuffix = $rsuffix == '.~' ? '.' . c('url_default_suffix') : $rsuffix;
                    $route = substr($route, $pos + 1);
                }
                else
                {
                    $rsuffix = $suffix;
                }
                // If any of the pseudo-wildcards are referenced in $route, put them in.
                $regex = '|^' . $match . '$|';
                $ruri_string = preg_replace($regex, $route, $uri_string);
                // The string, $route, has already been check if it has been formatted correctly, the only problem we
                // may have is if there is a stray '$' floating about from where pseudo-wildcards are referenced.
                if(strpos($ruri_string, '$') !== false)
                {
                    // Oh noes! A stray dollar symbol! That means the $route is not formatted properly, return default.
                    return array($uri_string, $suffix);
                }
                // Nope, nothing wrong with it. Return the re-routed URI string and re-routed suffix.
                return array($ruri_string, $rsuffix);
            }
            // We've exhausted all possibilities, just return the default!
            return array($uri_string, $suffix);
        }

        /**
         * Determine
         *
         * Determines the controller and method, and which directory it is located in.
         *
         * @access private
         * @param string $ruri_string
         * @param string $rsuffix
         * @return void
         */
        private function _determine($ruri_string, $rsuffix)
        {
            if($ruri_string == '')
            {
                return array('', c('default_controller'), c('default_method'));
            }
            if($rsuffix == '')
            {
                // It's a folder, no need to use recursive method.
                return array($ruri_string . '/', c('default_controller'), c('default_method'));
            }
            $fof = bool(c('file_over_folder'));
            if($fof)
            {
                return $this->_recursive_file($ruri_string, $rsuffix);
            }
            else
            {
                return $this->_recursive_folder($ruri_string, $rsuffix);
            }
        }

        /**
         * Recursive File
         *
         * File over folder recursive method for finding the controller class file.
         *
         * @access private
         * @param string $ruri_string
         * @param string $rsuffix
         * @return void
         */
        private function _recursive_file($ruri_string, $rsuffix)
        {
            $ext = '.' . c('url_default_suffix') == $rsuffix ? '' : $rsuffix;
            $ruri = explode('/', $ruri_string);
            $d = '';

            for($i = 0; $i < count($ruri); $i++)
            {
                if(file_exists($this->controllers . $d . $ruri[$i] . $ext . EXT))
                {
                    $m = isset($ruri[$i + 1]) ? $ruri[$i + 1] : c('default_method');
                    return array($d, $ruri[$i] . $ext, $m);
                }
                $d .= $ruri[$i] . '/';
            }
            // If the file has not been found, return an array of empty strings.
            return array('', '', '');
        }

        /**
         * Recursive Folder
         *
         * Folder over file recursive method for finding the controller class file.
         *
         * @access private
         * @param string $ruri_string
         * @param string $rsuffix
         * @return void
         */
        private function _recursive_folder($ruri_string, $rsuffix)
        {
            $ext = '.' . c('url_default_suffix') == $rsuffix ? '' : $rsuffix;
            $ruri = explode('/', $ruri_string);
            $d = '';

            $m = c('default_method');
            $c = array_pop($ruri);
            for($i = count($ruri); $i >= 0; $i--)
            {
                $d = implode('/', $ruri);
                if($d == '/')
                {
                    $d = '';
                }
                $file = $this->controllers . $d . $c . $ext . EXT;
                if(file_exists($file))
                {
                    return array(implode('/', $ruri), $c . $ext, $m);
                }
                $m = $c;
                $c = array_pop($ruri);
            }
            // If the file has not been found, return an array of empty strings.
            return array('', '', '');
        }

        /**
         * DCM
         *
         * Returns the controller and method name, and the directory the controller class file is located in.
         *
         * @return array
         */
        public function dcm()
        {
            return array($this->d, $this->c, $this->m);
        }

        /**
         * Segment
         *
         * Return a specific segment from the re-routed URI.
         *
         * @param integer $n
         * @param boolean $return
         * @return string|mixed
         */
        public function segment($n, $return = false)
        {
            if(isset($this->rsegments[$n]))
            {
                return $this->rsegments[$n];
            }
            return $return;
        }

        /**
         * Segments
         *
         * Return an array of all the segments in the re-routed URI.
         *
         * @return array
         */
        public function segments()
        {
            return $this->rsegments;
        }

        /**
         * Total Segments
         *
         * Return the number of segments in the re-routed URI string.
         *
         * @return integer
         */
        public function total_segments()
        {
            return count($this->rsegments);
        }

        /**
         * URI String
         *
         * Return the re-routed URI.
         *
         * @return string
         */
        public function uri_string()
        {
            return $this->ruri_string;
        }

        /**
         * Suffix
         *
         * Return the suffix from the re-routed URI string.
         *
         * @return string
         */
        public function suffix()
        {
            return $this->rsuffix;
        }

    }
