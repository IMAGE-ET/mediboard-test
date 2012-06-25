<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");

// Initialisation des variables
$plageconsult_id = CValue::get("plageconsult_id");
$display_nb_consult = CAppUI::conf("dPcabinet display_nb_consult");
$quotas = null;

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
if ($plageconsult_id) {
  $plage->load($plageconsult_id);
  $plage->loadRefsNotes();
  $date  = $plage->date;
} else {
  $date  = CValue::get("date", mbDate());
}

function utilisation_rdv($plages, $list, $plage) {
  $utilisation = array();
  
  // Granularité de 5 minutes.
  // 288 créneaux de 5 minutes dans 24 heures
  for ($i=0 ; $i < 288 ; $i++) {
    $time = mbTime(($i*5)." minutes", $plage->debut);
    $utilisation[$time] = 0;
    if ($time == $plage->fin) break;
  }
  
  foreach($plages as $_plage) {
    $rdvs = $_plage->loadRefsConsultations(false);
    $freq = mbTransformTime($_plage->freq, null, "%M");
    
    foreach ($rdvs as $_rdv) {
      $nb_cases = ($_rdv->duree * $freq) / 5 ;
      for ($i=0 ; $i < $nb_cases ; $i++) {
        
        $time = mbTime(($i*5)." minutes", $_rdv->heure);
        if (!isset($utilisation[$time])) {
          continue;
        }
        $utilisation[$time] ++;
      }
    }
  }
  
  ksort($utilisation);
  
  // Granularité à la fréquence des consultations de la plage
  $creneaux = array_flip(CMbArray::pluck($list, "time"));
  $save_key = 0;
  
  foreach ($utilisation as $key => $_util) {
    if (!isset($creneaux[$key]) && isset($utilisation[$save_key])) {
      $utilisation[$save_key] = max($_util, $utilisation[$save_key]);
      unset($utilisation[$key]);
    }
    else {
      $utilisation[$key] = $_util;
      $save_key = $key;
    }
  }
  
  return $utilisation;
}

// Chargement des places disponibles
$listPlace   = array();
$listBefore  = array();
$listAfter   = array();

if ($plageconsult_id) {
  if (!$plage->plageconsult_id) {
    $plage->load($plageconsult_id);
  }
  $plage->loadRefs(false);
  $plage->_ref_chir->loadRefFunction();
  
  for ($i = 0; $i < $plage->_total; $i++) {
    $minutes = $plage->_freq * $i;
    $listPlace[$i]["time"]          = mbTime("+ $minutes minutes", $plage->debut);
    $listPlace[$i]["consultations"] = array();
  }
  
  foreach ($plage->_ref_consultations as $keyConsult => $valConsult) {
    $consultation =& $plage->_ref_consultations[$keyConsult];
    $consultation->loadRefPatient();
    // Chargement de la categorie
    $consultation->loadRefCategorie();
    
    $keyPlace = mbTimeCountIntervals($plage->debut, $consultation->heure, $plage->freq);
  
    if($keyPlace < 0) {
      $listBefore[$keyPlace] =& $consultation;
    }
    
    if($consultation->heure >= $plage->fin) {
      $listAfter[$keyPlace] =& $consultation;
    }
    
    for  ($i = 0;  $i < $consultation->duree; $i++) {
      if (isset($listPlace[($keyPlace + $i)])) {
        $listPlace[($keyPlace + $i)]["consultations"][] =& $consultation;
      }
    }
  }
  
  // Utilisation des prises de rdv
  // Pour ceux de la même fonction
  $user = new CMediusers;
  $user->load($plage->chir_id);
  $function = $user->loadRefFunction();
  if ($function->quotas) {
    $quotas = $function->quotas;
  }
  
  if(CAppUI::pref("pratOnlyForConsult", 1)) {
    $listPrat    = $user->loadPraticiens(PERM_EDIT, $user->function_id, null, true);
    $listAllPrat = $user->loadPraticiens(null, null, null, true);
  } else {
    $listPrat    = $user->loadProfessionnelDeSante(PERM_EDIT, $user->function_id, null, true);
    $listAllPrat = $user->loadProfessionnelDeSante(null, null, null, true);
  }
  
  $where = array();
  $where["date"]    = $ds->prepare("BETWEEN %1 AND %2", "$plage->date", "$plage->date");
  $where[]          = "libelle != 'automatique' OR libelle IS NULL";
  $where["chir_id"] = " = '$user->_id'";
  
  
  if ($display_nb_consult == "cab" || $display_nb_consult == "etab") {
    $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat));
    $plages_func      = $plage->loadList($where);
    $utilisation_func = utilisation_rdv($plages_func, $listPlace, $plage);
  }
  if ($display_nb_consult == "etab") {
    $where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listAllPrat));
    $plages_etab      = $plage->loadList($where);
    $utilisation_etab = utilisation_rdv($plages_etab, $listPlace, $plage);
  }
}

$user = CMediusers::get();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("listPlace"      , $listPlace);
$smarty->assign("listBefore"     , $listBefore);
$smarty->assign("listAfter"      , $listAfter);
$smarty->assign("quotas"         , $quotas);
$smarty->assign("user"           , $user);

if ($display_nb_consult == "cab" || $display_nb_consult == "etab") {
  $smarty->assign("utilisation_func", $utilisation_func);
}
if ($display_nb_consult == "etab") {
  $smarty->assign("utilisation_etab", $utilisation_etab);
}

$smarty->assign("online"         , true);

$smarty->display("inc_list_places.tpl");

?>