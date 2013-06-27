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
 * Susceptible de gérer les dates de naissance non grégorienne 
 * au format pseudo ISO : YYYY-MM-DD mais avec potentiellement :
 *  MM > 12
 *  DD > 31
 */
class CBirthDateSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "birthDate";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "CHAR(10)";
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    if (!$propValue || $propValue === "0000-00-00") {
      return "";
    }
    return parent::getValue($object, $smarty, $params);
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $object->{$this->fieldName}, $match)) {
      return "Format de date invalide";
    }

    if ($match[1] < 1850) {
      return "Année inférieure a 1850";
    }
  }

  /**
   * @see parent::getPropSuffix()
   */
  function getPropSuffix() {
    return "mask|99/99/9999 format|$3-$2-$1";
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    
    $object->{$this->fieldName} = sprintf(
      "19%02d-%02d-%02d", 
      self::randomString(CMbFieldSpec::$nums, 2),
      self::randomString(CMbFieldSpec::$months, 1),
      self::randomString(CMbFieldSpec::$days, 1)
    );
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    $maxLength = 10;
    CMbArray::defaultValue($params, "size", $maxLength);
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    return $this->getFormElementText($object, $params, $value, $className);
  }
}
