<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

// Liste des praticiens accessibles
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// L'utilisateur est-il praticien?
$prat_id = mbGetValueFromGetOrSession("selPrat");

if (!$prat_id) {
  $mediuser = new CMediusers;
  $mediuser->load($AppUI->user_id);

  if ($mediuser->isPraticien()) {
    $prat_id = $AppUI->user_id;
    mbSetValueToSession("selPrat", $prat_id);
  }
}

$med = new CMediusers();
$user_id = $AppUI->user_id;
$userCourant = $med->load($user_id);

// Compte-rendu selectionné
$compte_rendu_id = mbGetValueFromGetOrSession("compte_rendu_id");
$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
if($compte_rendu->object_id){
  $compte_rendu = new CCompteRendu();
}else{
  $compte_rendu->loadCategory();
}
// Gestion du modèle
$templateManager = new CTemplateManager;
$templateManager->editor = "fckeditor2.3.2";


// L'utilisateur est il une secretaire ou un administrateur?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$secretaire = $mediuser->isFromType(array("Secrétaire", "Administrator"));

// si l'utilisateur courant est la secretaire ou le proprietaire du modele alors droit dessus
if(!($compte_rendu->_id)||($secretaire)||($compte_rendu->chir_id == $mediuser->user_id)||($compte_rendu->function_id==$mediuser->function_id)){
	$droit = 1;
}
// sinon, seulement, droit en lecture
else {
	$droit = 0;
}


if ($compte_rendu->_id) {
  // si pas de droit en ecriture, pas besoin d'initialiser fckeditor
  if($droit){
    $prat_id = $compte_rendu->chir_id;
    $templateManager->valueMode = false;
    $templateManager->loadLists($compte_rendu->chir_id, $compte_rendu->_id);
    $templateManager->loadHelpers($compte_rendu->chir_id, $compte_rendu->object_class);
    $templateManager->applyTemplate($compte_rendu);
    $templateManager->initHTMLArea();
  }
}



// Class and fields
$listObjectClass     = array();
$listObjectAffichage = array();
foreach($compte_rendu->_enums["object_class"] as $valueClass){
  $listObjectClass[$valueClass]     = array();
  $listObjectAffichage[$valueClass] = CAppUI::tr($valueClass);
}
foreach($listObjectClass as $keyClass=>$value){
  $listCategory = CFilesCategory::listCatClass($keyClass);
  foreach($listCategory as $keyCat=>$valueCat){
    $listObjectClass[$keyClass][$keyCat] = htmlentities($listCategory[$keyCat]->nom);
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"            , $mediuser);
$smarty->assign("isPraticien"         , $userCourant->isPraticien());
$smarty->assign("user_id"             , $user_id);
$smarty->assign("prat_id"             , $prat_id);
$smarty->assign("compte_rendu_id"     , $compte_rendu_id);
$smarty->assign("listPrat"            , $listPrat);
$smarty->assign("listFunc"            , $listFunc);
$smarty->assign("listObjectClass"     , $listObjectClass);
$smarty->assign("compte_rendu"        , $compte_rendu);
$smarty->assign("listObjectAffichage" , $listObjectAffichage);
$smarty->assign("droit"               , $droit);
$smarty->display("addedit_modeles.tpl");

?>