<?php

CCanDo::checkAdmin();

$exchange_class = CValue::get("exchange_class");
$count          = CValue::get("count", 1000);
$date_min       = CValue::get('date_min');
$date_max       = CValue::get('date_max');

if (!$date_min) {
  $date_min = CMbDT::dateTime("-3 day");
}

if (!$date_max) {
  $date_max = CMbDT::dateTime("+1 day");
}

if (!($limit = CAppUI::conf("eai max_files_to_process"))) {
  return;
}

if ($count) {
  $limit = "0, $count";
}

/** @var CExchangeDataFormat $exchange */
$exchange = new $exchange_class;

$where = array();
$where['master_idex_missing'] = "= '1'";
$where["date_production"]     = "BETWEEN '$date_min' AND '$date_max'";

$order = $exchange->_spec->key . " ASC";

/** @var CExchangeDataFormat[] $exchanges */
$exchanges = $exchange->loadList($where, $order, $limit);

foreach ($exchanges as $_exchange) {
  try {
    $_exchange->injectMasterIdexMissing();
  }
  catch (CMbException $e) {
    $e->stepAjax(UI_MSG_WARNING);
    continue;
  }

  CAppUI::stepAjax("$_exchange->_guid : CExchangeDataFormat-confirm-Data injected");
}