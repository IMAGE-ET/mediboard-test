<?php
/**
 * $Id: $
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: $
 */

/**
 * CSV Files general purpose wrapper class
 * Responsibilities:
 *  - read, write and stream CSV files
 *  - delimiters, enclosures configuration
 */
class CCSVFile {
  var $path = null;
  var $handle = null;
  var $delimiter = ',';
  var $enclosure = '"';
  
  static $profiles = array(
    "openoffice" => array(
      "delimiter" => ',',
      "enclosure" => '"',
    ),
    "excel"     => array(
      "delimiter" => ';',
      "enclosure" => '"',
    ),
  );
  
  /**
   * Standard constructor 
   * 
   * @param string $path         File path
   * @param enum   $profile_name Profile name, one of openoffice and excel 
   * 
   * @return void
   */
  function __construct($path = null, $profile_name = "excel"){
    $this->path = $path;
    $this->handle = fopen($this->path, "r+");
    
    $this->setProfile($profile_name);
  }
  
  /**
   * Set the profile parameters
   * 
   * @param enum $profile_name Profile name, one of openoffice and excel 
   * 
   * @return void
   */
  function setProfile($profile_name) {
    if (!isset(self::$profiles[$profile_name])) {
      return;
    }
    
    $profile = self::$profiles[$profile_name];
    
    $this->delimiter = $profile["delimiter"];
    $this->enclosure = $profile["enclosure"];
  }
  
  /**
   * Read a line of the file
   * 
   * @return array An indexed array containing the fields read
   */
  function readLine() {
    return fgetcsv($this->path, null, $this->delimiter, $this->enclosure);
  }
  
  /**
   * Write a line into the file
   * 
   * @param array $values An array of string values
   * 
   * @return int The length of the written string, or false on failure
   */
  function writeLine($values) {
    return fputcsv($this->path, $values, $this->delimiter, $this->enclosure);
  }
  
  /**
   * Get the full content of the file
   * 
   * @return string
   * @todo duplicate of file_get_contents() ?
   */
  function getContent() {
    rewind($this->path);
    
    $content = "";
    while ($s = fgets($this->path)) {
      $content .= $s;
    }
    
    return $content;
  }
  
  /**
   * Stream the content to the browser
   * 
   * @param string $file_name File name for the browser
   * 
   * @return unknown_type
   */
  function stream($file_name) {
    $content = $this->getContent();
    
    header("Content-Type: text/plain;charset=".CApp::$encoding);
    header("Content-Disposition: attachment;filename=\"$file_name.csv\"");
    header("Content-Length: ".strlen($content).";");
    
    echo $content;
  }
}
