<?php 

global $AppUI, $m, $dbChronos, $dPconfig;

require_once($AppUI->getModuleClass("dPsante400", "mouvsejourtonkin"));

set_time_limit(90);

switch ($mode_compat = @$dPconfig["interop"]["mode_compat"]) {
  case "medicap" : 
  $mouvFactory = new CMouvSejourEcap;
  break;

  case "tonkin" : 
  $mouvFactory = new CMouvSejourTonkin;
  break;
  
  default: 
  trigger_error($mode_compat ? "Mode de compatibilité '$mode_compat' inconnu" : "Mode de compatibilité non initalisé", E_USER_ERROR);
  die();
}

$marked = mbGetValueFromGetOrSession("marked");
$max = mbGetValueFromGet("max", 5);

$count = $mouvFactory->count($marked);
$procs = 0;

$mouvs = array();
if ($rec = mbGetValueFromGet("rec")) {
  try {
    $mouv = $mouvFactory;
    $mouv->load($rec);
    $mouvs = array($mouv);
  } catch (Exception $e) {
    trigger_error("Mouvement with id '$rec'has been deleted : " . $e->getMessage(), E_USER_ERROR);
  }
} else {
  $mouvs = $mouvFactory->multipleLoad($marked, $max);
}

foreach ($mouvs as $mouv) {
  $mouv->verbose = mbGetValueFromGet("verbose");
  if ($mouv->proceed()) {
    $procs++;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("connection", CRecordSante400::$dbh);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>
