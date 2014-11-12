<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$date = CMbDT::date("-1 week");

$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->display("vw_search_log.tpl");