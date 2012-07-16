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

class CXmlSpec extends CMbFieldSpec { 
  function getSpecType() {
    return "xml";
  }  
  
  function getDBSpec() {
    return "MEDIUMTEXT";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    return utf8_decode(CMbString::highlightCode("xml", $object->{$this->fieldName}, false));
  }
  
  function sample(&$object, $consistent = true){
    $object->{$this->fieldName} = <<<EOD
<?xml version="1.0" encoding="ISO-8859-1"?>
<note>
  <to>Tove</to>
  <from>Jani</from>
  <heading>Reminder</heading>
  <body>Don't forget me this weekend!</body>
</note>
EOD;
  }
}
