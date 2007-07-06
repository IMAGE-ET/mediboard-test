<?php 

global $m, $can, $dbChronos, $dPconfig;

set_time_limit(90);

$can->needsEdit();

CRecordSante400::$verbose = mbGetValueFromGet("verbose");

switch ($mode_compat = $dPconfig["interop"]["mode_compat"]) {
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

$marked = mbGetValueFromGetOrSession("marked", "1");
$max = mbGetValueFromGet("max", $dPconfig["dPsante400"]["nb_rows"]);

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
  if ($mouv->proceed()) {
    $procs++;
  }
}

//mbTrace(CRecordSante400::$chrono, "Chrono");


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("connection", CRecordSante400::$dbh);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>
