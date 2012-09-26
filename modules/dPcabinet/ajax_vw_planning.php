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
$today = mbDate();

// Liste des consultations a avancer si desistement
$where = array(
  "plageconsult.date" => " > '$today'",
  "plageconsult.chir_id" => "= '$chirSel'",
  "consultation.si_desistement" => "= '1'",
);
$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);

$consultation_desist = new CConsultation();
$count_si_desistement = $consultation_desist->countList($where, null, $ljoin);

// Période
$debut = CValue::getOrSession("debut");
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prev = mbDate("-1 week", $debut);
$next = mbDate("+1 week", $debut);

$dateArr = mbDate("+6 day", $debut);
$nbDays = 7;
$listPlage = new CPlageconsult();

$where = array();
$where["date"] = "= '$dateArr'";
$where["chir_id"] = " = '$chirSel'";

if (!$listPlage->countList($where)) {
  $nbDays--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = mbDate("+5 day", $debut);
  $where["date"] = "= '$dateArr'"; 
  if (!$listPlage->countList($where)) {
    $nbDays--;
  }
}



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

for ($i = 0; $i < $nbDays; $i++) {
  $jour = mbDate("+$i day", $debut);
  $where["date"] = "= '$jour'";
  $plages = $plage->loadList($where);
  
  CMbObject::massLoadFwdRef($plages, "chir_id");
  
  foreach($plages as $_plage){
    $_plage->loadRefsFwd(1);
    $_plage->loadRefsConsultations(false);
    
    // Affichage de la plage sur le planning
    
    $range = new CPlanningRange($_plage->_guid, $jour." ".$_plage->debut, mbMinutesRelative($_plage->debut, $_plage->fin), $_plage->libelle, $_plage->color);
    
    $range->type = "plageconsult";
    $planning->addRange($range);
    
    foreach($_plage->_ref_consultations as $_consult) {
      $debute = "$jour $_consult->heure";
      if($_consult->patient_id) {
        $_consult->loadRefPatient();
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->_ref_patient->_view, "#fee", true, "droppable", $_consult->_guid);
      } else {
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->motif ? $_consult->motif : "[PAUSE]", "#ffa", true, null, null);
      }
      $event->type        = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      $event->plage["consult_id"] = $_consult->_id;
      if($_plage->locked == 1) {
        $event->disabled = true;
      }
      
      $_consult->loadRefCategorie();
      if($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/".$_consult->_ref_categorie->nom_icone;
        $event->icon_desc = $_consult->_ref_categorie->nom_categorie;
      }
      
      if ($_consult->patient_id) {
        $event->draggable /*= $event->resizable */ = $can_edit;
        $event->hour_divider = 60 / mbTransformTime($_plage->freq, null, "%M");
        
        if ($can_edit) {
          $event->addMenuItem("copy", "Copier cette consultation");
          $event->addMenuItem("cut" , "Couper cette consultation");
        }
      }
      
      //Ajout de l'évènement au planning 
      $event->plage["color"] = $_plage->color;
      $planning->addEvent($event);
    }
    $utilisation = $_plage->getUtilisation();
    foreach($utilisation as $_timing => $_nb) {
      if(!$_nb) {
        $debute = "$jour $_timing";
        $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", "#cfc", true, "droppable", null);
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

$smarty = new CSmartyDP();

$smarty->assign("planning" , $planning);
$smarty->assign("debut"    , $debut);
$smarty->assign("fin"      , $fin);
$smarty->assign("prev"     , $prev);
$smarty->assign("next"     , $next);
$smarty->assign("chirSel"  , $chirSel);
$smarty->assign("today"    , $today);
$smarty->assign("count_si_desistement", $count_si_desistement);

$smarty->display("inc_vw_planning.tpl");