<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 2249 $
* @author Thomas Despoix
*/

/**
 * Abstract class for LogicMax objects
 * - base association
 */
class CLmObject extends CMbObject {  
  public $_ref_id400 = null;
  
  function CLmObject() {
    $spec_keys = array_keys($this->getProps());
    foreach ($spec_keys as $prop) {
      $this->$prop = null;
    }
    
    parent::__construct();
  }
  
  function loadExternal() {
    $this->_external = true;
  }
    
  function getSpec() {
    $spec = parent::getSpec();
    $spec->dsn         = "logicmax";
    $spec->incremented = false;
    $spec->loggable    = false;
    return $spec;
  }

  function getBackProps() {
    return array();
  }
 
  function getProps() {
    return array();
  }
}

?>