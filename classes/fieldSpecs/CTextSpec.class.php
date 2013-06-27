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

/**
 * Long string
 */
class CTextSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "text";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "TEXT";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object) {
    return null;
  }

  /**
   * @see parent::getHtmlValue()
   */
  function getHtmlValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    if ($truncate = CValue::read($params, "truncate")) {
      $value = CMbString::truncate($value, $truncate === true ? null : $truncate);
    }
    return $value ? '<p>'.nl2br(CMbString::htmlSpecialChars($value)).'</p>': "";
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $chars = array_merge(CMbFieldSpec::$chars, array(' ', ' ', ', ', '. '));
    $object->{$this->fieldName} = self::randomString($chars, 200);
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  /**
   * @see parent::filter()
   */
  function filter($value) {
    if (CAppUI::conf("purify_text_input")) {
      $value = CMbString::purifyHTML($value);
    }
    return parent::filter($value);
  }
}
