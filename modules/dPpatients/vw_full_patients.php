<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
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

if(!$patient_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}

$canReadFiles     = isMbModuleVisible("dPfiles") and isMbModuleReadAll("dPfiles");
$canEditFiles     = isMbModuleVisible("dPfiles") and isMbModuleEditAll("dPfiles");
$canReadCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleReadAll("dPcompteRendu");
$canEditCptRendus = isMbModuleVisible("dPcompteRendu") and isMbModuleEditAll("dPcompteRendu");

// Liste des modèles
$listModeleAuth = array();

$listModelePrat = new CCompteRendu;
$listModeleFct = new CCompteRendu;

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);
$listFct = new CMediusers();
$listFct = $listFct->loadFonctions(PERM_EDIT);
  
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

$canEditCabinet = !getDenyEdit("dPcabinet");

// Chargement des fichiers

$accordion_open = 0;
$typeVue        = 1;
$cat_id         = mbGetValueFromGetOrSession("cat_id", 0);
$selClass       = mbGetValueFromGet("selClass", "CPatient");
$selKey         = mbGetValueFromGet("selKey"  , $patient_id);

$object = new $selClass;
$object->load($selKey);
$object->loadRefsFiles();

$listCategory = CFilesCategory::listCatClass($selClass);

$affichageFile = array();
$affichageFile[0] = array();
$affichageFile[0]["name"] = "Aucune Catégorie";
$affichageFile[0]["file"] = array();
foreach($listCategory as $keyCat => $curr_Cat) {
  $affichageFile[$keyCat]["name"] = $curr_Cat->nom;
  $affichageFile[$keyCat]["file"] = array();
}
foreach($object->_ref_files as $keyFile => $FileData){
  $affichageFile[$FileData->file_category_id]["file"][] = $FileData;
}

// Création du template
require_once($AppUI->getSystemClass ("smartydp"));
$smarty = new CSmartyDP(1);

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
$smarty->assign("listCategory"    , $listCategory    );
$smarty->assign("accordion_open"  , $accordion_open  );
$smarty->assign("selClass"        , $selClass        );
$smarty->assign("selKey"          , $selKey          );
$smarty->assign("selView"         , $object->_view   );
$smarty->assign("typeVue"         , $typeVue         );
$smarty->assign("cat_id"          , $cat_id          );

$smarty->display("vw_full_patients.tpl");

?>