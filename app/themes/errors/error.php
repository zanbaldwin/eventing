<?php
  echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Eventing: <?php echo $title; ?></title>
    <meta charset="utf-8" />
    <style>
      body{margin:2em;font-family:Frutiger,"Frutiger Linotype",Univers,Calibri,
      "Gill Sans","Gill Sans MT","Myriad Pro",Myriad,"DejaVu Sans Condensed",
      "Liberation Sans","Nimbus Sans L",Tahoma,Geneva,"Helvetica Neue",Helvetica,
      Arial,sans-serif;font-size:75%;}header,section,footer{margin:0 auto 2em;}
      hgroup,h1,p{margin:0;padding:0;}h1{font-size:175%;color:#020;}h2{color:#040;
      font-size:125%;}p+p{padding-top:.5em;}section{border:1px solid #090;padding:
      1em;}a{color:#060;text-decoration:none;}a:hover{text-decoration:underline;}
    </style>
  </head>
  <body>
    <header>
      <h1><?php echo htmlentities($title); ?></h1>
    </header>
    <section>
      <p>
        An error occured on line <strong><?php echo $line; ?></strong> in file
        <strong><?php echo htmlentities($file); ?></strong>.
      </p>
      <p>"<?php echo $message; ?>"</p>
    </section>
    <footer>
      <p>Powered by the <a href="http://github.com/mynameiszanders/eventing">Eventing Framework</a>.</p>
    </footer>
  </body>

</html>