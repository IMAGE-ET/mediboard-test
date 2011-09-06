<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12920 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("fieldSpecs/CTextSpec");

class CER7Spec extends CTextSpec { 
  function getSpecType() {
    return "er7";
  }
  
  function getDBSpec() {
    return "MEDIUMTEXT";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    return CHL7v2Message::hightligt_er7($value);
  }
  
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<EOD
MSH|^~\\&|MYSENDER|MYRECEIVER|MYAPPLICATION||200612211200||QRY^A19|1234|P|2.4
QRD|200612211200|R|I|GetPatient|||1^RD|0101701234|DEM||
EOD;
  }
}

?>