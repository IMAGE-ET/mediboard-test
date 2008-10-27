<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("fieldSpecs/floatSpec");

class CCurrencySpec extends CFloatSpec {
  function getSpecType() {
    return("currency");
  }
  
  function getValue($object, $smarty, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $decimals = CMbArray::extract($params, "decimals");
    
    return ($propValue !== null && $propValue !== "") ? 
      htmlspecialchars(sprintf("%.".($decimals ? $decimals : 2)."f", $propValue).CAppUI::conf("currency_symbol")) : 
      "-";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 4);
    return parent::getFormHtmlElement($object, $params, $value, $className).CAppUI::conf("currency_symbol");
  }
	
}

?>