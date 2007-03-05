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
  
  function CExamComp() {
    $this->CMbObject("exams_comp", "exam_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "consultation_id" => "notNull ref",
      "examen"          => "text",
      "realisation"     => "notNull enum list|avant|pendant",
      "fait"            => "num minMax|0|1"
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