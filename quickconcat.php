<?php
/*
quickconcat: a simple dynamic concatenator for html, css, and js files
	Copyright 2012, Scott Jehl, Filament Group, Inc. Dual licensed under MIT and GPLv2
	*  accepts 2 query strings:
		* files (required): a comma-separated list of root-relative file paths
		* wrap (optional): Enclose each result in an element node with url attribute? False by default.
*/

function is_file_in_scope( $file ){
	$appRoot = dirname(__FILE__);
	$realpath = realpath($file);
	return ($file && is_file($realpath) && strpos($realpath, $appRoot) === 0);
}

// List of files, comma-separated paths
$filelist = $_REQUEST[ "files" ];

// If quickconcat is in a directory off the root, add a relative path here back to the root, like "../"
$relativeroot = "";

// get the public path to this file, plus the baseurl
$pubroot = dirname( $_SERVER['PHP_SELF'] ) . "/" . $relativeroot;

// Enclose each result in an element node with url attribute?
$wrap = isset( $_REQUEST[ "wrap" ] );

// Get the filetype and array of files
if ( ! isset( $filelist ) ){
	echo '$files must be specified!';
	exit;
}

$files = explode( ",", $filelist );

// sanitize file-parameters
foreach ( $files as $idx => $file ) {
	// we allow only certain filetypes
	if( !( preg_match( '/\.(js|html|css)$/', $file ) && is_file_in_scope($file) ) ){
		unset($files[$idx]);
	}
}

$files = array_values( $files );
if( count( $files ) == 0 ){
	exit;
}

$lmodified = 0;

// build last-modified date for the file-bundle
foreach ( $files as $file ) {
	$mtime = filemtime($file);
	if($mtime > $lmodified) {
		$lmodified = $mtime;
	}
}

// fast exit, in case the browsers-cache is up2date
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $lmodified > 0 && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lmodified) {
	header('HTTP/1.1 304 Not Modified');
	exit();
}
// Guess file type
$fext = preg_match( '/\.(js|html|css)$/', $files[ 0 ], $match );
$ftype = $fext ? $match[ 1 ] : "html";
$type = "text/" . ( $ftype === "js" ? "javascript" : $ftype );

$contents = '';

// Loop through the files adding them to a string
foreach ( $files as $file ) {
	$open = $wrap ? "<entry url=\"". $file . "\">" : "";
	$close = $wrap ? "</entry>\n" : "";
	$newcontents = $open . file_get_contents($relativeroot . $file). $close;
	//prefix relative CSS paths (TODO: HTML as well)
	if( $ftype === "css" ){
		$prefix = $pubroot . dirname($file) . "/";
		$newcontents = preg_replace( '/(url\(["\']?)([^\/"\'])([^\:\)]+["\']?\))/i', "$1" . $prefix .  "$2$3", $newcontents );
	}
	$contents .= $newcontents;
	if( $ftype === "js" ){
		// add an extra semicolon, so ASI and non-ASI js-files will not clash
		$contents .= ';';
	}
}

// Set the content type and filesize headers
header('Content-Type: ' . $type);
header('Content-Length: ' . strlen($contents));
if ($lmodified > 0) {
	header('Last-Modified: '. $lmodified);
}

// Deliver the file
echo $contents;
