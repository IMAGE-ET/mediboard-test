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

class CIpAddressSpec extends CMbFieldSpec {
  function getSpecType() {
    return "ipAddress";
  }
  
  function getDBSpec(){
    return "VARBINARY(16)";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    return $propValue ? inet_ntop($propValue) : "";
  }
  
  function checkProperty($object){
    return null;
  }
  
  function filter($value){
    return $value;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = inet_pton("127.0.0.1");
  }
}
