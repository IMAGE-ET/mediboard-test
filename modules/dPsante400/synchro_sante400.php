<?php 

global $m, $can, $dbChronos, $dPconfig;

set_time_limit(90);

$can->needsEdit();

CRecordSante400::$verbose = mbGetValueFromGet("verbose");

// Factory matrix
$factories = array (
  "medicap" => array (
    "sejour" => new CMouvSejourEcap,
    "intervention" => new CMouvInterventionECap,
  ),
  "tonkin" => array (
    "sejour" => new CMouvSejourTonkin,
  ),
);

if (null == $mode_compat = $dPconfig["interop"]["mode_compat"]) {
  trigger_error("Mode de compatibilité non initalisé", E_USER_ERROR);  
}

if (!array_key_exists($mode_compat, $factories)) {
  trigger_error("Mode de compatibilité '$mode_compat' non géré", E_USER_ERROR);  
}

$types = array_keys($factories[$mode_compat]);
$type = mbGetValueFromGetOrSession("type", reset($types));

if (null == $mouvFactory = @$factories[$mode_compat][$type]) {
  trigger_error("Pas de gestionnaire en mode de compatibilité '$mode_compat' et type de mouvement '$type'", E_USER_ERROR);
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
$smarty->assign("types", $types);
$smarty->assign("type", $type);
$smarty->assign("marked", $marked);
$smarty->assign("count", $count);
$smarty->assign("procs", $procs);
$smarty->assign("mouvs", $mouvs);
$smarty->display("synchro_sante400.tpl");

?>
