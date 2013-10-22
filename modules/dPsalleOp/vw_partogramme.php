<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// @todo bloc n'est pas forcément actif
global $can;
$can->read |= CModule::getActive("dPbloc")->_can->read;
$can->needsRead();

$operation_id  = CValue::getOrSession("operation_id", null);

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefsAnesthPerops();
$operation->loadRefsFwd();
$operation->loadRefsActesCCAM();
foreach ($operation->_ref_actes_ccam as $keyActe => $valueActe) {
  $acte =& $operation->_ref_actes_ccam[$keyActe];
  $acte->loadRefsFwd();
  $acte->guessAssociation();
}  
$sejour =& $operation->_ref_sejour;
$sejour->loadRefsFwd();
$sejour->loadRefPrescriptionSejour();

list(
  $perop_graphs, $yaxes_count,
  $time_min, $time_max,
  $time_debut_op_iso, $time_fin_op_iso
) = CObservationResultSet::buildGraphs($operation, $operation->graph_pack_id);

$time_debut_op = CMbDate::toUTCTimestamp($time_debut_op_iso);
$time_fin_op   = CMbDate::toUTCTimestamp($time_fin_op_iso);

$evenements = CObservationResultSet::buildEventsGrid($operation, $time_debut_op_iso, $time_fin_op_iso, $time_min, $time_max);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("patient"      , $operation->_ref_sejour->_ref_patient);
$smarty->assign("operation"    , $operation);
$smarty->assign("perop_graphs" , $perop_graphs);
$smarty->assign("yaxes_count"  , $yaxes_count);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op"  , $time_fin_op);
$smarty->assign("evenements"   , $evenements);

$smarty->display("vw_partogramme.tpl");
