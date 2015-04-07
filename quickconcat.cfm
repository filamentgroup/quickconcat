<cfscript>
	/*
	quickconcat: a simple dynamic concatenator based on https://github.com/filamentgroup/quickconcat/blob/master/quickconcat.php
		Copyright 2013, Chris Weller. Dual licensed under MIT and GPLv2
		*  accepts 2 query strings:
			* files (required): a comma-separated list of root-relative file paths
			* wrap (optional): Enclose each result in an element node with url attribute? False by default.
	*/

	// default variables
	param name="url.files" default="";
	param name="url.wrap" default="false";
	variables.concatcontent = "";
	variables.fileindex = 1;
	variables.files = url.files;
	variables.wrap = url.wrap;

	// if wrap is in the query string but is not explicitly defined then set it to true
	if ( !len(trim(variables.wrap)) ) {
		variables.wrap = true;
	}

	// exit if no files provided
	if ( !ListLen(variables.files) ) {
		exit;
	}

	// used to parse the query string into a structure
	variables.jHTMLToolsObj = createObject('java', 'coldfusion.util.HTMLTools');

	// loop through the files list, get their content, and concatenate them to a string
	for ( variables.fileindex; variables.fileindex LTE ListLen(variables.files); variables.fileindex++ ) {

		// file for this iteration
		variables.includefile = ListGetAt(variables.files, variables.fileindex);

		// path to file for include
		variables.includefilepath = ListFirst(variables.includefile, "?");

		// create structure from query string (useful for conditionally returning file content since it is available in the included file)
		variables.includefilevars = variables.jHTMLToolsObj.parseQueryString(ListLast(variables.includefile, "?"));

		// file extension for include file
		variables.incext = ListLast(variables.includefile, ".");

		// get absolute path of the file for file functions
		variables.absolutefilepath = ExpandPath(variables.includefilepath);

		savecontent variable="variables.includefilecontent" {
			if ( variables.incext IS "css" ) {
				variables.cssfilecontent = getFileContent(filepath=variables.absolutefilepath);
				writeOutput("<style type=""text/css"">#variables.cssfilecontent#</style>");
			}
			else if ( variables.incext IS "js" ) {
				variables.jsfilecontent = getFileContent(filepath=variables.absolutefilepath);
				writeOutput("<script type=""text/javascript"">#variables.jsfilecontent#</script>");
			}
			else {
				if ( FileExists(variables.absolutefilepath) ) {
					// include files are not cached
					include "#variables.includefilepath#";
				}
				else { writeOutput("#variables.includefile#"); }
			}
		}

		if ( variables.wrap ) {
			savecontent variable="variables.modifiedincludefilecontent" {
				writeOutput("<entry url=""#variables.includefile#"">#trim(variables.includefilecontent)#</entry>");
			}
		}
		else {
			savecontent variable="variables.modifiedincludefilecontent" {
				writeOutput(trim(variables.includefilecontent));
			}
		}

		variables.concatcontent &= variables.modifiedincludefilecontent;

	}

	// deliver the content
	writeOutput("#REReplace(variables.concatcontent, '[\t\r\n]', '', 'all')#");

	// read and return content of a file
	function getFileContent(filepath) {
		var local = {};
		local.fileinfo = GetFileInfo(arguments.filepath);
		if (local.fileinfo.size) {
			local.openfile = FileOpen(arguments.filepath, "read");
			local.readfile = FileRead(local.openfile, local.fileinfo.size);
			FileClose(local.openfile);
			return local.readfile;
		}
		else {
			return "";
		}
	}
</cfscript>