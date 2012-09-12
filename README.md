# opentag

## INTRODUCTION:

[opentag](http://github.com/reubano/opentag) is a command line wrapper for [openmeta](http://code.google.com/p/openmeta/). It adds support for spotlight comments and, long options, and STDIN piping. It has been tested on the following configuration:
* MacOS X 10.7.4
* PHP 5.3.15
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

	git clone git://github.com/reubano/opentag.git

## INSTALLATION:
_Create symlink of opentag in your ~/bin directory_

	if [-d $~/bin]; then mkdir ~/bin; fi
	ln -s ./opentag/opentag.php ~/bin/opentag

_Make opentag executable and add ~/bin to your PATH variable so that you can run 'opentag' from the command line_

	chmod 755 ~/bin/opentag
	echo 'export PATH=~/bin:$PATH' >> ~/.profile
	. ~/.profile

## USING opentag:
### Examples:
	show tags
	  opentag -T /path
	
	show tags and ratings for multiple files
	  opentag -TR /path/1 /path/2
	
	show opentag and spotlight tags
	  opentag -sT /path
	
	add tags foo and bar
	  opentag -a foo,bar /path
	
	add opentag and spotlight tags (prefix spotlight tags with '@')
	  opentag -sp '@' -a foo,bar /path
	  
	add tags foo and bar to all pdf files
	  opentag -a foo,bar *.pdf
	
	set tags to foo and bar for all files with "portfolio" in the name
	  ls | grep -i portfolio | opentag -s foo,bar $
	
	clear all tags
	  opentag -c /path
	
	clear tag foo
	  opentag -u foo /path
	  
	set rating (0 - 5 stars)
	  opentag -r 3 /path
	
	show rating
	  opentag -R /path
	
	clear rating
	  opentag -U /path
  
### Usage:
	opentag [options] <file...>
	
	Options:
	  -a tag(s), --atag=tag(s)    add tag(s) (use a comma to seperate multiple
	                              tags)
	  -c, --ctag                  clear all tags
	  -d, --debug                 enables debug mode, displays the options and
	                              arguments passed to the parser
	  -p prefix, --prefix=prefix  spotlight comment tag prefix, defaults to '&'
	  -r rating, --rate=rating    set rating (0 - 5)
	  -R, --rating                show rating
	  -s, --spotlight             apply tagging commands to spotlight comments
	  -t tag(s), --stag=tag(s)    set tag(s) (use a comma to seperate multiple
	                              tags)
	  -T, --tags                  show all tags
	  -u tag(s), --untag=tag(s)   remove tag(s) (use a comma to seperate
	                              multiple tags)
	  -U, --unrate                remove rating
	  -v, --verbose               verbose output
	  -V, --variables             enables variable mode, displays the value of
	                              all program variables
	  -h, --help                  show this help message and exit
	  --version                   show the program version and exit
	
	Arguments:
	  file  file(s) to tag, enter '$' to read from standard input

## LICENSE:

opentag is distributed under the [MIT License](http://opensource.org/licenses/mit-license.php), the same as [openmeta](http://code.google.com/p/openmeta/) on which this library depends on.