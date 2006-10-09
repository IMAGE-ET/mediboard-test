<?php 

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

CMouvSejourTonkin::$verbose = mbGetValueFromGet("verbose");

$marked = mbGetValueFromGetOrSession("marked");
$max = mbGetValueFromGet("max", 10);

$count = CMouvSejourTonkin::count($marked);
$procs = 0;

if ($rec = mbGetValueFromGet("rec")) {
  $mouv = new CMouvSejourTonkin;
  $mouv->load($rec);
  $mouvs = array($mouv);
} else {
  $mouvs = CMouvSejourTonkin::multipleLoad($marked, $max);
}

foreach ($mouvs as $mouv) {
  if ($mouv->proceed()) {
    $procs++;
  }
}

// Création du template
$smarty = new CSmartyDP(1);
$smarty->assign("connection", CRecordSante400::$dbh);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>
