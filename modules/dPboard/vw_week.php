<?php

/**
 *  $Id$
 *  
 * @package    Mediboard
 * @subpackage dPboard
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CAppUI::requireModuleFile("dPboard", "inc_board");

$date = CValue::getOrSession("date", mbDate());
$prec = mbDate("-1 week", $date);
$suiv = mbDate("+1 week", $date);
$today = mbDate();

global $smarty;

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("date", $date);
$debut = mbDate("-1 week", $debut);
$debut = mbDate("next monday", $debut);
$fin   = mbDate("next sunday", $debut);

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Praticien selectionné
$chirSel = CValue::getOrSession("praticien_id", $chir ? $chir->user_id : null);

$user = new CMediusers();
//Instanciation du planning
$planning = new CPlanningWeek($debut, $debut, $fin, 5, false, null, null, true);
if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}
$planning->guid     = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");

$hours = CPlageconsult::$hours;

$plageConsult     = new CPlageconsult();
$plageOp          = new CPlageOp();
$plagesConsult    = array();
$plagesOp         = array();

$hours_stop       = CPlageconsult::$hours_stop;
$hours_start      = CPlageconsult::$hours_start;

$where            = array();
$where["chir_id"] = "= '$chirSel'";

for ($i = 0; $i < 7; $i++) {
  $date             = mbDate("+$i day", $debut);
  $where["date"]    = "= '$date'";
  $plagesConsult = $plageConsult->loadList($where);
  
  foreach ($plagesConsult as $_consult) {
    $_consult->loadFillRate();
    $_consult->countPatients();
    ajoutEvent($planning, $_consult, $date, $_consult->libelle, "#BFB", "consultation");
  }
}

$where = array();
$where[] = "chir_id = '$chirSel' OR anesth_id = '$chirSel'";

for ($i = 0; $i < 7; $i++) {
  $date             = mbDate("+$i day", $debut);
  $where["date"]    = "= '$date'";
  $plagesOp = $plageOp->loadList($where);
  
  foreach ($plagesOp as $_op) {
    $_op->loadRefSalle();
    $_op->getNbOperations();
    ajoutEvent($planning, $_op, $date,$_op->_ref_salle->nom,  "#BCE", "operation");
  }
}

function ajoutEvent(&$planning, $_plage, $date, $libelle, $color, $type){
  $debute = "$date $_plage->debut";
  $event = new CPlanningEvent($_plage->_guid, $debute, mbMinutesRelative($_plage->debut, $_plage->fin), $libelle, $color, true, null, null);
    $event->resizable = true;
  
    //Menu des évènements
    $event->addMenuItem($_plage->_guid, $date);
    $event->addMenuItem("", "");
    $event->addMenuItem($date, $type);
    $event->addMenuItem("", "");
    $event->addMenuItem("", "");
  
  //Paramètres de la plage de consultation
  $event->type = $type;
  $pct = $_plage->_fill_rate;
    if ($pct > "100") {
      $pct = "100";
    }
    if ($pct == "") {
      $pct = 0;
    }
    
  if ($type == "consultation") {
    $event->plage["id"] = $_plage->plageconsult_id; 
    $event->plage["pct"] = $pct;
    $event->plage["locked"] = $_plage->locked;
    $event->plage["_affected"] = $_plage->_affected;
    $event->plage["_nb_patients"] = $_plage->_nb_patients;
    $event->plage["_total"] = $_plage->_total;
  }
  else {
    $event->plage["id"] = $_plage->plageop_id; 
    $event->plage["pct"] = $pct;
    $event->plage["locked"] = 0;
    $event->plage["_nb_operations"] = $_plage->_nb_operations;
  }
  
  //Ajout de l'évènement au planning 
  $planning->addEvent($event);
}

// Variables de templates
$smarty->assign("date", $date);
$smarty->assign("today", $today);

$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("chirSel",  $chirSel);
$smarty->assign("planning",  $planning);

$smarty->display("vw_week.tpl");

?>