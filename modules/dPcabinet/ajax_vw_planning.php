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
$chirSel        = CValue::getOrSession("chirSel");
$function_id    = CValue::get("function_id");
$today          = CMbDT::date();

$show_free      = CValue::get("show_free");
$show_cancelled = CValue::get("show_cancelled");
$facturated     = CValue::get("facturated");
$status         = CValue::get("status");
$actes          = CValue::get("actes");
$hide_in_conge  = CValue::get("hide_in_conge", 0);

$min_hour = 23;

// gathering prat ids
$ids = array();
$function = new CFunctions();
$function->load($function_id);
if ($function->_id) {
  $function->loadRefsUsers();
  foreach ($function->_ref_users as $_user) {
    $ids[] = $_user->_id;
  }
}

if (!$function_id && $chirSel) {
  $ids[] = $chirSel;
}

// Liste des consultations a avancer si desistement
$count_si_desistement = CConsultation::countDesistementsForDay($ids, $today);

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
$whereInterv["chir_id"] = $whereHP["chir_id"] =  "= '$chirSel' ";
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
$user->load($chirSel);
if ($user->_id) {
  $user->loadRefFunction();
  $planning->title = CMbString::htmlEntities($user->_view);
}
else {
  $planning->title = "";
}

$can_edit = CCanDo::edit();

$planning->guid = $user->_guid;
$planning->hour_min = "07";
$planning->hour_max = "20";
$planning->pauses   = array("07", "12", "19");
$planning->dragndrop = $planning->resizable = $can_edit ? 1 : 0;
$planning->hour_divider = 60 / CAppUI::conf("dPcabinet CPlageconsult minutes_interval");
$planning->no_dates = 1;

$plage = new CPlageconsult();

$whereHP["plageop_id"] = " IS NULL";

$users = array();
$conges_day = array();
if ($user->_id) {
  $muser = new CMediusers();
  $users = $muser->loadUsers(PERM_READ, $user->function_id);
}

