<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Sébastien Fillonneau
*/

require_once($AppUI->getSystemClass("mbobject"));


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

    $this->_props["exam_id"]         = "ref|notNull";
    $this->_props["consultation_id"] = "ref|notNull";
    $this->_props["examen"]          = "str";
    $this->_props["fait"]            = "num|minMax|0|1";
  }
  
  function loadRefsFwd() {
    // Forward references
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }
  
  function canRead() {
    $this->loadRefFwd();
    $this->_canRead = $this->_ref_consult->canRead();
    return $this->_canRead;
  }

  function canEdit() {
    $this->loadRefFwd();
    $this->_canEdit = $this->_ref_consult->canEdit();
    return $this->_canEdit;
  }
}

?>