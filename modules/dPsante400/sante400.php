<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

$mouv = new CMouvSejourTonkin();
$mouv->load();
$mouv->proceed();

?>
