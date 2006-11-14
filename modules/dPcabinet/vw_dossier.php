<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$pat_id = mbGetValueFromGetOrSession("patSel");

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);


$patient = new CPatient;
$patient->load($pat_id);

// Chargement des références du patient
if ($pat_id) {
  // Infos patient complètes (tableau de droite)
  $patient->loadDossierComplet();
/*
  foreach ($patient->_ref_consultations as $key => $value) {
    if (!array_key_exists($value->_ref_plageconsult->chir_id, $listPrat)) {
        unset($patient->_ref_consultations[$key]);
    }
  }

  foreach ($patient->_ref_sejours as $key => $sejour) {
    if (!array_key_exists($sejour->praticien_id, $listPrat)) {
      unset($patient->_ref_sejours[$key]);
    } else {
      $patient->_ref_sejours[$key]->loadRefsFwd();
      $patient->_ref_sejours[$key]->loadRefsOperations();
      foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
        if (!array_key_exists($op->chir_id, $listPrat)) {
          unset($patient->_ref_sejours[$key]->_ref_operations[$keyOp]);
        } else {
          //$patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefPlageOp();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefChir();
          $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->getNumDocsAndFiles();
        }
      }
    }
  }
  */
}

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("patient", $patient                    );
$smarty->assign("listPrat", $listPrat                  );
$smarty->assign("canEditCabinet", $canEditCabinet      );

$smarty->display("vw_dossier.tpl");

?>