<?php
/******************************************************************************
 * purpose: contains metadata functions
 *****************************************************************************/

//<-- begin class -->
class Openmeta {
	protected $className 	= __CLASS__;	// class name
	protected $verbose;
	protected $files;
	protected $openmeta 	= '~/Documents/Projects/opentag/lib_openmeta/openmeta';
	public $openTags;
	public $spotlightTags;
	public $rating;
	/************************************************************************** 
	 * The class constructor
	 *
	 * @param 	boolean $verbose	enable verbose comments
	 *************************************************************************/
	function __construct($files, $verbose = FALSE) {
		$this->files = $files;
		$this->verbose = $verbose;
		
		if ($this->verbose) {
			fwrite(STDOUT, "$this->className class constructor set.\n");
		} //<-- end if -->
	} //<-- end function -->

	/************************************************************************** 
	 * Adds openmeta tags
	 *
	 * @param 	string 	$tags			space delimited tags to add
	 * @return 	string	$this->openTags	the resulting openmeta tags
	 * @throws 	Exception if $tags is empty
	 *************************************************************************/
	public function addOpenTags($tags) {
		if (empty($tags)) {
			throw new Exception('Empty string passed from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				exec("$this->openmeta -a $tags -p $this->files");
				
				if (isset($this->openTags)) {
					$this->openTags[] = explode(' ', $tags);
					
					if (count($this->openTags) > 1) {
						sort($this->openTags);
					} //<-- end if -->
				} else {
					self::getOpenTags();
				} //<-- end if -->
				
				return $this->openTags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/************************************************************************** 
	 * Sets openmeta tags
	 *
	 * @param 	string 	$tags 			space delimited tags to set
	 * @return 	string	$this->openTags	the set openmeta tags
	 * @throws 	Exception if $tags is empty
	 *************************************************************************/
	public function setOpenTags($tags) {
		if (empty($tags)) {
			throw new Exception('Empty string passed from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				exec("$this->openmeta -s $tags -p $this->files");
				$this->openTags = explode(' ', $tags);
				
				if (count($this->openTags) > 1) {
					sort($this->openTags);
				} //<-- end if -->
					
				return $this->openTags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/************************************************************************** 
	 * Sets openmeta rating
	 *
	 * @param 	string 	$rating the rating
	 * @return 	string	$this->rating	the set rating
	 * @throws 	Exception if $rating is empty
	 *************************************************************************/
	public function setRating($rating) {
		if (empty($rating)) {
			throw new Exception('Empty rating passed from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				$this->rating = $rating;
				exec("$this->openmeta -r $this->rating -p $this->files");
				return $this->rating;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/************************************************************************** 
	 * Return openmeta tags
	 *
	 * @return 	string	$this->openTags	the current openmeta tags
	 *************************************************************************/
	public function getOpenTags() {
		try {
			if (!isset($this->openTags)) {
				exec("mdls -name kMDItemOMUserTags -raw $this->files", $this->openTags);
				array_shift($this->openTags); // remove '('
				array_pop($this->openTags); // remove ')'
				$count = count($this->openTags);
				
				if ($count > 0) {
					sort($this->openTags);
					$this->openTags = array_map(
						'trim',
						$this->openTags,
						array_fill(0, $count, " \t,")
					);
				} //<-- end if -->
			} //<-- end if -->
			
			return $this->openTags;
		} catch (Exception $e) {
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/************************************************************************** 
	 * Return spotlight tags
	 *
	 * @param 	string	$prefix					spotlight comment tag prefix
	 * @return 	string	$this->spotlightTags	the current spotlight tags
	 *************************************************************************/
	public function getSpotlightTags($prefix) {
		try {
			if (!isset($this->spotlightTags)) {
				$tags = exec("mdls -name kMDItemFinderComment -raw $this->files");
				$tags = explode(' ', $tags);

				foreach ($tags as $tag) {
					if (strpos($tag, $prefix) === 0) {
						$this->spotlightTags[] = str_replace($prefix, '', $tag);
					} //<-- end if -->
				} //<-- end foreach -->
			
				if (count($this->spotlightTags) > 1) {
					sort($this->spotlightTags);
				} //<-- end if -->
			} //<-- end if -->
			
			return $this->spotlightTags;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/************************************************************************** 
	 * Return rating
	 *
	 * @return 	string	$this->rating	the rating
	 *************************************************************************/
	public function getRating() {
		try {
			if (!isset($this->rating)) {
				exec("mdls -name kMDItemStarRating -raw $this->files", $this->rating);
			} //<-- end if -->
			
			return $this->rating;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/************************************************************************** 
	 * Clear all openmeta tags
	 *
	 * @return 	string	$result		the last line from openmeta command
	 *************************************************************************/
	public function clearOpenTags() {
		try {
			$result = exec("$this->openmeta -s -p $this->files");
			$this->openTags = array();
			return $result;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/************************************************************************** 
	 * Remove requested openmeta tags
	 *
	 * @param 	string 	$tags 	space delimited tags to remove
	 * @return 	string	$this->openTags	the resulting openmeta tags
	 *************************************************************************/
	public function removeOpenTags($tags) {
		try {
			$tags = explode(' ', $tags); // string to array
			
			if (!isset($this->openTags)) {
				self::getOpenTags();
			} //<-- end if -->
			
			$remainingTags = array_diff($this->openTags, $tags);
			
			if (count($remainingTags) > 0) {
				$remainingTags = implode(' ', $remainingTags); // array to string
				self::clearOpenTags();
				self::addOpenTags($remainingTags);
			} //<-- end if -->
			
			return $this->openTags;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/************************************************************************** 
	 * Clear rating
	 *
	 * @return 	string	$result		the last line from openmeta command
	 *************************************************************************/
	public function clearRating() {
		try {
			$result = exec("$this->openmeta -r 0 -p $this->files");
			$this->rating = 0;
			return $result;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.__CLASS__.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->
} //<-- end class -->
?>