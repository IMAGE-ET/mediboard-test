<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


if (!class_exists("PHP_CodeSniffer", false)) {
  @include "PHP/CodeSniffer.php";
}

// Useful whenever you want to use GIT version 
// @include "../PHP_CodeSniffer/CodeSniffer.php";

// Still does not exist, just quit
if (!class_exists("PHP_CodeSniffer", false)) {
  return;
}

/**
 * Code Sniffer frameworked class
 * File tree and report caching
 */
class CMbCodeSniffer extends PHP_CodeSniffer {
  public $reports = array();
  public $stats = array();
  
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
   * 
   * @return bool
   */
  function process($file) {
    $root_dir = CAppUI::conf("root_dir");
    $file     = "$root_dir/$file";
    $standard = $this->getStandardDir();
    
    parent::process($file, $standard);
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
    return CMbPath::getPathTreeUnder($root_dir, array_keys($this->ignorePatterns), $extensions);
  }
  
  /**
   * Print a report into a file
   * 
   * @param string $file       File path
   * @param string $reportType One of full xml checkstyle csv emacs source summary svnblame gitblame
   *
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
   * @param string $file       File name
   * @param string $reportType One of full xml checkstyle csv emacs source summary svnblame gitblame
   *                           
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
   *
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
   * @param mixed  $file    Tree node, either a single file or a collection
   *
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

  /**
   * Build sniff stats for entire tree of files
   *
   * @param string[] $files Files collection
   *
   * @return array
   */
  function buildStats($files) {
    $this->stats = array();
    $this->buildStat("", $files);

    /**
     * Internal sum count arrays from recursive array count
     *
     * @param string $key    Key name
     * @param int[]  &$value Counters as array for leaf, branch otherwise
     *
     * @return void Value is altered in all nodes of the tree
     */
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

  /**
   * Recursive stat building for given files
   *
   * @param string          $basedir Base directory of the node
   * @param string|string[] $file    File or file collection of the node
   *
   * @return array|mixed
   */
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

  /**
   * Build an flattened array or alerts
   *
   * @return array
   */
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

  /**
   * Get all stats for one file
   *
   * @param string $file File name
   *
   * @return null
   */
  function stat($file) {
    
    /**
     * Recursive increment routine
     *
     * @param array &$stats Stats
     * @param array $parts  Parts
     *
     * @return array
     */
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
