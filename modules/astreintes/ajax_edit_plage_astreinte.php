<?php /** $Id: **/

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkEdit();


$plage_id = CValue::get("plage_id");
$plage_date = CValue::get("date");
$plage_hour = CValue::get("hour");
$plage_minutes = CValue::get("minutes");
$user_id = CValue::get("user_id");
$user = CMediusers::get($user_id);

$users = array($user);

if ($user->isAdmin()) {
  $where = array("actif" => "= '1'");
  $users = $user->loadListWithPerms(PERM_EDIT);
}

$plageastreinte = new CPlageAstreinte();

// edition
if ($plage_id) {
  $plageastreinte->load($plage_id);
  $plageastreinte->loadRefsNotes();
}
else {
  $plageastreinte->phone_astreinte = $user->_user_astreinte;
}

//creation
if (!$plageastreinte->_id) {
  if ($plage_date && $plage_hour) {
    $plageastreinte->start = "$plage_date $plage_hour:$plage_minutes:00";
  }
  $plageastreinte->user_id = $user_id;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("users",      $users);
$smarty->assign("plageastreinte",  $plageastreinte);
$smarty->display("inc_edit_plage_astreinte.tpl");
