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

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = CMediusers::get();
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$show_payees   = CValue::getOrSession("show_payees"  , 1);
$show_annulees = CValue::getOrSession("show_annulees", 0);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", $chir ? $chir->user_id : null);

// Période
$today         = CMbDT::date();
$debut         = CValue::getOrSession("debut", $today);
$debut         = CMbDT::date("last sunday", $debut);
$fin           = CMbDT::date("next sunday", $debut);
$debut         = CMbDT::date("+1 day", $debut);
$bank_holidays = array_merge(CMbDate::getHolidays($debut), CMbDate::getHolidays($fin));

$is_in_period = ($today >= $debut) && ($today <= $fin);

$prec = CMbDT::date("-1 week", $debut);
$suiv = CMbDT::date("+1 week", $debut);


// Plage de consultation selectionnée
$plageconsult_id = CValue::getOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
$canEditPlage = $plageSel->getPerm(PERM_EDIT);
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

if ($plageSel->chir_id != $chirSel && $plageSel->remplacant_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

CValue::setSession("plageconsult_id", $plageconsult_id);

// Liste des consultations a avancer si desistement
$count_si_desistement = CConsultation::countDesistementsForDay(array($chirSel));
  
$nbjours = 7;

$dateArr = CMbDT::date("+6 day", $debut);

$plage = new CPlageconsult();

//where interv/hp
$whereInterv = array();
$whereHP = array();
$where = array();

$where["date"] = "= '$dateArr'";
$where["chir_id"] = " = '$chirSel'";
$whereInterv["chir_id"] = $whereHP["chir_id"] =  " = '$chirSel'";
$whereInterv["date"] = $whereHP["date"] = "= '$dateArr'";



if (!$plage->countList($where)) {
  $nbjours--;
  // Aucune plage le dimanche, on peut donc tester le samedi.
  $dateArr = CMbDT::date("+5 day", $debut);
  $where["date"] = "= '$dateArr'"; 
  if (!$plage->countList($where)) {
    $nbjours--;
  }
}

$hours = CPlageconsult::$hours;

//Planning au format  CPlanningWeek
$debut = CValue::getOrSession("debut", $today);
$debut = CMbDT::date("-1 week", $debut);
$debut = CMbDT::date("next monday", $debut);

//Instanciation du planning
$user = new CMediusers();
$planning = new CPlanningWeek($debut, $debut, $fin, $nbjours, false, 450, null, true);
if ($user->load($chirSel)) {
  $planning->title = $user->load($chirSel)->_view;
}
else {
  $planning->title = "";
}
$planning->guid = $mediuser->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses = array("07", "12", "19");

// Save history
$params = array(
  "chirSel"         => $chirSel,
  "debut"           => $debut,
  "plageconsult_id" => $plageconsult_id,
  "show_payees"     => $show_payees,
  "show_annulees"   => $show_annulees,
);
CViewHistory::save($user, CViewHistory::TYPE_VIEW, $params);

$where = array();
$where[] = "chir_id = '$chirSel' OR remplacant_id = '$chirSel' OR pour_compte_id = '$chirSel'";

for ($i = 0; $i < 7; $i++) {
  $jour = CMbDT::date("+$i day", $debut);

  $is_holiday = array_key_exists($jour, $bank_holidays);

  $whereInterv["date"] = $whereHP["date"] = "= '$jour'";
  $where["date"] = "= '$jour'";

  if ($is_holiday && !CAppUI::pref("show_plage_holiday")) {
    continue;
  }

  if (CAppUI::pref("showIntervPlanning")) {
    //INTERVENTIONS
    /** @var CPlageOp[] $intervs */
    $interv = new CPlageOp();
    $intervs = $interv->loadList($whereInterv);
    CMbObject::massLoadFwdRef($intervs, "chir_id");
    foreach ($intervs as $_interv) {
      $range = new CPlanningRange(
        $_interv->_guid,
        $jour . " " . $_interv->debut, CMbDT::minutesRelative($_interv->debut, $_interv->fin),
        CAppUI::tr($_interv->_class),
        "bbccee",
        "plageop"
      );
      $planning->addRange($range);
    }

    //HORS PLAGE
    $horsPlage = new COperation();
    /** @var COperation[] $horsPlages */
    $horsPlages = $horsPlage->loadList($whereHP);
    CMbObject::massLoadFwdRef($horsPlages, "chir_id");
    foreach ($horsPlages as $_horsplage) {
      $lenght = (CMBDT::minutesRelative("00:00:00", $_horsplage->temp_operation));
      $op = new CPlanningRange(
        $_horsplage->_guid,
        $jour . " " . $_horsplage->time_operation,
        $lenght,
        $_horsplage,
        "3c75ea",
        "horsplage"
      );
      $planning->addRange($op);
    }
  }

  /** @var CPlageconsult[] $listPlages */
  $listPlages = $plage->loadList($where, "date, debut");
  foreach ($listPlages as $_plage) {
    $_plage->loadRefsBack();
    $_plage->countPatients();
    $_plage->loadDisponibilities();
    $debute = "$jour $_plage->debut";
    $libelle = "";
    if (CMbDT::minutesRelative($_plage->debut, $_plage->fin) >= 30 ) {
      $libelle = $_plage->libelle;
    }

    $color = "#DDD";
    if ($_plage->desistee) {
      if (!$_plage->remplacant_id) {
        $color = "#CCC";
      }
      elseif ($_plage->remplacant_id && $_plage->remplacant_id != $chirSel) {
        $color = "#FAA";
      }
      elseif ($_plage->remplacant_id && !$_plage->remplacant_ok) {
        $color = "#FDA";
      }
      elseif ($_plage->remplacant_id && $_plage->remplacant_ok) {
        $color = "#BFB";
      }
    }
    elseif ($_plage->pour_compte_id) {
      $color = "#EDC";
    }

    $class = null;
    if ($_plage->pour_tiers) {
      $class = "pour_tiers";
    }

    $event = new CPlanningEvent(
      $_plage->_guid,
      $debute,
      CMbDT::minutesRelative($_plage->debut, $_plage->fin),
      $libelle,
      $color,
      true,
      $class,
      null
    );
    $event->useHeight = true;

    //Menu des évènements
    $event->addMenuItem("list", "Voir le contenu de la plage");
    $nonRemplace = !$_plage->remplacant_id ||
      $_plage->remplacant_id != $chirSel ||
      ($_plage->remplacant_id == $chirSel && $_plage->chir_id == $chirSel);
    $nonDelegue = !$_plage->pour_compte_id ||
      $_plage->pour_compte_id != $chirSel ||
      ($_plage->pour_compte_id == $chirSel && $_plage->chir_id == $chirSel);
    if ($nonRemplace && $nonDelegue && $_plage->getPerm(PERM_EDIT)) {
      $event->addMenuItem("edit", "Modifier cette plage");
    }
    $event->addMenuItem("clock", "Planifier une consultation dans cette plage");

    //Paramètres de la plage de consultation
    $event->type = "consultation";
    $event->plage["id"] = $_plage->plageconsult_id;
    
    $pct = $_plage->_fill_rate;
    if ($pct > "100") {
      $pct = "100";
    }
    if ($pct == "") {
      $pct = 0;
    }
    
    $event->plage["pct"]          = $pct;
    $event->plage["locked"]       = $_plage->locked;
    $event->plage["_affected"]    = $_plage->_affected;
    $event->plage["_nb_patients"] = $_plage->_nb_patients;
    $event->plage["_total"]       = $_plage->_total;
    $event->plage["color"]        = $_plage->color;
    $event->plage["list_class"]   = "list";
    $event->plage["add_class"]    = "clock";
    $event->plage["list_title"]   = "Voir le contenu de la plage";
    $event->plage["add_title"]    = "Planifier une consultation dans cette plage";
    $event->_disponibilities    = $_plage->_disponibilities;

    //Ajout de l'évènement au planning 
    $planning->addEvent($event);
  }
}

$planning->allow_superposition = false;
$planning->rearrange();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("planning"            , $planning);
$smarty->assign("show_payees"         , $show_payees);
$smarty->assign("show_annulees"       , $show_annulees);
$smarty->assign("chirSel"             , $chirSel);
$smarty->assign("canEditPlage"        , $canEditPlage);
$smarty->assign("plageSel"            , $plageSel);
$smarty->assign("today"               , $today);
$smarty->assign("debut"               , $debut);
$smarty->assign("fin"                 , $fin);
$smarty->assign("prec"                , $prec);
$smarty->assign("suiv"                , $suiv);
$smarty->assign("plageconsult_id"     , $plageconsult_id);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->assign("bank_holidays"       , $bank_holidays);
$smarty->assign("mediuser"            , $mediuser);

$smarty->display("vw_planning.tpl");
