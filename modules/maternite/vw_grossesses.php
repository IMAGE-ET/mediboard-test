<?php

/**
 * maternite
 *  
 * @category maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

$date = CValue::getOrSession("date", mbDate());

$days_terme = CAppUI::conf("maternite days_terme");
$date_min = mbDate("- $days_terme days", $date);
$date_max = mbDate("+$days_terme days", $date);
$grossesse = new CGrossesse;

$where = array();
$where["terme_prevu"] = "BETWEEN '$date_min' AND '$date_max'";

$grossesses = $grossesse->loadList($where);
CMbObject::massLoadFwdRef($grossesses, "parturiente_id");

foreach ($grossesses as $_grossesse) {
  $_grossesse->loadRefParturiente();
}

$smarty = new CSmartyDP;

$smarty->assign("grossesses", $grossesses);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);
$smarty->display("vw_grossesses.tpl");

?>