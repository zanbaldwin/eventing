<?php
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" />\n";
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

	<head>
		<meta charset="utf-8" />
		<title><?php echo $title; ?></title>
		<meta name="description" content="Eventing PHP Application Framework" />
		<link href="<?php echo content('css/main.css'); ?>" rel="stylesheet" media="screen, projection" type="text/css" title="Stylesheet" />
		<script type="text/javascript">
			//<![CDATA[
				// Define external scripts to be loaded.
				var s = [
					"https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js",
					"<?php echo content('js/main.js'); ?>",
				];
				// Load HeadCatch <http://github.com/mynameiszanders/headcatch>.
				(function(d) {
					var h = d.createElement("script"),
					t = d.getElementsByTagName("script")[0];
					h.src = "<?php echo content('js/head.catch.js'); ?>";
					h.async = true;
					t.parentNode.insertBefore(h, t);
				})(document);
			//]]>
		</script>
	</head>

	<body>
		<div class="container">
			<hgroup>
				<h1>Eventing</h1>
			</hgroup>
			<section>
				<p>
					<?php echo a('~eventingsource', 'Eventing'); ?> is a PHP framework based on the HMVC design pattern
					by <?php echo a('~profile', 'Alexander Baldwin'); ?>, licensed under the <?php echo a('~licensemit',
					'MIT/X11 license'); ?>. At the core, the basic structure is similar to <?php echo a('~ci',
					'CodeIgniter'); ?> (the early core libraries were inspired by Codeigniter's design), but the
					framework boasts features such as modules, namespaces, <?php echo a('example.aspx',
					'multiple URL suffixes'); ?>, a moderately advanced theme templating system, etc.
				</p>
				<p>
					The Eventing Framework, at this moment in time, is a fun side-project and is not recommended for
					production use - unfortunately, documentation is also currently lacking (this will be rectified by
					the time we get round to releasing a public version.<br />
					You can grab a copy of the source <?php echo a('~eventingsource', 'on GitHub'); ?>, or follow
					development progress <?php echo a('~eventingtwitter', 'on Twitter'); ?>.
				</p>
			</section>
			<section>
				<p>
					The page you are looking at is being generated dynamically. If you would like to edit this page's
					controller and view, you'll find themeslocated respectively at:
				</p>
				<code>
					app/controllers/home.php<br />
					app/themes/default/welcome_message.php
				</code>
				<p>Enjoy!</p>
			</section>
			<p style="font-size:80%;">
				Eventing requires <?php echo a('~php53', 'PHP v5.3'); ?> or greater.<br />
				This page was rendered in {elapsed_time} seconds and used {memory_usage} of memory.
			</p>
		</div>
	</body>

</html>