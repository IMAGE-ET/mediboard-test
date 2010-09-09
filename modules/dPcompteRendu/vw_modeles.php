<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Liste des praticiens et cabinets accessibles
$user = CMediusers::getCurrent();
$praticiens = $user->loadUsers(PERM_EDIT);

// Filtres
$filtre = new CCompteRendu();
$filtre->chir_id      = CValue::getOrSession("chir_id", $user->_id);
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

// Praticien
$user = new CMediUsers;
$user->load($filtre->chir_id);
if ($user->isPraticien()) {
  CValue::setSession("prat_id", $user->_id);
}

$modeles = CCompteRendu::loadAllModelesFor($filtre->chir_id, 'prat', $filtre->object_class, $filtre->type);
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