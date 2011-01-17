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

class CMbCodeSniffer extends PHP_CodeSniffer {
	
	function __construct() {
		$verbosity = 1;
		$tabwidth = 2;
		parent::__construct($verbosity, $tabwidth);
	}
	
	function process($file) {
    $root_dir = CAppUI::conf("root_dir");
		$file     = "$root_dir/$file";
		$standard = "$root_dir/dev/CodeSniffer/Standard";
		return parent::process($file, $standard);
	}
	
	function getFilesTree() {
    $extensions = array("php");
    $root_dir = CAppUI::conf("root_dir");
    $standard = "$root_dir/dev/CodeSniffer/Standard";
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
		$reportFile = "$root_dir/tmp/CodeSniffer/$file.xml";
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