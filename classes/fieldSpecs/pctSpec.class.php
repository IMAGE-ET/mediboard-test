<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("fieldSpecs/floatSpec");

class CPctSpec extends CFloatSpec {
  function getSpecType() {
    return "pct";
  }
  
  function getDBSpec(){
    return "FLOAT";
  }
  
  function checkProperty($object){
    $propValue = CMbFieldSpec::checkNumeric($object->{$this->fieldName}, false);
    if($propValue === null){
      return "N'est pas une valeur décimale";
    }
    
    if (!preg_match ("/^([0-9]+)(\.[0-9]{0,4}){0,1}$/", $propValue)) {
      return "N'est pas un pourcentage";
    }
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $decimals = CMbArray::extract($params, "decimals");
    return number_format($object->{$this->fieldName}, ($decimals ? $decimals : 2), ',', ' ').' %';
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    CMbArray::defaultValue($params, "size", 6);
    return parent::getFormHtmlElement($object, $params, $value, $className)."%";
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = rand(0, 100);
  }
}

?>