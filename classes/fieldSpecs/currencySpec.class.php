<?php /* $Id: floatSpec.class.php 1794 2007-04-10 17:59:55Z MyttO $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/fieldSpecs/floatSpec.class.php");

class CCurrencySpec extends CFloatSpec {
  function getSpecType() {
    return("currency");
  }
  
  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    return ($propValue !== null && $propValue !== "") ? 
      htmlspecialchars(sprintf("%.2f", $propValue).CAppUI::conf("currency_symbol")) : 
      "-";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 4);
    return parent::getFormHtmlElement($object, $params, $value, $className).CAppUI::conf("currency_symbol");
  }
	
}

?>