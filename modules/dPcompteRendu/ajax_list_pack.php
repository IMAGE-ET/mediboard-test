<?php /* $ */

/**
 *  @package Mediboard
 *  @subpackage dPmedicament
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$user_id      = CValue::getOrSession("user_id", CAppUI::$user->_id);
$object_class = CValue::getOrSession("object_class");

$user = CMediusers::get($user_id);
$user->loadRefFunction();

$packs = CPack::loadAllPacksFor($user_id, "user", $object_class);
foreach ($packs as $_packs_by_owner) {
  foreach ($_packs_by_owner as $_pack) {
    $_pack->loadRefOwner();
    $_pack->loadBackRefs("modele_links");
    $_pack->loadHeaderFooter();
  }
}

// Utilisateurs modifiables

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user" , $user);
$smarty->assign("packs", $packs);

$smarty->display("inc_list_pack.tpl");

?>