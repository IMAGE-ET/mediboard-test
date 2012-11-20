<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$plage_id = CValue::get("plage_id");
$user_id  = CValue::get("user_id");

$user = CMediusers::get($user_id);
$users = CMediusers::loadListFromType(null,PERM_EDIT);

// Chargement de la plage
$plageastreinte = new CPlageAstreinte();
$plageastreinte->user_id = $user_id;
$plageastreinte->load($plage_id);
$plageastreinte->loadRefsNotes();
$plageastreinte->loadRefUser();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user",      $user);
$smarty->assign("users",      $users);
$smarty->assign("plageastreinte",  $plageastreinte);
$smarty->display("inc_edit_plage_astreinte.tpl");
?>