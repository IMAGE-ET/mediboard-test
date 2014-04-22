<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Board
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Turn a iso time to a string representation for iCal exports
 * 
 * @param time $time Time to convert
 * 
 * @return string
 */
function ical_time($time) {
  list($hour, $min) = explode(":", $time);
  return "{$hour}h{$min}";
}

CCanDo::checkRead();

// Récupération des paramètres
$prat_id      = CValue::get("prat_id");
$details      = CValue::get("details");
$export       = CValue::get("export");

$weeks_before = CValue::get("weeks_before");
$weeks_after  = CValue::get("weeks_after");

$date         = CValue::get("date", CMbDT::date());
$debut        = CMbDT::date("-$weeks_before week", $date);
$debut        = CMbDT::date("last sunday", $debut);
$fin          = CMbDT::date("+$weeks_after week", $date);
$fin          = CMbDT::date("next sunday", $fin);

// Liste des Salles
$salle = new CSalle();
/** @var CSalle[] $listSalles */
$listSalles = $salle->loadGroupList();

// Plages de Consultations
$plageConsult   = new CPlageconsult();
$plageOp        = new CPlageOp();
$listDays       = array();
/** @var CPlageconsult[] $plagesConsult */
$plagesConsult  = array();
$plagesOp       = array();
$plagesPerDayOp = array();

for ($i = 0; CMbDT::date("+$i day", $debut)!=$fin ; $i++) {
  $date             = CMbDT::date("+$i day", $debut);

  if (in_array("consult", $export)) {
    $where = array();
    $where["chir_id"] = "= '$prat_id'";
    $where["date"]    = "= '$date'";
    /** @var CPlageconsult[] $plagesPerDayConsult */
    $plagesPerDayConsult = $plageConsult->loadList($where);

    if ($details) {
      CMbObject::massLoadBackRefs($plagesPerDayConsult, "consultations");
    }

    foreach ($plagesPerDayConsult as $key => $plageConsult) {
      $plageConsult->countPatients();
      $plageConsult->loadFillRate();

      if ($details) {
        $plageConsult->loadRefsConsultations();
      }
    }
    $plagesConsult[$date] = $plagesPerDayConsult;
  }

  if (in_array("interv", $export)) {
    $where          = array();
    $where[]        = "chir_id = '$prat_id' OR anesth_id = '$prat_id'";
    $where["date"]  = "= '$date'";
    $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
    $plagesPerDayOp = $plageOp->loadList($where);

    $salles = CMbObject::massLoadFwdRef($plagesPerDayOp, "salle_id");
    CMbObject::massLoadFwdRef($salles, "bloc_id");
    CMbObject::massLoadBackRefs($plagesPerDayOp, "operations");

    foreach ($plagesPerDayOp as $key => $plage) {
      $plage->loadRefSalle();
      $plage->_ref_salle->loadRefBloc();
      $plage->_ref_salle->_ref_bloc->loadRefGroup();
      if ($details) {
        $plage->loadRefsOperations();
      }
      $plage->multicountOperations();
      $plagesOp[$plage->salle_id][$date] = $plagesPerDayOp[$key];
    }
  }
}

// Création du calendrier
$v = new CMbCalendar("Planning");

// Création des évènements plages de consultation
if (in_array("consult", $export)) {
  foreach ($plagesConsult as $plagesPerDay) {
    foreach ($plagesPerDay as $rdv) {
      $description = "$rdv->_nb_patients patient(s)";

      // Evènement détaillé
      if ($details) {
        foreach ($rdv->_ref_consultations as $consult) {
          /** @var CConsultation $consult */
          $when = ical_time($consult->heure);
          $patient = $consult->loadRefPatient();
          $what = $patient->_id ? "$patient->_civilite $patient->nom" : "Pause: $consult->motif"; 
          $description.= "\n$when: $what";
        }
      }
      
      $deb = "$rdv->date $rdv->debut";
      $fin = "$rdv->date $rdv->fin";
      $v->addEvent("", "Consultation - $rdv->libelle", $description, null, $rdv->_guid, $deb, $fin);
    }
  }
}

// Création des évènements plages d'interventions
if (in_array("interv", $export)) {
  foreach ($plagesOp as $salle) {
    foreach ($salle as $plagesPerDay => $rdv) {
      $description = "$rdv->_count_operations intervention(s)";
      // Evènement détaillé
      if ($details) {
        foreach ($rdv->_ref_operations as $op) {
          /** @var COperation $op */
          $op->loadRefPatient();
          $op->loadRefPlageOp();
          $duration = ical_time($op->temp_operation);
          $when     = ical_time(CMbDT::time($op->_datetime));
          $patient = $op->_ref_patient->_view;
          $description.= "\n$when: $patient (duree: $duration)";
        }
      }

      $deb = "$rdv->date $rdv->debut";
      $fin = "$rdv->date $rdv->fin";

      $location = $rdv->_ref_salle->_ref_bloc->_ref_group->_view;
      $v->addEvent($location, $rdv->_ref_salle->_view, $description, null, $rdv->_guid, $deb, $fin);
    }
  }
}

// Conversion du calendrier en champ texte
$str = $v->createCalendar();

//echo "<pre>$str</pre>"; return;

header("Content-disposition: attachment; filename=agenda.ics"); 
header("Content-Type: text/calendar; charset=".CApp::$encoding);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header("Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($str));
echo $str;
