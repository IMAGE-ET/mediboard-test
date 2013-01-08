<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Thomas Despoix
*/


class CDoActeCCAMAddEdit extends CDoObjectAddEdit {
  
  var $_ref_object = null;
  
  function CDoActeCCAMAddEdit() {
    $this->CDoObjectAddEdit("CActeCCAM", "acte_id");
  }
  
  function doBind() {
    parent::doBind();
    $this->_obj->modificateurs = "";
    foreach ($_POST as $propName => $propValue) {
      $matches = null;
      if (preg_match("/modificateur_(.)/", $propName, $matches)) {      
        $modificateur = $matches[1];
        $this->_obj->modificateurs .= $modificateur;
      }
    }
    $this->_obj->loadRefObject();
    $this->_ref_object = $this->_obj->_ref_object;
  }
  
  function doRedirect() {
    if(CAppUI::conf("dPsalleOp CActeCCAM codage_strict") || !$this->_old->_id) {
      $this->_ref_object->correctActes();
    }
    parent::doRedirect();
  }
}

$do = new CDoActeCCAMAddEdit();
$do->doIt();
?>