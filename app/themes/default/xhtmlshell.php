<?php
    if(!headers_sent())
    {
        $content_type = stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml')
                     && !preg_match("/application\/xhtml\+xml;\s*q=0(\.0)?\s*(,|$)/", $_SERVER['HTTP_ACCEPT'])
                      ? 'application/xhtml+xml'
                      : 'text/html';
        header('Content-Type: ' . $content_type);
    }
    echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <!-- This page was generated in {elapsed_time} seconds and took {memory_usage} of memory. -->
        <title><?php echo $title; ?></title>
        <meta http-equiv="content-type" content="text/html" />
        <meta http-equiv="content-style-type" content="text/css" />
        <link href="<?php echo $favicon; ?>" rel="shortcut icon" type="image/png" title="Eventing Favicon" />
        <!--{googleapi}-->
        <!--{style}-->
    </head>

    <body>
        <!--{welcome_message}-->
    </body>
</html>