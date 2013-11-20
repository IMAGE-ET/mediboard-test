<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$operation_id = CValue::get("operation_id");

$interv = new COperation;
$interv->load($operation_id);
$interv->loadRefSejour()->loadRefPatient()->loadRefConstantesMedicales();
$interv->loadRefPlageOp();
$interv->_ref_sejour->_ref_patient->loadRefDossierMedical();

$consult_anesth = $interv->loadRefsConsultAnesth();

$group = CGroups::loadCurrent();

$pack = $interv->loadRefGraphPack();

list(
  $graphs, $yaxes_count,
  $time_min, $time_max,
  $time_debut_op_iso, $time_fin_op_iso
) = CObservationResultSet::buildGraphs($interv, $pack->_id);

$time_debut_op = CMbDate::toUTCTimestamp($time_debut_op_iso);
$time_fin_op   = CMbDate::toUTCTimestamp($time_fin_op_iso);

$evenements = CObservationResultSet::buildEventsGrid($interv, $time_debut_op_iso, $time_fin_op_iso, $time_min, $time_max);

$now = 100 * (CMbDate::toUTCTimestamp(CMbDT::dateTime()) - $time_min) / ($time_max - $time_min);

$graph_packs = CSupervisionGraphPack::getAllFor($group);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("pack",         $pack);
$smarty->assign("interv",       $interv);
$smarty->assign("graphs",       $graphs);
$smarty->assign("evenements",   $evenements);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op",  $time_fin_op);
$smarty->assign("yaxes_count",  $yaxes_count);
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("now",          $now);
$smarty->assign("time_debut_op_iso", $time_debut_op_iso);
$smarty->assign("time_fin_op_iso",   $time_fin_op_iso);
$smarty->assign("graph_packs",  $graph_packs);
$smarty->assign("nb_minutes", CMbDT::minutesRelative($time_debut_op_iso, $time_fin_op_iso));

$smarty->display("inc_vw_surveillance_perop.tpl");
