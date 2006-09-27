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
  var $examen  = null;
  var $fait    = null;

  // Fwd References
  var $_ref_consult = null;

  function CExamComp() {
    $this->CMbObject("exams_comp", "exam_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));

    static $props = array (
      "exam_id"         => "ref|notNull",
      "consultation_id" => "ref|notNull",
      "examen"          => "text",
      "fait"            => "num|minMax|0|1"
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