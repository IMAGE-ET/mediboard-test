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
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "technique_id"           => "ref|notNull",
      "consultation_anesth_id" => "ref|notNull",
      "technique"              => "text"
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_consult_anesth = new CConsultAnesth;
    $this->_ref_consult_anesth->load($this->consultation_anesth_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consult_anesth) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult_anesth->getPerm($permType);
  }
}

?>