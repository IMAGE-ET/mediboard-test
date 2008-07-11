<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage bloodSalvage
 *	@version $Revision: $
 *  @author Alexandre Germonneau
 */

global $AppUI, $can, $m, $g, $dPconfig;
$can->needsRead();
/*
 * Récupération des variables en session et ou issues des formulaires.
 */
$salle        		= mbGetValueFromGetOrSession("salle");
$op           		= mbGetValueFromGetOrSession("op");
$date         		= mbGetValueFromGetOrSession("date", mbDate());


$blood_salvage = new CBloodSalvage();
$totaltime = "00:00:00";



$liste_anticoagulants = array();
$classeBCB = new CBcbClasseTherapeutique();
$classeBCB->loadRefsProduits();
$classeBCB->loadRefsProduits("LABA");
$liste_anticoagulants= $classeBCB->_refs_produits;

$selOp = new COperation();

if ($op) {
  $selOp->load($op);
  $selOp->loadRefs();
  $selOp->_ref_sejour->loadExtDiagnostics();
  $selOp->_ref_sejour->loadRefDossierMedical();
  $selOp->_ref_sejour->_ref_dossier_medical->loadRefsBack();
	$selOp->_ref_plageop->loadRefsFwd();
	$selOp->_ref_sejour->_ref_patient->loadRefsfwd();	
	$selOp->_ref_sejour->_ref_patient->loadRefDossierMedical();	
	$selOp->_ref_sejour->_ref_patient->loadRefConstantesMedicales();	
	
	$where = array();
	$where["operation_id"] = "='$selOp->_id'";	
	$result = $blood_salvage->loadlist($where);
	
	foreach ($result as $key => $value){
		$blood_salvage = $result[$key];
	}
}
  
$smarty = new CSmartyDP();

$smarty->assign("blood_salvage", $blood_salvage);	
$smarty->assign("salle", $salle);
$smarty->assign("selOp", $selOp);
$smarty->assign("date", $date);
$smarty->assign("totaltime", $totaltime);
$smarty->assign("liste_anticoagulants", $liste_anticoagulants);

$smarty->display("vw_bloodSalvage.tpl");
?>