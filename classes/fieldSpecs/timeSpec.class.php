<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CTimeSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("time");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2}))?$/", $propValue)) {
      return "format de time invalide";
    }
    return null;
  }

  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = $this->randomString($this->_hours, 1).":".$this->randomString($this->_mins, 1).":".$this->randomString($this->_mins, 1);
  }
  
  function checkFieldType(){
    return "text";
  }
  
  function getDBSpec(){
    return "time";
  }
}

?>