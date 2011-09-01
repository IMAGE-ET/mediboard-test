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
    $is_encoding_chars_field = $field->name === "MSH.2";
    
    $components = CHL7v2::split($message->componentSeparator, $data, $is_encoding_chars_field);
    
    foreach($components as &$component) {
      $component = CHL7v2::split($message->subcomponentSeparator, $component, $is_encoding_chars_field);
    }
    
    $this->components = $components;
  }
  
  function validate(){
    $this->composite_specs = CHL7v2DataType::load($this->field->datatype, $this->field->getVersion());
    if (!$this->composite_specs->validate($this->components)) {
      throw new Exception("Invalid data {$this->field->name} : ".var_export($this->components, true));
    }
  }
}