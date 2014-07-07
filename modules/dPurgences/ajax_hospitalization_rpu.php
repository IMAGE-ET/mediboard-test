<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$rpu_id           = CValue::get("rpu_id");

$number_tolerance = CAppUI::conf("dPurgences CRPU search_visit_days_count", CGroups::loadCurrent());
$now              = CMbDT::dateTime();
$after            = CMbDT::dateTime("+ $number_tolerance DAY", $now);

$rpu = new CRPU();
$rpu->load($rpu_id);

$sejour_rpu = $rpu->loadRefSejour();
$patient    = $sejour_rpu->loadRefPatient();

$sejour_rpu->type = "comp";
$collisions = $sejour_rpu->getCollisions();


$check_merge      = "";
$sejours_futur    = array();
$count_collision  = count($collisions);
$sejour_collision = "";

if ($count_collision == 1) {
  $sejour_collision = current($collisions);
  $check_merge      = $sejour_rpu->checkMerge($collisions);
}
else if (!$count_collision) {
  $sejour = new CSejour();
  $where = array(
    "entree"      => "BETWEEN '$now' AND '$after'",
    "sejour_id"   => "!= '$sejour->_id'",
    "patient_id"  => "= '$patient->_id'",
  );
  /** @var CSejour[] $sejours_futur */
  $sejours_futur = $sejour->loadList($where, "entree DESC", null, "type");
  foreach ($sejours_futur as $_sejour_futur) {
    $_sejour_futur->loadRefPraticien()->loadRefFunction();
  }
}

$smarty = new CSmartyDP();

$smarty->assign("count_collision" , $count_collision);
$smarty->assign("rpu"             , $rpu);
$smarty->assign("sejour"          , $sejour_rpu);
$smarty->assign("sejours_futur"   , $sejours_futur);
$smarty->assign("sejour_collision", $sejour_collision);
$smarty->assign("check_merge"     , $check_merge);

$smarty->display("inc_hospitalization_rpu.tpl");