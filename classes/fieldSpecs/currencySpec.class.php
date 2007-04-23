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
    return sprintf("%.2f", $propValue);
  }

}

?>