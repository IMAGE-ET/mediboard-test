<?php 

/**
 * $Id$
 *  
 * @category Astreintes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


CCanDo::checkRead();

$user_id = CValue::get("user_id");

$muser = new CMediusers();
$muser->load($user_id);

$user = $muser->loadRefUser();
$muser->loadRefFunction()->loadRefGroup();

$phones = array();
addPhone("CUser-user_astreinte", $user->user_astreinte, $phones);
addPhone("CUser-user_mobile", $user->user_mobile, $phones);
addPhone("CUser-user_phone", $user->user_phone, $phones);
addPhone("CFunctions-tel", $muser->_ref_function->tel, $phones);

function addPhone($field_str, $field, &$phones) {
  if ($field && !in_array($field, $phones)) {
    $phones[$field_str] = $field;
  }
}

//smarty
$smarty = new CSmartyDP();
$smarty->assign("phones", $phones);
$smarty->display("inc_list_phones.tpl");