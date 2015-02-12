<?php

$_date_min        = CValue::post('_date_min');
$_date_max        = CValue::post('_date_max');
$user_id          = CValue::post('user_id');
$duration         = CValue::post('duration');
$duration_operand = CValue::post('duration_operand');
$purge_limit      = CValue::post('purge_limit', '100');
$just_count       = CValue::post('just_count');

$purge_limit = ($purge_limit) ? $purge_limit : 100;

$ds  = CSQLDataSource::get('std');
$log = new CLongRequestLog();

$where = array();

if ($_date_min) {
  $where[] = $ds->prepare('`datetime` >= ?', $_date_min);
}

if ($_date_max) {
  $where[] = $ds->prepare('`datetime` <= ?', $_date_max);
}

if ($user_id) {
  $where['user_id'] = $ds->prepare('= ?', $user_id);
}

if ($duration && in_array($duration_operand, array('<', '<=', '=', '>', '>='))) {
  $where['duration'] = $ds->prepare("$duration_operand ?", $duration);
}

$count = $log->countList($where);

$msg = '%d CLongRequestLog to be removed.';
if ($count == 1) {
  $msg = 'One CLongRequestLog to be removed.';
}
elseif (!$count) {
  $msg = 'No CLongRequestLog to be removed.';
}

CAppUI::stepAjax("CLongRequestLog-msg-$msg", UI_MSG_OK, $count);

if ($just_count || !$count) {
  CAppUI::js("\$('clean_auto').checked = false");
  CApp::rip();
}

$logs = $log->loadList($where, null, $purge_limit);

if (!$logs) {
  CAppUI::js("\$('clean_auto').checked = false");
  CAppUI::stepAjax("CLongRequestLog-msg-No CLongRequestLog to be removed.", UI_MSG_OK);
  CApp::rip();
}

$deleted_logs = 0;
foreach ($logs as $_log) {
  if ($msg = $_log->delete()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg('CLongRequestLog-msg-delete', UI_MSG_OK);
    $deleted_logs++;
  }
}
CAppUI::setMsg('CLongRequestLog-msg-%d CLongRequestLog to be removed.', UI_MSG_OK, $count - $deleted_logs);

echo CAppUI::getMsg();
CApp::rip();