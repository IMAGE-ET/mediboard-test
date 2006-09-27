<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

//$mouv = new CMouvSejourTonkin();
//$mouv->load();
//$mouv->proceed();

$patients = array();
for ($i = 0; $i < 10000; $i++) {
  $patients[] = new COperation;
}

?>
