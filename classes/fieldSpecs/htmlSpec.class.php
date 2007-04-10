<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CHtmlSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("html");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    // @todo Should validate against XHTML DTD
        
    // Purges empty spans
    $regexps = array (
      "<span[^>]*>[\s]*<\/span>" => " ",
      "<font[^>]*>[\s]*<\/font>" => " ",
      "<span class=\"field\">([^\[].*)<\/span>" => "$1"
      );
    
    // while (purgeHtmlText($regexps, $propValue));
    return null;
  }
  
  function sample(&$object){
    parent::sample($object);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "Document confidentiel";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getDBSpec(){
    return "mediumtext";
  }
}

?>