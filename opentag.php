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
$intOption 			= 1;
$strOption 			= 'string';
$ext 				= 'png';

// include files
require_once 'Console/CommandLine.php';
require_once $thisProjectDir.'/lib_general/General.inc.php';

// create the parser from xml file
$xmlFile = $thisProjectDir.'/someprogram.xml';
$parser = Console_CommandLine::fromxmlFile($xmlFile);

try {	
	// run the parser
	$result = $parser->parse();

	// command arguments
	$input = $result->args['input'];
	
	if ($input == '$') {
		$stdin = TRUE;
	} //<-- end if -->

	if ($result->args['output']) {
		$output = $result->args['output'];
	} else {
		$output = $thisProjectDir.'/export/chart'.'_'.$timeStamp.'.'.$ext;
	} //<-- end if -->
	
	// command options
	$debugmode	= $result->options['debug'];
	$varmode	= $result->options['variables'];
	$verbose	= $result->options['verbose'];

	// program setting
	$general = new general($verbose);
	$program = $general->getBase(__FILE__);
	
	// load options if present
	if ($result->options['intOption']) {
		$intOption = $result->options['intOption'];
	} //<-- end if -->
	
	if ($result->options['strOption']) {
		$strOption = $result->options['strOption'];
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
		$input = $general->readSTDIN();
	} //<-- end if -->

	exit(0);
} catch (Exception $e) {
	fwrite(STDOUT, 'Program '.$program.': '.$e->getMessage()."\n");
	exit(1);
}
?>