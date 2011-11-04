# opentag

## LICENSE:

This file is part of opentag.

opentag is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version. 

opentag is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see [GNU.](http://www.gnu.org/licenses/)

Copyright © 2010 [Reuben Cummings](mailto:reubano@gmail.com?subject=opentag)

## INTRODUCTION:

[opentag](http://github.com/reubano/opentag) is a command line program that converts CSV files to GPX files for importing into GPSPhotoLinker or similar geotagging programs. "opentag](http://github.com/reubano/opentag is distributed under the terms of the GNU General Public License (GPL) and has been tested on the following configuration:
* MacOS X 10.5.8
* PHP 5.3.0
* Console_CommandLine 1.1.1

## REQUIREMENTS:

[opentag](http://github.com/reubano/opentag) requires the following programs in order to run properly:
* [PHP 5.3+](http://pear.php.net/manual/en/installation.php)
* [PEAR](http://us2.php.net/downloads.php)
* [Console_CommandLine](http://pear.php.net/package/Console_CommandLine/download)
* [reubano/opentag](http://github.com/reubano/opentag)

## PREPARATION:

_check that PHP, PEAR, and Console_CommandLine are installed_

	pear -V
	pear list

_set the directory that you wish to hold the program_

	cd /path/to/repos

_clone opentag using [Git](http://git-scm.com/download) (recommended)_

	git clone git://github.com/reubano/library.git
	git clone git://github.com/reubano/opentag.git

## INSTALLATION:


## USING opentag:

### Usage:
	#!/usr/bin/env php
	<?php
	try {
		require_once 'opentag.inc.php';
		$time = time();
		$imageObj = new opentag('src', 1000, 600);
		$imageObj->execute();
		$imageObj->saveFile("opentag/$time.png");
		echo "done!\n";
		exit(0);
	} catch (Exception $e) {
		fwrite(STDOUT, 'Program '.$program.': '.$e->getMessage()."\n");
		exit(1);
	}
	?>
