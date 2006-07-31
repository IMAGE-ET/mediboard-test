<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass("mediusers") );
require_once( $AppUI->getModuleClass("dPcabinet", "consultation") );
require_once( $AppUI->getModuleClass("dPplanningOp", "planning") );
require_once( $AppUI->getModuleClass("dPcompteRendu", "compteRendu") );
require_once( $AppUI->getModuleClass("dPcompteRendu", "pack") );
require_once( $AppUI->getModuleClass("dPpatients", "patients") );
require_once( $AppUI->getModuleClass("dPfiles"     , "filescategory"));

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// L'utilisateur est-il praticien?
$chirSel = new CMediusers;
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chirSel = $mediuser;
}

$pat_id = mbGetValueFromGetOrSession("patSel");
$patSel = new CPatient;
$patSel->load($pat_id);
$patient = new CPatient;
$patient->load($pat_id);
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement des références du patient
if ($pat_id) {
  
  // Infos patient complètes (tableau de droite)
  $patient->loadDossierComplet();
  
  // Infos patient du cabinet (tableau de gauche)
  $patSel = clone $patient;
  foreach ($patSel->_ref_consultations as $key => $value) {
    if (!array_key_exists($value->_ref_plageconsult->chir_id, $listPrat)) {
    	unset($patSel->_ref_consultations[$key]);
    }
  }

  foreach ($patSel->_ref_sejours as $key => $sejour) {
    if (!array_key_exists($sejour->praticien_id, $listPrat)) {
      unset($patSel->_ref_sejours[$key]);
    } else {
      $patSel->_ref_sejours[$key]->loadRefsFwd();
      $patSel->_ref_sejours[$key]->loadRefsOperations();
      foreach($patSel->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
        if (!array_key_exists($op->chir_id, $listPrat)) {
          unset($patSel->_ref_sejours[$key]->_ref_operations[$keyOp]);
        } else {
          $patSel->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
        }
      }
    }
  }
}

$canEditCabinet = !getDenyEdit("dPcabinet");

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile",$affichageNbFile                           );
$smarty->assign("patSel", $patSel);
$smarty->assign("patient", $patient);
$smarty->assign("chirSel", $chirSel);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("canEditCabinet", $canEditCabinet);
$smarty->assign("listCategory"  , $listCategory );

$smarty->display("vw_dossier.tpl");

?>