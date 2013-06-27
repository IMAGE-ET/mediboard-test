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
 * XML code
 */
class CXmlSpec extends CMbFieldSpec {
  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "xml";
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
    return utf8_decode(CMbString::highlightCode("xml", $object->{$this->fieldName}, false));
  }

  /**
   * @see parent::sample()
   */
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<note>
  <to>Tove</to>
  <from>Jani</from>
  <heading>Reminder</heading>
  <body>Don't forget me this weekend!</body>
</note>
XML;
  }
}
