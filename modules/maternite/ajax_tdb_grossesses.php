<?php

/**
 * Liste des grossesses en cours du tableau de bord
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date  = CValue::get("date", CMbDT::date());

$date_min = CMbDT::date("-7 days", $date);
$date_max = CMbDT::date("+21 days", $date);

$where = array();
$ljoin = array();
$where["terme_prevu"] = "BETWEEN '$date_min' AND '$date_max'";
$ljoin["patients"]    = "patients.patient_id = grossesse.parturiente_id";

$grossesse = new CGrossesse();
$grossesses = $grossesse->loadGroupList($where, "terme_prevu ASC, nom ASC", null, null, $ljoin);

/** @var CStoredObject[] $grossesses */
CMbObject::massLoadFwdRef($grossesses, "parturiente_id");
CMbObject::massCountBackRefs($grossesses, "sejours");
CMbObject::massCountBackRefs($grossesses, "consultations");

/** @var CGrossesse[] $grossesses */
foreach ($grossesses as $_grossesse) {
  $_grossesse->loadRefParturiente();
  $_grossesse->loadLastConsultAnesth();
  $_grossesse->_ref_last_consult_anesth->loadRefPlageConsult();
}

$smarty = new CSmartyDP();

$smarty->assign("grossesses", $grossesses);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);

$smarty->display("inc_tdb_grossesses.tpl");