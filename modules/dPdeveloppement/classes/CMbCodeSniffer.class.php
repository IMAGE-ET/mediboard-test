<?php /* $Id: aidesaisie.class.php 8576 2010-04-15 12:35:57Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 8576 $
* @author SARL OpenXtrem
*/

@include "PHP/CodeSniffer.php";
if (!class_exists("PHP_CodeSniffer")) {
	return;
}

/**
 * Code Sniffer frameworked class
 * File tree and report caching
 */
class CMbCodeSniffer extends PHP_CodeSniffer {
	
  /**
   * Adapt CLI behaviour to framework
   * 
   * @return CMbCodeSniffer
   */
	function __construct() {
		$verbosity = 1;
		$tabwidth = 2;
		// Use for Apache MacOSX
		if (!isset($_SERVER["argc"])) {
		  $_SERVER["argc"] = 0;
		}
		parent::__construct($verbosity, $tabwidth);
	}
	
	/**
	 * Get CS standard directory
	 * 
	 * @return string Directory path
	 */
  function getStandardDir() {
    $root_dir = CAppUI::conf("root_dir");
    $standard = "$root_dir/dev/CodeSniffer/Standard";
    $standard = strtr($standard, "/", DIRECTORY_SEPARATOR);
    return $standard;
  }	
	
	/**
	 * Process analysis with framework standard
	 * 
	 * @param  string $file
	 * @see    parent::process()
	 * @return bool
	 */
	function process($file) {
    $root_dir = CAppUI::conf("root_dir");
    $file     = "$root_dir/$file";
    $standard = $this->getStandardDir();
    return parent::process($file, $standard);
	}
	
	/**
	 * Build analysed file tree according to standard rules
	 * 
	 * @return array Recursive file array (tree)
	 */
	function getFilesTree() {
    $extensions = array("php");
    $root_dir = CAppUI::conf("root_dir");
    $standard = $this->getStandardDir();
		$this->populateCustomRules($standard);
		return CMbPath::getPathTreeUnder($root_dir, $this->ignorePatterns, $extensions);
	}
	
	/**
	 * Print a report into a file
	 * 
	 * @param string $file       
	 * @param string $reportType One of full xml checkstyle csv emacs source summary svnblame gitblame
	 * @return 
	 */
	function report($file, $reportType) {
		// Create the file
    $root_dir = CAppUI::conf("root_dir");
		$reportFile = "$root_dir/tmp/CodeSniffer/$file.$reportType.xml";
		CMbPath::forceDir(dirname($reportFile));
		touch($reportFile);

    // Build the report
    $showSources = true;
		$reportWidth = 120;
		$reporting = new PHP_CodeSniffer_Reporting();
		return $reporting->printReport(
		  $reportType,
		  $this->getFilesErrors(),
		  $showSources,
		  $reportFile,
		  $reportWidth
		);
		
	}
}
?>