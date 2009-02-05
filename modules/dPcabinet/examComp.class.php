<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
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
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "consultation_id" => "ref notNull class|CConsultation",
      "examen"          => "text",
      "realisation"     => "enum notNull list|avant|pendant",
      "fait"            => "num minMax|0|1"
    );
    return array_merge($specsParent, $specs);
  }

  function getHelpedFields(){
    return array(
      "examen" => null
    );
  }
  
  function loadRefsFwd() {
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consult) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult->getPerm($permType);
  }
}

?>