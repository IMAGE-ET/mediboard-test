<?php 

/**
 * $Id$
 *  
 * @category atih
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkEdit();

$rss_id = CValue::getOrSession("rss_id");
$rss = new CRSS();
$rss = $rss->load($rss_id);
$search_items = $rss->loadRefsSearchItems();
foreach ($search_items as $_search_item) {
  /** @var CSearchItem  $_search_item*/
  $_search_item->loadRefMediuser()->loadRefFunction();
}

// Faire une recherche sur search pour afficher les documents et les remarques associées
$smarty = new CSmartyDP();

$smarty->assign("search_items", $search_items);
$smarty->assign("rss", $rss);

$smarty->display("inc_vw_search_items.tpl");