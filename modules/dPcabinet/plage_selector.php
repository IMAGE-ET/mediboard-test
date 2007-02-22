<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Initialisation des variables
$plageconsult_id = mbGetValueFromGet("plageconsult_id");

$period = mbGetValueFromGetOrSession("period", $AppUI->user_prefs["DefaultPeriod"]);
$periods = array("day", "week", "month");
$nextPeriod = CMbArray::cycleValue($periods, $period);

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
$listPlace = array();
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $date  = $plage->date;
} else {
  $date  = mbGetValueFromGet("date", mbDate());
}

$ndate = mbDate("+1 $period", $date);
$pdate = mbDate("-1 $period", $date);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();

// Praticiens sélectionnés
$chir_id = mbGetValueFromGet("chir_id");
$listPrat = new CMediusers;
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$where["chir_id"] = db_prepare_in(array_keys($listPrat), $chir_id);

// Choix du mois
$month = mbTranformTime(null, $date, "%Y-%m-__");

switch ($period) {
  case "day":
    $minDate = mbDate(null, $date);
    $maxDate = mbDate(null, $date);
    break;

  case "week":
    $minDate = mbDate("last sunday", $date);
    $maxDate = mbDate("next saturday", $date);
    break;

  case "month":
    $minDate = mbTranformTime(null, $date, "%Y-%m-01");
    $maxDate = mbTranformTime("+1 month", $date, "%Y-%m-00");
    break;

	default:
    trigger_error("Période '$period' inconnue");
		break;
}

$where["date"] = db_prepare("BETWEEN %1 AND %2", $minDate, $maxDate);
$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);
foreach ($listPlage as $keyPlage => &$currPlage) {
  if (!$plageconsult_id && $date == $currPlage->date) {
    $plageconsult_id = $currPlage->plageconsult_id;
  }

  $currPlage->_ref_chir =& $listPrat[$currPlage->chir_id];
  $currPlage->loadFillRate();
}

if($plageconsult_id) {
  if(!$plage->plageconsult_id) {
    $plage->load($plageconsult_id);
  }
  $plage->loadRefs(false);
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $listPlace[$i]["time"] = mbTime("+ $minutes minutes", $plage->debut);
    $listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient();
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($listPlace[($keyPlace + $i)])) {
        $listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
    }
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("nextPeriod"     , $nextPeriod);
$smarty->assign("date"           , $date);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlage"      , $listPlage);
$smarty->assign("listPlace"      , $listPlace);

$smarty->display("plage_selector.tpl");

?>