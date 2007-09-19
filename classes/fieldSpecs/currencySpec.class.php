<?php /* $Id: floatSpec.class.php 1794 2007-04-10 17:59:55Z MyttO $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
*/

require_once("./classes/fieldSpecs/floatSpec.class.php");
require_once("./includes/config_dist.php");

class CCurrencySpec extends CFloatSpec {
  //global $dPconfig;
    
  function getSpecType() {
    return("currency");
  }
  
  function getValue($object, $smarty, $params = null) {
  	global $dPconfig;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return htmlspecialchars(sprintf("%.2f", $propValue).$dPconfig["currency_symbol"]);
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 6);
    global $dPconfig;
    return parent::getFormHtmlElement($object, $params, $value, $className).$dPconfig["currency_symbol"];
  }
	
}

?>