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

$chirSel   = CValue::getOrSession("praticien_id", null);
$all_prats = CValue::get("all_prats", 0);
$board     = CValue::get("board", 0);

$fin   = CValue::getOrSession("fin", mbDate());
$debut = CValue::getOrSession("debut", mbDate("-1 week", $fin));

$mediuser = CMediusers::get();

if ($mediuser->isPraticien()) {
  $chirSel = $mediuser->_id;
}

$user = new CMediusers();
$user->load($chirSel);

$operation = new COperation();

$ljoin = array();
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where = array();
$where[] = "operations.date BETWEEN '$debut' AND '$fin' OR plagesop.date BETWEEN '$debut' AND '$fin'";
$where["operations.annulee"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens();
  
  $where["operations.chir_id"]   = CSQLDataSource::prepareIn(array_keys($prats));
  $where[] = "operations.anesth_id IS NULL OR operations.anesth_id ".CSQLDataSource::prepareIn(array_keys($prats));
}
else {
  if ($user->isAnesth()) {
    $where[] = "operations.anesth_id = '$user->_id' OR (operations.anesth_id IS NULL && plagesop.anesth_id = '$user->_id')";
  }
  else {
    $where["operations.chir_id"] = "= '$user->_id'";
  }
}
$interventions = $operation->loadList($where, null, null, null, $ljoin);

foreach ($interventions as $key => $_interv) {
  $_plage = $_interv->loadRefPlageOp();
  
  $_interv->loadExtCodesCCAM(true);
  $codes_ccam = $_interv->_ext_codes_ccam;
  
  $where = array();
  if($all_prats) {
    $_interv->countActes();
  } else {
    $_interv->countActes($user->_id);
    $where["executant_id"] = "= '$user->_id'";
  }
  
  $nb_actes_ccam = $_interv->countBackRefs("actes_ccam", $where);
  
  // Aucun acte prévu ou coté
  if(!count($codes_ccam) && !$_interv->_count_actes) {
    $_interv->loadRefSejour();
    $_interv->loadRefPlageOp();
    $_interv->loadRefChir()->loadRefFunction();
    $_interv->loadRefAnesth()->loadRefFunction();
    $_interv->loadRefPatient();
    continue;
  }
  
  // Actes prévus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");
  $nbCodes = 0;
  
  foreach ($activites as $_activite) {
    if($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach($_activite as $_type) {
      if($user->_is_anesth && $_type->numero == 4) {
        $nbCodes++;
      }
      elseif(!$user->_is_anesth && $_type->numero != 4) {
        $nbCodes++;
      }
    }
  }
    
  // Si tout est coté, on n'affiche pas l'opération
  if ($nb_actes_ccam == $nbCodes) {
    unset($interventions[$key]);
    continue;
  }
  
  $where = array();
  if(!$all_prats) {
    $where["executant_id"] = "= '$user->_id'";
  }
  $_interv->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
  $_interv->loadRefSejour();
  $_interv->loadRefPlageOp();
  $_interv->loadRefChir()->loadRefFunction();
  $_interv->loadRefAnesth()->loadRefFunction();
  $_interv->loadRefPatient();
}

$interventions = CMbObject::naturalSort($interventions, array("_datetime"));

$smarty = new CSmartyDP;

$smarty->assign("interventions", $interventions);
$smarty->assign("debut"        , $debut);
$smarty->assign("fin"          , $fin);
$smarty->assign("all_prats"    , $all_prats);
$smarty->assign("board"        , $board);
$smarty->display("../../dPboard/templates/inc_list_interv_non_cotees.tpl");

?>