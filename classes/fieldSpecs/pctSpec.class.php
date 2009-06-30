<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CPctSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("pct");
  }
  
  function getDBSpec(){
    return "FLOAT";
  }
  
  function checkProperty($object){
    if (!preg_match ("/^([0-9]+)(\.[0-9]{0,4}){0,1}$/", $object->{$this->fieldName})) {
      return "n'est pas un pourcentage (utilisez le . pour la virgule)";
    }
    return null;
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $decimals = CMbArray::extract($params, "decimals");
    return number_format($object->{$this->fieldName}, ($decimals ? $decimals : 2)).' %';
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    CMbArray::defaultValue($params, "size", 6);
    return $this->getFormElementText($object, $params, $value, $className)."%";
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = rand(0, 100);
  }
}

?>