<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

class CExamComp extends CMbObject {
  // DB Table key
  var $exam_id = null;

  // DB References
  var $consultation_id = null;

  // DB fields
  var $examen      = null;
  var $realisation = null;
  var $fait        = null;

  // Fwd References
  var $_ref_consult = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'exams_comp';
    $spec->key   = 'exam_id';
    return $spec;
  }
  
  function getProps() {
  	$props = parent::getProps();
		
    $props["consultation_id"] = "ref notNull class|CConsultation";
		$props["examen"]          = "text helped";
		$props["realisation"]     = "enum notNull list|avant|pendant";
    $props["fait"]            = "num min|0 max|1";

    return $props;
  }
  
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = $this->examen;
	}
	
  function loadRefConsult() {
    return $this->_ref_consult = $this->loadFwdRef("consultation_id", true);
  }
  
  function getPerm($permType) {
    return $this->loadRefConsult()->getPerm($permType);
  }
}

?>