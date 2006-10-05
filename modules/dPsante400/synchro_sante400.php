<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

foreach (CMouvSejourTonkin::multipleLoad() as $mouv) {
  $mouv->proceed();
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("synchro_sante400.tpl");

?>
