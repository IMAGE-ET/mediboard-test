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
 * IP address
 */
class CIpAddressSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "ipAddress";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "VARBINARY(16)";
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    return $propValue ? inet_ntop($propValue) : "";
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    return null;
  }

  /**
   * @see parent::filter()
   */
  function filter($value){
    return $value;
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = inet_pton("127.0.0.1");
  }
}
