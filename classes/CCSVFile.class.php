<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * CSV Files general purpose wrapper class
 * Responsibilities:
 *  - read, write and stream CSV files
 *  - delimiters, enclosures configuration
 */
class CCSVFile {
  const PROFILE_OPENOFFICE = "openoffice";
  const PROFILE_EXCEL      = "excel";
  
  var $handle       = null;
  var $delimiter    = ',';
  var $enclosure    = '"';
  var $column_names = null;
  
  static $profiles = array(
    self::PROFILE_OPENOFFICE => array(
      "delimiter" => ',',
      "enclosure" => '"',
    ),
    self::PROFILE_EXCEL => array(
      "delimiter" => ';',
      "enclosure" => '"',
    ),
  );
  
  /**
   * Standard constructor 
   * 
   * @param mixed $handle       File handle of file path
   * @param enum  $profile_name Profile name, one of openoffice and excel 
   * 
   * @return void
   */
  function __construct($handle = null, $profile_name = self::PROFILE_EXCEL) {
    if ($handle) {
      $this->handle = $handle;
      
      if (is_string($handle)) {
        $this->handle = fopen($handle, "r+");
      }
    }
    else {
      $this->handle = CMbPath::getTempFile();
    }
    
    $this->setProfile($profile_name);
  }
  
  /**
   * Set the profile parameters
   * 
   * @param string $profile_name Profile name, one of "openoffice" and "excel"
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
  function readLine($assoc = false, $nullify_empty_values = false) {
    $line = fgetcsv($this->handle, null, $this->delimiter, $this->enclosure);
    
    if (!empty($line)) {
      if ($nullify_empty_values) {
        $line = $this->nullifyEmptyValues($line);
      }
      
      if ($assoc && $this->column_names) {
        return array_combine($this->column_names, $line);
      }
    }
    
    return $line;
  }
  
  /**
   * Write a line into the file
   * 
   * @param array $values An array of string values
   * 
   * @return integer The length of the written string, or false on failure
   */
  function writeLine($values) {
    return fputcsv($this->handle, $values, $this->delimiter, $this->enclosure);
  }
  
  /**
   * Set columns names to be used when reading the CSV file (to return associative arrays)
   * 
   * @param array $names The columns names
   * 
   * @return void
   */
  function setColumnNames($names) {
    $this->column_names = $names;
  }
  
  function nullifyEmptyValues($values) {
    foreach ($values as &$_value) {
      if ($_value === "") {
        $_value = null;
      }
    }
    
    return $values;
  }
  
  /**
   * Get the full content of the file
   * 
   * @return string
   */
  function getContent() {
    rewind($this->handle);
    
    $content = "";
    while ($s = fgets($this->handle)) {
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
