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

$praticien_id = CValue::get('praticien_id');
$codable_class = CValue::get('codable_class');
$codable_id = CValue::get('codable_id');
$date = Cvalue::get('date');
$lock = CValue::get('lock', 1);

$user = CMediusers::get();
/** @var CCodable $codable */
$codable = CMbObject::loadFromGuid("$codable_class-$codable_id");
$codage = CCodageCCAM::get($codable, $praticien_id, 1, $date);

if (CAppUI::conf('ccam CCodable lock_codage_ccam') != 'password' && $codable_class != 'CSejour') {
  $codage = new CCodageCCAM();
  $codage->praticien_id = $praticien_id;
  $codage->codable_class = $codable_class;
  $codage->codable_id = $codable_id;
  $codages = $codage->loadMatchingList();

  foreach ($codages as $_codage) {
    $_codage->locked = $lock;
    $_codage->store();
  }

  $msg = $lock ? 'CCodageCCAM-msg-codage_locked' : 'CCodageCCAM-msg-codage_unlocked';
  CAppUI::setMsg($msg, UI_MSG_OK);
  echo CAppUI::getMsg();
  CApp::rip();
}

$smarty = new CSmartyDP();
$smarty->assign('praticien_id', $praticien_id);
$smarty->assign('praticien', $codage->loadPraticien());
$smarty->assign('codable_class', $codable->_class);
$smarty->assign('codable_id', $codable->_id);
$smarty->assign('date', $date);
$smarty->assign('lock', $lock);

if (CAppUI::conf('ccam CCodable lock_codage_ccam') == 'password' && $user->_id != $codage->praticien_id) {
  $smarty->assign('error', 1);
}

$smarty->display('inc_check_lock_codage.tpl');