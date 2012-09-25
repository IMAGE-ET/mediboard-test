<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Sébastien Fillonneau
*/

CCanDo::checkAdmin();

$charge_id = CValue::get("charge_id");

$charge = new CChargePriceIndicator;
$charge->load($charge_id);
$charge->loadRefsNotes();
if (!$charge->_id) {
  $charge->group_id = CGroups::loadCurrent()->_id;
}

$smarty = new CSmartyDP();
$smarty->assign("charge", $charge);
$smarty->display("inc_edit_charge_price_indicator.tpl");
