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

$where = array("actif" => "= '1' ");
$users = $user->loadListWithPerms(PERM_EDIT, $where);

$plageastreinte = new CPlageAstreinte();

// edition
if ($plage_id) {
  $plageastreinte->load($plage_id);
  $plageastreinte->loadRefsNotes();
}

// creation
if (!$plageastreinte->_id) {
  // phone
  $plageastreinte->phone_astreinte = $user->_user_astreinte;

  // date & hour
  if ($plage_date && $plage_hour) {
    $plageastreinte->start = "$plage_date $plage_hour:$plage_minutes:00";
  }

  // user
  if (in_array($user->_id, array_keys($users))) {
    $plageastreinte->user_id = $user->_id;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("users",     $users);
$smarty->assign("user",      $user);
$smarty->assign("plageastreinte",  $plageastreinte);
$smarty->display("inc_edit_plage_astreinte.tpl");
