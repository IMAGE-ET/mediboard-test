<?php

/**
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

if (!CAppUI::pref("allowed_modify_identity_status")) {
  CAppUI::redirect("m=system&a=access_denied");
}

$date = CValue::get('date');
$ids  = CValue::get('ids');

if (!$ids || !$date) {
  CAppUI::stepAjax('common-error-Missing parameter', UI_MSG_ERROR);
}

$patient_ids = explode('-', $ids);
$date        = preg_replace('/(\d\d)\/(\d\d)\/(\d\d\d\d)/', '\\3-\\2-\\1', $date);

if (!$patient_ids || !$date) {
  CAppUI::stepAjax('common-error-Invalid parameter', UI_MSG_ERROR);
}

$user_log = new CUserLog();
$ds       = $user_log->getDS();

$where = array(
  'object_class' => "= 'CPatient'",
  'object_id'    => $ds->prepareIn($patient_ids),
  'type'         => "= 'merge'",
  'date'         => $ds->prepare("BETWEEN ?1 AND ?2", "$date 00:00:00", "$date 23:59:59")
);

$logs = $user_log->loadList($where);
foreach ($logs as $_log) {
  $_log->loadView();

  /** @var CPatient $patient */
  $patient = $_log->_ref_object;
  $identifiants = $patient->loadBackRefs('identifiants');

  /** @var CIdSante400 $_id */
  foreach ($identifiants as $_id) {
    $_id->getSpecialType();
  }
}

$smarty = new CSmartyDP();
$smarty->assign('date', $date);
$smarty->assign('logs', $logs);
$smarty->assign('logs_count', count($logs));
$smarty->display("patient_state/vw_merged_patients_details.tpl");