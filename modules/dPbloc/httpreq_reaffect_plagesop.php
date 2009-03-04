<?php

// Script à lancer entre minuit et 6h du matin
// pour que les dates limites soient respectées

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
CAppUI::setMsg("Lancement à : ".mbDateTime());
foreach($listPlages as $curr_plage) {
  //mbTrace($curr_plage->date, $curr_plage->_view." : date initiale");
  //mbTrace($curr_plage->spec_repl_id, $curr_plage->_view);
  //mbTrace($curr_plage->delay_repl, $curr_plage->_view);
  //mbTrace(mbDate("-$curr_plage->delay_repl days", $curr_plage->date)." (-".$curr_plage->delay_repl." jours)", $curr_plage->_view." : date de remplacement");
  $curr_plage->spec_id      = $curr_plage->spec_repl_id;
  $curr_plage->spec_repl_id = "";
  $curr_plage->delay_repl   = "";
  $curr_plage->chir_id   = "";
  CAppUI::setMsg($curr_plage->store());
}

echo CAppUI::getMsg();

?>