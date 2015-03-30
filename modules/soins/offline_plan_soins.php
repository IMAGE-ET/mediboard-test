<?php

/**
 * $Id$
 *
 * @category Soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */


CCanDo::checkRead();

set_time_limit(120);
ini_set("memory_limit", "2048M");

$sejours_ids  = CValue::get("sejours_ids", null);
$service_id   = CValue::get("service_id");
$date         = CValue::get("date", CMbDT::date());
$mode_dupa    = CValue::get("mode_dupa", 0);
$empty_lines  = CValue::get("empty_lines", 0);
$mode_lite    = CValue::get("mode_lite", 0);
$page_break   = CValue::get("page_break", 0);

$sejours = array();

$now = CMbDT::dateTime();
$now_date = CMbDT::date();

$group = CGroups::loadCurrent();

if ($mode_dupa) {
  $period = CAppUI::conf("soins plan_soins period", $group->_guid);
  $datetime_min = "$date 00:00:00";
  $datetime_max = CMbDT::date("+ $period days", $date) . " 23:59:59";
}
else {
  $hours_before = CAppUI::conf("soins plan_soins hours_before", $group->_guid);
  $hours_after  = CAppUI::conf("soins plan_soins hours_after" , $group->_guid);
  $datetime_min = CMbDT::dateTime("-$hours_before hour", $date == $now_date ? $now : "$date 00:00:00");
  $datetime_max = CMbDT::dateTime("+$hours_after  hour", $date == $now_date ? $now : "$date 00:00:00");
}

if ($sejours_ids) {
  $sejours_ids = explode(",", $sejours_ids);
  $sejour = new CSejour();
  $where = array();
  $where["sejour.sejour_id"] = CSQLDataSource::prepareIn($sejours_ids);
  $sejours = $sejour->loadList($where);
}
else {
  $service = new CService();
  $service->load($service_id);

  $sejour = new CSejour();
  $where  = array();
  $ljoin  = array();

  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["prescription"] = "prescription.object_id = sejour.sejour_id";

  $where["sejour.entree"]          = "<= '$datetime_max'";
  $where["sejour.sortie"]          = " >= '$datetime_min'";
  if ($service->_id) {
    $where["affectation.entree"]     = "<= '$datetime_max'";
    $where["affectation.sortie"]     = ">= '$datetime_min'";
    $where["affectation.service_id"] = " = '$service_id'";
  }
  else {
    $where["affectation.affectation_id"] = "IS NULL";
    $where["sejour.group_id"] = "= '$group->_id'";

  }
  $where["prescription.type"]      = "= 'sejour'";

  $sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);
}

CStoredObject::massLoadFwdRef($sejours, "patient_id");
CStoredObject::massLoadBackRefs($sejours, "operations", "date ASC");

$dates = array();

$date_temp = CMbDT::date($datetime_min);
$date_max = CMbDT::date($datetime_max);

CPrescription::$mode_plan_soins = true;
CPrescription::$show_inactive = false;

$freq_poste = CAppUI::conf("soins plan_soins freq_postes", $group->_guid);

$postes = array();
$periods = array();
$moments = array();
$dates_plan_soin = array();
$periods = "";

