<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

$mouv = new CMouvSejourTonkin();
$mouv->load();
$mouv->proceed();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("synchro_sante400.tpl");

?>
