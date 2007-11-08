<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m, $dPconfig;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

if(!$patient_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet(PERM_READ);
$patient->loadRefDossierMedical();
$patient->_ref_dossier_medical->loadRefsAntecedents();
$patient->_ref_dossier_medical->loadRefsTraitements();

$diagnosticsInstall = CModule::getActive("dPImeds") && CModule::getActive("dPsante400");

$etablissements = CMediusers::loadEtablissements(PERM_EDIT);
$codePraticienEc = null;
$userSel = new CMediusers;
$userSel->load($AppUI->user_id);

$patient->makeDHEUrl();
if(CModule::getInstalled("dPsante400") && ($dPconfig["interop"]["mode_compat"] == "medicap")) {
  $tmpEtab = array();
  foreach($etablissements as $etab) {
    $idExt = new CIdSante400;
    $idExt->loadLatestFor($etab);
    if($idExt->id400) {
      $tmpEtab[$idExt->id400] = $etab;
    }
  }
  $etablissements = $tmpEtab;

  $idExt = new CIdSante400;
  $idExt->loadLatestFor($patient);
  $patIdentEc = $idExt->id400;
  $patient->_urlDHEParams["patIdentEc"]      = $patIdentEc;

  $idExt = new CIdSante400;
  $idExt->loadLatestFor($userSel);
  $codePraticienEc = $idExt->id400;
  $patient->_urlDHEParams["codePraticienEc"] = $codePraticienEc;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("canCabinet", CModule::getCanDo("dPcabinet"));

$smarty->assign("codePraticienEc"    , $codePraticienEc   );
$smarty->assign("listPrat"           , $listPrat          );
$smarty->assign("patient"            , $patient           );
$smarty->assign("diagnosticsInstall" , $diagnosticsInstall);

$smarty->display("inc_vw_full_patients.tpl");
?>