while ($date_temp <= $date_max) {
  $dates[$date_temp] = $date_temp;
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

$colspan = 0;

switch ($freq_poste) {
  case "1":
  case "2":
  case "4":
    $date_temp = CMbDT::transform(null, $datetime_min, "%Y-%m-%d %H:00:00");
    while ($date_temp < CMbDT::transform(null, $datetime_max, "%Y-%m-%d %H:00:00")) {
      @$dates_plan_soin[CMbDT::date($date_temp)][CMbDT::time($date_temp)] = CMbDT::transform(null, $date_temp, "%H");
      $date_temp = CMbDT::addDateTime("$freq_poste:00:00", $date_temp);
      $colspan++;
    }

    // Il faut tester la parité de l'heure de début.
    $count = 1;
    $first_key = reset($dates_plan_soin);
    $first_key  = reset($first_key);

    for ($i = intval($first_key) % $freq_poste ; $i < 24 ; $i += $freq_poste) {
      $postes["Poste $count"] = str_pad($i, 2, "0", STR_PAD_LEFT);
      $postes_to_hour[str_pad($i, 2, "0", STR_PAD_LEFT)] = str_pad($i, 2, "0", STR_PAD_LEFT);
      $moments["poste-$count"] = str_pad($i, 2, "0", STR_PAD_LEFT);
      $count++;
      $periods[] = $i;
    }

    break;
  case "poste":
  default:
    $postes = array(
      "Poste 1" => CAppUI::conf("soins plan_soins hour_matin", $group->_guid),
      "Poste 2" => CAppUI::conf("soins plan_soins hour_midi" , $group->_guid),
      "Poste 3" => CAppUI::conf("soins plan_soins hour_soir" , $group->_guid),
      "Poste 4" => CAppUI::conf("soins plan_soins hour_nuit" , $group->_guid));

    $postes_to_hour = array(
      "matin" => str_pad($postes["Poste 1"], 2, "0", STR_PAD_LEFT),
      "midi"  => str_pad($postes["Poste 2"], 2, "0", STR_PAD_LEFT),
      "soir"  => str_pad($postes["Poste 3"], 2, "0", STR_PAD_LEFT),
      "nuit"  => str_pad($postes["Poste 4"], 2, "0", STR_PAD_LEFT),
    );

    $moments = array(
      "poste-1" => "matin",
      "poste-2" => "midi" ,
      "poste-3" => "soir" ,
      "poste-4" => "nuit"
    );

    foreach ($dates as $_date) {
      $dates_plan_soin[$_date] = $moments;
      $colspan += 4;
    }
    break;
}

$dates_postes = array();
foreach ($dates as $_date) {
  $dates_postes[$_date] = CAdministration::getTimingPlanSoins($_date, $postes, $periods, 15, 15);
}

$postes_by_date = array();
foreach ($dates_postes as $_dates_postes) {
  foreach ($_dates_postes as $day => $__dates_postes) {
    foreach ($__dates_postes as $poste => $_dates) {
      foreach ($_dates as $_day => $hours) {
        foreach ($hours as $_hour) {
          @$postes_by_date[$_day][$_hour] = array(
            "day"  => $day,
            "moment" => $moments[$poste],
          );
        }
      }
    }
  }
}

$initiales = array();

/** @var $_sejour CSejour */
foreach ($sejours as $_sejour) {
  $prescription = $_sejour->loadRefPrescriptionSejour();

  $prescription->loadRefsLinesMedByCat("1", "1", "", "0", "1");
  $prescription->loadRefsLinesElementByCat("1", "1", "", "", "", "", "0", "1");
  $prescription->loadRefsPrescriptionLineMixes("", "0", "1", "", "0", "1");
  $prescription->loadRefsLinesInscriptions();

  CStoredObject::massLoadBackRefs($prescription->_ref_prescription_lines, "prise_posologie", "moment_unitaire_id, prise_posologie_id");
  CStoredObject::massLoadBackRefs($prescription->_ref_prescription_lines_element, "prise_posologie", "moment_unitaire_id, prise_posologie_id");
  CPrescription::massLoadAdministrations($prescription, $dates);

  $prescription->calculAllPlanifSysteme();
  $prescription->calculPlanSoin($dates, 0, null, null, null, true);

  CPrescription::massCountPlanifications($prescription);

  $_sejour->loadRefCurrAffectation($now);

  if (!$service_id) {
    $_sejour->_ref_curr_affectation->loadRefService();
  }

  $patient = $_sejour->loadRefPatient();
  $patient->loadRefLatestConstantes(null, array("poids", "taille"));
  $_sejour->loadNDA();
  $_sejour->loadRefsOperations();
  $_sejour->loadJourOp($date);
  $_sejour->_ref_last_operation->loadRefPlageOp();

  $initiales[$prescription->_id] = array();

  foreach ($prescription->_ref_prescription_lines as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadActiveDates();

    if (count($line->_quantity_by_date)) {
      foreach ($line->_quantity_by_date as $_unite => $_quantites_by_unite) {
        foreach ($_quantites_by_unite as $_date => $_quantites_by_hour) {
          if (!isset($_quantites_by_hour["quantites"]) || !is_array($_quantites_by_hour["quantites"])) {
            continue;
          }
          foreach ($_quantites_by_hour["quantites"] as $_hour => $_quantite) {
            if (!isset($postes_by_date[$_date][$_hour])) {
              continue;
            }
            $key = $postes_by_date[$_date][$_hour];
            @$line->_quantity_by_date_moment[$_unite][$key["day"]][$key["moment"]]["total"] += $_quantite["total"];
            @$line->_quantity_by_date_moment[$_unite][$key["day"]][$key["moment"]]["nb_adm"] += $_quantite["nb_adm"];
          }
        }
      }
    }

    if (count($line->_administrations)) {
      CMbObject::massLoadFwdRef($line->_ref_administrations, "administrateur_id");

      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            if ($_hour == "list") {
              $adm_ids = explode("|", $_administrations_by_hour);
              foreach ($adm_ids as $_adm_id) {
                $adm = $line->_ref_administrations[$_adm_id];
                if (!isset($postes_by_date[$_date][$adm->_heure])) {
                  continue;
                }
                $key = $postes_by_date[$_date][$adm->_heure];
                @$initiales[$prescription->_id][$_date][$key["moment"]][] = $adm->loadRefAdministrateur()->_shortview;
              }
              continue;
            }
            if (!isset($postes_by_date[$_date][$_hour])) {
              continue;
            }
            $key = $postes_by_date[$_date][$_hour];
            @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite'] += $_administrations_by_hour['quantite'];
            @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite_planifiee'] += $_administrations_by_hour['quantite_planifiee'];
          }
        }
      }
    }
  }

  if (count($prescription->_ref_prescription_line_mixes)) {
    CStoredObject::massLoadBackRefs($prescription->_ref_prescription_line_mixes, "variations", "dateTime");

    foreach ($prescription->_ref_prescription_line_mixes as $line) {
      $line->_prises_prevues_moment = array();
      $line->loadRefPraticien();
      $line->loadActiveDates();
      $line->loadRefsVariations();

      if (count($line->_prises_prevues)) {
        foreach ($line->_prises_prevues as $_date => $_prises_by_date) {
          foreach ($_prises_by_date as $_hour => $_prise) {
            if (!isset($postes_by_date[$_date][$_hour])) {
              continue;
            }
            $key = $postes_by_date[$_date][$_hour];
            if (isset($_prise["real_hour"]) && is_array($_prise["real_hour"])) {
              foreach ($_prise["real_hour"] as $_real_hour) {
                @$line->_prises_prevues_moment[$key["day"]][$key["moment"]]["real_hour"][] = $_real_hour;
              }
            }
          }
        }
      }
      if (count($line->_ref_lines)) {
        foreach ($line->_ref_lines as $_line_item) {
          $_line_item->_administrations_moment = array();
          if (count($_line_item->_administrations)) {
            foreach ($_line_item->_administrations as $date => $_administrations_by_date) {
              foreach ($_administrations_by_date as $_hour => $_quantite) {
                if (!isset($postes_by_date[$date][$_hour])) {
                  continue;
                }
                $key = $postes_by_date[$date][$_hour];
                @$_line_item->_administrations_moment[$date][$key["moment"]] += $_quantite;
              }
            }
          }
        }
      }
    }
  }

  foreach ($prescription->_ref_prescription_lines_element as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadActiveDates();

    if (count($line->_quantity_by_date)) {
      foreach ($line->_quantity_by_date as $_unite => $_quantites_by_unite) {
        foreach ($_quantites_by_unite as $_date => $_quantites_by_hour) {
          if (!isset($_quantites_by_hour["quantites"]) || !is_array($_quantites_by_hour["quantites"])) {
            continue;
          }
          foreach ($_quantites_by_hour["quantites"] as $_hour => $_quantite) {
            if (!isset($postes_by_date[$_date][$_hour])) {
              continue;
            }
            $key = $postes_by_date[$_date][$_hour];
            @$line->_quantity_by_date_moment[$_unite][$key["day"]][$key["moment"]]["total"] += $_quantite["total"];
            @$line->_quantity_by_date_moment[$_unite][$key["day"]][$key["moment"]]["nb_adm"] += $_quantite["nb_adm"];
          }
        }
      }
    }

    if (count($line->_administrations)) {
      CMbObject::massLoadFwdRef($line->_ref_administrations, "administrateur_id");
      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            if ($_hour == "list") {
              $adm_ids = explode("|", $_administrations_by_hour);
              foreach ($adm_ids as $_adm_id) {
                $adm = $line->_ref_administrations[$_adm_id];
                if (!isset($postes_by_date[$_date][$adm->_heure])) {
                  continue;
                }
                $key = $postes_by_date[$_date][$adm->_heure];
                @$initiales[$prescription->_id][$_date][$key["moment"]][] = $adm->loadRefAdministrateur()->_shortview;
              }
              continue;
            }
            if (!isset($postes_by_date[$_date][$_hour])) {
              continue;
            }
            $key = $postes_by_date[$_date][$_hour];
            @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite'] += $_administrations_by_hour['quantite'];
            @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite_planifiee'] += $_administrations_by_hour['quantite_planifiee'];
          }
        }
      }
    }
  }

  if (count($prescription->_count_inscriptions)) {
    foreach ($prescription->_ref_lines_inscriptions as $lines_by_type) {
      foreach ($lines_by_type as $line) {
        $line->_quantity_by_date_moment = array();
        $line->_administrations_moment  = array();

        if (count($line->_administrations)) {
          foreach ($line->_administrations as $_key => $_administrations) {
            foreach ($_administrations as $_date => $_administrations_by_date) {
              foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
                if ($_hour == "list") {
                  $adm_ids = explode("|", $_administrations_by_hour);
                  foreach ($adm_ids as $_adm_id) {
                    $adm = $line->_ref_administrations[$_adm_id];
                    if (!isset($postes_by_date[$_date][$adm->_heure])) {
                      continue;
                    }
                    $key = $postes_by_date[$_date][$adm->_heure];
                    @$initiales[$prescription->_id][$_date][$key["moment"]][] = $adm->loadRefAdministrateur()->_shortview;
                  }
                  continue;
                }
                if (!isset($postes_by_date[$_date][$_hour])) {
                  continue;
                }
                $key = $postes_by_date[$_date][$_hour];
                @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite'] += $_administrations_by_hour['quantite'];
                @$line->_administrations_moment[$_key][$key["day"]][$key["moment"]]['quantite_planifiee'] += $_administrations_by_hour['quantite_planifiee'];
              }
            }
          }
        }
      }
    }
  }

  if ($mode_lite) {
    CPrescription::massLoadLastAdministration($prescription);
  }
}

