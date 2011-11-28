<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 13455 $
* @author Romain OLLIVIER
*/

CCanDo::checkRead();

$plageop = new CPlageOp();
$plageop->load(CValue::get("plageop_id"));
$plageop->loadRefsOperations(0);
$plageop->loadRefSalle();

$_op = new COperation();
foreach($plageop->_ref_operations as $_op) {
  $_op->loadRefChir();
  $_op->_ref_chir->loadRefFunction();
  $_op->loadRefSejour();
  $_op->_ref_sejour->loadRefPatient();
  $_op->loadExtCodesCCAM();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("plageop", $plageop);

$smarty->display("inc_prog_plageop.tpl");

?>