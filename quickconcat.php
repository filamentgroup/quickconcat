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

// Enclose each result in an element node with url attribute?
$wrap = isset( $_REQUEST[ 'wrap' ] );

// Get the filetype and array of files
if ( ! isset( $filelist ) ){
	echo '$files must be specified!';
	exit;
}

$files = explode( ',', $filelist );
$ftype = null;
$appRoot = dirname(__FILE__);

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
	}
}

// collect metadata
$fps = array();
$fsize = 0;
$lmodified = 0;
foreach ( $files as $idx => $file ) {
	$fps[$idx] = fopen($file, 'rb');
	
	$fsize += filesize($file);
	// account for additional chars which will be added due to wrapping
	if($wrap) {
		$fsize += 14; // <entry url="">
		$fsize += strlen($file); // the url itself
		$fsize += 8; // </entry>
	}
	
	$mtime = filemtime($file);
	if($mtime > $lmodified) {
		$lmodified = $mtime;
	}
}

// fast exit, in case the browsers-cache is up2date
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lmodified) {
	header('HTTP/1.1 304 Not Modified');
	exit();
}

// Set the content type, length and last-modification stamp
header('Content-Type: ' . $ftype);
header('Content-Length: ' . $fsize);
header('Last-Modified: '. $lmodified);

// Deliver the files
foreach ( $fps as $idx => $fp ) {
	if ($fp) {
		if($wrap) {
			// in case you change the markup here, don't forget to update the corresponding filesize additions
			echo '<entry url="'. $files[$idx] . '">';
			fpassthru($fp);
			echo '</entry>';
		}
		else {
			fpassthru($fp);
		}
		fclose($fp);
	}
}
