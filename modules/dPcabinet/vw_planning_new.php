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

CCanDo::checkRead();

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Liste des consultations a avancer si desistement
$now = mbDate();
$where = array(
  "plageconsult.date" => " > '$now'",
  "plageconsult.chir_id" => "= '$chirSel'",
  "consultation.si_desistement" => "= '1'",
);
$ljoin = array(
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);

$consultation_desist = new CConsultation();
$count_si_desistement = $consultation_desist->countList($where, null, $ljoin);

// Période
$today = mbDate();
$debut = CValue::getOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

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

// Liste des chirurgiens
$user = new CMediusers();
$listChir = CAppUI::pref("pratOnlyForConsult", 1) ?
  $user->loadPraticiens(PERM_EDIT) :
  $user->loadProfessionnelDeSante(PERM_EDIT);

// Planning Week
$planning = new CPlanningWeek($debut, $debut, $fin, $nbDays, false, "auto");
if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}

$can_edit = CCanDo::edit();

$planning->guid = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");
$planning->dragndrop = $planning->resizable = $can_edit;
//$planning->show_half = true;
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
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, $_consult->_ref_patient->_view, "#fee", true, null, $_consult->_guid);
      } else {
        $event = new CPlanningEvent($_consult->_guid, $debute, $_consult->duree * $_plage->_freq, "[PAUSE]", "#ffa", true, null, null);
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
      
      $event->draggable /*= $event->resizable */ = $can_edit;
      
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
$smarty->assign("listChirs", $listChir);
$smarty->assign("today"    , $today);
$smarty->assign("debut"    , $debut);
$smarty->assign("fin"      , $fin);
$smarty->assign("prec"     , $prec);
$smarty->assign("suiv"     , $suiv);
$smarty->assign("chirSel"  , $chirSel);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->display("vw_planning_new.tpl");
