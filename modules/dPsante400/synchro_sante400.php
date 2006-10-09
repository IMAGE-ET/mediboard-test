<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

CMouvSejourTonkin::$verbose = mbGetValueFromGet("verbose");

$imports = 3;
$count = CMouvSejourTonkin::count();
$procs = 0;

if ($rec = mbGetValueFromGet("rec")) {
  $mouv = new CMouvSejourTonkin;
  $mouv->load($rec);
  $mouvs = array($mouv);
} else {
  $mouvs = CMouvSejourTonkin::multipleLoad($imports);
}

foreach ($mouvs as $mouv) {
  if ($mouv->proceed()) {
    $procs++;
  }
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>
