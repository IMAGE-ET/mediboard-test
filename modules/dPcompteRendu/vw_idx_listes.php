<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$filter_user_id = CValue::getOrSession("filter_user_id");
$liste_id = CValue::getOrSession("liste_id");

if (!$filter_user_id) {
  $filter_user_id = CAppUI::$user->_id;
}

// Utilisateurs disponibles
$user = CMediusers::get();
$users = $user->loadUsers(PERM_EDIT);

// Functions disponibles
$func = new CFunctions();
$funcs = $func->loadSpecialites(PERM_EDIT);

// Etablissements disponibles
$etabs = array(CGroups::loadCurrent());

$user = new CMediusers();
$user->load($filter_user_id);

$owners  = $user->getOwners();
$modeles = CCompteRendu::loadAllModelesFor($user->_id);
$listes  = CListeChoix::loadAllFor($user->_id);

// Modèles associés
foreach($listes as $_listes) {
  foreach($_listes as $_liste) {
    $_liste->loadRefModele();
  }
}

// Liste sélectionnée
$liste = new CListeChoix();
$liste->user_id = $user->_id;
$liste->load($liste_id); 
$liste->loadRefOwner();
$liste->loadRefModele();
$liste->loadRefsNotes();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("users"  , $users);

$smarty->assign("prats"  , $users);
$smarty->assign("funcs"  , $funcs);
$smarty->assign("etabs"  , $etabs);

$smarty->assign("owners" , $owners);
$smarty->assign("modeles", $modeles);
$smarty->assign("listes" , $listes);

$smarty->assign("user"   , $user );
$smarty->assign("liste"  , $liste);

$smarty->display("vw_idx_listes.tpl");

?>