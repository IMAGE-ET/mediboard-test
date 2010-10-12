<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "hl7v2_segment");

class CHL7v2Field extends CHL7V2 {  
  static $typesBase = array(
    "Date",
    "DateTime",
    "Double", 
    "Integer",
    "String",
    "Time"
  );
  
  var $owner_segment = null;
  var $datatype      = null;
  var $value         = array();
  var $values        = array();
  
  var $_is_base_type = null;
  
  function __construct(CHL7v2Segment $segment) {
    $this->owner_segment = $segment;
  }
  
  function parseField($field) {
    if ($field) {
      $this->value[]  = $field;
      $this->values[] = explode($this->owner_segment->getMessage()->componentSeparator, $field);
    }
  }
  
  function validateField() {
    $this->loadFieldSchema();   
    
    $this->isBaseType();
    
    $this->getMinOccurs(self::$specs[$this->datatype]);
  }
  
  function loadFieldSchema() {
    if (isset(self::$specs[$this->datatype])) {
      return;
    }
    
    $this->spec_hl7_dir  = self::LIB_HL7."/hl7v2_".$this->owner_segment->getMessage()->version."/";
    $this->spec_filename = self::PREFIX_COMPOSITE_NAME.$this->datatype.".xml";
    
    self::$specs[$this->datatype] = simplexml_load_file($this->spec_hl7_dir.$this->spec_filename);
    
  }
  
  function getSpecs() {
    return self::$specs[self::PREFIX_COMPOSITE_NAME.$this->name];
  }
  
  function isBaseType() {
    $this->_is_base_type = isset(self::$typesBase[$this->datatype]);
  }
}

?>