<?php

// Script à lancer entre minuit et 6h du matin
// pour que les dates limites soient respectées

global $AppUI;

$mode_real = mbGetValueFromGet("mode_real", 1);

$plageop = new CPlageOp();
$where = array();
$where["plagesop.spec_repl_id"] = "IS NOT NULL";
$where["plagesop.delay_repl"]   = "IS NOT NULL";
$where[] = "`plagesop`.`date` < DATE_ADD('".mbDate()."', INTERVAL `plagesop`.`delay_repl` DAY)";
$where[] = "`plagesop`.`date` >= '".mbDate()."'";
$where["operations.operation_id"] = "IS NULL";
$order = "`plagesop`.`date`, `plagesop`.`debut`";
$limit = null;
$group = null;
$ljoin = array();
$ljoin["operations"] = "operations.plageop_id = plagesop.plageop_id AND operations.annulee = '0'";
$listPlages = $plageop->loadList($where, $order, $limit, $group, $ljoin);
if($mode_real) {
  $AppUI->getMsg();setMsg("Lancement à : ".mbDateTime()." en mode réel");
} else {
  $AppUI->setMsg("Lancement à : ".mbDateTime()." en mode test");
}
foreach($listPlages as $curr_plage) {
  if($mode_real) {
    // Suppression des interventions annulées de cette plage pour les mettre en hors plannifié
    $curr_plage->loadRefsBack();
    foreach($curr_plage->_ref_operations as $curr_op) {
      $curr_op->plageop_id = "";
      $curr_op->date       = $curr_plage->date;
      $curr_op->store();
    }
    // Réaffectation de la plage
    $curr_plage->spec_id      = $curr_plage->spec_repl_id;
    $curr_plage->chir_id   = "";
    if($msg = $curr_plage->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR);
    } else {
      $AppUI->setMsg("Plage $curr_plage->_id mise à jour", UI_MSG_OK);
    }
  } else {
    $curr_plage->loadRefsFwd(1);
    $curr_plage->loadRefSpecRepl(1);
    $msg = "plage du $curr_plage->date de $curr_plage->debut à $curr_plage->fin : Dr ".$curr_plage->_ref_chir->_view." vers ".$curr_plage->_ref_spec_repl->_view;
    $AppUI->setMsg($msg, UI_MSG_OK);
  }
}

echo $AppUI->getMsg();

?>