<?php 

/**
 * $Id$
 *  
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$date = CValue::get("date", CMbDT::date());

$user = CMediusers::get();
$prat_id      = CValue::get("prat_id");
$function_id  = CValue::get("function_id");

if (!$prat_id && !$function_id) {
  $prat_id = $user->_id;
}

$prat = new CMediusers();
$where = array();
if ($function_id) {
  $where["function_id"] = " = '$function_id'";
}
else {
  $where["user_id"] = " = '$prat_id'";
}
$prats = $prat->loadList($where);

$function = new CFunctions();
$function->load($function_id);

$ds = $function->getDS();



$calendar = new CPlanningMonth($date);
$calendar->title = CMbDT::format($date, "%B %Y");

foreach ($prats as $_prat) {

  // plages de congés (si mode prat)
  if (!$function_id) {
    $plage_cong = new CPlageConge();
    $plages_cong = $plage_cong->loadListForRange($_prat->_id, $calendar->date_min, $calendar->date_max);
    foreach ($plages_cong as $_conge) {
      $first_day = $_conge->date_debut;
      $last_day = $_conge->date_fin;
      $replaced_by = new CMediusers();
      $replaced_by->load($_conge->replacer_id);
      while ($last_day >= $first_day) {
        $calendar->addClassesForDay("disabled", $first_day);
        if ($replaced_by->_id) {
          $event = new CPlanningEvent($_conge->_guid, $first_day);
          $event->title = "<strong>".($_conge->_view ? $_conge->_view : "Congés");
          if ($function_id) {
            $event->title .= " (".$_prat->_shortview.")";
          }
          $event->title .= $replaced_by ? " remplacé par ".$replaced_by->_view."</strong> " : null;
          $calendar->days[$first_day][$_conge->_guid] = $event;
        }
        $first_day = CMbDT::date("+1 DAY", $first_day);
      }
    }
  }

  // plages consult
  $plage = new CPlageconsult();
  $plages_c = $plage->loadForDays($_prat->_id, $calendar->date_min, $calendar->date_max);
  foreach ($plages_c as $_plage) {
    $_plage->loadRefsConsultations(false);
    $count = count($_plage->_ref_consultations);
    $_plage->loadFillRate();
    $event = new CPlanningEvent($_plage->_guid, $_plage->date." $_plage->debut", CMbDT::minutesRelative($_plage->date." ".$_plage->debut, $_plage->date." ".$_plage->fin));
    $title = $_plage->libelle ? $_plage->libelle : CAppUI::tr($_plage->_class);
    $event->title = "<strong>".CMbDT::format($_plage->debut, "%H:%M"). " - " .CMbDT::format($_plage->fin, "%H:%M")."</strong> $count ".CAppUI::tr("CConsultation");
    $event->title .= "<small>";
    $event->title .= "<br/>$title";
    if ($function_id) {
      $event->title .= " - ".$_prat->_shortview."";
    }
    $event->title .= "<br/> Durée cumulée : ".CMbDT::time("+ $_plage->_cumulative_minutes MINUTES", "00:00:00");
    $event->title .= "</small>";
    $event->type = $_plage->_class;
    $event->css_class = $_plage->_class;
    $event->setObject($_plage);
    $calendar->days[$_plage->date][$_plage->_guid] = $event;
  }

  // plages op
  $plage = new CPlageOp();
  $plages_op = $plage->loadForDays($_prat->_id, $calendar->date_min, $calendar->date_max);
  /** @var CPlageOp[] $plages_op */
  foreach ($plages_op as $_plage) {
    $_plage->loadRefsOperations(false);
    $_plage->loadRefSalle();
    $event = new CPlanningEvent($_plage->_guid, $_plage->date);
    $title = CAppUI::tr($_plage->_class);
    if ($_plage->spec_id) {
      $event->title.= "<img src=\"images/icons/user-function.png\" style=\" float:right;\" alt=\"\"/>";
    }
    $event->title .= "
    <strong>".CMbDT::format($_plage->debut, "%H:%M"). " - " .CMbDT::format($_plage->fin, "%H:%M")."</strong>
     ".count($_plage->_ref_operations)." ".CAppUI::tr('COperation');
    $event->title .="<small>";
    $event->title .="<br/>$_plage->_ref_salle";
    if ($function_id && !$_plage->spec_id) {
      $event->title .= " - ".$_prat->_shortview."";
    }
    $event->title .= "<br/>Durée cumulée : ";
    $event->title .= $_plage->_cumulative_minutes ? CMbDT::transform("+ $_plage->_cumulative_minutes MINUTES", "00:00:00", "%Hh%M") : " &mdash;" ;
    $event->title .= "</small>";
    $event->type = $_plage->_class;
    $event->css_class = $_plage->_class;
    $event->setObject($_plage);
    $calendar->days[$_plage->date][$_plage->_guid] = $event;
  }

  //hors plage
  $sql = "
    SELECT plageop_id, date,
      SEC_TO_TIME(SUM(TIME_TO_SEC(temp_operation))) as accumulated_time,
      MIN(time_operation) AS first_time,
      MAX(time_operation) AS last_time,
      COUNT(*) AS nb_op
    FROM operations
    WHERE date BETWEEN  '$calendar->date_min' AND  '$calendar->date_max'
    AND chir_id = '$_prat->_id'
    GROUP BY date, plageop_id";
  $results = $ds->loadList($sql);

  foreach ($results as $_hp) {
    $guid = "hps_".$_hp["date"].$_prat->_id;
    $event = new CPlanningEvent($guid, $_hp["date"]." ".$_hp["first_time"], CMbDT::minutesRelative($_hp["date"]." 00:00:00", $_hp["date"]." ".$_hp["accumulated_time"]));
    $event->title = "<strong>".CMbDT::format($_hp["first_time"], '%H:%M')." - ".CMbDT::format($_hp["last_time"], "%H:%M")."</strong> ".$_hp["nb_op"]." ".CAppUI::tr("CIntervHorsPlage");
    $event->title.= "<small>";
    if ($function_id) {
      $event->title .= " - ".$_prat->_shortview;
    }
    $event->title.= "<br/>Durée cumulée : ".CMbDT::format($_hp["accumulated_time"], '%Hh%M');
    $event->title.= "</small>";

    $event->css_class = $event->type = "CIntervHorsPlage";
    $calendar->days[$_hp["date"]][$guid] = $event;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("calendar", $calendar);
$smarty->display("inc_vw_month.tpl");