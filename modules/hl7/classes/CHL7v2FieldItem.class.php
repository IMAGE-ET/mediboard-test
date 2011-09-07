<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2FieldItem {
  /**
   * @var CHL7v2Field
   */
  var $field = null;
  var $data = null;
  var $components = array();
  var $specs = null;
  var $composite_specs = null;
  
  function __construct(CHL7v2Field $field, $data) {
    $this->field = $field;
    $this->specs = $field->getSpecs();
    $this->data = $data;
    
    $message = $field->getMessage();
    $keep_original = CHL7v2::keep($field->name);
    
    $components = CHL7v2::split($message->componentSeparator, $data, $keep_original);
    
    foreach($components as &$component) {
      $component = CHL7v2::split($message->subcomponentSeparator, $component, $keep_original);
    }
    
    $this->components = $components;
  }
  
  function validate(){
    $field = $this->field;
    
    $this->composite_specs = CHL7v2DataType::load($field->datatype, $field->getVersion());
    
    if (!$this->composite_specs->validate($this->components, $field)) {
      $field->error(CHL7v2Exception::INVALID_DATA_FORMAT, var_export($this->components, true), $field);
    }
  }
  
  function getValue() {
    return $this->composite_specs->toMB($this->components, $this->field);
  }
}