<?php 

/**
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$words = CValue::getOrSession("words");
$modal = CValue::get("modal");

$smarty = new CSmartyDP();
$smarty->assign("words"      , $words);
$smarty->assign("modal"      , $modal);

$smarty->display("nomenclature_cim/vw_cim_explorer.tpl");