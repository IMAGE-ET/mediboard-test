<?php /* $Id: addedit_modeles.php,v 1.14 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision: 1.14 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'templatemanager') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

global $ECompteRenduType;

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

// Gestion du modèle
if ($compte_rendu->compte_rendu_id) {
  $prat_id = $compte_rendu->chir_id;
  $templateManager = new CTemplateManager;
  $templateManager->valueMode = false;
  $templateManager->loadLists($compte_rendu->chir_id, $compte_rendu->compte_rendu_id);
  $templateManager->loadHelpers($compte_rendu->chir_id, $compte_rendu->type);
  $templateManager->applyTemplate($compte_rendu);
  $templateManager->initHTMLArea();
}

// Création du template
require_once( $AppUI->getSystemClass('smartydp'));
$smarty = new CSmartyDP;

$smarty->assign('prat_id', $prat_id);
$smarty->assign('compte_rendu_id', $compte_rendu_id);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('listFunc', $listFunc);
$smarty->assign('ECompteRenduType', $ECompteRenduType);
$smarty->assign('compte_rendu', $compte_rendu);

$smarty->display('addedit_modeles.tpl');

?>