// Dédoublonne les initiales
foreach ($initiales as $prescription_id => $_initiales) {
  foreach ($_initiales as $_date => $_initiales_by_date) {
    foreach ($_initiales_by_date as $_hour => $_initiales_by_hour) {
      $initiales[$prescription_id][$_date][$_hour] = array_unique($_initiales_by_hour);
    }
  }
}

$current_moment =
  isset($postes_by_date[$now_date]) && isset($postes_by_date[$now_date][CMbDT::transform(null, CMbDT::time(), "%H")]) ?
    $postes_by_date[$now_date][CMbDT::transform(null, CMbDT::time(), "%H")]["moment"] : "";

// Chargement des cis à risque
$where = array();
$where["risque"]    = " = '1'";
$risques_cis = CProduitLivretTherapeutique::getCISList($where);

$smarty = new CSmartyDP();

$smarty->assign("now"            , $now);
$smarty->assign("now_date"       , $now_date);
$smarty->assign("sejours"        , $sejours);
if ($service_id && !$sejours_ids) {
  $smarty->assign("service"      , $service);
}
$smarty->assign("dates"          , $dates);
$smarty->assign("moments"        , $moments);
$smarty->assign("mode_dupa"      , $mode_dupa);
$smarty->assign("initiales"      , $initiales);
$smarty->assign("current_moment" , $current_moment);
$smarty->assign("empty_lines"    , $empty_lines);
$smarty->assign("dates_plan_soin", $dates_plan_soin);
$smarty->assign("colspan"        , $colspan);
$smarty->assign("risques_cis"    , $risques_cis);
$smarty->assign("mode_lite"      , $mode_lite);
$smarty->assign("page_break"     , $page_break);
$smarty->assign("postes_to_hour" , $postes_to_hour);

$smarty->display("offline_plan_soins.tpl");