<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CTextSpec extends CMbFieldSpec {
  
  function getValue($object, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return smarty_modifier_date_format(nl2br($propValue), $format);
  }
  
  function getSpecType() {
    return("text");
  }
  
  function checkProperty(&$object){
    return null;
  }
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->randomString($this->_chars, 40);
  }
  
  function checkFieldType(){
    return "textarea";
  }
  
  function getDBSpec(){
    return "text";
  }
}

?>