<?php

/**
 * $Id$
 *
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org
 */


CCanDo::checkRead();
$search_item_id = CValue::get("search_item_id");
$object_id      = CValue::get("object_id");
$class          = CValue::get("object_type");
$rmq            = CValue::get("rmq");
$rss_id         = CValue::get("rss_id");

if ($rss_id) {
  $rss            = new CRSS();
  $rss->sejour_id = $rss_id;
  $rss->loadMatchingObject();
}

$searchItem = new CSearchItem();
if ($search_item_id) {
  $searchItem->load($search_item_id);
  $searchItem->loadRefMediuser();
}
else {
  $searchItem->rss_id        = $rss->_id;
  $searchItem->search_id     = $object_id;
  $searchItem->search_class  = $class;
  $searchItem->rmq           = $rmq;
  $searchItem->_ref_mediuser = CMediusers::get();
}

$smarty = new CSmartyDP();
$smarty->assign("search_item", $searchItem);
$smarty->display("vw_search_item.tpl");