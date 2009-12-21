<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");
CAppUI::requireLibraryFile("geshi/geshi");

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
    $geshi = new Geshi($object->{$this->fieldName}, "xml");
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style("max-height: 100%;");
    $geshi->enable_classes();
    
    return utf8_decode($geshi->parse_code());
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

?>