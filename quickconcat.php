<?php
/*
quickconcat: a simple dynamic concatenator for html, css, and js files
	Copyright 2012, Scott Jehl, Filament Group, Inc. Dual licensed under MIT and GPLv2
	*  accepts 3 query strings:
		* files (required): a comma-separated list of root-relative file paths
		* wrap (optional): Enclose each result in an element node with url attribute? False by default.
		* nocache (optional): Do not read from or create a cache file? False by default.
*/
// List of files, comma-separated paths
$filelist = $_REQUEST[ "files" ];

// Enclose each result in an element node with url attribute?
$wrap = isset( $_REQUEST[ "wrap" ] );

// Do not read from or create a cache file?
$nocache = isset( $_REQUEST[ "nocache" ] );

// Get the filetype and array of files
if ( ! isset( $filelist ) ) {
	echo '$files must be specified!';
	exit;
}

$files = explode( ",", $filelist );

// Guess file type
$fext = preg_match( '/\.(js|html|css)$/', $files[ 0 ], $match );
$ftype = $fext ? $match[ 1 ] : "html";
$type = "text/" . ( $ftype === "js" ? "javascript" : $ftype );

$cache_dir =  __DIR__ . '/.quickconcat-cache';
$cache_name = $cache_dir . '/' . md5( $filelist . ($wrap ? 'wrap' : '') );

// See if cached copy exists
if ( ! $nocache && file_exists($cache_name) ) {
	$contents = file_get_contents($cache_name);
}
else {
	$contents = '';

	// Loop through the files adding them to a string
	foreach ( $files as $file ) {
		if ( preg_match( '/\.(js|html|css)$/', $file ) ){
			$open = $wrap ? "<entry url=\"". $file . "\">" : "";
			$close = $wrap ? "</entry>\n" : "";
			$contents .= $open . file_get_contents($file). $close;
		}
	}

	if ( ! $nocache ) {
		// Write out cache file
		if ( ! is_dir($cache_dir) ) {
			mkdir($cache_dir);
		}
		file_put_contents($cache_name, $contents);
	}
}

// Set the content type and filesize headers
header('Content-Type: ' . $type);
header('Content-Length: ' . strlen($contents));

// Deliver the file
echo $contents;
