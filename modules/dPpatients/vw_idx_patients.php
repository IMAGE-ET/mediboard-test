<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPpatients"  , "patients"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPcabinet"   , "consultation"));
require_once($AppUI->getModuleClass("dPfiles"     , "filescategory"));
require_once($AppUI->getModuleClass("dPfiles"     , "files"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);


$canReadFiles     = isMbModuleVisible("dPfiles") and isMbModuleReadAll("dPfiles");
$canEditFiles     = isMbModuleVisible("dPfiles") and isMbModuleEditAll("dPfiles");
$canReadCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleReadAll("dPcompteRendu");
$canEditCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleEditAll("dPcompteRendu");

// Liste des modèles
$listModelePrat = array(); 
$listModeleFct  = array();
$compteRendu = new CCompteRendu;

if ($patient_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $listFct = new CMediusers();
  $listFct = $listFct->loadFonctions(PERM_READ);
  
  $where = array();
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";  

  if (count(array_keys($listPrat))) {
    $where["chir_id"] = "IN (".implode(", ",array_keys($listPrat)).")";
    $listModelePrat = $compteRendu->loadlist($where, $order);
    unset($where["chir_id"]);
  }
 
  if(count(array_keys($listFct))) {
    $where["function_id"] = "IN (".implode(", ",array_keys($listFct)).")";
    $listModeleFct = $compteRendu->loadlist($where, $order);
    unset($where["function_id"]);
  }
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
if(mbGetValueFromGet("new", 0)) {
  $patient->load(null);
  mbSetValueToSession("patient_id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->patient_id) {
  $patient->loadDossierComplet();
}

// Récuperation des patients recherchés
$patient_nom       = mbGetValueFromGetOrSession("nom"       , ""       );
$patient_prenom    = mbGetValueFromGetOrSession("prenom"    , ""       );
$patient_naissance = mbGetValueFromGetOrSession("naissance" , "off"    );
$patient_day       = mbGetValueFromGetOrSession("Date_Day"  , date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year" , date("Y"));

$where = null;
if ($patient_nom   ) $where[] = "nom LIKE '".addslashes($patient_nom)."%'";
if ($patient_prenom) $where[] = "prenom LIKE '".addslashes($patient_prenom)."%'";
if ($patient_naissance == "on") {
  $where["naissance"] = "= '$patient_year/$patient_month/$patient_day'";
}

$patients = null;
if ($where) {
  $patients = new CPatient();
  $patients = $patients->loadList($where, "nom, prenom, naissance", "0, 100");
}

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$canEditCabinet = !getDenyEdit("dPcabinet");

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile",$affichageNbFile                           );
$smarty->assign("nom"           , $patient_nom                               );
$smarty->assign("prenom"        , $patient_prenom                            );
$smarty->assign("naissance"     , $patient_naissance                         );
$smarty->assign("date"          , "$patient_year-$patient_month-$patient_day");
$smarty->assign("patients"      , $patients                                  );
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
$smarty->display("vw_idx_patients.tpl");
?>