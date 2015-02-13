<?php

CCanDo::checkAdmin();

$exchange_class = CValue::get("exchange_class");
$count          = CValue::get("count");

$where = array();
if (!($limit = CAppUI::conf("eai max_files_to_process"))) {
  return;
}

if ($count) {
  $limit = "0, $count";
}

/** @var CExchangeDataFormat $exchange */
$exchange = new $exchange_class;

$where['master_idex_missing'] = "= '1'";

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

  CAppUI::stepAjax("CExchangeDataFormat-confirm-Data injected");
}