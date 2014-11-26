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
$search_item= new CSearchItem();
$search_item->rss_id = $rss_id;
$search_items = $search_item->loadMatchingList();

// Faire une recherche sur search pour afficher les documents et les remarques associées
$smarty = new CSmartyDP();

$smarty->assign("search_items", $search_items);
$smarty->assign("search_item", $search_item);
$smarty->assign("rss", $rss);

$smarty->display("inc_vw_search_items.tpl");