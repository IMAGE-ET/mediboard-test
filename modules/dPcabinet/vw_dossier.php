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

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Liste des modèles
$listModeleAuth = array();
$listModelePrat = new CCompteRendu;
$listModeleFct = new CCompteRendu;
if ($pat_id) {
  $listFct = new CMediusers();
  $listFct = $listFct->loadFonctions(PERM_READ);
  
  $where = array();
  $where["chir_id"] = db_prepare_in(array_keys($listPrat));
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModelePrat = $listModelePrat->loadlist($where, $order);
 
  $where = array();
  $where["function_id"] = db_prepare_in(array_keys($listFct));
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModeleFct = $listModeleFct->loadlist($where, $order);
}

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

$patient = new CPatient;
$patient->load($pat_id);

// Chargement des références du patient
if ($pat_id) {
  // Infos patient complètes (tableau de droite)
  $patient->loadDossierComplet();

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
        }
      }
    }
  }
}

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile",$affichageNbFile     );
$smarty->assign("patient", $patient                    );
$smarty->assign("listPrat", $listPrat                  );
$smarty->assign("canEditCabinet", $canEditCabinet      );
$smarty->assign("listCategory"  , $listCategory        );
$smarty->assign("canReadFiles"  , $canReadFiles        );
$smarty->assign("canEditFiles"  , $canEditFiles        );
$smarty->assign("canReadCptRendus", $canReadCptRendus  );
$smarty->assign("canEditCptRendus", $canEditCptRendus  );
$smarty->assign("listModelePrat", $listModelePrat      );
$smarty->assign("listModeleFct" , $listModeleFct       );

$smarty->display("vw_dossier.tpl");

?>