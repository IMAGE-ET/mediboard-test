<?php /* $Id: aidesaisie.class.php 8576 2010-04-15 12:35:57Z phenxdesign $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 8576 $
* @author SARL OpenXtrem
*/

@include "PHP/CodeSniffer.php";

// Useful whenever you want to use GIT version 
// @include "../PHP_CodeSniffer/CodeSniffer.php";

if (!class_exists("PHP_CodeSniffer", false)) {
  return;
}

/**
 * Code Sniffer frameworked class
 * File tree and report caching
 */
class CMbCodeSniffer extends PHP_CodeSniffer {
  
  var $reports = array();
  
  /**
   * Adapt CLI behaviour to framework
   * 
   * @return CMbCodeSniffer
   */
  function __construct() {
    $verbosity = 0;
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
   * @return int               Error and warning count
   */
  function report($file, $reportType = "xml") {
    // Create the file
    $reportPath = $this->makeReportPath($file, $reportType);
    CMbPath::forceDir(dirname($reportPath));
    touch($reportPath);

    // Build the report
    $reporting = new PHP_CodeSniffer_Reporting();
    return $reporting->printReport(
      $reportType,
      $this->getFilesErrors(),
      $showSources = true,
      $reportPath,
      $reportWidth = 120
    );
  }
  
  /**
   * Make a report file path
   * 
   * @param string $file       
   * @param string $reportType One of full xml checkstyle csv emacs source summary svnblame gitblame
   * @return string
   */
  function makeReportPath($file, $reportType = "xml") {
    $root_dir = CAppUI::conf("root_dir");
    return "$root_dir/tmp/CodeSniffer/$file.$reportType";
  }
  
  /**
   * Check reports for file tree
   * 
   * @param array $files Tree
   * @return array Reports status for files
   */
  function checkReports($files) {
    $this->reports = array();
    $this->checkReport("", $files);
    return $this->reports;
  } 
  
  /**
   * Check report status for a specific file tree node
   * 
   * @param string $basedir Tree node base directory context
   * @param mixed  $files   Tree node, either a single file or a collection
   * @return void
   */
  function checkReport($basedir, $file) {
    // Directory case
    if (is_array($file)) {
      foreach ($file as $dirname => $basename) {
        $this->checkReport("$basedir/$dirname", $basename);
      }
      return;
    }
    
    // File case
    $subpath = "$basedir";
    $report = $this->makeReportPath($subpath, "json");
    $check = "none";
    if (is_file($report)) {
      $check = "obsolete";
      
      $root_dir = CAppUI::conf("root_dir");
      $codefile  = "$root_dir/$subpath";
      if (filemtime($report) > filemtime($codefile)) {
        $check = "uptodate";
      }
    }

    $this->reports[$subpath] = $check;
  }
  
  function buildStats($files) {
    $this->stats = array();
    $this->buildStat("", $files);
    
    // Sum count arrays from recursive array count
    function sumCounts($key, &$value) {
      if ($key == "count" && is_array($value)) {
        $value = array_sum($value);
        return;
	  }
	  if (is_array($value)) {
	  	foreach ($value as $_key => &$_value) {
	  	  sumCounts($_key, $_value);
	  	}
	  }
	}
    
    sumCounts(null, $this->stats);
    
    return $this->stats;
  } 
  
  function buildStat($basedir, $file) {
    $stat = array();
  	
    // Directory case
    if (is_array($file)) {
      $stats = array();
      foreach ($file as $filename => $basename) {
        $stats[] = $this->buildStat("$basedir/$filename", $basename);
      }

      $stat = call_user_func_array("array_merge_recursive", $stats);
      return $this->stats[CValue::first($basedir, "-root-")] = $stat;
    }
    
    // File case
    $subpath = "$basedir";
    $report = $this->makeReportPath($subpath, "json");
    if (is_file($report)) {
      $stat = json_decode(file_get_contents($report), true);
    }
    
    if (!is_array($stat)) {
      $stat = array();
    }

    return $this->stats[$subpath] = $stat;
  }
  
  function getFlattenAlerts() {
    $alerts = array();
    foreach ($this->getFilesErrors() as $_file => $_by_file) {
      foreach ($_by_file as $_type => $_by_type) {
        if ($_type == "numWarnings" || $_type == "numErrors") {
          continue;
        }
      
        foreach ($_by_type as $_line => $_by_line) {
          foreach ($_by_line as $_column => $_by_column) {
            foreach ($_by_column as $_info) {
              $alerts[] = array (
                "file"     => $_file,
                "type"     => substr($_type, 0, -1),
                "line"     => $_line,
                "column"   => $_column,
                "severity" => $_info["severity"],
                "source"   => $_info["source"],
                "message"  => $_info["message"],
              );
            }
          } 
        }
      }
    }

    array_multisort(CMbArray::pluck($alerts, "line"), SORT_ASC, $alerts);
    return $alerts;
  }
  
  function stat($file) {
  	
  	// Recursive increment routine
    function increment(&$stats, $parts) {
      if (!isset ($stats)) {
        $stats = array (
          "count" => null,
          "items" => null,
        );
      }
    	
      $stats["count"]++;
      if ($first = array_shift($parts)) {
        increment($stats["items"][$first], $parts);
      }
      
    }
  	
    // Recursive call
    $stats = null;
  	foreach ($this->getFlattenAlerts() as $_alert) {
  	  $parts = explode(".", $_alert["source"]);
  	  increment($stats, $parts);
  	}
  	
    // Create the file
    $path = $this->makeReportPath($file, "json");
    CMbPath::forceDir(dirname($path));
    touch($path);
    file_put_contents($path, json_encode($stats));
  	
  	return $stats;
  }
}
?>