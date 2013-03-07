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

$chirSel   = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);
$all_prats = CValue::get("all_prats", 0);
$board     = CValue::get("board", 0);

$fin   = CValue::getOrSession("fin", CMbDT::date());
$debut = CValue::getOrSession("debut", CMbDT::date("-1 month"));

$user = new CMediusers();
$user->load($chirSel);

$plage     = new CPlageOp();
$operation = new COperation();

$where = array();
$where["date"] = "BETWEEN '$debut' AND '$fin'";

if ($all_prats) {
  if ($chir) {
    $prats = $chir->loadPraticiens();
  }
  else {
    $prats = $user->loadPraticiens();
  }
  
  $where["chir_id"]   = CSQLDataSource::prepareIn(array_keys($prats));
  $where[] = "anesth_id IS NULL OR anesth_id ".CSQLDataSource::prepareIn(array_keys($prats));
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
    
    $actes_ccam = $_operation->loadRefsActesCCAM();
    $_operation->loadExtCodesCCAM(true);
    $codes_ccam = $_operation->_ext_codes_ccam;
    
    $activites = CMbArray::pluck($codes_ccam, "activites");
    $nbCodes = 0;
    
    foreach ($activites as $_activite) {
      $nbCodes += count($_activite);
    }
    
    // Si tout est coté, on unset l'opération
    if (count($actes_ccam) == $nbCodes) {
      unset($operations[$key]);
      continue;
    }
    
    $_operation->_actes_non_cotes = $nbCodes - count($actes_ccam);
    $_operation->loadRefPlageOp(1);
    $_operation->loadRefPatient();
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
  
  $_operation->loadExtCodesCCAM(true);
  $codes_ccam = $_operation->_ext_codes_ccam;
  
  $activites = CMbArray::pluck($codes_ccam, "activites");
  $nbCodes = 0;
  
  foreach ($activites as $_activite) {
    $nbCodes += count($_activite);
  }
  
  // Si tout est coté, on unset l'opération
  if (count($actes_ccam) == $nbCodes) {
    unset($hors_plage[$key]);
    continue;
  }
  
  $_operation->_actes_non_cotes = $nbCodes - count($actes_ccam);
  $_operation->loadRefPlageOp(1);
}

$smarty = new CSmartyDP;

$smarty->assign("plages", $plages);
$smarty->assign("hors_plage", $hors_plage);
$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("all_prats", $all_prats);
$smarty->display("../../dPboard/templates/inc_list_interv_non_cotees.tpl");

?>