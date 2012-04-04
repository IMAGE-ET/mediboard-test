<?php

/**
 * dPboard
 *  
 * @category dPboard
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$chir = null;
$mediuser = CMediusers::get();

if ($mediuser->isPraticien()) {
  $chir = $mediuser;
}

$chirSel      = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);
$all_prats    = CValue::getOrSession("all_prats", 0);

if (!$all_prats) {
  CAppUI::requireModuleFile("dPboard", "inc_board");
}

$user = new CMediusers();
$user->load($chirSel);

$fin = CValue::getOrSession("fin", mbDate());
$debut = CValue::getOrSession("debut", mbDate("-1 month"));

$plage     = new CPlageOp;
$operation = new COperation;

$where = array();
$where["date"] = "BETWEEN '$debut' AND '$fin'";

if ($all_prats) {
  if ($chir) {
    $prats = $chir->loadPraticiens();
  }
  else {
    $prats = $user->loadPraticiens();
  }
  
  $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($prats));
  $where["anesth_id"] = CSQLDataSource::prepareIn(array_keys($prats));
}
else {
  if ($user->isAnesth()) {
    $where["anesth_id"] = "= '$user->_id'";
  }
  else {
    $where["chir_id"] = "= '$user->_id'";
  }
}
$plages = $plage->loadList($where, "date");

foreach ($plages as $_plage_id => $_plage) {
  $_plage->loadRefChir()->loadRefFunction();
  $_plage->loadRefsOperations();
  $operations =& $_plage->_ref_operations;
  
  foreach ($operations as $key => $_operation) {
    if ($_operation->annulee) {
      unset($operations[$key]);
      continue;
    }
    
    $actes_ccam = &$_operation->loadRefsActesCCAM();
    if (count($actes_ccam) || !$_operation->codes_ccam) {
      unset($operations[$key]);
      continue;
    }
    
    $_operation->loadRefPlageOp(1);
  }
  
  if (count($operations) == 0) {
    unset($plages[$_plage_id]);
  }
}

// Inclusion des hors plages
$where["plageop_id"] = "IS NULL";
$where["annulee"]    = " = '0'";

$hors_plage = $operation->loadList($where);

foreach ($hors_plage as $key => $_operation) {
  $actes_ccam = $_operation->loadRefsActesCCAM();
  
  if (count($actes_ccam) || !$_operation->codes_ccam) {
    unset($hors_plage[$key]);
    continue;
  }
  $_operation->loadRefPlageOp(1);
}

$smarty = new CSmartyDP;

$smarty->assign("plages", $plages);
$smarty->assign("hors_plage", $hors_plage);
$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("all_prats", $all_prats);
$smarty->display("../../dPboard/templates/inc_vw_interv_non_cotees.tpl");

?>