for ($i = 0; $i < $nbDays; $i++) {
  $jour = CMbDT::date("+$i day", $debut);
  $is_holiday = array_key_exists($jour, $bank_holidays);

  $planning->addDayLabel($jour, '<span style="font-size: 1.4em">'.CMbDT::format($jour, "%a %d %b").'</span>');

  // conges dans le header
  if (count($users)) {
    if (CModule::getActive("dPpersonnel")) {
      $_conges = CPlageConge::loadForIdsForDate(array_keys($users), $jour);
      foreach ($_conges as $key => $_conge) {
        $_conge->loadRefUser();
        $conges_day[$i][] = $_conge->_ref_user->_shortview;
      }
    }
  }
  $where["date"] = $whereInterv["date"] = $whereHP["date"] = "= '$jour'";

  if (CAppUI::pref("showIntervPlanning")) {
    if (!$is_holiday || CAppUI::pref("show_plage_holiday")) {
      //INTERVENTIONS
      /** @var CPlageOp[] $intervs */
      $interv = new CPlageOp();
      $intervs = $interv->loadList($whereInterv);
      CMbObject::massLoadFwdRef($intervs, "chir_id");
      foreach ($intervs as $_interv) {
        $range = new CPlanningRange(
          $_interv->_guid, $jour . " " . $_interv->debut,
          CMbDT::minutesRelative($_interv->debut, $_interv->fin),
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
          CMbString::htmlEntities($_horsplage->_view),
          "3c75ea",
          "horsplage"
        );
        $planning->addRange($op);
      }
    }
  }

  // PLAGES CONGE
  $is_conge = false;
  if (CModule::getActive("dPpersonnel")) {
    $conge = new CPlageConge();
    $where_conge = array();
    $where_conge["date_debut"] = " <= '$jour' ";
    $where_conge["date_fin"] = " >= '$jour' ";
    $where_conge["user_id"] = "= '$chirSel'";
    /** @var CPlageconge[] $conges */
    $conges = $conge->loadList($where_conge);
    $is_conge = count($conges) > 0;
    foreach ($conges as $_conge) {
      $libelle = '<h3 style="text-align: center">
    CONGES</h3>
    <p style="text-align: center">' . CMbString::htmlEntities($_conge->libelle) . '</p>';
      $event = new CPlanningEvent(
        $_conge->_guid . $jour,
        $jour . " 00:00:00",
        1440,       // 1440 min = 1 day
        $libelle,
        "#ffe87e",
        true,
        "hatching",
        null,
        false
      );
      $event->below = 1;
      $planning->addEvent($event);
    }
  }

  //PLAGES CONSULT

  // férié & pref
  if ($is_holiday && !CAppUI::pref("show_plage_holiday")) {
    continue;
  }

  // conges
  if ($is_conge && $hide_in_conge) {
    continue;
  }

  /** @var CPlageConsult[] $plages */
  $plages = $plage->loadList($where, "date, debut");
  CMbObject::massLoadFwdRef($plages, "chir_id");
  foreach ($plages as $_plage) {
    $_plage->loadRefsFwd(1);
    $_plage->loadRefsConsultations(false);
    $_plage->loadRefChir()->loadRefFunction();

    if (CMbDT::format($_plage->debut, "%H") < $min_hour) {
      $min_hour = CMbDT::format($_plage->debut, "%H");
    }

    // Affichage de la plage sur le planning
    $range = new CPlanningRange(
      $_plage->_guid,
      $jour . " " . $_plage->debut,
      CMbDT::minutesRelative($_plage->debut, $_plage->fin),
      $_plage->libelle,
      $_plage->color
    );
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
    if ($show_free) {
      $_plage->loadRefsConsultations(false);
      $utilisation = $_plage->getUtilisation();
      foreach ($utilisation as $_timing => $_nb) {
        if (!$_nb) {
          $debute = "$jour $_timing";
          $event = new CPlanningEvent($debute, $debute, $_plage->_freq, "", $color, true, "droppable", null);
          $event->type = "rdvfree";
          $event->plage["id"] = $_plage->_id;
          if ($_plage->locked == 1) {
            $event->disabled = true;
          }
          $event->plage["color"] = $_plage->color;
          //Ajout de l'évènement au planning
          $event->below = 1;
          $planning->addEvent($event);
        }
      }
    }

    //consultations
    $consult = new CConsultation();
    $whereC = array();
    $whereC["plageconsult_id"] = " = '$_plage->_id' ";
    $whereC["annule"] = " = '$show_cancelled'";
    if ($status) {
      $whereC["chrono"] = " = '$status'";
    }
    if ($facturated) {
      $whereC["facture"] = " = '1'";
    }
    /** @var CConsultation[] $consults */
    $consults = $consult->loadList($whereC, "heure");

    foreach ($consults as $_consult) {

      if ($_consult->heure < $min_hour) {
        $min_hour = CMbDT::format($_consult->heure, "%H");
      }

      if ($actes != "") {
        $_actes = $_consult->loadRefsActes();
        $nb_actes = $_consult->_count_actes;
        // avec des actes
        if ($actes && !$nb_actes) {
          continue;
        }
        // sans actes
        if ($actes === "0" && $nb_actes > 0) {
          continue;
        }
      }

      $_consult->loadPosition();
      $debute = "$jour $_consult->heure";
      $motif = CMbString::htmlEntities($_consult->motif);
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

        $style = "";
        if ($_consult->annule) {
          $style .= "text-decoration:line-through;";
        }

        $title = "";
        if ($_consult->_consult_sejour_out_of_nb) {
          $nb = $_consult->_consult_sejour_nb;
          $of = $_consult->_consult_sejour_out_of_nb;
          $title .= "<span style=\"float:right;\">$nb / $of</span>";
        }
        $title .= "<span style=\"$style\">";
        $title .= CMbString::htmlEntities($_consult->_ref_patient->_view) . "\n" . $motif;
        $title .= "</span>";

        $event = new CPlanningEvent(
          $_consult->_guid,
          $debute,
          $_consult->duree * $_plage->_freq,
          $title,
          $color,
          true,
          "droppable $debute",
          $_consult->_guid,
          false
        );
      } else {
        $color = "#faa";
        $event = new CPlanningEvent(
          $_consult->_guid,
          $debute, $_consult->duree * $_plage->_freq,
          $motif ? $motif : "[PAUSE]",
          $color,
          true,
          "droppable $debute",
          $_consult->_guid,
          false
        );
      }
      $event->type = "rdvfull";
      $event->plage["id"] = $_plage->_id;
      $event->plage["consult_id"] = $_consult->_id;
      if ($_plage->locked == 1) {
        $event->disabled = true;
      }

      $_consult->loadRefCategorie();
      if ($_consult->categorie_id) {
        $event->icon = "./modules/dPcabinet/images/categories/" . $_consult->_ref_categorie->nom_icone;
        $event->icon_desc = CMbString::htmlEntities($_consult->_ref_categorie->nom_categorie);
      }

      if ($_consult->_id) {
        $event->draggable /*= $event->resizable */ = $can_edit;
        $freq = $_plage->freq ? CMbDT::transform($_plage->freq, null, "%M") : 1;
        $event->hour_divider = 60 / $freq;

        if ($can_edit) {
          $event->addMenuItem("copy", "Copier cette consultation");
          $event->addMenuItem("cut", "Couper cette consultation");
          if ($_consult->patient_id) {
            $event->addMenuItem("add", "Ajouter une consultation");
            if ($_consult->chrono == CConsultation::PLANIFIE) {
              $event->addMenuItem("tick", CMbString::htmlEntities("Notifier l'arrivée"));
            }
            if ($_consult->chrono == CConsultation::PATIENT_ARRIVE) {
              $event->addMenuItem("tick_cancel", CMbString::htmlEntities("Annuler l'arrivée"));
            }
          }
        }
      }

      //Ajout de l'évènement au planning
      $event->plage["color"] = $_plage->color;
      $event->below = 0;
      $planning->addEvent($event);
    }
  }
}

$planning->hour_min = $min_hour;

// conges
foreach ($conges_day as $key => $_day) {
  $conges_day[$key] = implode(", ", $_day);;
}

$planning->rearrange(true);

$smarty = new CSmartyDP();
$smarty->assign("planning"            , $planning);
$smarty->assign("debut"               , $debut);
$smarty->assign("fin"                 , $fin);
$smarty->assign("prev"                , $prev);
$smarty->assign("next"                , $next);
$smarty->assign("chirSel"             , $chirSel);
$smarty->assign("conges"              , $conges_day);
$smarty->assign("function_id"         , $function_id);
$smarty->assign("user"                , $user);
$smarty->assign("today"               , $today);
$smarty->assign("height_calendar"     , CAppUI::pref("height_calendar", "2000"));
$smarty->assign("bank_holidays"       , $bank_holidays);
$smarty->assign("count_si_desistement", $count_si_desistement);
$smarty->display("inc_vw_planning.tpl");