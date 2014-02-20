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
$date         = CValue::get("date", CMbDT::dateTime());
$mode_dupa    = CValue::get("mode_dupa", 0);

$sejours = array();

$period = CAppUI::conf("soins plan_soins period" , CGroups::loadCurrent()->_guid);

$datetime_min = "$date 00:00:00";
$datetime_max = CMbDT::date("+ $period days", $date) . " 23:59:59";

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

$date_temp = $date;
$date_max = CMbDT::date($datetime_max);

CPrescription::$mode_plan_soins = true;
CPrescription::$show_inactive = false;

while ($date_temp < $date_max) {
  $dates[$date_temp] = $date_temp;
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

$postes = array(
  "Poste 1" => "08",
  "Poste 2" => "12",
  "Poste 3" => "18",
  "Poste 4" => "22");

$dates_postes = array();

$dates_postes[CMbDT::date("-1 day", $date)] = reset(CAdministration::getTimingPlanSoins(CMbDT::date("-1 day", $date), $postes, null, 1, 2));

foreach ($dates as $_date) {
  $dates_postes[$_date] = reset(CAdministration::getTimingPlanSoins($_date, $postes, null, 1, 2));
}

$dates_postes[CMbDT::date("+1 day", $date)] = reset(CAdministration::getTimingPlanSoins(CMbDT::date("+1 day", $date), $postes, null, 1, 2));

$postes_by_date = array();
$moments = array(
  "poste-1" => "matin",
  "poste-2" => "midi" ,
  "poste-3" => "soir" ,
  "poste-4" => "nuit"
);

foreach ($dates_postes as $day => $_dates_postes) {
  foreach ($_dates_postes as $poste => $_dates) {
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

  $_sejour->loadRefPatient();
  $_sejour->loadNDA();

  foreach ($prescription->_ref_prescription_lines as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadFirstLog();
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
          }
        }
      }
    }

    if (count($line->_administrations)) {
      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            if ($_hour == "list") {
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
    $line->loadFirstLog();
    $line->loadRefLogSignaturePrat();

    if (count($line->_prises_prevues)) {
      foreach ($line->_prises_prevues as $_date => $_prises_by_date) {
        foreach ($_prises_by_date as $_hour => $_prise) {
          $key = $postes_by_date[$_date][$_hour];
          if (isset($_prise["real_hour"])) {
            foreach ($_prise["real_hour"] as $_real_hour) {
              @$line->_prises_prevues_moment[$key["day"]][$key["moment"]]["real_hour"][] = $_real_hour;
            }
          }
        }
      }
    }
  }

  foreach ($prescription->_ref_prescription_lines_element as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    $line->loadFirstLog();
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
          }
        }
      }
    }

    if (count($line->_administrations)) {
      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            if ($_hour == "list") {
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

$smarty = new CSmartyDP();

$smarty->assign("now"      , CMbDT::dateTime());
$smarty->assign("sejours"  , $sejours);
if ($service_id) {
  $smarty->assign("service"  , $service);
}
$smarty->assign("period"   , $period);
$smarty->assign("dates"    , $dates);
$smarty->assign("moments"  , $moments);
$smarty->assign("mode_dupa", $mode_dupa);

$smarty->display("offline_plan_soins.tpl");