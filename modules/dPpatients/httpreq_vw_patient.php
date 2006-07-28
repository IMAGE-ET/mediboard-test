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

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
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

$patient_id = mbGetValueFromGetOrSession("id", 0);

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

$afficahgeNbFile = array();
$affichageNbFile[""] = array();
$affichageNbFile[""]["name"] = "Aucune Catégorie";
$affichageNbFile[""]["nb"]   = 0;
foreach($listCategory as $keyCat => $currCat){
  $affichageNbFile["$keyCat"] = array();
  $affichageNbFile["$keyCat"]["name"] = $currCat->nom;
  $affichageNbFile["$keyCat"]["nb"]   = 0;
}
if ($patient->patient_id) {
  foreach($patient->_ref_files as $keyFile => $curr_file){
    $affichageNbFile["".$curr_file->file_category_id.""]["nb"] ++;
  }
}
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

$smarty->display("inc_vw_patient.tpl");
?>