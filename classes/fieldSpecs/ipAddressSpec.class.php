<?php /* $Id: strSpec.class.php 7205 2009-11-03 11:52:40Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 7205 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

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
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = inet_pton("127.0.0.1");
  }
}