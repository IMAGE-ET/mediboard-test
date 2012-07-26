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
    
    if (isset($params["advanced"]) && $params["advanced"]) {
      $message = new CHL7v2Message;
      $message->parse($value);
      return $message->flatten(true);
    }
    
    return CHL7v2Message::highlightER7($value);
  }
  
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<EOD
MSH|^~\&|MYSENDER|MYRECEIVER|MYAPPLICATION||200612211200||QRY^A19|1234|P|2.5
QRD|200612211200|R|I|GetPatient|||1^RD|0101701234|DEM||
EOD;
  }
}
