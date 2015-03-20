<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$_firstconsult_time  = null;
$_lastconsult_time   = null;
$today = CMbDT::date();
$modal = CValue::get("modal", 0);

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);
$date   = CValue::getOrSession("debut", $today);
$debut  = CMbDT::date("last sunday", $date);
$fin    = CMbDT::date("next sunday", $debut);
$debut  = CMbDT::date("+1 day", $debut);

$is_in_period = ($today >= $debut) && ($today <= $fin);

// Plage de consultation selectionnée
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if (($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = CMbDT::time();
  $where = array(
    "chir_id" => "= '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if (!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
}
else {
  $plageconsult_id = $plageSel->plageconsult_id;
}

$plageSel->loadRefsFwd(1);
$plageSel->loadRefsNotes();
$plageSel->loadRefsBack();
//check 3333tel
if (CModule::getActive("3333tel")) {
  C3333TelTools::checkPlagesConsult($plageSel, $plageSel->_ref_chir->function_id);
}

$pause = new CConsultation();
//find the unique pause;
if ($plageSel->_id) {
  $where_p = array();
  $where_p["plageconsult_id"] = " = '$plageSel->_id' ";
  $where_p["patient_id"] = " IS NULL";
  $list = $pause->loadList($where_p);
  if (count($list) == 1) {
    /** @var CConsultation $pause */
    $pause = reset($list);
    $plageSel->_pause = $pause->heure;
    $plageSel->_pause_id = $pause->_id;
    $plageSel->_pause_repeat_time = $pause->duree;
  }
}

if ($plageSel->_affected) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
}

// Détails sur les consultation affichées
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  // Cache les payées
  if ($consultation->patient_date_reglement) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  // Cache les annulées
  if ($consultation->annule) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  $consultation->loadRefSejour(1);
  $consultation->loadRefPatient(1);
  $consultation->loadRefCategorie(1);
  $consultation->countDocItems();    
}

if ($chirSel && $plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

CValue::setSession("plageconsult_id", $plageconsult_id);

// Liste des chirurgiens
$mediusers = new CMediusers();
$listChirs = $mediusers->loadProfessionnelDeSanteByPref(PERM_EDIT);

$listDaysSelect = array();
for ($i = 0; $i < 7; $i++) {
  $dateArr = CMbDT::date("+$i day", $debut);
  $listDaysSelect[$dateArr] = $dateArr;    
}

$holidays = CMbDate::getHolidays();

// Variable permettant de compter les jours pour la suppression du samedi et du dimanche
$i = 0;

// Détermination des bornes du semainier
$min = CPlageconsult::$hours_start.":".reset(CPlageconsult::$minutes).":00";
$max = CPlageconsult::$hours_stop.":".end(CPlageconsult::$minutes).":00";

// Extension du semainier s'il y a des plages qui dépassent des bornes
// de configuration hours_start et hours_stop
$hours = CPlageconsult::$hours;

$min_hour = sprintf("%01d", CMbDT::transform($min, null, "%H"));
$max_hour = sprintf("%01d", CMbDT::transform($max, null, "%H"));

if (!isset($hours[$min_hour])) {
  for ($i = $min_hour; $i < CPlageconsult::$hours_start; $i++) {
    $hours[$i] = sprintf("%02d", $i);
  }
}

if (!isset($hours[$max_hour])) {
  for ($i = CPlageconsult::$hours_stop + 1; $i < ($max_hour + 1); $i++) {
    $hours[$i] = sprintf("%02d", $i);
  }
}

// Vérifier le droit d'écriture sur la plage sélectionnée
$plageSel->canDo();

ksort($hours);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time" , $_lastconsult_time);
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("user"              , CMediusers::get());
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChirs);
$smarty->assign("pause"             , $pause);
$smarty->assign("debut"             , $date);
$smarty->assign("listDaysSelect"    , $listDaysSelect);
$smarty->assign("holidays"          , $holidays);
$smarty->assign("listHours"         , $hours);
$smarty->assign("listMins"          , CPlageconsult::$minutes);
$smarty->assign("nb_intervals_hour" , intval(60/CPlageconsult::$minutes_interval));
$smarty->assign("modal" , $modal);

$smarty->display("edit_plage_consultation.tpl");

