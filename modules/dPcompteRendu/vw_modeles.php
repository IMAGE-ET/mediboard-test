<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers();

// Liste des praticiens et cabinets accessibles
$praticiens = $user->loadUsers(PERM_EDIT);

// Filtres
$filtre = new CCompteRendu();
$filtre->chir_id      = mbGetValueFromGetOrSession("chir_id", $AppUI->user_id);
$filtre->object_class = mbGetValueFromGetOrSession("object_class");
$filtre->type         = mbGetValueFromGetOrSession("type");

$user = new CMediusers;
$user->load($filtre->chir_id);
$user->loadRefFunction();
$user->_ref_function->loadRefGroup();

if ($user->isPraticien()) {
  mbSetValueToSession("prat_id", $user->user_id);
}

$modeles = CCompteRendu::loadAllModelesFor($filtre->chir_id, 'prat', $filtre->object_class, $filtre->type);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("filtre"    , $filtre);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("modeles"   , $modeles);

$smarty->display("vw_modeles.tpl");

?>