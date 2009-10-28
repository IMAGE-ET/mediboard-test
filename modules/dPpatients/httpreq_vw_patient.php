<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

// Liste des modèles
$where = array();
$where["object_id"] = "IS NULL";
$where["object_class"] = "= 'CPatient'";
$order = "nom"; 

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
$modele = new CCompteRendu();
$listModelePrat = $modele->loadlist($where, $order);
unset($where["chir_id"]);

$listFct = new CMediusers();
$listFct = $listFct->loadFonctions(PERM_EDIT);
$where["function_id"] = CSQLDataSource::prepareIn(array_keys($listFct));
$modele = new CCompteRendu();
$listModeleFct = $modele->loadlist($where, $order);
unset($where["function_id"]);

// Liste des Category pour les fichiers
$listCategory = CFilesCategory::listCatClass("CPatient");

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

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("affichageNbFile" , $affichageNbFile );
$smarty->assign("patient"         , $patient         );
$smarty->assign("chir"            , $chir            );
$smarty->assign("anesth"          , $anesth          );
$smarty->assign("listPrat"        , $listPrat        );
$smarty->assign("listCategory"    , $listCategory    );

$smarty->assign("canPatients"  , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions", CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"   , CModule::getCanDo("dPcabinet"));

$smarty->assign("listModelePrat"  , $listModelePrat  );
$smarty->assign("listModeleFct"   , $listModeleFct   );

$smarty->display("inc_vw_patient.tpl");
?>