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

$modFile    = CModule::getInstalled("dPfiles");
$modCR      = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $modFile->canRead();
$canEditFiles     = $modFile->canEdit();
$canEditDoc       = $modCR->canEdit();

$diagnosticsInstall = CModule::getActive("dPImeds") && CModule::getActive("dPsante400");

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

// Chargement des fichiers
$typeVue        = 1;

  $selClass = "CPatient";
  $selKey   = $patient_id;
  mbSetValueToSession("selClass", $selClass);
  mbSetValueToSession("selKey"  , $selKey);

$object = new $selClass;
$object->load($selKey);

$affichageFile = CFile::loadFilesAndDocsByObject($object);

// Création du template
$smarty = new CSmartyDP();

$canEditFileDoc = $canEditFiles || $canEditDoc;

$smarty->assign("canEditFileDoc"    , $canEditFileDoc);
$smarty->assign("canEditDoc"        , $canEditDoc);
$smarty->assign("patient"           , $patient         );
$smarty->assign("canEditCabinet"    , $canEditCabinet  );
$smarty->assign("listPrat"          , $listPrat        );
$smarty->assign("listCategory"      , $listCategory    );

$smarty->assign("canReadFiles"      , $canReadFiles    );
$smarty->assign("canEditFiles"      , $canEditFiles    );

$smarty->assign("affichageFile"     , $affichageFile   );
$smarty->assign("selClass"          , $selClass        );
$smarty->assign("selKey"            , $selKey          );
$smarty->assign("selView"           , $object->_view   );
$smarty->assign("typeVue"           , $typeVue         );
$smarty->assign("accordDossier"     , 0                );
$smarty->assign("object"            , $patient         );

$smarty->assign("diagnosticsInstall", $diagnosticsInstall);

$smarty->display("vw_full_patients.tpl");

?>