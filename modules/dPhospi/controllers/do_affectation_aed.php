<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/
$_lock_all_lits = CValue::post("_lock_all_lits");

if($_lock_all_lits){
  $lit_id = CValue::post("lit_id");
  $entree = CValue::post("entree");
  $sortie = CValue::post("sortie");
  $lit = new CLit();
  $lit = $lit->load($lit_id);
  $lit->loadRefChambre()->loadRefService()->loadRefsChambres();
  
  foreach($lit->_ref_chambre->_ref_service->_ref_chambres as $chambre){
    $chambre->loadRefsLits();
    foreach($chambre->_ref_lits as $lit){
      $affectation = new CAffectation();
      $affectation->lit_id = $lit->_id;
      $affectation->entree = $entree;
      $affectation->sortie = $sortie;
	    if ($msg = $affectation->store()) {
	      CAppUI::setMsg($msg, UI_MSG_ERROR);
	    }
    }
  }

	echo CAppUI::getMsg();
	CApp::rip();
}
else{
	$do = new CDoObjectAddEdit("CAffectation", "affectation_id");
	$do->doIt();
}
?>