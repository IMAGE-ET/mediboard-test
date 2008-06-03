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
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'techniques_anesth';
    $spec->key   = 'technique_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "consultation_anesth_id" => "notNull ref class|CConsultAnesth",
      "technique"              => "text"
    );
    return array_merge($specsParent, $specs);
  }

  function getHelpedFields(){
    return array(
      "technique" => null
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