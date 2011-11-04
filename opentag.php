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
$addTag				= NULL;
$setTag				= NULL;
$untag				= NULL;
$rate				= NULL;

// include files
require_once 'Console/CommandLine.php';
require_once $thisProjectDir.'/lib_general/General.inc.php';
require_once $thisProjectDir.'/lib_openmeta/Openmeta.inc.php';

// create the parser from xml file
$xmlFile = $thisProjectDir.'/opentag.xml';
$parser = Console_CommandLine::fromxmlFile($xmlFile);

try {	
	// run the parser
	$result = $parser->parse();

	// command argument
	$files = $result->args['files'];
	
	if ($files == '$') {
		$stdin = TRUE;
	} //<-- end if -->
	
	// command options
	$debugmode	= $result->options['debug'];
	$varmode	= $result->options['variables'];
	$verbose	= $result->options['verbose'];
	$rating		= $result->options['rating'];
	$spotlight	= $result->options['spotlight'];
	$tags		= $result->options['tags'];
	$clearTag	= $result->options['clearTag'];
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

	if ($result->options['untag']) {
		$untag = $result->options['untag'];
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
		$files = $general->readSTDIN();
		$files = $general->lines2Array($files);
	} //<-- end if -->
	
	$files = $general->getFullPath($files);
	$files = '\''.implode('\' \'', $files).'\''; // array to string	
	$openmeta = new openmeta($files);
	
	if ($addTag) {
		$openTags = str_replace(',', ' ', $addTag);
		$openmeta->addOpenTags($openTags);
	} //<-- end if -->

	if ($setTag) {
		$openTags = str_replace(',', ' ', $setTag);
		$openmeta->setOpenTags($openTags);
	} //<-- end if -->
	
	if ($clearTag) {
		$openmeta->clearOpenTags();
	} //<-- end if -->

	if ($addTag) {
		$openTags = str_replace(',', ' ', $addTag);
		$openmeta->addOpenTags($openTags);
	} //<-- end if -->

	if ($untag) {
		$openTags = str_replace(',', ' ', $untag);
		$openmeta->removeOpenTags($openTags);
	} //<-- end if -->

	if ($tags) {
		$openTags = $openmeta->getOpenTags();
		
		if (count($openTags) > 0) {
			$openTags = implode(', ', $openTags); // array to string
		} else {
			$openTags = '';
		}//<-- end if -->
		
		fwrite(STDOUT, "openmeta tags: $openTags\n");
		
		if ($spotlight) {
			$spotlightTags = $openmeta->getSpotlightTags($prefix);
			
			if (count($spotlightTags) > 0) {
				$spotlightTags = implode(', ', $spotlightTags); // array to string
			} else {
				$spotlightTags = '';
			}//<-- end if -->
			
			fwrite(STDOUT, "spotlight tags: $spotlightTags\n");
		} //<-- end if -->
	} //<-- end if -->
	
	if ($rate) {
		$openmeta->setRating($rate);
	} //<-- end if -->
	
	if ($unrate) {
		$openmeta->clearRating($rate);
	} //<-- end if -->
	
	if ($rating) {
		$rating = $openmeta->getRating();
		fwrite(STDOUT, "rating: $rating[0]\n");
	} //<-- end if -->
	
	exit(0);
} catch (Exception $e) {
	fwrite(STDOUT, 'Program '.$program.': '.$e->getMessage()."\n");
	exit(1);
}
?>
