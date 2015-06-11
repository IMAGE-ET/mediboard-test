<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

global $period, $periods, $listPraticiens, $chir_id, $function_id, $date, $ndate, $pdate, $plageconsult_id, $print, $consultation_id;

$plageconsult_id = 0;

$_line_element_id = CValue::get("_line_element_id");

if (!$chir_id) {
  $chir_id = reset($listPraticiens);
}

$prat = new CMediusers;
$prat->load($chir_id);

//Planning au format  CPlanningWeek
$today         = CMbDT::date();
$debut         = $date;
$debut         = CMbDT::date("-1 week", $debut);
$debut         = CMbDT::date("next monday", $debut);
$fin           = CMbDT::date("next sunday", $debut);
$bank_holidays = array_merge(CMbDate::getHolidays($debut), CMbDate::getHolidays($fin));

// Nombre de jours
$nbDays = 5;
$plage  = new CPlageconsult();
$whereInterv = array();
$whereHP = array();
$where = array();
$whereInterv["chir_id"] = $whereHP["chir_id"] =  " = '$chir_id'";
$where[] = "chir_id = '$chir_id' OR remplacant_id = '$chir_id'";
$where["date"] = $whereInterv["date"] = $whereHP["date"] = "= '$fin'";

if ($_line_element_id) {
  $where["pour_tiers"] = "= '1'";
}

if ($plage->countList($where)) {
  $nbDays = 7;
}
else {
  $where["date"] = "= '".CMbDT::date("-1 day", $fin)."'";
  if ($plage->countList($where)) {
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

$whereHP["plageop_id"] = " IS NULL";

for ($i = 0; $i < $nbDays; $i++) {
  $jour = CMbDT::date("+$i day", $debut);
  $where["date"] = $whereInterv["date"] = $whereHP["date"] = "= '$jour'";

  if (CAppUI::pref("showIntervPlanning")) {
    // HORS PLAGE
    $horsPlage = new COperation();
    /** @var COperation[] $horsPlages */
    $horsPlages = $horsPlage->loadList($whereHP);
    CMbObject::massLoadFwdRef($horsPlages, "chir_id");
    foreach ($horsPlages as $_horsplage) {
      $lenght = (CMBDT::minutesRelative("00:00:00", $_horsplage->temp_operation));
      $op = new CPlanningRange($_horsplage->_guid, $jour." ".$_horsplage->time_operation, $lenght, $_horsplage, "3c75ea", "horsplage");
      $planning->addRange($op);
    }

    // INTERVENTIONS
    /** @var CPlageOp[] $intervs */
    $interv = new CPlageOp();
    $intervs = $interv->loadList($whereInterv);
    CMbObject::massLoadFwdRef($intervs, "chir_id");
    foreach ($intervs as $_interv) {
      $range = new CPlanningRange($_interv->_guid, $jour." ".$_interv->debut, CMbDT::minutesRelative($_interv->debut, $_interv->fin), CAppUI::tr($_interv->_class), "bbccee", "plageop");
      $planning->addRange($range);
    }
  }

  $plages = $plage->loadList($where);
  CMbObject::massLoadFwdRef($plages, "chir_id");
  
  /** @var $_plage CPlageconsult */
  foreach ($plages as $_plage) {
    $_plage->loadRefsFwd(1);
    $_plage->loadRefsConsultations(false);
    
    $range = new CPlanningRange($_plage->_guid, $jour." ".$_plage->debut, CMbDT::minutesRelative($_plage->debut, $_plage->fin));
    $range->color = $_plage->color;
    $range->type = "plageconsult";
    $planning->addRange($range);

    foreach ($_plage->_ref_consultations as $_consult) {
      $debute = "$jour $_consult->heure";
      if ($_consult->patient_id) {
        $_consult->loadRefPatient();
        $color = "#fee";
        if ($_consult->premiere) {
          $color = "#faa";
        } elseif ($_consult->derniere) {
          $color = "#faf";
        } elseif ($_consult->sejour_id) {
          $color = "#CFFFAD";
        }
        $event = new CPlanningEvent(
          $_consult->_guid,
          $debute,
          $_consult->duree * $_plage->_freq,
          $_consult->_ref_patient->_view,
          $color,
          true,
          null,
          null
        );
      }
      else {
        $color = "#faa";
        $motif = $_consult->motif ? $_consult->motif : "[PAUSE]";
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $motif, $color, true, null, null);
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      
      if ($_plage->locked == 1) {
        $event->disabled = true;
      }
      
      $_consult->loadRefCategorie();
      if ($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/".basename($_consult->_ref_categorie->nom_icone);
        $event->icon_desc = CMbString::htmlEntities($_consult->_ref_categorie->nom_categorie);
      }
      //Ajout de l'évènement au planning 
      $event->plage["color"] = $_plage->color;
      $planning->addEvent($event);
    }

    $color = "#cfc";
    if ($_plage->remplacant_id && $_plage->remplacant_id != $chir_id) {
      $color = "#FAA";
    }
    if ($_plage->remplacant_id && $_plage->remplacant_id == $chir_id) {
      $color = "#FDA";
    }
    $utilisation = $_plage->getUtilisation();
    foreach ($utilisation as $_timing => $_nb) {
      if (!$_nb) {
        $debute = "$jour $_timing";
        $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", $color, true, null, null);
        $event->type        = "rdvfree";
        $event->plage["id"] = $_plage->_id;
        if ($_plage->locked == 1) {
          $event->disabled = true;
        }
        $event->plage["color"] = $_plage->color;
        //Ajout de l'évènement au planning 
        $planning->addEvent($event);
      }
    }
  }    
}

$week = CMbDate::weekNumber($debut);

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
$smarty->assign("bank_holidays"  , $bank_holidays);
$smarty->assign("print"          , $print);
$smarty->assign("week"           , $week);
$smarty->assign("_line_element_id", $_line_element_id);

$smarty->display("inc_plage_selector_weekly.tpl");