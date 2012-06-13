<?php
/*******************************************************************************
 * purpose: contains metadata functions
 ******************************************************************************/

// include files
$thisProjectDir	= dirname(dirname(__FILE__));
require_once $thisProjectDir.'/lib_general/General.inc.php';

//<-- begin class -->
class Openmeta {
	protected $className = __CLASS__;	// class name
	protected $verbose;
	protected $files;
	protected $fileList;
	protected $openmeta;
	public $openTags;
	public $spotlightTags;
	public $rating;

	/*************************************************************************** 
	 * The class constructor
	 *
	 * @param 	array $files		list of files
	 * @param 	string $projectDir	project directory
	 * @param 	boolean $verbose	enable verbose comments
	 **************************************************************************/
	function __construct($files, $projectDir, $verbose = FALSE) {
		$this->files = $files;
		$this->fileList = general::extraImplode($files);
		$this->openmeta = $projectDir.'/lib_openmeta/openmeta';
		$this->verbose = $verbose;
		
		if ($this->verbose) {
			fwrite(STDOUT, "$this->className class constructor set.\n");
		} //<-- end if -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Adds openmeta tags
	 *
	 * @param 	string 	$tags			space delimited tags to add
	 * @return 	array	$this->openTags	the resulting openmeta tags
	 * @throws 	Exception if $tags is empty
	 **************************************************************************/
	public function addOpenTags($tags) {
		if (empty($tags)) {
			throw new Exception('Empty tag passed from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				// set result (new complete list of tags) to $openTagList
				exec("$this->openmeta -a $tags -p $this->fileList", 
					$openTagList
				);
				
				$count = count($openTagList);
				
				if ($count > 0) {				
					$openTagList = array_map(
						'rtrim',
						$openTagList,
						array_fill(0, $count, implode(' ', $this->files))
					);
					
					// reset $this->openTags since I have the new complete list
					$this->openTags = array();
					
					foreach ($openTagList as $tags) {
						$this->openTags[] = explode(' ', $tags);
					} //<-- end foreach -->
				} //<-- end if -->
					
				return $this->openTags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.$this->className.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Adds spotlight tags
	 *
	 * @param 	string 	$tags					space delimited tags to add
	 * @return 	array	$this->spotlightTags	the resulting spotlight tags
	 * @throws 	Exception if $tags is empty
	 **************************************************************************/
	public function addSpotlightTags($tags, $prefix = '&') {
		if (empty($tags)) {
			throw new Exception('Empty tag passed from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				if (!isset($this->spotlightTags)) {
					self::getSpotlightTags();
				} //<-- end if -->
				
				$additionalTags = self::_makeSpotlightTags($tags, $prefix);
				$additionalTags = explode(' ', $tags);

				foreach ($this->spotlightTags as $key => $value) {
					$spotlightTags[$key] = array_merge($value, $additionalTags);
					$spotlightTags[$key] = array_unique($spotlightTags[$key]);
					$spotlightTagList[$key] = implode(' ', 
						$spotlightTags[$key]
					);
				} //<-- end foreach -->
				
				$files = $this->files;
				
				foreach ($files as $key => $value) {
					$this->files = array($value);
					self::setSpotlightTags($spotlightTagList[$key], $prefix);
				} //<-- end foreach -->
								
				if (count($spotlightTags) > 0) {									
					$this->spotlightTags = $spotlightTags;
				} //<-- end if -->

				$this->files = $files;
				return $this->spotlightTags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.$this->className.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Sets openmeta tags
	 *
	 * @param 	string 	$tags 			space delimited tags to set
	 * @return 	array	$this->openTags	the set openmeta tags
	 * @throws 	Exception if $tags is empty
	 **************************************************************************/
	public function setOpenTags($tags) {
		if (empty($tags)) {
			throw new Exception('Empty tag passed from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				exec("$this->openmeta -s $tags -p $this->fileList");
				$this->openTags = array();
				$count = count($this->files);
				$i = 0;

				while ($i < $count) {
					$this->openTags[$i] = explode(' ', $tags);
					
					if (count($this->openTags[$i]) > 1) {
						sort($this->openTags[$i]);
					} //<-- end if -->
					
					$i++;
				} //<-- end foreach -->

				return $this->openTags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.$this->className.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Sets spotlight tags
	 *
	 * @param 	string 	$tags 					space delimited tags to set
	 * @return 	array	$this->spotlightTags	the set spotlight tags
	 **************************************************************************/
	public function setSpotlightTags($tags, $prefix = '&') {
		try {
			if ($tags) {
				$tags = self::_makeSpotlightTags($tags, $prefix);
			} //<-- end if -->

			foreach ($this->files as $file) {
				exec('osascript -e \'tell application "Finder" to set comment '.
					'of file POSIX file "'.$file.'" to "'.$tags.'"\''
				);
			} //<-- end foreach -->
			
			$count = count($this->files);
			$i = 0;

			while ($i < $count) {
				$this->spotlightTags[$i] = explode(' ', $tags);
				
				if (count($this->spotlightTags[$i]) > 1) {
					sort($this->spotlightTags[$i]);
				} //<-- end if -->
				
				$i++;
			} //<-- end foreach -->

			return $this->spotlightTags;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Sets openmeta rating
	 *
	 * @param 	string 	$rating 		the rating
	 * @return 	string	$this->rating	the set rating
	 * @throws 	Exception if $rating is empty
	 **************************************************************************/
	public function setRating($rating) {
		if (empty($rating)) {
			throw new Exception('Empty rating passed from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				$this->rating = $rating;
				exec("$this->openmeta -r $this->rating -p $this->fileList");
				return $this->rating;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.$this->className.'->'.
					__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Return openmeta tags
	 *
	 * @return 	array	$tags	the current openmeta tags
	 **************************************************************************/
	public function getOpenTags() {
		try {
			exec("mdls -name kMDItemOMUserTags -raw $this->fileList", $tags);
			array_shift($tags); // remove first '('
			$child = array();
			$openTags = array();
			
			foreach ($tags as $tag) {
				if (strpos($tag, ')') !== 0) {
					$child[] = trim($tag, ", \t");
				} else {
					$openTags[] = $child;
					$child = array();
				} //<-- end if -->
			} //<-- end foreach -->
			
			unset($tags);

			if (count($openTags) > 0) {
				$this->openTags = $openTags;
				
				// use $tags to display tag array as comma separated values			
				foreach ($openTags as $key => $tag) {
					sort($tag);
					$tags[$key] = implode(', ', $tag); // array to string
					
					if (count($tags[$key]) > 1) {
						sort($tags[$key]);
					} //<-- end if -->
				} //<-- end foreach -->
			} else {
				foreach ($this->files as $key => $value) {
					$tags[$key] = '(null)';
				} //<-- end foreach -->
			} //<-- end if -->
			
		return $tags;
		} catch (Exception $e) {
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Return spotlight tags
	 *
	 * @param 	string	$prefix	spotlight comment tag prefix
	 * @return 	array	$tags	the current spotlight tags
	 **************************************************************************/
	public function getSpotlightTags($prefix = '&') {
		try {
			exec("mdls -name kMDItemFinderComment $this->fileList", $tags);
			$count = count($tags);

			if ($count > 0) {				
				$tags = array_map(
					'ltrim',
					$tags,
					array_fill(0, $count, ' kMDItemFinderComment="')
				);

				$tags = array_map(
					'rtrim',
					$tags,
					array_fill(0, $count, '"')
				);
				
				$child = array();
				$spotlightTags = array();
				
				foreach ($tags as $tag) {
					$files[] = explode(' ', $tag);
				} //<-- end foreach -->
				
				foreach ($files as $file) {
					foreach ($file as $tag) {
						if (strpos($tag, $prefix) === 0) {
							$child[] = str_replace($prefix, '', $tag);
						} //<-- end if -->
					} //<-- end foreach -->
					
					if ($child == array()) {
						$child = array('(null)');
					} //<-- end if -->
					
					$spotlightTags[] = $child;
					$child = array();
				} //<-- end foreach -->
			} //<-- end if -->
							
			$this->spotlightTags = $spotlightTags;
			$tags = array();
			
			foreach ($spotlightTags as $tag) {
				sort($tag);
				$tags[] = implode(', ', $tag); // array to string
			} //<-- end foreach -->
			
			return $tags;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Return rating
	 *
	 * @return 	array	$this->rating	the rating
	 **************************************************************************/
	public function getRating() {
		try {
			exec("mdls -name kMDItemStarRating $this->fileList", $rating);
			$count = count($rating);

			if ($count > 0) {	
				$this->rating = array_map(
					'trim',
					$rating,
					array_fill(0, $count, ' kMDItemStarRating=')
				);
			} //<-- end if -->
			
			return $this->rating;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Clear all openmeta tags
	 *
	 * @return 	string	$result	the last line from openmeta command
	 **************************************************************************/
	public function clearOpenTags() {
		try {
			$result = exec("$this->openmeta -s -p $this->fileList");
			$this->openTags = array();
			return $result;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Clear all spotlight tags
	 *
	 * @return 	boolean	TRUE
	 **************************************************************************/
	public function clearSpotlightTags($prefix = '&') {
		try {
			self::setSpotlightTags('', $prefix);
			$this->spotlightTags = array();
			return TRUE;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Remove requested openmeta tags
	 *
	 * @param 	string 	$tags 			space delimited tags to remove
	 * @return 	array	$openTagList	the resulting openmeta tags
	 **************************************************************************/
	public function removeOpenTags($tags) {
		try {
			$tags = explode(' ', $tags); // string to array

			if (!isset($this->openTags)) {
				self::getOpenTags();
			} //<-- end if -->

			$remainingTags = array();
				
			if ($this->openTags) {
				foreach ($this->openTags as $file) {
					$remainingTags[] = array_diff($file, $tags);
				} //<-- end foreach -->
			} //<-- end if -->

			$count = count($remainingTags);

			if ($count > 0) {
				foreach ($this->files as $key => $value) {
					$tags = implode(' ', $remainingTags[$key]);
					$this->fileList = '\''.$value.'\'';
					self::setOpenTags($tags);
				} //<-- end foreach -->
				
				$this->fileList = general::extraImplode($this->files);
			} //<-- end if -->

			$this->openTags = $remainingTags;
			$tags = array();
			
			if ($remainingTags) {
				foreach ($remainingTags as $tag) {
					$openTagList[] = implode(', ', $tag); // array to string
				} //<-- end foreach -->
				
				return $openTagList;
			} //<-- end if -->
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Remove requested spotlight tags
	 *
	 * @param 	string 	$tags 				space delimited tags to remove
	 * @return 	array	$spotlightTagList	the resulting spotlight tags
	 **************************************************************************/
	public function removeSpotlightTags($tags, $prefix = '&') {
		try {
			$tags = explode(' ', $tags); // string to array

			if (!isset($this->spotlightTags)) {
				self::getSpotlightTags();
			} //<-- end if -->

			$remainingTags = array();
				
			foreach ($this->spotlightTags as $file) {
				$remainingTags[] = array_diff($file, $tags);
			} //<-- end foreach -->
			
			$count = count($remainingTags);

			if ($count > 0) {
				$files = $this->files;
										
				foreach ($files as $key => $value) {
					$tags = implode(' ', $remainingTags[$key]);
					$this->files = array($value);
					self::setSpotlightTags($tags, $prefix);
				} //<-- end foreach -->
			} //<-- end if -->

			$this->files = $files;
			$this->spotlightTags = $remainingTags;
			$tags = array();
			
			foreach ($remainingTags as $tag) {
				$openTagList[] = implode(', ', $tag); // array to string
			} //<-- end foreach -->
			
			return $openTagList;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Clear rating
	 *
	 * @return 	string	$result		the last line from openmeta command
	 **************************************************************************/
	public function clearRating() {
		try {
			$result = exec("$this->openmeta -r 0 -p $this->fileList");
			$this->rating = 0;
			return $result;
		} catch (Exception $e) { 
			throw new Exception($e->getMessage().' from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} //<-- end try -->
	} //<-- end function -->

	/*************************************************************************** 
	 * Adds prefix to make spotlight tags
	 *
	 * @param 	string 	$tags	space delimited tags to convert
	 * @return 	string	$tags	space delimited tags with spotlight prefix
	 * @throws 	Exception if $tags is empty
	 **************************************************************************/
	private function _makeSpotlightTags($tags, $prefix = '&') {
		if (empty($tags)) {
			throw new Exception('Empty tag passed from '.$this->className.'->'.
				__FUNCTION__.'() line '.__LINE__
			);
		} else {
			try {
				$spotlightTags = explode(' ', $tags);
				
				foreach ($spotlightTags as $key => $value) {
					$spotlightTags[$key] =  $prefix.$value;
				} //<-- end foreach -->
				
				$tags = implode(' ', $spotlightTags);
				return $tags;
			} catch (Exception $e) { 
				throw new Exception($e->getMessage().' from '.$this->className
					.'->'.__FUNCTION__.'() line '.__LINE__
				);
			} //<-- end try -->
		} //<-- end if -->
	} //<-- end function -->
} //<-- end class -->
?>