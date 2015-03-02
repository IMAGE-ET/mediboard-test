<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ccam
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$codable_id = Cvalue::post('codable_id');
$codable_class = CValue::post('codable_class');
$praticien_id = Cvalue::post('praticien_id');
$date = Cvalue::post('date');
$user_password = CValue::post('user_password');
$lock_all_codages = Cvalue::post('lock_all_codages', 0);
$lock = CValue::post('lock', 1);

$codage = new CCodageCCAM();
$codage->praticien_id = $praticien_id;
$codage->codable_class = $codable_class;
$codage->codable_id = $codable_id;
if ($date && !$lock_all_codages) {
  $codage->date = $date;
}

$codages = $codage->loadMatchingList();
$user = CMediusers::get();

if (CAppUI::conf('ccam CCodable lock_codage_ccam') != 'password' ||
    (CAppUI::conf('ccam CCodable lock_codage_ccam') == 'password' && $user->_id &&
     CUser::checkPassword($user->_user_username, $user_password)
    )
) {
  foreach ($codages as $_codage) {
    $_codage->locked = $lock;
    $_codage->store();
  }

  $msg = $lock ? 'CCodageCCAM-msg-codage_locked' : 'CCodageCCAM-msg-codage_unlocked';
  CAppUI::setMsg($msg, UI_MSG_OK);
  echo CAppUI::getMsg();
}
elseif ($user_password && CAppUI::conf('ccam CCodable lock_codage_ccam') == 'password') {
  CAppUI::setMsg("CUser-user_password-nomatch", UI_MSG_ERROR);
  echo CAppUI::getMsg();
}