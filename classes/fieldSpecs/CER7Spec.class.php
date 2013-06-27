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
 * ER7 string (HL7v2 message)
 */
class CER7Spec extends CTextSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "er7";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec() {
    return "MEDIUMTEXT";
  }

  /**
   * @see parent::getFormHtmlElement()
   */
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $value = $object->{$this->fieldName};
    
    if (isset($params["advanced"]) && $params["advanced"]) {
      $message = new CHL7v2Message;
      $message->parse($value);
      return $message->flatten(true);
    }
    
    return CHL7v2Message::highlight($value);
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<ER7
MSH|^~\&|MYSENDER|MYRECEIVER|MYAPPLICATION||200612211200||QRY^A19|1234|P|2.5
QRD|200612211200|R|I|GetPatient|||1^RD|0101701234|DEM||
ER7;
  }
}
