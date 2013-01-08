<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

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
  
  function getHtmlValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    if ($truncate = CValue::read($params, "truncate")) {
      $value = CMbString::truncate($value, $truncate === true ? null : $truncate);
    }
    return $value ? '<p>'.nl2br(CMbString::htmlSpecialChars($value)).'</p>': "";
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $chars = array_merge(CMbFieldSpec::$chars, array(' ', ' ', ', ', '. '));
    $object->{$this->fieldName} = self::randomString($chars, 200);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
}
