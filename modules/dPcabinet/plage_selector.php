<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// Initialisation des variables
$plageSel = dPgetParam( $_GET, 'plagesel');
$date = dPgetParam( $_GET, 'date', mbDate() );
$ndate = mbDate("+1 MONTH", $date);
$pdate = mbDate("-1 MONTH", $date);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();

// Praticien sélectionnés
$chir_id = dPgetParam( $_GET, 'chir_id');
if (!$chir_id) {
  $listChir = new CMediusers;
  $listChir = $listChir->loadPraticiens(PERM_EDIT);
  $inChir = join(array_keys($listChir), ", ");
}

$where["chir_id"] = $chir_id ? "= '$chir_id'" : "IN ($inChir)";

// Choix du mois
$month = mbTranformTime(null, $date, "%Y-%m-__");
$where["date"] = "LIKE '$month'";
$order = "date, debut";

// Chargement des plages disponibles
$listPlage = $listPlage->loadList($where, $order);
foreach ($listPlage as $keyPlage => $valuePlage) {
  if (!$plageSel && $date == $valuePlage->date) {
    $plageSel = $valuePlage->plageconsult_id;
  }
  
  $listPlage[$keyPlage]->loadRefs(false);
}

// Récupération des consultations de la plage séléctionnée
$plage = new CPlageconsult;
$plage->_ref_chir = new CMediusers;
$listPlace = array();
if($plageSel) {
  $plage->load($plageSel);
  $plage->loadRefs(false);
  $date = $plage->date;
  $currMin = intval($plage->_min_deb);
  $currHour = intval($plage->_hour_deb);
  for($i = 0; $i < $plage->_total; $i++) {
    $listPlace[$i]["patient"] = array();
  }
  for($i = 0; $i < $plage->_total; $i++) {
    $listPlace[$i]["hour"] = $currHour;
    if($currMin != 0)
      $listPlace[$i]["min"] = $currMin;
    else
      $listPlace[$i]["min"] = "00";
    $qte = 0;
    $nextHour = $currHour;
    $nextMin = $currMin + intval($plage->_freq);
    if($nextMin >= 60) {
      $nextHour = $currHour + 1;
      $nextMin -= 60;
    }
    if(count($plage->_ref_consultations)) {
      foreach($plage->_ref_consultations as $key => $value) {
        if($currHour == $nextHour) {
          $rightPlace = (intval($value->_hour) == $currHour) && (intval($value->_min) >= $currMin) && (intval($value->_min) < $nextMin);
        } else {
          if(intval($value->_hour) == $currHour)
            $rightPlace = (intval($value->_min) >= $currMin);
          else
            $rightPlace = (intval($value->_min) < $nextMin);
        }
        if($rightPlace) {
          $tmp = array();
          $tmp["premiere"] = $plage->_ref_consultations[$key]->premiere;
          $tmp["duree"] = $plage->_ref_consultations[$key]->duree;
          $tmp["motif"] = $plage->_ref_consultations[$key]->motif;
          $tmpduree = $tmp["duree"];
          $plage->_ref_consultations[$key]->loadRefs();
          $tmp["patient"] = $plage->_ref_consultations[$key]->_ref_patient->_view;
          while($tmpduree--) {
            $listPlace[($i+$tmpduree)]["patient"][] = $tmp;
          }
        }
      }
      $currMin = $nextMin;
      $currHour = $nextHour;
    } else {
      $currMin = $nextMin;
      $currHour = $nextHour;
    }
  }
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('date', $date);
$smarty->assign('ndate', $ndate);
$smarty->assign('pdate', $pdate);
$smarty->assign('chir_id', $chir_id);
$smarty->assign('plageSel', $plageSel);
$smarty->assign('plage', $plage);
$smarty->assign('listPlage', $listPlage);
$smarty->assign('listPlace', $listPlace);

$smarty->display('plage_selector.tpl');

?>