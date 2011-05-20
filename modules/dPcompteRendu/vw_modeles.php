<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Liste des praticiens et cabinets accessibles
$user = CMediusers::get();
$praticiens = $user->loadUsers(PERM_EDIT);

// Filtres
$filtre = new CCompteRendu();
$filtre->user_id      = CValue::getOrSession("user_id", $user->_id);
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

// Praticien
$user = new CMediusers;
$user->load($filtre->user_id);
if ($user->isPraticien()) {
  CValue::setSession("prat_id", $user->_id);
}

$modeles = CCompteRendu::loadAllModelesFor($filtre->user_id, 'prat', $filtre->object_class, $filtre->type);
$owners = $user->getOwners();

// On ne met que les classes qui ont une methode filTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::getTemplatedClasses();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("filtre"    , $filtre);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("modeles"   , $modeles);
$smarty->assign("owners"    , $owners);

$smarty->display("vw_modeles.tpl");

?>