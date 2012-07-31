<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author SARL Openxtrem
*/

CCanDo::checkRead();

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id, $print;

$plageconsult_id = 0;

if(!$chir_id) {
  $chir_id = reset($listPraticiens);
}

$prat = new CMediusers;
$prat->load($chir_id);

//Planning au format  CPlanningWeek
$today = mbDate();
$debut = $date;
$debut = mbDate("-1 week", $debut);
$debut = mbDate("next monday", $debut);
$fin   = mbDate("next sunday", $debut);

// Nombre de jours
$nbDays = 5;
$plage  = new CPlageconsult();
$where  = array();
$where["chir_id"] = " = '$chir_id'";
$where["date"] = "= '$fin'";
if($plage->countList($where)) {
  $nbDays = 7;
} else {
  $where["date"] = "= '".mbDate("-1 day", $fin)."'";
  if($plage->countList($where)) {
    $nbDays = 6;
  }
}

//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, $nbDays, false, $print ? "1000" : "auto");
$planning->title    = $prat->_view;
$planning->guid     = $prat->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");

for ($i = 0; $i < $nbDays; $i++) {
  $jour = mbDate("+$i day", $debut);
  $where["date"] = "= '$jour'";
  $plages = $plage->loadList($where);
  
  CMbObject::massLoadFwdRef($plages, "chir_id");
  
  foreach($plages as $_plage){
    $_plage->loadRefsFwd(1);
    $_plage->loadRefsConsultations(false);
    foreach($_plage->_ref_consultations as $_consult) {
      $debute = "$jour $_consult->heure";
      if($_consult->patient_id) {
        $_consult->loadRefPatient();
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->_ref_patient->_view, "#fee", true, null, null);
      } else {
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, "[PAUSE]", "#ffa", true, null, null);
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      
      if($_plage->locked == 1) {
        $event->disabled = true;
      }
      
      $_consult->loadRefCategorie();
      if($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/".$_consult->_ref_categorie->nom_icone;
        $event->icon_desc = $_consult->_ref_categorie->nom_categorie;
      }
      //Ajout de l'évènement au planning 
      $event->plage["color"] = $_plage->color;
      $planning->addEvent($event);
    }
    $utilisation = $_plage->getUtilisation();
    foreach($utilisation as $_timing => $_nb) {
      if(!$_nb) {
        $debute = "$jour $_timing";
        $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", "#cfc", true, null, null);
        $event->type        = "rdvfree";
        $event->plage["id"] = $_plage->_id;
        if($_plage->locked == 1) {
          $event->disabled = true;
        }
        $event->plage["color"] = $_plage->color;
        //Ajout de l'évènement au planning 
        $planning->addEvent($event);
      }
    }
  }    
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("period"         , $period);
$smarty->assign("periods"        , $periods);
$smarty->assign("date"           , $date);
$smarty->assign("refDate"        , $debut);
$smarty->assign("ndate"          , $ndate);
$smarty->assign("pdate"          , $pdate);
$smarty->assign("listPraticiens" , $listPraticiens);
$smarty->assign("chir_id"        , $chir_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("plage"          , $plage);
$smarty->assign("planning"       , $planning);
$smarty->assign("bank_holidays"  , mbBankHolidays($today));
$smarty->assign("print"          , $print);

$smarty->display("inc_plage_selector_weekly.tpl");
