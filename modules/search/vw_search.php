<?php 

/**
 * $Id$
 *  
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  OXOL, see http://www.mediboard.org/public/OXOL
 * @link     http://www.mediboard.org */

CCanDo::checkRead();

$date = CMbDT::date("-1 month");

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->display("vw_search.tpl");

