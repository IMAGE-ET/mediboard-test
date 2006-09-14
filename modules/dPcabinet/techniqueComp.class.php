<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/


class CTechniqueComp extends CMbObject {
  // DB Table key
  var $technique_id = null;

  // DB References
  var $consultation_anesth_id = null;

  // DB fields
  var $technique  = null;

  // Fwd References
  var $_ref_consult_anesth = null;
  
  function CTechniqueComp() {
    $this->CMbObject("techniques_anesth", "technique_id");

    $this->_props["technique_id"]           = "ref|notNull";
    $this->_props["consultation_anesth_id"] = "ref|notNull";
    $this->_props["technique"]              = "text";
  }
  
  function loadRefsFwd() {
    $this->_ref_consult_anesth = new CConsultAnesth;
    $this->_ref_consult_anesth->load($this->consultation_anesth_id);
  }
  
  function canRead($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canRead = $this->_ref_consult_anesth->canRead();
    return $this->_canRead;
  }

  function canEdit($withRefs = true) {
    if($withRefs) {
      $this->loadRefsFwd();
    }
    $this->_canEdit = $this->_ref_consult_anesth->canEdit();
    return $this->_canEdit;
  }
}

?>