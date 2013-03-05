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

$date           = CValue::getOrSession("date", CMbDT::date());
$show_cancelled = CValue::getOrSession("show_cancelled", 0);

$days_terme = CAppUI::conf("maternite days_terme");
$date_min = CMbDT::date("- $days_terme days", $date);
$date_max = CMbDT::date("+$days_terme days", $date);
$grossesse = new CGrossesse();

$where = array();
$ljoin = array();
$where["terme_prevu"] = "BETWEEN '$date_min' AND '$date_max'";
$ljoin["patients"]    = "patients.patient_id = grossesse.parturiente_id";

$grossesses = $grossesse->loadList($where, "terme_prevu DESC, nom ASC", null, null, $ljoin);
CMbObject::massLoadFwdRef($grossesses, "parturiente_id");
CMbObject::massCountBackRefs($grossesses, "sejours");
CMbObject::massCountBackRefs($grossesses, "consultations");

foreach ($grossesses as $key => $_grossesse) {
  $sejours = $_grossesse->loadRefsSejours();
  if (!$show_cancelled && count($sejours) == 1 && reset($sejours)->annule == 1) {
    unset($grossesses[$key]);
    continue;
  }
  $_grossesse->loadRefParturiente();
  $_grossesse->loadLastConsultAnesth();
}

$smarty = new CSmartyDP();

$smarty->assign("grossesses", $grossesses);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);
$smarty->assign("show_cancelled", $show_cancelled);

$smarty->display("vw_grossesses.tpl");
