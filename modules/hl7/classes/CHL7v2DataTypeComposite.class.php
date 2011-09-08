<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeComposite extends CHL7v2DataType {
  var $components = array();
  
  protected function __construct($datatype, $version) {
    parent::__construct($datatype, $version);
    
    $specs = $this->getSpecs();
    
    foreach($specs->elements->field as $field) {
      $this->components[] = CHL7v2DataType::load((string)$field->datatype, $this->version);
    }
  }
  
  function getRegExpMB() {
    //
  }
  
  function getRegExpHL7() {
    //
  }
  
  function toHL7($components, CHLv2Field $field){
    $hl7 = array();
    
    foreach($components as $k => $component) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      $component_spec = $this->components[$k];
      
      /*if (!$component_spec instanceof self) {
        mbTrace($component);
        $component = reset($component); // @todo ajouter une exception si jamais on a plusieurs elt
      }*/
      
      $hl7[] = $component_spec->toHL7($component, $field);
    }
    
    return $hl7;
  }
  
  function validate($components, CHL7v2Field $field) {
    // Happens for ST, ID, NM, etc (they are nearly base types, they were not split as sub-sub-component)
    if (!is_array($components)) {
      $components = array($components);
    }
    
    foreach($components as $k => $component) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      if (!$this->components[$k]->validate($component, $field)) {
        $field->error(CHL7v2Exception::INVALID_DATA_FORMAT, $this->type, $field);
        return false;
      }
    }
    
    return true;
  }
}


/* 
formulaire de fiche de liaison qui suit le patient (etapes de brancardage)

 */

