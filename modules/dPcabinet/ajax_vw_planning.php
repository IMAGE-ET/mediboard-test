<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$chirSel = CValue::getOrSession("chirSel");
$today = CMbDT::date();

// Liste des consultations a avancer si desistement
$where = array(
  "plageconsult.date" => " > '$today'",
  "consultation.si_desistement" => "= '1'",
);
$where[] = "plageconsult.chir_id = '$chirSel' OR plageconsult.remplacant_id = '$chirSel'";
$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);

$consultation_desist = new CConsultation();
$count_si_desistement = $consultation_desist->countList($where, null, $ljoin);

// Période
$debut = CValue::getOrSession("debut");
$debut = CMbDT::date("last sunday", $debut);
$fin   = CMbDT::date("next sunday", $debut);
$debut = CMbDT::date("+1 day", $debut);

$prev = CMbDT::date("-1 week", $debut);
$next = CMbDT::date("+1 week", $debut);

$dateArr = CMbDT::date("+6 day", $debut);
$nbDays = 7;
$listPlage = new CPlageconsult();

$whereInterv = array();
$whereHP = array();
$where = array();
$where["date"] = $whereInterv["date"] = $whereHP["date"] = "= '$dateArr'";
$whereInterv["chir_id"] = $whereHP["chir_id"] =  " = '$chirSel'";
$where[] = "chir_id = '$chirSel' OR remplacant_id = '$chirSel'";

if (!$listPlage->countList($where)) {
  $nbDays--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = CMbDT::date("+5 day", $debut);
  $where["date"] = "= '$dateArr'"; 
  if (!$listPlage->countList($where)) {
    $nbDays--;
  }
}

$bank_holidays = array_merge(CMbDate::getHolidays($debut), CMbDate::getHolidays($fin));

// Planning Week
$planning = new CPlanningWeek($debut, $debut, $fin, $nbDays, false, "auto");
$user = new CMediusers();
if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}

$can_edit = CCanDo::edit();

$planning->guid = $user->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");
$planning->dragndrop = $planning->resizable = $can_edit;
$planning->hour_divider = 60 / CAppUI::conf("dPcabinet CPlageconsult minutes_interval");

$plage = new CPlageconsult();

$whereHP["plageop_id"] = " IS NULL";

for ($i = 0; $i < $nbDays; $i++) {
  $jour = CMbDT::date("+$i day", $debut);
  $where["date"] = $whereInterv["date"] = $whereHP["date"] = "= '$jour'";

  if (CAppUI::pref("showIntervPlanning")) {
    //HORS PLAGE
    $horsPlage = new COperation();
    /** @var COperation[] $horsPlages */
    $horsPlages = $horsPlage->loadList($whereHP);
    CMbObject::massLoadFwdRef($horsPlages, "chir_id");
    foreach ($horsPlages as $_horsplage) {
      $lenght = (CMBDT::minutesRelative("00:00:00", $_horsplage->temp_operation));
      $op = new CPlanningRange($_horsplage->_guid, $jour." ".$_horsplage->time_operation, $lenght, $_horsplage,"3c75ea", "horsplage");
      $planning->addRange($op);
    }


    //INTERVENTIONS
    /** @var CPlageOp[] $intervs */
    $interv = new CPlageOp();
    $intervs = $interv->loadList($whereInterv);
    CMbObject::massLoadFwdRef($intervs, "chir_id");
    foreach ($intervs as $_interv) {
      $range = new CPlanningRange($_interv->_guid, $jour." ".$_interv->debut, CMbDT::minutesRelative($_interv->debut, $_interv->fin), CAppUI::tr($_interv->_class),"bbccee", "plageop");
      $planning->addRange($range);
    }
  }

  //PLAGES CONSULT
  /** @var CPlageConsult[] $plages */
  $plages = $plage->loadList($where, "date, debut");
  CMbObject::massLoadFwdRef($plages, "chir_id");
  foreach ($plages as $_plage) {
    $_plage->loadRefsFwd(1);
    $_plage->loadRefsConsultations(false);

    // Affichage de la plage sur le planning
    $range = new CPlanningRange($_plage->_guid, $jour." ".$_plage->debut, CMbDT::minutesRelative($_plage->debut, $_plage->fin), $_plage->libelle, $_plage->color);
    $range->type = "plageconsult";
    $planning->addRange($range);

    //colors
    $color = "#cfc";
    if ($_plage->remplacant_id && $_plage->remplacant_id != $chirSel) {
      // Je suis remplacé par un autre médecin
      $color = "#FAA";
    }
    if ($_plage->remplacant_id && $_plage->remplacant_id == $chirSel) {
      // Je remplace un autre médecin
      $color = "#FDA";
    }

    //RdvFree
    $utilisation = $_plage->getUtilisation();
    foreach ($utilisation as $_timing => $_nb) {
      if (!$_nb) {
        $debute = "$jour $_timing";
        $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", $color, true, "droppable", null);
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

    //consultations
    foreach ($_plage->_ref_consultations as $_consult) {
      $debute = "$jour $_consult->heure";
      if ($_consult->patient_id) {
        $_consult->loadRefPatient();
        if ($color = "#cfc") {
          $color = "#fee";
          if ($_consult->premiere) {
            $color = "#faa";
          }
          if ($_consult->derniere) {
            $color = "#faf";
          }
        }
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->_ref_patient->_view . "\n" . $_consult->motif, $color, true, "droppable $debute", $_consult->_guid);
      }
      else {
        if ($color = "#cfc") {
           $color = "#faa";
        }
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->motif ? $_consult->motif : "[PAUSE]", $color, true, null, null);
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      $event->plage["consult_id"] = $_consult->_id;
      if ($_plage->locked == 1) {
        $event->disabled = true;
      }
      
      $_consult->loadRefCategorie();
      if ($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/".$_consult->_ref_categorie->nom_icone;
        $event->icon_desc = CMbString::htmlEntities($_consult->_ref_categorie->nom_categorie);
      }
      
      if ($_consult->patient_id) {
        $event->draggable /*= $event->resizable */ = $can_edit;
        $event->hour_divider = 60 / CMbDT::transform($_plage->freq, null, "%M");
        
        if ($can_edit) {
          $event->addMenuItem("copy", "Copier cette consultation");
          $event->addMenuItem("cut" , "Couper cette consultation");
          $event->addMenuItem("add" , "Ajouter une consultation");
        }
      }
      
      //Ajout de l'évènement au planning 
      $event->plage["color"] = $_plage->color;
      $planning->addEvent($event);
    }
  }
}

$planning->rearrange();
$smarty = new CSmartyDP();

$smarty->assign("planning" , $planning);
$smarty->assign("debut"    , $debut);
$smarty->assign("fin"      , $fin);
$smarty->assign("prev"     , $prev);
$smarty->assign("next"     , $next);
$smarty->assign("chirSel"  , $chirSel);
$smarty->assign("today"    , $today);
$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign("count_si_desistement", $count_si_desistement);

$smarty->display("inc_vw_planning.tpl");