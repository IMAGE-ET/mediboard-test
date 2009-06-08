<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CTextSpec extends CMbFieldSpec {
  function getSpecType() {
    return("text");
  }
  
  function getDBSpec(){
    return "TEXT";
  }
  
  function checkProperty($object) {
    return null;
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return '<p>'.nl2br(htmlspecialchars($propValue)).'</p>';
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $object->$fieldName = $this->randomString(array_merge(CMbFieldSpec::$chars, array(' ', ' ', ', ', '. ')), 200);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
}

?>