<?php
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Eventing: 404 Not Found</title>
		<style type="text/css">
			body{margin:2em;font-family:Frutiger,"Frutiger Linotype",Univers,Calibri,"Gill Sans","Gill Sans MT","Myriad Pro",Myriad,"DejaVu Sans Condensed","Liberation Sans","Nimbus Sans L",Tahoma,Geneva,"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:75%;}
			header,section,footer{margin:0 auto 2em;}
			hgroup,h1,p{margin:0;padding:0;}
			h1{font-size:175%;color:#020;}
			h2{color:#040;font-size:125%;}
			p+p{padding-top:.5em;}
			section{border:1px solid #090;padding:1em;}
			a{color:#060;text-decoration:none;}
			a:hover{text-decoration:underline;}
		</style>
	</head>
	<body>
		<header>
			<h1>404 Not Found</h1>
		</header>
		<section>
			<p>The page you requested does not exist.</p>
			<p>
				Please try navigating to the page from the <?php echo a('', 'homepage'); ?>.
				If you arrived here from a broken link, please <?php echo a('feedback/brokenlink', 'report it'); ?>.
			</p>
		</section>
		<footer>
			<p>Powered by the <?php echo a('https://github.com/lesshub/eventing', 'Eventing Framework'); ?>.</p>
		</footer>
	</body>

</html>