<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CEmailSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("email");
  }
  
  function getDBSpec(){
    return "VARCHAR(50)";
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $propValue = $object->{$this->fieldName};
    
    return ($propValue !== null && $propValue !== "") ? 
      "<a href='mailto:$propValue'>$propValue</a>" : 
      '';
  }
  
  function checkProperty($object){
    if (!preg_match("/^[-a-z0-9\._]+@[-a-z0-9\.]+\.[a-z]{2,4}$/i", $object->{$this->fieldName})) {
      return "Le format de l'email n'est pas valide";
    }
    return null;
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementText($object, $params, $value, $className);
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = "noone@nowhere.com";
  }
}

?>