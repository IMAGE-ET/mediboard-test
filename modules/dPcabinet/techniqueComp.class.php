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

    static $props = array (
      "technique_id"           => "ref|notNull",
      "consultation_anesth_id" => "ref|notNull",
      "technique"              => "text"
    );
    $this->_props =& $props;

    static $seek = array (
    );
    $this->_seek =& $seek;

    static $enums = null;
    if (!$enums) {
      $enums = $this->getEnums();
    }
    
    $this->_enums =& $enums;
    
    static $enumsTrans = null;
    if (!$enumsTrans) {
      $enumsTrans = $this->getEnumsTrans();
    }
    
    $this->_enumsTrans =& $enumsTrans;
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