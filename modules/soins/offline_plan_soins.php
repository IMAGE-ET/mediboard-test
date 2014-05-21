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

ini_set("memory_limit", "2048M");

$sejours_ids  = CValue::get("sejours_ids", null);
$service_id   = CValue::get("service_id");
$date         = CValue::get("date", CMbDT::date());
$mode_dupa    = CValue::get("mode_dupa", 0);

$sejours = array();

$now = CMbDT::dateTime();
$now_date = CMbDT::date();

$group = CGroups::loadCurrent();

if ($mode_dupa) {
  $period = CAppUI::conf("soins plan_soins period" , $group->_guid);
  $datetime_min = "$date 00:00:00";
  $datetime_max = CMbDT::date("+ $period days", $date) . " 23:59:59";
}
else {
  $period = 4;
  $datetime_min = CMbDT::date("-1 day", $date) . " 00:00:00";
  $datetime_max = CMbDT::date("+ 3 days", $date) . " 23:59:59";
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
  $where["affectation.entree"]     = "<= '$datetime_max'";
  $where["affectation.sortie"]     = ">= '$datetime_min'";
  $where["affectation.service_id"] = " = '$service_id'";
  $where["prescription.type"]      = "= 'sejour'";

  $sejours = $sejour->loadList($where, null, null, "sejour.sejour_id", $ljoin);
}

CMbObject::massLoadFwdRef($sejours, "patient_id");

$dates = array();

$date_temp = CMbDT::date($datetime_min);
$date_max = CMbDT::date($datetime_max);

CPrescription::$mode_plan_soins = true;
CPrescription::$show_inactive = false;

while ($date_temp < $date_max) {
  $dates[$date_temp] = $date_temp;
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

$postes = array(
  "Poste 1" => CAppUI::conf("soins plan_soins hour_matin", $group->_guid),
  "Poste 2" => CAppUI::conf("soins plan_soins hour_midi" , $group->_guid),
  "Poste 3" => CAppUI::conf("soins plan_soins hour_soir" , $group->_guid),
  "Poste 4" => CAppUI::conf("soins plan_soins hour_nuit" , $group->_guid)
);

$dates_postes = array();

$dates_postes[CMbDT::date("-1 day", $date)] = CAdministration::getTimingPlanSoins(CMbDT::date("-1 day", $date), $postes, null, 1, 2);
foreach ($dates as $_date) {
  $dates_postes[$_date] = CAdministration::getTimingPlanSoins($_date, $postes, null, 1, 2);
}
$dates_postes[CMbDT::date("+1 day", $date)] = CAdministration::getTimingPlanSoins(CMbDT::date("+1 day", $date), $postes, null, 1, 2);

$postes_by_date = array();
$moments = array(
  "poste-1" => "matin",
  "poste-2" => "midi" ,
  "poste-3" => "soir" ,
  "poste-4" => "nuit"
);

$moments_reverse = array_flip($moments);

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

  // Si aucune ligne, on retire le séjour
  if (!count($prescription->_ref_prescription_lines) &&
    !count($prescription->_ref_prescription_line_mixes) &&
    !count($prescription->_ref_prescription_lines_element)) {
    unset($sejours[$_sejour->_id]);
    continue;
  }

  $prescription->calculAllPlanifSysteme();
  $prescription->calculPlanSoin($dates, 0, null, null, null, true);

  // Si aucune ligne à afficher après calcul du plan de soins, on retire le séjour
  if (array_sum($prescription->_nb_lines_plan_soins) == 0) {
    unset ($sejours[$_sejour->_id]);
    continue;
  }

  $_sejour->loadRefCurrAffectation($now);

  if (!$service_id) {
    $_sejour->_ref_curr_affectation->loadRefService();
  }

  $_sejour->loadRefPatient();
  $_sejour->loadNDA();

  $initiales[$prescription->_id] = array();

  foreach ($prescription->_ref_prescription_lines as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadRefLogSignee();

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

  foreach ($prescription->_ref_prescription_line_mixes as $line) {
    $line->_prises_prevues_moment = array();
    $line->loadRefPraticien();
    $line->loadRefLogSignaturePrat();

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
              if (!isset($postes_by_date[$_date][$_hour])) {
                continue;
              }
              $key = $postes_by_date[$_date][$_hour];
              @$_line_item->_administrations_moment[$_date][$key["moment"]] += $_quantite;
            }
          }
        }
      }
    }
  }

  foreach ($prescription->_ref_prescription_lines_element as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadRefLogSignee();

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

// Dédoublonne les initiales
foreach ($initiales as $prescription_id => $_initiales) {
  foreach ($_initiales as $_date => $_initiales_by_date) {
    foreach ($_initiales_by_date as $_hour => $_initiales_by_hour) {
      $initiales[$prescription_id][$_date][$_hour] = array_unique($_initiales_by_hour);
    }
  }
}

$current_moment = $postes_by_date[$now_date][CMbDT::transform(null, CMbDT::time(), "%H")]["moment"];

$smarty = new CSmartyDP();

$smarty->assign("now"      , $now);
$smarty->assign("now_date" , $now_date);
$smarty->assign("sejours"  , $sejours);
if ($service_id) {
  $smarty->assign("service"  , $service);
}
$smarty->assign("period"   , $period);
$smarty->assign("dates"    , $dates);
$smarty->assign("moments"  , $moments);
$smarty->assign("mode_dupa", $mode_dupa);
$smarty->assign("initiales", $initiales);
$smarty->assign("current_moment", $current_moment);
$smarty->assign("moments_reverse", $moments_reverse);

$smarty->display("offline_plan_soins.tpl");