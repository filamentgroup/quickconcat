# quickconcat

a simple dynamic concatenator for html, css, and js files, written in PHP

* Copyright 2012, Scott Jehl, Filament Group, Inc. 
* Dual licensed under MIT and GPLv3

concat.php accepts 2 query strings: 

		* files (required): a comma-separated list of root-relative file paths
		* wrap (optional): Enclose each result in an element node with url attribute? False by default.
		
[![Build Status](https://travis-ci.org/filamentgroup/quickconcat.png)](http://travis-ci.org/filamentgroup/quickconcat)

Example CSS url: 

    quickconcat.php?files=css/a.css,css/b.css,css/c.css

Example JS url:

    quickconcat.php?files=js/a.js,js/b.js,js/c.js

Example HTML url: 

    quickconcat.php?files=a.html,b.html,c.html

Example HTML url with wrapped entries:

    quickconcat.php?files=a.html,b.html,c.html&wrap

Note that when `wrap` is passed, like above, each file will be wrapped in a `entry` element with a `url` attribute corresponding to the URL from which the content was included. For example:

    <entry url="a.html">...content from file a here...</entry><entry url="b.html">...content from file b here...</entry><entry url="c.html">...content from file c here...</entry>
	
## optional .htaccess ideas

To clean up your file paths, gzip output, and more, you might consider incorporating some server rewrites via Apache `.htaccess` or otherwise. Here's an example that allows for cleaner urls and zipped output:

	# Cleaner URLs for quickconcat.php
	# this allows for urls like this: "/path/to/file.html,path/to/fileb.html=concat"
	# or wrapped, "/path/to/file.html,path/to/fileb.html=concat&wrap"
	RewriteEngine On
	RewriteRule ^([^\?]+)=concat(&wrap)?$ quickconcat.php?files=$1$2

	# compress transfer
	<IfModule mod_deflate.c>
	AddOutputFilterByType DEFLATE text/html text/css text/javascript
	</IfModule>

With the above dropped into your `.htaccess` file, you can concatenate files like this: `/path/to/file.html,path/to/fileb.html=concat` or like this (if you want to add wrappers) `/path/to/file.html,path/to/fileb.html=concat&wrap`
	
	

