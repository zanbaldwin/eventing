<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <!-- This page was generated in {elapsed_time} seconds and took {memory_usage} of memory. -->
    <title><?php echo $title; ?></title>
    <meta charset="utf-8" />
    <link href="<?php echo content('css/main.less'); ?>" rel="stylesheet/less" media="screen, projection" title="Eventing Styles" />
    <script src="<?php echo content('js/mootools.js'); ?>"></script>
    <script src="<?php echo content('js/less.js'); ?>"></script>
    <script src="<?php echo content('js/main.js'); ?>"></script>
  </head>

  <body><div id="container">

    <header>
      <hgroup>
        <h1>Eventing Framework / <a href="<?php echo BASEURL; ?>user_guide/">User Guide</a></h1>
        <h2>PHP Application Framework based on CodeIgniter</h2>
      </hgroup>
    </header>

    <section>
      <article>
        <p>
          Eventing is a <strong>PHP Application Framework</strong> based on <?php echo a('~ci', 'CodeIgniter'); ?> by <?php echo a('~profile', 'Alexander Baldwin'); ?>,
          licensed under either <?php echo a('~licensemit', 'MIT'); ?> or <?php echo a('~licensegpl', 'GPL v3'); ?>, whichever suits you best.
        </p>
        <p>
          Like CodeIgniter, it uses <strong>Model-View-Controller</strong>, but adds other features like <?php echo a('aspx:example', 'multiple suffixes (file extensions)'); ?>
          and a moderately advanced view templating system.
        </p>
        <p>
          At this moment in time, Eventing is a personal project and is not recommended for production use (late-alpha development). If you would like to tinker around with it,
          the source code is <?php echo a('~eventingsource', 'hosted on GitHub'); ?>.
        </p>
        <p>
          If you already have the source, the page you are viewing is calling the
          <?php
            echo a(
              'home/index??',
              '<code>home::index()</code>',
              array(
                'query_string' => array(
                  'rel' => 'install',
                  'version' => '1.0'
                )
              )
            );
          ?>
          controller, located in the file 
          "<code title="Load the default controller in a modal window.">app/controllers/home.php</code>".<br />
          The HTML of this page is found in the file "<code title="Load the HTML page in a modal window.">app/themes/default/html5shell.php</code>".
        </p>
        <p>
          <?php /* For the documentation link, we are using BASEURL instead of a(), incase SELF is in the URL. */ ?>
          Before you dive in, you might want to read the <a href="<?php echo BASEURL; ?>user_guide/">documentation</a>.
        </p>
      </article>
    </section>

    <footer>
      <p>This page was generated in {elapsed_time} seconds and took {memory_usage} of memory.</p>
      <p>Powered by the <?php echo a('~eventingsource', 'Eventing Framework'); ?>.</p>
    </footer>

  </div></body>
</html>
