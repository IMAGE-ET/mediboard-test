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
    return "text";
  }
  
  function getDBSpec(){
    return "TEXT";
  }
  
  function checkProperty($object) {
    return null;
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    if ($truncate = CValue::read($params, "truncate")) {
      $value = CMbString::truncate($value, $truncate === true ? null : $truncate);
    }
    return '<p>'.nl2br(htmlspecialchars($value)).'</p>';
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = self::randomString(array_merge(CMbFieldSpec::$chars, array(' ', ' ', ', ', '. ')), 200);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
}

?>