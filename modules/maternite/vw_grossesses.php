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
$ljoin = array();
$where["terme_prevu"] = "BETWEEN '$date_min' AND '$date_max'";
$ljoin["patients"]    = "patients.patient_id = grossesse.parturiente_id";

$grossesses = $grossesse->loadList($where, "terme_prevu DESC, nom ASC", null, null, $ljoin);
CMbObject::massLoadFwdRef($grossesses, "parturiente_id");

foreach ($grossesses as $_grossesse) {
  $_grossesse->loadRefParturiente();
  $_grossesse->loadRefsSejours();
  $_grossesse->loadLastConsultAnesth();
  mbTrace($_grossesse->_allaitement_en_cours);
}

$smarty = new CSmartyDP;

$smarty->assign("grossesses", $grossesses);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);
$smarty->display("vw_grossesses.tpl");

?>