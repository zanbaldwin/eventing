<head>
  <meta charset="utf-8" />
  <title>Skype Jobs</title>
  <meta name="description" content="Eventing PHP Application Framework" />
  <!-- The favourites icon must be in the web root (for IE, surprise surprise). -->
  <link href="<?php echo content('css/main.css'); ?>" rel="stylesheet" media="screen, projection" type="text/css" title="Stylesheet" />
  <script type="text/javascript">
      // Define external scripts to be loaded.
      var s = [
        "https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js",
        "<?php echo content('js/main.js'); ?>,
      ];
      // Load HeadCatch <http://github.com/mynameiszanders/headcatch>.
      (function() {
        var h = d.createElement("script"),
            t = d.getElementsByTagName("script")[0];
        h.src = "<?php echo content('js/head.catch.js'); ?>;
        h.async = true;
        t.parentNode.insertBefore(h, t);
      })();
    ]]>
  </script>
</head>