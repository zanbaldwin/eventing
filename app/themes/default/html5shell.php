<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <!-- This page was generated in {elapsed_time} seconds, rendered in {render_time} seconds and took {memory_usage} of memory. -->
    <title><?php echo $title; ?></title>
    <meta charset="utf-8" />
    <link href="<?php echo content('css/main.css'); ?>" rel="stylesheet" media="screen, projection" title="Eventing Styles" type="text/css" />
    <link href="<?php echo content('images/crown.png'); ?>" rel="shortcut icon" type="image/png" />
  </head>

  <body><div id="container">

    <header>
      <hgroup>
        <h1><?php echo isset($heading) ? $heading : 'Eventing'; ?></a></h1>
        <h2>PHP Application Framework based on the HMVC design pattern.</h2>
      </hgroup>
    </header>

    <section>
      <article>
        <p>
          <?php echo a('~eventingsource', 'Eventing'); ?> is a PHP framework
          based on the HMVC design pattern by
          <?php echo a('~profile', 'Alexander Baldwin'); ?>, licensed under the
          <?php echo a('~licensemit', 'MIT/X11 license'); ?>. At the core, the
          basic structure is similar to <?php echo a('~ci', 'CodeIgniter'); ?>
          (the early core libraries were inspired by Codeigniter's design), but
          the framework boasts features such as <?php echo a('module@', 'modules'); ?>,
          namespaces, <?php echo a('example.aspx', 'multiple URL suffixes'); ?>,
          a moderately advanced theme templating system, etc.
        </p>
        <p>
          The Eventing Framework, at this moment in time, is a fun side-project
          and is not recommended for production use. You can grab a copy of the
          source <?php echo a('~eventingsource', 'on GitHub'); ?>, or follow
          development progress <?php echo a('~eventingtwitter', 'on Twitter'); ?>.
        </p>
        <p>
          Eventing requires <?php echo a('~php53', 'PHP v5.3'); ?> or greater.
        </p>
        <p>
          Enjoy!
        </p>
      </article>
    </section>
    <!--{content}-->

    <footer>
      <p>This page was generated in {elapsed_time} seconds and took {memory_usage} of memory.</p>
      <p>Powered by the <?php echo a('~eventingsource', 'Eventing Framework'); ?>.</p>
    </footer>

  </div></body>
</html>
