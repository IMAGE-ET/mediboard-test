<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 13455 $
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$chir_id = CValue::get("chir_id");

$plageop = new CPlageOp();
$plageop->load(CValue::get("plageop_id"));
$plageop->loadRefSalle();
$where = array("chir_id" => "= '$chir_id'");
$plageop->loadRefsOperations(false, null, true, null, $where);
$plageop->guessHoraireVoulu();

$rank_validated = array();
$rank_not_validated = array();

$_op = new COperation();
$_last_op = null;
foreach($plageop->_ref_operations as $_op) {
  $_op->loadRefChir()->loadRefFunction();
  $_op->loadRefSejour()->loadRefPatient()->loadRefDossierMedical()->countAllergies();
  $_op->loadExtCodesCCAM();
  
  $_last_op = $_op;
}

$horaire_voulu = $plageop->debut;

if ($_last_op) {
  $horaire_voulu = $_last_op->_horaire_voulu;
  $horaire_voulu = CMbDT::addTime($_last_op->temp_operation, $horaire_voulu);
  $horaire_voulu = CMbDT::addTime($plageop->temps_inter_op, $horaire_voulu);
  $horaire_voulu = CMbDT::addTime($_last_op->pause, $horaire_voulu);
}

$new_op = new COperation;
$new_op->_horaire_voulu = $horaire_voulu;
$plageop->_ref_operations[] = $new_op;

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("plageop", $plageop);

$smarty->display("inc_prog_plageop.tpl");
