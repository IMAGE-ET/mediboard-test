<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CCSVFile {
  var $f = null;
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
  
  function __construct($f = null, $profile_name = "excel"){
    $this->f = $f;
    
    if ($profile_name) {
      $this->setProfile($profile_name);
    }
  }
  
  function setProfile($profile_name) {
    if (!isset(self::$profiles[$profile_name])) {
      return;
    }
    
    $profile = self::$profiles[$profile_name];
    
    $this->delimiter = $profile["delimiter"];
    $this->enclosure = $profile["enclosure"];
  }
  
  function readLine(){
    return fgetcsv($this->f, null, $this->delimiter, $this->enclosure);
  }
  
  function writeLine($line){
    return fputcsv($this->f, $line, $this->delimiter, $this->enclosure);
  }
  
  function getContent(){
    rewind($this->f);
    
    $content = "";
    while($s = fgets($this->f)) {
      $content .= $s;
    }
    
    return $content;
  }
  
  function stream($file_name) {
    $content = $this->getContent();
    
    header("Content-Type: text/plain;charset=".CApp::$encoding);
    header("Content-Disposition: attachment;filename=\"$file_name.csv\"");
    header("Content-Length: ".strlen($content).";");
    
    echo $content;
  }
}
