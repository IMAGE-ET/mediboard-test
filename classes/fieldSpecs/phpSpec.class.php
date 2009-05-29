<?php /* $Id: xmlSpec.class.php 6043 2009-04-09 08:15:26Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 6043 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");
CAppUI::requireLibraryFile("geshi/geshi");

class CPhpSpec extends CMbFieldSpec {

  function getSpecType() {
    return("php");
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "Document confidentiel";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    return $this->getFormElementTextarea($object, $params, $value, $className);
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = var_export($object->$fieldName, true);
    
    $geshi = new Geshi($propValue, "php");
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style("max-height: 100%;");
    
    return utf8_decode($geshi->parse_code());
  }
  
  function getDBSpec(){
    return "MEDIUMTEXT";
  }
}

?>