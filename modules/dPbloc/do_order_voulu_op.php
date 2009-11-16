<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m;

$plageop_id = CValue::post("plageop_id");

$plageop = new CPlageOp();
$plageop->load($plageop_id);

// Suppression du placement initial
$operation = new COperation();
$where = array();
$where["plageop_id"] = "= '$plageop->_id'";
$where["rank"] = "!= '0'";
$listOp = $operation->loadList($where);
foreach($listOp as &$_op) {
  $_op->rank = 0;
  $_op->store();
}

// Récupération des interventions à placer
$where = array();
$where["plageop_id"] = "= '$plageop->_id'";
$where["annulee"]    = "= '0'";
$where["rank"]       = "= '0'";
$where["horaire_voulu"] = "IS NOT NULL";
$order = "horaire_voulu";
$listOp = $operation->loadList($where, $order);

// Modification de la plage pour la première intervention
$firstOp = reset($listOp);
$plageop->debut = $firstOp->horaire_voulu;
$oldOp = new COperation();
$rank = 1;
$horaire = $plageop->debut;
$HourForNext = $horaire;
foreach($listOp as $_op) {
  $_op->rank = $rank++;
  if($oldOp->_id) {
    $timeToNext   = mbAddTime($plageop->temps_inter_op, $oldOp->temp_operation);
    $HourForNext  = mbAddTime($timeToNext, $horaire);
    if($HourForNext < $_op->horaire_voulu) {
      $oldOp->pause = mbSubTime($HourForNext, $_op->horaire_voulu);
      $oldOp->store();
    } else {
      $oldOp->pause = "00:00:00";
      $oldOp->store();
    }
  }
  $horaire = max($HourForNext, $_op->horaire_voulu);
  $_op->store();
  $oldOp = $_op;
}
if(!$plageop->chir_id) $plageop->chir_id = "";
if(!$plageop->spec_id) $plageop->spec_id = "";
$plageop->updateFormFields();
$plageop->store();
$plageop->reorderOp();

CAppUI::redirect("m=$m");
?>