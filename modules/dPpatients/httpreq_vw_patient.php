<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients"  , "patients"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPcabinet"   , "consultation"));
require_once($AppUI->getModuleClass("dPfiles"     , "filescategory"));
require_once($AppUI->getModuleClass("dPfiles"     , "files"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("id", 0);

$canReadFiles     = isMbModuleVisible("dPfiles") and isMbModuleReadAll("dPfiles");
$canEditFiles     = isMbModuleVisible("dPfiles") and isMbModuleEditAll("dPfiles");
$canReadCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleReadAll("dPcompteRendu");
$canEditCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleEditAll("dPcompteRendu");

// Liste des modèles
$listModeleAuth = array();
if ($patient_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $listFct = new CMediusers();
  $listFct = $listFct->loadFonctions(PERM_READ);
  
  $where = array();
  $where["chir_id"] = "IN (".implode(", ",array_keys($listPrat)).")";
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
 
  $where = array();
  $where["function_id"] = "IN (".implode(", ",array_keys($listFct)).")";
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModeleFct = new CCompteRendu;
  $listModeleFct = $listModeleFct->loadlist($where, $order);
}

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// L'utilisateur est-il un chirurgien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Chirurgien"))) {
  $chir = $mediuser;
} else {
  $chir = null;
}

// L'utilisateur est-il un anesthésiste
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Anesthésiste"))) {
  $anesth = $mediuser;
} else {
  $anesth = null;
}


// Récuperation du patient sélectionné
$patient = new CPatient;
if(dPgetParam($_GET, "new", 0)) {
  $patient->load(NULL);
  mbSetValueToSession("id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->patient_id) {
  $patient->loadDossierComplet();
}


$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$canEditCabinet = !getDenyEdit("dPcabinet");

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile",$affichageNbFile                           );
$smarty->assign("patient"       , $patient                                   );
$smarty->assign("chir"          , $chir                                      );
$smarty->assign("anesth"        , $anesth                                    );
$smarty->assign("listPrat"      , $listPrat                                  );
$smarty->assign("canEditCabinet", $canEditCabinet                            );
$smarty->assign("listCategory"  , $listCategory                              );
$smarty->assign("canReadFiles"  , $canReadFiles                              );
$smarty->assign("canEditFiles"  , $canEditFiles                              );
$smarty->assign("canReadCptRendus", $canReadCptRendus                        );
$smarty->assign("canEditCptRendus", $canEditCptRendus                        );
$smarty->assign("listModelePrat", $listModelePrat                            );
$smarty->assign("listModeleFct" , $listModeleFct                             );

$smarty->display("inc_vw_patient.tpl");
?>