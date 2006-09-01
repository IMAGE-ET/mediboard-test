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
  var $consult_id = null;

  // DB fields
  var $examen  = null;
  var $fait    = null;

  function CExamComp() {
    $this->CMbObject("exams_comp", "exam_id");

    $this->_props["exam_id"]    = "ref|notNull";
    $this->_props["consult_id"] = "ref|notNull";
    $this->_props["examen"]     = "str";
    $this->_props["fait"]       = "num|minMax|0|1";
  }
}

?>