<?php 

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision:  $
* @author Alexis Granger
*/

function viewMsg($msg, $action, $txt = ""){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
    $AppUI->redirect("m=$m&tab=$tab");
    return;
  }
  $AppUI->setMsg("$action $txt", UI_MSG_OK );
}


global $AppUI;

// Récupération du rpu
$rpu_id = mbGetValueFromPost("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

// Creation du nouveau sejour et pre-remplissage des champs
$sejour = new CSejour();
$sejour->patient_id = $rpu->_ref_sejour->patient_id;
$sejour->praticien_id = $rpu->_ref_sejour->praticien_id;
$sejour->group_id = $rpu->_ref_sejour->group_id;
$sejour->entree_prevue = mbDateTime();
$sejour->sortie_prevue = mbDateTime("+ 1 day");
$sejour->entree_reelle = mbDateTime();
$sejour->type = "comp";
$sejour->DP = $rpu->_ref_sejour->DP;
$sejour->DR = $rpu->_ref_sejour->DR;
if($rpu->diag_infirmier){ 
  $sejour->rques = "Diagnostic infirmier: $rpu->diag_infirmier\n";
}
if($rpu->motif){
  $sejour->rques .= "Motif de recours aux urgences: $rpu->motif";
}
$msg = $sejour->store();
viewMsg($msg, "msg-CSejour-title-create");

// Chargement des actes de la prise en charge aux urgences
$actes_ccam = array();
$acte_ccam = new CActeCCAM();
$acte_ccam->object_id = $rpu->_ref_consult->_id;
$acte_ccam->object_class = "CConsultation";
$actes_ccam = $acte_ccam->loadMatchingList();

// Transfert des actes CCAM sur le sejour
foreach($actes_ccam as $key => $_acteCCAM){
  $_acteCCAM->object_class = "CSejour";
  $_acteCCAM->object_id = $sejour->_id;
  $_acteCCAM->_adapt_object = 1;
  $msg = $_acteCCAM->store();
  viewMsg($msg, "msg-CActeCCAM-title-modify");
}


$rpu->_ref_consult->codes_ccam = "";
$rpu->_ref_consult->tarif = "Transfert Séjour";
$rpu->_ref_consult->du_patient = 0;
$rpu->_ref_consult->du_tiers = 0;
$rpu->_ref_consult->secteur1 = 0;
$rpu->_ref_consult->secteur2 = 0;
$msg = $rpu->_ref_consult->store();
viewMsg($msg, "msg-CConsultation-title-modify");

// Chargement des actes NGAP de la prise en charge
$actes_ngap = array();
$acte_ngap = new CActeNGAP();
$acte_ngap->object_id = $rpu->_ref_consult->_id;
$acte_ngap->object_class = "CConsultation";
$actes_ngap = $acte_ngap->loadMatchingList();

// Transfert des actes NGAP sur le sejour
foreach($actes_ngap as $key => $_acteNGAP){
  $_acteNGAP->object_class = "CSejour";
  $_acteNGAP->object_id = $sejour->_id;
  $msg = $_acteNGAP->store();
  viewMsg($msg, "msg-CActeNGAP-title-modify");
}

// Sauvegarde du RPU
$rpu->orientation = "HO";
$rpu->mutation_sejour_id = $sejour->_id;
$msg = $rpu->store();
viewMsg($msg, "msg-CRPU-title-close");

// Sauvegarde du sejour
$rpu->_ref_sejour->sortie_reelle = mbDateTime();
$msg = $rpu->_ref_sejour->store();
viewMsg($msg, "msg-CSejour-title-close", "(Urgences)");

$AppUI->redirect("m=dPplanningOp&tab=vw_edit_sejour&sejour_id=$sejour->_id");

?>