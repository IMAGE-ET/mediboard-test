<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPcompteRendu
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$pack_id = CValue::get("pack_id");
$user_id = CValue::getOrSession("user_id", CAppUI::$user->_id);

// Pour la création d'un pack, on affecte comme utilisateur celui de la session par défaut.
$user = CMediusers::get($user_id);
$user->loadRefFunction()->loadRefGroup();

// Utilisateurs disponibles
$user = new CMediusers();
$users = $user->loadUsers(PERM_EDIT);

// Fonctions disponibles
$function = new CFunctions();
$functions = $function->loadSpecialites(PERM_EDIT);

// Groupes disponibles
$groups = array(CGroups::loadCurrent());

// Chargement du pack
$pack = new CPack;
$pack->load($pack_id);
$pack->loadRefsNotes();
$pack->loadRefOwner();
$pack->loadBackRefs("modele_links", "modele_to_pack_id");
if (!$pack->_id) {
  $pack->user_id = $user->_id;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack"     , $pack);
$smarty->assign("user_id"  , $user_id);
$smarty->assign("users"    , $users);
$smarty->assign("functions", $functions);
$smarty->assign("groups"   , $groups);

$smarty->display("inc_edit_pack.tpl"); 
?>