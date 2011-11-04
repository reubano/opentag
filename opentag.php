#!/usr/bin/env php
<?php
/**
 ********************************************************************************
 * Program description
 ********************************************************************************
 */
 
$thisProjectDir		= dirname(__FILE__);
$timeStamp			= date("mdy").'_'.date("His"); // format to mmddyy_hhmmss
$stdin 				= FALSE;
$prefix 			= '&';
$allFiles			= '';
$addTag				= '';
$setTag				= '';
$rate				= 0;

// include files
require_once 'Console/CommandLine.php';
require_once $thisProjectDir.'/lib_general/General.inc.php';

// create the parser from xml file
$xmlFile = $thisProjectDir.'/opentag.xml';
$parser = Console_CommandLine::fromxmlFile($xmlFile);

try {	
	// run the parser
	$result = $parser->parse();

	// command argument
	$file = $result->args['file'];
	
	if ($file == '$') {
		$stdin = TRUE;
	} //<-- end if -->
	
	// command options
	$debugmode	= $result->options['debug'];
	$varmode	= $result->options['variables'];
	$verbose	= $result->options['verbose'];
	$rating		= $result->options['rating'];
	$spotlight	= $result->options['spotlight'];
	$tags		= $result->options['tags'];
	$untag		= $result->options['untag'];
	$unrate		= $result->options['unrate'];
	
	// program setting
	$general = new general($verbose);
	$program = $general->getBase(__FILE__);
	
	// load options if present
	if ($result->options['prefix']) {
		$prefix = $result->options['prefix'];
	} //<-- end if -->
	
	if ($result->options['rate']) {
		$rate = $result->options['rate'];
	} //<-- end if -->
	
	if ($result->options['addTag']) {
		$addTag = $result->options['addTag'];
	} //<-- end if -->
	
	if ($result->options['setTag']) {
		$setTag = $result->options['setTag'];
	} //<-- end if -->

	// debug and variable mode settings
	if ($debugmode OR $varmode) {
		if ($debugmode) {
			print('[Command opts] ');
			print_r($result->options);
			print('[Command args] ');
			print_r($result->args);
		} //<-- end if -->

		if ($varmode) {
			print_r($general->getVars());
		} //<-- end if -->
		
		exit(0);
	} //<-- end if -->

	// execute program
	if($stdin) {
		$file = $general->readSTDIN();
		$file = explode("\n", $file); // turn string to array
		array_pop($file); // remove last element since it is empty
	} //<-- end if -->
			
	foreach ($file as $key => $value) {
		if (strpos($value, '/') === FALSE) {
			$file[$key] = '"'.exec("pwd").'/'.$value.'"';
		} //<-- end if -->
	} //<-- end foreach -->

	foreach ($file as $aFile) {
		$allFiles .= $aFile.' ';
	} //<-- end foreach -->
	
	$allFiles = trim($allFiles);
	
	if ($addTag || $setTag) {
		$theTags = str_replace(',', ' ', $addTag);
		
		if ($addTag) {
			exec("openmeta -a $theTags -p $allFiles");
		} else {
			exec("openmeta -s $theTags -p $allFiles");
		} //<-- end if -->
	} //<-- end if -->
	
	if ($untag) {
		exec("openmeta -s -p $allFiles");
	} //<-- end if -->
	
	if ($tags) {
		exec("mdls -name kMDItemOMUserTags -raw $allFiles", $output);
		array_pop($output);
		array_shift($output);
		foreach 
		print_r($output);
		fwrite(STDOUT, "openmeta: \n");

		if ($spotlight) {
			$comment = exec("mdls -name kMDItemFinderComment -raw $allFiles");	
			$comment = explode(' ', $comment);
			
			foreach ($comment as $chunk) {
				if (strpos($chunk, $prefix) === 0) {
					$tags .= str_replace($prefix, '', $chunk).' ';
				} //<-- end if -->
			} //<-- end foreach -->
			
			fwrite(STDOUT, "spotlight: $tags\n");
		} //<-- end if -->
	} //<-- end if -->
	
	if ($rate) {
		exec("openmeta -r $rate -p $allFiles");
	} //<-- end if -->
	
	if ($unrate) {
		exec("openmeta -r 0 -p $allFiles");
	} //<-- end if -->
	
	if ($rating) {
		exec("openmeta -r -p $allFiles");
	} //<-- end if -->
	
	exit(0);
} catch (Exception $e) {
	fwrite(STDOUT, 'Program '.$program.': '.$e->getMessage()."\n");
	exit(1);
}
?>
