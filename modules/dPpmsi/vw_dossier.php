<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$pat_id = mbGetValueFromGetOrSession("pat_id");

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();

// Liste des modèles
$listModeleAuth = array();
$listModelePrat = new CCompteRendu;
$listModeleFct = new CCompteRendu;
if ($pat_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $listFct = new CMediusers();
  $listFct = $listFct->loadFonctions(PERM_READ);
  
  $where = array();
  $where["chir_id"] = "IN (".implode(", ",array_keys($listPrat)).")";
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModelePrat = $listModelePrat->loadlist($where, $order);
 
  $where = array();
  $where["function_id"] = "IN (".implode(", ",array_keys($listFct)).")";
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModeleFct = $listModeleFct->loadlist($where, $order);
}

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// Chargement des praticiens
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement complet du dossier patient
$patient = new CPatient;
$patient->load($pat_id);
if ($patient->patient_id) {
  $patient->loadDossierComplet();
    
  // Chargements complémentaires sur les opérations
  foreach ($patient->_ref_sejours as $keySejour => $valueSejour) {
    $sejour =& $patient->_ref_sejours[$keySejour];
    $sejour->loadRefGHM();

    foreach ($sejour->_ref_operations as $keyOp => $valueOp) {
      $operation =& $sejour->_ref_operations[$keyOp];
      $operation->loadRefsDocuments();

      $operation->loadRefsActesCCAM();  
      foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
        $acte =& $operation->_ref_actes_ccam[$keyActe];
        $acte->loadRefsFwd();
      }
      
      if($operation->plageop_id) {
        $plage =& $operation->_ref_plageop;
        $plage->loadRefsFwd();
      }
      
      $consultAnest =& $operation->_ref_consult_anesth;
      if ($consultAnest->consultation_anesth_id) {
        $consultAnest->loadRefsFwd();
        $consultAnest->_ref_plageconsult->loadRefsFwd();
      }
    }
  }
}

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->debugging = false;

$smarty->assign("affichageNbFile", $affichageNbFile);
$smarty->assign("patient"       , $patient       );
$smarty->assign("listPrat"      , $listPrat      );
$smarty->assign("canEditCabinet", $canEditCabinet);
$smarty->assign("listCategory"  , $listCategory  );
$smarty->assign("canReadFiles"  , $canReadFiles                              );
$smarty->assign("canEditFiles"  , $canEditFiles                              );
$smarty->assign("canReadCptRendus", $canReadCptRendus                        );
$smarty->assign("canEditCptRendus", $canEditCptRendus                        );
$smarty->assign("listModelePrat", $listModelePrat                            );
$smarty->assign("listModeleFct" , $listModeleFct                             );

$smarty->display("vw_dossier.tpl");

?>