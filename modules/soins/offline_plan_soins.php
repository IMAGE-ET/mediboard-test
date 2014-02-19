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

$service_id = CValue::get("service_id");
$date       = CValue::get("date", CMbDT::date());

$service = new CService();
$service->load($service_id);

$period = CAppUI::conf("soins plan_soins period" , CGroups::loadCurrent()->_guid);

$datetime_min = "$date 00:00:00";
$datetime_max = CMbDT::date("+ $period days", $date) . " 23:59:59";
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

function setMoment($hour) {
  if ($hour >= 22 || $hour <= 7) {
    return "nuit";
  }
  elseif ($hour >= 8 && $hour <= 11) {
    return "matin";
  }
  elseif ($hour >= 12 && $hour <= 17) {
    return "midi";
  }
  elseif ($hour >= 18 && $hour <= 21) {
    return "soir";
  }
}

/** @var $_sejour CSejour */
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPatient();
  $_sejour->loadNDA();

  $prescription = $_sejour->loadRefPrescriptionSejour();
  $prescription->calculAllPlanifSysteme();

  $prescription->loadRefsLinesMedByCat("1", "1");
  $prescription->loadRefsPrescriptionLineMixes(null, "1");
  $prescription->loadRefsLinesElementByCat("1", "1");

  $prescription->calculPlanSoin($dates, 0, null, null, null, true, "");

  foreach ($prescription->_ref_prescription_lines as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    if (count($line->_quantity_by_date)) {
      foreach ($line->_quantity_by_date as $_unite => $_quantites_by_unite) {
        foreach ($_quantites_by_unite as $_date => $_quantites_by_hour) {
          if (!is_array($_quantites_by_hour["quantites"])) {
            continue;
          }
          foreach ($_quantites_by_hour["quantites"] as $_hour => $_quantite) {
            $hour = intval($_hour);
            $key = setMoment($hour);
            @$line->_quantity_by_date_moment[$_unite][$_date][$key]["total"] += $_quantite["total"];
          }
        }
      }
    }

    if (count($line->_administrations)) {
      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            $hour = intval($_hour);
            $key = setMoment($hour);
            @$line->_administrations_moment[$_key][$_date][$key]['quantite'] += $_administrations_by_hour['quantite'];
            @$line->_administrations_moment[$_key][$_date][$key]['quantite_planifiee'] += $_administrations_by_hour['quantite_planifiee'];
          }
        }
      }
    }
  }

  foreach ($prescription->_ref_prescription_line_mixes as $line) {
    $line->loadRefPraticien();
    $line->loadRefsLines();
    //mbTrace($line->_prises_prevues);
  }

  foreach ($prescription->_ref_prescription_lines_element as $line) {
    $line->_quantity_by_date_moment = array();
    $line->_administrations_moment  = array();

    if (count($line->_quantity_by_date)) {
      foreach ($line->_quantity_by_date as $_unite => $_quantites_by_unite) {
        foreach ($_quantites_by_unite as $_date => $_quantites_by_hour) {
          if (!is_array($_quantites_by_hour["quantites"])) {
            continue;
          }
          foreach ($_quantites_by_hour["quantites"] as $_hour => $_quantite) {
            $hour = intval($_hour);
            $key = setMoment($hour);
            @$line->_quantity_by_date_moment[$_unite][$_date][$key]["total"] += $_quantite["total"];
          }
        }
      }
    }

    if (count($line->_administrations)) {
      foreach ($line->_administrations as $_key => $_administrations) {
        foreach ($_administrations as $_date => $_administrations_by_date) {
          foreach ($_administrations_by_date as $_hour => $_administrations_by_hour) {
            $hour = intval($_hour);
            $key = setMoment($hour);
            @$line->_administrations_moment[$_key][$_date][$key]['quantite'] += $_administrations_by_hour['quantite'];
            @$line->_administrations_moment[$_key][$_date][$key]['quantite_planifiee'] += $_administrations_by_hour['quantite_planifiee'];
          }
        }
      }
    }

  }
}

$moments = array("matin", "midi", "soir", "nuit");

$smarty = new CSmartyDP();

$smarty->assign("now"    , CMbDT::dateTime());
$smarty->assign("sejours", $sejours);
$smarty->assign("service", $service);
$smarty->assign("period" , $period);
$smarty->assign("dates"  , $dates);
$smarty->assign("moments", $moments);

$smarty->display("offline_plan_soins.tpl");