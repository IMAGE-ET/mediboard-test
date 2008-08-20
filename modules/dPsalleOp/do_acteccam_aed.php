<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Thomas Despoix
*/


class CDoActeCCAMAddEdit extends CDoObjectAddEdit {
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
  }
}

$do = new CDoActeCCAMAddEdit();
$do->doIt();
?>