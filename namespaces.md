Framework Structure
===================

Namespaces
----------

To stop myself from changing namespaces around every five minutes, I've taken
some time to figure out how they should be implemented.

Everything - apart from `index.php`, `init.php` and `common.php` - should be
encapsulated within the `Eventing` namespace.

The namespace structure goes as following

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

From your application, models, views and libraries can be access should be
accessible from the super object like so

    $this->load->library('system_library');
    $this->system_library->some_method();
    
    $this->load->model('some_model');
    $this->model('some_model')->some_method();
    
    $html = $this->load->view('some_view', $data);

By default, only libraries should be accessible from the super object
properties, though this can be overwritten (via the second parameter).
Unlike CodeIgniter, the view loader returns the output rather than add it to the
Output library, this due to the integration with the Template library.

From your application, modules are available via the `module()` method.

    $this->load->module('some_module');
    
    $this->module('some_module')->library('some_library');
    $this->module('some_module')->some_library->some_method();
    
    $this->module('some_module')->model('some_model')->some_method();
    
    $html = $this->module('some_modele')->view('some_view');

URLs
----

Now this framework implements modules, URLs have to be reconsidered, too.
Currently, the format is one of the following three:

- `suffix:/segments/and/more/segments?query_string?#fragment`
- `~shortcut`
- `http://realurl.com/`

The first format is the only one that may change; the others are fine as they
are.

As the `load_class()` function now uses the syntax `module:path/to/lib`, this
might be a good place to start. The query string, fragment and segment syntax do
not need altering; just module and suffix referencing.

Edit: It has been decided upon the following syntax.

    module @ segments .suffix ?query? #fragment
