# Eventing Framework

## Structure

To stop myself from changing the hard-coded structure every five minutes, I've
taken the time to write out the structure implementation here.

### Namespaces

Everything - apart from `index.php`, `init.php` and `common.php` - should be
encapsulated within the `Eventing` namespace. The namespace structure goes as
following:

    \Eventing
        \Library
        \Application
            \Library
        \Model
        \Module
            \yourmodule
                \Library
                \Application
                \Model
            \anothermodule
                \Library
                \Application
                \Model
            etc...

### Directory Structure

    /
        /app
            /config
                /autoload.php
                /config.php
                /links.php
                /routes.php
            /controllers
            /libraries
            /models
            /themes
                /default
                ...
                /errors
                    ...
                    /error.php
        /modules
            /yourmodule
                /controllers
                /libraries
                /models
            ...
        /system
            /libraries
                /controller.php
                /core.php
                /input.php
                /library.php
                /load.php
                /model.php
                /module.php
                /output.php
                /router.php
                /singleton.php
                /template.php
            /common.php
            /init.php

## Dynamic Assets

From your application, models, views and libraries can be access should be
accessible from the super object like so...

    $this->load->library('system_library');
    $this->system_library->some_method();
    
    $this->load->model('some_model');
    $this->model('some_model')->some_method();
    
    // Unlike CodeIgniter, the view loader returns the output rather than add
    // it to the Output library, this due to the integration with the Template
    // library.
    $html = $this->load->view('some_view', $data);

By default, only libraries should be accessible from the super object
properties, though this can be overwritten (via the second parameter); models
should be returned from the model() method of the Core library.

    $this->load->module('some_module');
    
    $this->module('some_module')->library('some_library');
    $this->module('some_module')->some_library->some_method();
    
    $this->module('some_module')->model('some_model')->some_method();

## URLs

The two main functions for URLs and URIs are anchor function `a()` and the URI
parser function `uri()`.

### Anchor `a()`

The anchor function takes up to three parameters, though only the first is
required.

    a(string $path, string $title, array $options);

The `$path` parameter can be in any of the following three formats:

- `~shortcut`
- `http://example.com/any/valid/absolute/url.php`
- A string that is considered valid by the `uri()` function.

If `$title` is a non-empty string, then the resulting URL (providing that
`$path` passed validation) will be wrapped in an HTML anchor tag, and if
`$options` is an array, the associative key/value pairs will be used as the tag
attributes.

### URI Parser `uri()`

The `uri()` function will take a string as it's only parameter, validate and
parse the string, and return an object with data about the string (or an array
if specified).

Note: Throughout the framework, the following syntax is refered to as an "eURI".

    module[ ]@ segments .suffix ?query[?] #fragment

- A directory separator (slash) preceeding the segments indicates that the
  resulting URL should be absolute, (ie. including the HTTP scheme and domain).
- Each part of the eURI is separated with a space in the above example, although
  any (any type and any amount) whitespace at these points is permitted. Each part
  of the eURI is optional, though the suffix will be ignored if there are no
  segments, regardless of what it has been set to.
- (square) Brackets infer that a character, or set of characters is optional within a
  part of the eURI.
- All hyphens in the application URI string will get replaced by underscores in
  the re-routed URI.

The object that the function returns will be an instance of `stdClass` with the
following properties:

    (string|false)       module
    (bool)               absolute
    (string|false)       segments
    (string)             suffix
    (string|array|false) query
    (string|false)       fragment

## Modules

Modules are proving to be rather tricky. So a couple of development decisions
must be made on simple ground rules governing how modules should be implemented.

- Firstly, the router might need to be rewritten AGAIN; module controllers
  should not be publicly accessible.
- A module is meant to be THIRD-PARTY generic add-on functionality that is not
  specific to your main application. Your main application should access modules
  to extend, not replace.
- A module should have access to the input, but not output of the application.
  They should therefore only have access to the Router and Input libraries by
  default (need some thought on whether they should be allowed other libraries
  such as HTTP, Prowl, etc.)
- A module should not be able to access the main application, either the
  controller that is requesting it, or any other controller.

Modules should be accessed from a controller, ideally like:

    $this->load->module('module_name');
    $module = $this->module('module_name');
