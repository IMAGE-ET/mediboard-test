<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

global  $can, $m, $g, $dPconfig;
$can->needsRead();
/*
 * Récupération des variables en session et ou issues des formulaires.
 */
$salle            = mbGetValueFromGetOrSession("salle");
$op               = mbGetValueFromGetOrSession("op");
$date             = mbGetValueFromGetOrSession("date", mbDate());


$blood_salvage = new CBloodSalvage();
$totaltime = "00:00:00";
$modif_operation    = $date>=mbDate();
$timing = array();



$anticoagulant = new CBcbClasseATC(); 
$anticoagulant_list = $anticoagulant->loadRefProduitsLivret("B01AB");

$version_patient = CModule::getActive("dPpatients");
$isInDM = ($version_patient->mod_version >= 0.71);

$selOp = new COperation();

if ($op) {
  $selOp->load($op);
  $selOp->loadRefsFwd();
  $selOp->_ref_sejour->loadExtDiagnostics();
  $selOp->_ref_sejour->loadRefDossierMedical();
  $selOp->_ref_sejour->_ref_dossier_medical->loadRefsBack();
  $selOp->_ref_plageop->loadRefsFwd();
  $selOp->_ref_sejour->_ref_patient->loadRefsfwd(); 
  $selOp->_ref_sejour->_ref_patient->loadRefDossierMedical(); 
  $selOp->_ref_sejour->_ref_patient->loadRefConstantesMedicales();  
  
  $where = array();
  $where["operation_id"] = "='$selOp->_id'";  
  $blood_salvage->loadObject($where);
  
  $timing["_recuperation_start"] = array();
  foreach($timing as $key => $value) {
    for($i = -CAppUI::conf("dPsalleOp max_sub_minutes"); $i < CAppUI::conf("dPsalleOp max_add_minutes") && $blood_salvage->$key !== null; $i++) {
      $timing[$key][] = mbTime("$i minutes", $blood_salvage->$key);
    }
  }
}

$smarty = new CSmartyDP();

$smarty->assign("blood_salvage", $blood_salvage); 
$smarty->assign("salle", $salle);
$smarty->assign("selOp", $selOp);
$smarty->assign("date", $date);
$smarty->assign("modif_operation", $modif_operation);
$smarty->assign("isInDM", $isInDM);
$smarty->assign("totaltime", $totaltime);
$smarty->assign("anticoagulant_list", $anticoagulant_list);
$smarty->assign("timing", $timing);

$smarty->display("inc_bloodSalvage.tpl");
?>