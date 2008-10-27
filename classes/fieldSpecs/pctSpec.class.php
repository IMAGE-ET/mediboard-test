<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author Sébastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CPctSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("pct");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    if (!preg_match ("/^([0-9]+)(\.[0-9]{0,4}){0,1}$/", $propValue)) {
      return "n'est pas un pourcentage (utilisez le . pour la virgule)";
    }
    return null;
  }
  
  function getDBSpec(){
    return "FLOAT";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    CMbArray::defaultValue($params, "size", 6);
    return $this->getFormElementText($object, $params, $value, $className)."%";
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $object->$fieldName = rand(0, 100);
  }
}

?>