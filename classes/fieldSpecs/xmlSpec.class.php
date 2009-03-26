<?php 

/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");
CAppUI::requireLibraryFile("geshi/geshi");

class CXmlSpec extends CMbFieldSpec {

  function getSpecType() {
    return("xml");
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
    $propValue = $object->$fieldName;
    $content_type = "application/xml; charset=UTF-8";
    
    $geshi = new Geshi($propValue, "xml");
    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    $geshi->set_overall_style("max-height: 100%;");
    $geshi->enable_classes();
    
    return utf8_decode($geshi->parse_code());
  }
  
  function getDBSpec(){
    return "MEDIUMTEXT";
  }
}

?>