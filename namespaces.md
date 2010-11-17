To stop myself from changing namespaces around every five minutes, I've taken
some time to figure out how they should be implemented.

Everything - apart from `index.php`, `init.php` and `common.php` - should be
encapsulated within the `Eventing` namespace.

The namespace structure goes as following

  \Eventing
           \Application
                       \Library
           \Model
           \Library
           \Module
                  \yourmodule
                             \Library
                             \Application
                             \Model