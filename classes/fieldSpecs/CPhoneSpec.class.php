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
 * Phone number
 */
class CPhoneSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "phone";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "VARCHAR (20)";
  }

  /**
   * Get the mask corresponding to the phone number format
   *
   * @return string
   */
  protected function getMask(){
    static $phone_number_mask = null;
    
    if ($phone_number_mask === null) {
      $phone_number_format = str_replace(' ', 'S', CAppUI::conf("system phone_number_format"));
      
      $phone_number_mask = "";
      
      if ($phone_number_format != "") {
        $phone_number_mask = " mask|$phone_number_format";
      }
    }

    return $phone_number_mask;
  }

  /**
   * @see parent::sample()
   */
  function sample($object, $consistent = true){
    parent::sample($object, $consistent);
    
    $nums = preg_replace("/[^0-9]/", "", CAppUI::conf("system phone_number_format"));
    
    $object->{$this->fieldName} = self::randomString(range(0, 9), strlen($nums));
  }

  /**
   * @see parent::getPropSuffix()
   */
  function getPropSuffix(){
    return "pattern|\d{10,}".$this->getMask();
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $field = CMbString::htmlSpecialChars($this->fieldName);
    $value = CMbString::htmlSpecialChars($value);
    $class = CMbString::htmlSpecialChars("$className $this->prop");
    
    $form  = CMbArray::extract($params, "form");
    $extra = CMbArray::makeXmlAttributes($params);
    
    return "<input type=\"tel\" name=\"$field\" value=\"$value\" class=\"$class styled-element\" $extra />";
  }
}
