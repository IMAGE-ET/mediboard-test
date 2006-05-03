<?php /* $Id: plage_selector.php,v 1.17 2006/04/21 16:56:07 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 1.17 $
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
$chir = dPgetParam( $_GET, 'chir', 0);
if(!$chir) {
  $listChir = new CMediusers;
  $listChir = $listChir->loadPraticiens(PERM_EDIT);
  $inChir = "(0";
  foreach($listChir as $key => $value) {
    $inChir .= ", '$value->user_id'";
  }
  $inChir .=")";
}
$plageSel = dPgetParam( $_GET, 'plagesel', NULL);
$date = dPgetParam( $_GET, 'date', mbDate() );
$ndate = mbDate("+1 MONTH", $date);
$pdate = mbDate("-1 MONTH", $date);

// Récupération des plages de consultation disponibles
$listPlage = new CPlageconsult;
$where = array();
if($chir) {
  $where["chir_id"] = "= '$chir'";
} else {
  $where["chir_id"] = "IN $inChir";
}
$where["date"] = "LIKE '".mbTranformTime(null, $date, "%Y-%m-__")."'";
$order = "date, debut";
$listPlage = $listPlage->loadList($where, $order);
foreach($listPlage as $key => $value) {
  if(!$plageSel && $date == $value->date) {
    $plageSel = $value->plageconsult_id;
  }
  $listPlage[$key]->loadRefs(false);
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
$smarty->assign('chir', $chir);
$smarty->assign('plageSel', $plageSel);
$smarty->assign('plage', $plage);
$smarty->assign('listPlage', $listPlage);
$smarty->assign('listPlace', $listPlace);

$smarty->display('plage_selector.tpl');

?>