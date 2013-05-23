<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
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
  $where = array();
  $where["chir_id"] = "= '$prat_id'";
  $date             = CMbDT::date("+$i day", $debut);
  $where["date"]    = "= '$date'";

  /** @var CPlageconsult[] $plagesPerDayConsult */
  $plagesPerDayConsult = $plageConsult->loadList($where);
  $nb_oper        = 0;
  $where          = array();
  $where[]        = "chir_id = '$prat_id' OR anesth_id = '$prat_id'";
  $where["date"]  = "= '$date'";
  
  foreach ($listSalles as $salle) {
    $where["salle_id"] = "= '$salle->_id'";
    $plagesPerDayOp[$salle->_id] = $plageOp->loadList($where);
    $nb_oper = $nb_oper + count($plagesPerDayOp[$salle->_id]);
  }
  
  foreach ($plagesPerDayConsult as $plageConsult) {
    $plageConsult->countPatients();
  }
  
  if (in_array("consult", $export) && count($plagesPerDayConsult)) {
    foreach ($plagesPerDayConsult as $plageConsult) {
      $plageConsult->loadFillRate();
      
      if ($details) {
        $plageConsult->loadRefsConsultations();
      }
    }
    
    $plagesConsult[$date] = $plagesPerDayConsult;
  }
  
  if (in_array("interv", $export) && $nb_oper) {
    foreach ($plagesPerDayOp as $key => $listPlages) {
      /** @var CPlageOp[] $listPlages */
      if (!count($listPlages)) {
        unset($plagesPerDayOp[$key]);
        continue;
      }

      foreach ($listPlages as $keyPlage => $plage) {
        $plage->loadRefSalle();
        $plage->_ref_salle->loadRefBloc();
        $plage->_ref_salle->_ref_bloc->loadRefGroup();
        if ($details) {
          $plage->loadRefsOperations();
        }

        $plage->getNbOperations();
      }
        
      $plagesOp[$key][$date] = $plagesPerDayOp[$key];
    }
  }
}

// Création du calendrier
$v = new CMbCalendar("Planning");

// Création des évènements plages de consultation
if (in_array("consult", $export)) {
  foreach ($plagesConsult as $curr_day => $plagesPerDay) {
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
    foreach ($salle as $curr_day => $plagesPerDay) {
      foreach ($plagesPerDay as $rdv) {
        $description = "$rdv->_nb_operations intervention(s)";
        
        // Evènement détaillé
        if ($details) {
          foreach ($rdv->_ref_operations as $op) {
            /** @var COperation $op */
            $op->loadComplete();
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
