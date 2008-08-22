<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

CAppUI::requireSystemClass("mbFieldSpec");

function htmlReplace($find, $replace, &$source) {
  $matches = array();
  $nbFound = preg_match_all("/$find/", $source, $matches);
  $source = preg_replace("/$find/", $replace, $source);
  return $nbFound;
}

function purgeHtmlText($regexps, &$source) {
  $total = 0;
  foreach ($regexps as $find => $replace) {
    $total += htmlReplace($find, $replace, $source); 
  }
  return $total;
}

class CHtmlSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("html");
  }
  
  /*function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    // @todo Should validate against XHTML DTD
        
    // Purges empty spans
    $regexps = array (
      "<span[^>]*>[\s]*<\/span>" => " ",
      "<font[^>]*>[\s]*<\/font>" => " ",
      "<span class=\"field\">([^\[].*)<\/span>" => "$1"
      );
    
     while (purgeHtmlText($regexps, $propValue));
    return null;
  }*/
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "Document confidentiel";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getDBSpec(){
    return "MEDIUMTEXT";
  }
}

?>