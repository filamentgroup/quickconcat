<?php
/*
quickconcat: a simple dynamic concatenator for html, css, and js files
	Copyright 2012, Scott Jehl, Filament Group, Inc. Dual licensed under MIT and GPLv2
	*  accepts 2 query strings:
	* files (required): a comma-separated list of root-relative file paths
	* wrap (optional): Enclose each result in an element node with url attribute? False by default.
*/

// List of files, comma-separated paths
$filelist = $_REQUEST[ 'files' ];

// If quickconcat is in a directory off the root, add a relative path here back to the root, like "../"
$relativeroot = "";

// get the public path to this file, plus the baseurl
$pubroot = dirname( $_SERVER['PHP_SELF'] ) . "/" . $relativeroot;

// Enclose each result in an element node with url attribute?
$wrap = isset( $_REQUEST[ 'wrap' ] );

// Get the filetype and array of files
if ( ! isset( $filelist ) ){
	echo '$files must be specified!';
	exit;
}

$files = explode( ",", $filelist );
$appRoot = dirname(__FILE__);
$ftype = null;
$lmodified = 0;

// sanitize file-parameters
foreach ( $files as $idx => $file ) {
    // we allow only certain filetypes
	// all files must be contained in the same folder or in one of our subfolders
	if( !($fext = preg_match( '/\.(js|html|css)$/', $file, $match )) || 
		strpos(realpath($file), $appRoot) !== 0){
		unset($files[$idx]);
	} else if (!$ftype && $match) {
		// Guess file type
		$type = $fext ? $match[ 1 ] : 'html';
		$ftype = 'text/' . ( $type === 'js' ? 'javascript' : $type );
		
		$mtime = filemtime($file);
		if($mtime > $lmodified) {
			$lmodified = $mtime;
		}
	}
}

// fast exit, in case the browsers-cache is up2date
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lmodified) {
	header('HTTP/1.1 304 Not Modified');
	exit();
}

$contents = '';

// Loop through the files adding them to a string
foreach ( $files as $file ) {
	$open = $wrap ? "<entry url=\"". $file . "\">" : "";
	$close = $wrap ? "</entry>\n" : "";
	$newcontents = $open . file_get_contents($relativeroot . $file). $close;
	//prefix relative CSS paths (TODO: HTML as well)
	if( $ftype === "css" ){
		$prefix = $pubroot . dirname($file) . "/";
		$newcontents = preg_replace( '/(url\(["\']?)([^\/])([^\:\)]+["\']?\))/', "$1" . $prefix .  "$2$3", $newcontents );
		//temp cleanup for root-relative paths that aren't caught when quoted above. should be doable in one replace above
		$newcontents = preg_replace( '/(url\()([^"\']+)(["\'])/', "$1$3", $newcontents ); 
	}
	$contents .= $newcontents;
}

// Set the content type and filesize headers
header('Content-Type: ' . $type);
header('Content-Length: ' . strlen($contents));
if ($lmodified > 0) {
	header('Last-Modified: '. $lmodified);
}

// Deliver the file
echo $contents;
