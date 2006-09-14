<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

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

// Compte-rendu selectionné
$compte_rendu_id = mbGetValueFromGetOrSession("compte_rendu_id");
$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
if($compte_rendu->object_id){
  $AppUI->redirect("m=$m&tab=vw_modeles");
}
// Gestion du modèle
$templateManager = new CTemplateManager;
if ($compte_rendu->compte_rendu_id) {
  $prat_id = $compte_rendu->chir_id;
  $templateManager->valueMode = false;
  $templateManager->loadLists($compte_rendu->chir_id, $compte_rendu->compte_rendu_id);
  $templateManager->loadHelpers($compte_rendu->chir_id, $compte_rendu->type);
  $templateManager->applyTemplate($compte_rendu);
  $templateManager->initHTMLArea();
}

// Liste des Types de documents
$listType = $templateManager->listType;
ksort($listType);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("prat_id"        , $prat_id);
$smarty->assign("compte_rendu_id", $compte_rendu_id);
$smarty->assign("listPrat"       , $listPrat);
$smarty->assign("listFunc"       , $listFunc);
$smarty->assign("listType"       , $listType);
$smarty->assign("compte_rendu"   , $compte_rendu);

$smarty->display("addedit_modeles.tpl");

?>