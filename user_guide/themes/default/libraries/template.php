<!DOCTYPE html><html><head><link href="" rel="stylesheet" media="all" /><title>Template Docs</title></head><body><div class="content">


<h1>Template Class</h1>
<p>
  The Template Library enables you to parse pseudo-variables contained
  within your view files. It can parse simple variables or variable tag pairs.
  If you've never used a template engine, pseudo-variables look like this:
</p>
 
<code>
  &lt;html&gt;<br />
  &lt;head&gt;<br />
  &lt;title&gt;<kbd>{blog_title}</kbd>&lt;/title&gt;<br />
  &lt;/head&gt;<br />
  &lt;body&gt;<br />
  <br />
  &lt;h3&gt;<kbd>{blog_heading}</kbd>&lt;/h3&gt;<br />
  <br />
  <kbd>{blog_entries}</kbd><br />
  &lt;h5&gt;<kbd>{title}</kbd>&lt;/h5&gt;<br />
  &lt;p&gt;<kbd>{body}</kbd>&lt;/p&gt;<br />
  <kbd>{/blog_entries}</kbd><br />
  &lt;/body&gt;<br />
  &lt;/html&gt;
</code>

<p>These variables are not actual PHP variables, but rather plain text representations that allow you to eliminate
PHP from your templates (view files).</p> 
 
<p class="important"><strong>Note:</strong> CodeIgniter does <strong>not</strong> require you to use this class
since using pure PHP in your view pages lets them run a little faster.  However, some developers prefer to use a template engine if
they work with designers who they feel would find some confusion working with PHP.</p> 
 
<p><strong>Also Note:</strong> The Template Parser Class is <strong>not</strong>  a
full-blown template parsing solution. We've kept it very lean on purpose in order to maintain maximum performance.</p> 
 
 
<h2>Initializing the Class</h2> 
 
<p>
  Like most other classes in Eventing, the Template class is initialized in your
  controller using the <dfn>$this->load->library</dfn> function:
</p>

<code>$this->load->library('template');</code>

<p>
  Once loaded, the Parser library object will be available using: <dfn>$this->template</dfn>
</p>

<p>The following functions are available in this library:</p>

<!--
BASE
====

set_dir
set_prefix
view_exists

TEMPLATE
========

section_exists
create
acreate
group
agroup
join
ajoin
active
section
link
autolink
propagate
autoload
load

SECTION
=======

name
data
content
add
-->
 
<h2>$this->template->set_dir()</h2>
 
<p>
  This method accepts a directory name as its only input.
  This is to set a sub-directory of the views directory to be used as the
  directory to fetch views from, useful if you wish to split your views into
  different themes.
  Example:
</p>
 
<code>
  $this->load->library('parser');<br />
  <br />
  // The section will have the view "views/document.php".<br />
  $this->template->create('document');<br />
  <br />
  // The following section will have the view "views/myTheme/document.php".<br />
  $this->template->set_dir('myTheme');<br />
  $this->template->create('document');
</code>
 
<p>The first, and only, parameter contains the name of the <a href="../general/views.html">view file</a> (in this example the file would be called blog_template.php),
and the second parameter contains an associative array of data to be replaced in the template.  In the above example, the
template would contain two variables: {blog_title} and {blog_heading}</p> 
 
<p>There is no need to "echo" or do something with the data returned by <dfn>$this->parser->parse()</dfn>.  It is automatically
passed to the output class to be sent to the browser.  However, if you do want the data returned instead of sent to the output class you can
pass TRUE (boolean) to the third parameter:</p> 
 
<code>$string = $this->parser->parse('blog_template', $data, TRUE);</code> 
 
 
<h2>Variable Pairs</h2> 
 
<p>The above example code allows simple variables to be replaced.  What if you would like an entire block of variables to be
repeated, with each iteration containing new values?  Consider the template example we showed at the top of the page:</p> 
 
<code>&lt;html&gt;<br /> 
&lt;head&gt;<br /> 
&lt;title&gt;<kbd>{blog_title}</kbd>&lt;/title&gt;<br /> 
&lt;/head&gt;<br /> 
&lt;body&gt;<br /> 
<br /> 
&lt;h3&gt;<kbd>{blog_heading}</kbd>&lt;/h3&gt;<br /> 
<br /> 
<kbd>{blog_entries}</kbd><br /> 
&lt;h5&gt;<kbd>{title}</kbd>&lt;/h5&gt;<br /> 
&lt;p&gt;<kbd>{body}</kbd>&lt;/p&gt;<br /> 
<kbd>{/blog_entries}</kbd><br /> 
 
&lt;/body&gt;<br /> 
&lt;/html&gt;</code> 
 
<p>In the above code you'll notice a pair of variables: <kbd>{blog_entries}</kbd> data... <kbd>{/blog_entries}</kbd>.
In a case like this, the entire chunk of data between these pairs would be repeated multiple times, corresponding
to the number of rows in a result.</p> 
 
<p>Parsing variable pairs is done using the identical code shown above to parse single variables,
except, you will add a multi-dimensional array corresponding to your variable pair data.
Consider this example:</p> 
 
 
<code>$this->load->library('parser');<br /> 
<br /> 
$data = array(<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_title'&nbsp;&nbsp; => 'My Blog Title',<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_heading' => 'My Blog Heading',<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_entries' => array(<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array('title' => 'Title 1', 'body' => 'Body 1'),<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array('title' => 'Title 2', 'body' => 'Body 2'),<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array('title' => 'Title 3', 'body' => 'Body 3'),<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array('title' => 'Title 4', 'body' => 'Body 4'),<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array('title' => 'Title 5', 'body' => 'Body 5')<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;);<br /> 
<br /> 
$this->parser->parse('blog_template', $data);</code> 
 
<p>If your "pair" data is coming from a database result, which is already a multi-dimensional array, you can simply
use the database result_array() function:</p> 
 
<code> 
$query = $this->db->query("SELECT * FROM blog");<br /> 
<br /> 
$this->load->library('parser');<br /> 
<br /> 
$data = array(<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_title'&nbsp;&nbsp; => 'My Blog Title',<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_heading' => 'My Blog Heading',<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'blog_entries' => $query->result_array()<br /> 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;);<br /> 
<br /> 
$this->parser->parse('blog_template', $data);</code> 













</div></body></html>