<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("fieldSpecs/floatSpec");

class CCurrencySpec extends CFloatSpec {
  var $precise = null;
  
  function getSpecType() {
    return "currency";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    $decimals = CMbArray::extract($params, "decimals", $this->precise ? 4 : 2);
    return ($propValue !== null && $propValue !== "") ? 
      number_format($propValue, $decimals, ',', ' ').' '.CAppUI::conf("currency_symbol") : 
      "-";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 4);
    return parent::getFormHtmlElement($object, $params, $value, $className).CAppUI::conf("currency_symbol");
  }
	
  function getDBSpec() {
    $size = $this->precise ? "12, 5" : "10, 3";
    return "DECIMAL ($size)".($this->pos ? " UNSIGNED" : "");
  }
}

?>