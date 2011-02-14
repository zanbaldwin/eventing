<?php
  echo "<?xml version=\"1.0\" encoding=\"utf-8\" />\n";
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

  <head>
    <meta charset="utf-8" />
    <title><?php echo $title; ?></title>
    <meta name="description" content="Eventing PHP Application Framework" />
    <!-- The favourites icon must be in the web root (for IE, surprise surprise). -->
    <link href="<?php echo content('css/main.css'); ?>" rel="stylesheet" media="screen, projection" type="text/css" title="Stylesheet" />
    <script type="text/javascript">
        // Define external scripts to be loaded.
        var s = [
          "https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js",
          "<?php echo content('js/main.js'); ?>",
        ];
        // Load HeadCatch <http://github.com/mynameiszanders/headcatch>.
        (function() {
          var h = d.createElement("script"),
              t = d.getElementsByTagName("script")[0];
          h.src = "<?php echo content('js/head.catch.js'); ?>";
          h.async = true;
          t.parentNode.insertBefore(h, t);
        })();
      ]]>
    </script>
  </head>

  <body>
    <div class="container">

      <h1>Eventing</h1>
      <p>Welcome to the Eventing PHP Application Framework.</p>

      <p>The page you are looking at is being generated dynamically. If you would like to edit this page, you'll find it located at:</p>
      <code>app/themes/default/welcome_message.php</code>
      
      <p>The corresponding controller for this page is located at:</p>
      <code>app/controllers/home.php</code>
      
      <p>Unfortunately, documentation is rather lacking at the moment. This will be rectified by the time we get round to releasing a public version (still under development).</p>
      <p>Page rendered in {elapsed_time} seconds and used {memory_usage} of memory.</p>

    </div>
  </body>

</html>