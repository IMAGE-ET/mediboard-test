<?php /*  */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: 2249 $
* @author Sherpa
*/

/**
 * Abstract class for bcb objects
 */
  

class CBcbObject extends CMbObject {  
  static $objDatabase = null;
  static $TypeDatabase = null;
  
  function initBCBConnection() {
    if(!self::$objDatabase) {
      include_once("lib/bcb/packageBCB.php");
      include_once("lib/bcb/bd_config.inc.php");
      self::$objDatabase = $objDatabase;
      self::$TypeDatabase = $TypeDatabase;
    }
  }
  
  function CBcbObject($table, $key) {
    foreach (array_keys($this->getSpecs()) as $prop) {
      $this->$prop = null;
    }
        
    $this->loadRefModule(basename(dirname(__FILE__)));
    if($this->_ref_module) {
      $this->CMbObject($table, $key);
    }
  }
  
  function store(){
    return;  
  }
  
  function delete(){
    return;
  }
}

?>