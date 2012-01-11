<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Observation value unit, based on the HL7 OBX specification
 * http://www.interfaceware.com/hl7-standard/hl7-segment-OBX.html
 */
class CObservationValueCodingSystem extends CMbObject {
  var $code                      = null;
  var $label                     = null;
  var $desc                      = null;
  var $coding_system             = null;
  
  function getProps() {
    $props = parent::getProps();
    $props["code"]          = "str notNull";
    $props["label"]         = "str notNull";
    $props["desc"]          = "str";
    $props["coding_system"] = "str notNull";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->label;
  }
  
  function loadMatch($code, $coding_system, $label, $desc = null) {
    $ds = $this->_spec->ds;
    
    $where = array(
      "code"          => $ds->escape("=%", $code),
      "coding_system" => $ds->escape("=%", $coding_system),
    );
    
    if (!$this->loadObject($where)) {
      $this->label = $label;
      $this->desc = $desc;
      $this->store();
    }
    
    return $this->_id;
  }
}
