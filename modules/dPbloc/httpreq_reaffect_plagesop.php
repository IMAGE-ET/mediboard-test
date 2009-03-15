<?php

// Script à lancer entre minuit et 6h du matin
// pour que les dates limites soient respectées

$mode_real = mbGetValueFromGet("mode_real", 1);

$plageop = new CPlageOp();
$where = array();
$where["plagesop.spec_repl_id"] = "IS NOT NULL";
$where["plagesop.delay_repl"]   = "IS NOT NULL";
$where[]               = "`plagesop`.`date` < DATE_ADD('".mbDate()."', INTERVAL `plagesop`.`delay_repl` DAY)";
$where[]               = "`plagesop`.`date` >= '".mbDate()."'";
$where["operations.operation_id"] = "IS NULL";
$order = "`plagesop`.`date`, `plagesop`.`debut`";
$limit = null;
$group = null;
$ljoin = array();
$ljoin["operations"] = "operations.plageop_id = plagesop.plageop_id";
$listPlages = $plageop->loadList($where, $order, $limit, $group, $ljoin);
if($mode_real) {
  CAppUI::setMsg("Lancement à : ".mbDateTime()." en mode réel");
} else {
  CAppUI::setMsg("Lancement à : ".mbDateTime()." en mode test");
}
foreach($listPlages as $curr_plage) {
  //mbTrace($curr_plage->date, $curr_plage->_view." : date initiale");
  //mbTrace($curr_plage->spec_repl_id, $curr_plage->_view);
  //mbTrace($curr_plage->delay_repl, $curr_plage->_view);
  //mbTrace(mbDate("-$curr_plage->delay_repl days", $curr_plage->date)." (-".$curr_plage->delay_repl." jours)", $curr_plage->_view." : date de remplacement");
  if($mode_real) {
    $curr_plage->spec_id      = $curr_plage->spec_repl_id;
    $curr_plage->spec_repl_id = "";
    $curr_plage->delay_repl   = "";
    $curr_plage->chir_id   = "";
    if($msg = $curr_plage->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    } else {
      CAppUI::setMsg("Plage $curr_plage->_id mise à jour", UI_MSG_OK);
    }
  } else {
    $curr_plage->loadRefsFwd(1);
    $curr_plage->loadRefSpecRepl(1);
    $msg = "plage du $curr_plage->date de $curr_plage->debut à $curr_plage->fin : Dr ".$curr_plage->_ref_chir->_view." vers ".$curr_plage->_ref_spec_repl->_view;
    CAppUI::setMsg($msg, UI_MSG_OK);
  }
}

echo CAppUI::getMsg();

?>