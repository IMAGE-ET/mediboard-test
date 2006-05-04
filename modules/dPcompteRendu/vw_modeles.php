<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'compteRendu') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des praticiens accessibles
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// Utilisateur sélectionné ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("prat_id");

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

if ($userSel->isPraticien()) {
  mbSetValueToSession("prat_id", $userSel->user_id);
}

// Liste des modèles pour le praticien
$listModelePrat = array();
if ($userSel->user_id) {
  $where = array();
  $where["chir_id"] = "= '$userSel->user_id'";
  $order = "type, nom";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Liste des modèles pour la fonction du praticien
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = array();
  $where["function_id"] = "= '$userSel->function_id'";
  $order = "type, nom";
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('userSel', $userSel);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('listModelePrat', $listModelePrat);
$smarty->assign('listModeleFunc', $listModeleFunc);

$smarty->display('vw_modeles.tpl');

?>