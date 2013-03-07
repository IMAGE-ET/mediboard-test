<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$evenement_guid = CValue::get("evenement_guid");
$operation_id   = CValue::get("operation_id");
$datetime       = CValue::get("datetime");

$interv = new COperation;
$interv->load($operation_id);

if (!$datetime) {
  $datetime = $interv->loadRefPlageOp()->date." ".CMbDT::time();
}

list($evenement_class, $evenement_id) = explode("-", $evenement_guid);

$evenement = new $evenement_class;

if ($evenement_id) {
  $evenement->load($evenement_id);
}

$evenement->datetime = $datetime;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("evenement", $evenement);
$smarty->assign("operation_id", $operation_id);
$smarty->assign("datetime", $datetime);
$smarty->display("inc_edit_evenement_perop.tpl");
