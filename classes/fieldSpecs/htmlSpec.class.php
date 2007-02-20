<?php /* $Id: $ */

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
  
  function checkProperty(&$object){
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
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "Document confidentiel";
  }
  
  function checkFieldType(){
    return "textarea";
  }
  
  function getDBSpec(){
    return "mediumtext";
  }
}

?>