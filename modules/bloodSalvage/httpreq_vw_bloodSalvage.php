<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;
$can->needsRead();

$salle            = CValue::getOrSession("salle");
$op               = CValue::getOrSession("op");
$date             = CValue::getOrSession("date", mbDate());

$modif_operation  = $date >= mbDate();
$timing = array();

$inLivretTherapeutique = CAppUI::conf("bloodSalvage inLivretTherapeutique");

if(CModule::getActive("dPmedicament")) {
  $anticoagulant = new CBcbClasseATC(); 
	if ($inLivretTherapeutique) {
		$anticoagulant_list = $anticoagulant->loadRefProduitsLivret("B01AB");
	}
	else {
	  $anticoagulant->loadRefsProduits("B01AB");
	  $anticoagulant_list = $anticoagulant->_ref_produits;
	}
} else {
	$list = CAppUI::conf("bloodSalvage AntiCoagulantList");
	$anticoagulant_list = explode("|", $list);
}

$selOp = new COperation();

if ($op) {
  $selOp->load($op);
  $selOp->loadRefsFwd();
  $selOp->_ref_sejour->loadExtDiagnostics();
  $selOp->_ref_consult_anesth->loadRefsFwd();
  $selOp->_ref_sejour->loadRefDossierMedical();
  $selOp->_ref_sejour->_ref_dossier_medical->loadRefsBack();
  $selOp->_ref_plageop->loadRefsFwd();
  $selOp->_ref_sejour->_ref_patient->loadRefsfwd(); 
  $selOp->_ref_sejour->_ref_patient->loadRefDossierMedical(); 
  $selOp->_ref_sejour->_ref_patient->loadRefConstantesMedicales();  
  
  $blood_salvage = new CBloodSalvage();
  $blood_salvage->operation_id = $op;
  $blood_salvage->loadMatchingObject();
  $blood_salvage->loadRefs();
  $timing["_recuperation_start"] = array();
  foreach($timing as $key => $value) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $blood_salvage->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $blood_salvage->$key);
    }
  }
}

/*
 * Liste des cell saver.
 */
$cell_saver = new CCellSaver();
$list_cell_saver = $cell_saver->loadList();

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage", $blood_salvage); 
$smarty->assign("salle", $salle);
$smarty->assign("selOp", $selOp);
$smarty->assign("date", $date);
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("totaltime", "00:00:00");
$smarty->assign("anticoagulant_list", $anticoagulant_list);
$smarty->assign("timing", $timing);
$smarty->assign("list_cell_saver", $list_cell_saver);
$smarty->assign("inLivretTherapeutique", $inLivretTherapeutique);

$smarty->display("inc_bloodSalvage.tpl");
?>