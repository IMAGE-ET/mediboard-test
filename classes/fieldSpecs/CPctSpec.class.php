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
 * Percentage value
 */
class CPctSpec extends CFloatSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "pct";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "FLOAT";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    $propValue = CMbFieldSpec::checkNumeric($object->{$this->fieldName}, false);
    if ($propValue === null) {
      return "N'est pas une valeur décimale";
    }
    
    if (!preg_match("/^([0-9]+)(\.[0-9]{0,4}){0,1}$/", $propValue)) {
      return "N'est pas un pourcentage";
    }

    return null;
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $decimals = CMbArray::extract($params, "decimals");
    return number_format($object->{$this->fieldName}, ($decimals ? $decimals : 2), ',', ' ').' %';
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    CMbArray::defaultValue($params, "size", 6);
    return parent::getFormHtmlElement($object, $params, $value, $className)."%";
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = rand(0, 100);
  }
}
