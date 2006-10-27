<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

if(!$patient_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();
$canEditDoc       = $fileCptRendus->canEdit();



$diagnosticsInstall = CModule::getActive("dPImeds") && CModule::getActive("dPsante400");

$where = array();
$where["object_id"] = "IS NULL";
$where["object_class"] = "= 'CPatient'";
$order = "nom"; 

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);
$where["chir_id"] = db_prepare_in(array_keys($listPrat));
$modele = new CCompteRendu();
$listModelePrat = $modele->loadlist($where, $order);
unset($where["chir_id"]);

$listFct = new CMediusers();
$listFct = $listFct->loadFonctions(PERM_EDIT);
$where["function_id"] = db_prepare_in(array_keys($listFct));
$modele = new CCompteRendu();
$listModeleFct = $modele->loadlist($where, $order);
unset($where["function_id"]);

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
$patient->load($patient_id);

$patient->loadDossierComplet();

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

// Chargement des fichiers
$typeVue        = 1;

$selClass       = mbGetValueFromGetOrSession("selClass", "CPatient");
$selKey         = mbGetValueFromGetOrSession("selKey"  , $patient_id);

$object = new $selClass;
$object->load($selKey);

$affichageFile = CFile::loadFilesAndDocsByObject($object);

// Création du template
$smarty = new CSmartyDP(1);

$canEditFileDoc = $canEditFiles || $canEditDoc;

$smarty->assign("canEditFileDoc" , $canEditFileDoc);
$smarty->assign("canEditDoc"     , $canEditDoc);
$smarty->assign("patient"         , $patient         );
$smarty->assign("chir"            , $chir            );
$smarty->assign("anesth"          , $anesth          );
$smarty->assign("listPrat"        , $listPrat        );
$smarty->assign("canEditCabinet"  , $canEditCabinet  );
$smarty->assign("listCategory"    , $listCategory    );
$smarty->assign("canReadFiles"    , $canReadFiles    );
$smarty->assign("canEditFiles"    , $canEditFiles    );
$smarty->assign("canReadCptRendus", $canReadCptRendus);
$smarty->assign("canEditCptRendus", $canEditCptRendus);
$smarty->assign("listModelePrat"  , $listModelePrat  );
$smarty->assign("listModeleFct"   , $listModeleFct   );
$smarty->assign("affichageFile"   , $affichageFile   );
$smarty->assign("selClass"        , $selClass        );
$smarty->assign("selKey"          , $selKey          );
$smarty->assign("selView"         , $object->_view   );
$smarty->assign("typeVue"         , $typeVue         );

$smarty->assign("diagnosticsInstall" , $diagnosticsInstall);

$smarty->display("vw_full_patients.tpl");

?>