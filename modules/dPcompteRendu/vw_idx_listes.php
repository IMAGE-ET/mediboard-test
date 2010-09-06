<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

// Utilisateurs disponibles
$user = CMediusers::getCurrent();
$users = $user->loadUsers(PERM_EDIT);


// Functions disponibles
$func = new CFunctions();
$funcs = $func->loadSpecialites(PERM_EDIT);

// Etablissements disponibles
$etabs = array(CGroups::loadCurrent());

$user->load(CValue::getOrSession("filter_user_id", $user->_id));

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
$liste->chir_id = $user->_id;
$liste->load(CValue::getOrSession("liste_id")); 
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