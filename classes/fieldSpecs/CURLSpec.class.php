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
 * URL
 */
class CURLSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "url";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "VARCHAR(255)";
  }

  /**
   * @see parent::getHtmlValue()
   */
  function getHtmlValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    return ($propValue !== null && $propValue !== "") ? 
      "<a class=\"inline-url\" target=\"_blank\" href=\"$propValue\">$propValue</a>" :
      "";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $regex = "@^(ftp|http|https)://(\w+:{0,1}\w*\@)?(\S+)(:[0-9]+)?(/|/([\w#!:.?+=&%\@!-/]))?$@i";
    if (!preg_match($regex, $object->{$this->fieldName})) {
      return "Le format de l'URL n'est pas valide";
    }

    return null;
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

    return "<input type=\"url\" name=\"$field\" value=\"$value\" class=\"$class styled-element\" $extra />";
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = "http://mediboard.org";
  }
}
