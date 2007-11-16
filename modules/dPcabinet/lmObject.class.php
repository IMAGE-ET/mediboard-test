<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage cabinet
* @version $Revision: 2249 $
* @author Thomas Despoix
*/

/**
 * Abstract class for LogicMax objects
 * - base association
 */
class CLmObject extends CMbObject {  
  public $_ref_id400 = null;
  
  function CLmObject($table, $key) {
    foreach (array_keys($this->getSpecs()) as $prop) {
      $this->$prop = null;
    }
    
    $this->loadRefModule(basename(dirname(__FILE__)));
    if ($this->_ref_module) {
      $this->CMbObject($table, $key);
    }
    
  }
  
  function loadExternal() {
    $this->_external = true;
  }
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn = "logicmax";
    $spec->incremented = false;
    $spec->loggable = false;
    return $spec;
  }

  function getBackRefs() {
    return array();
  }
 
  function getspecs() {
    return array();
  }
   
  function getSeeks() {
    return array();
  }

  function getHelpedFields(){
    return array();
  }
}

?>