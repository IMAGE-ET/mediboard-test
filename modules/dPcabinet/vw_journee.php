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

global $mode_maternite;

if (!isset($mode_maternite)) {
  $mode_maternite = false;
}

$mediuser = new CMediusers();
$mediuser->load(CAppUI::$instance->user_id);

//Initialisations des variables
$cabinet_id   = CValue::getOrSession("cabinet_id", $mediuser->function_id);
$date         = CValue::getOrSession("date", CMbDT::date());

$canceled       = CValue::getOrSession("canceled" , false);
$finished       = CValue::getOrSession("finished" , true);
$paid           = CValue::getOrSession("paid"     , true);
$empty          = CValue::getOrSession("empty"    , true);
$immediate      = CValue::getOrSession("immediate", true);
$mode_vue       = CValue::getOrSession("mode_vue" , "vertical");
$matin          = CValue::getOrSession("matin"    , true);
$apres_midi     = CValue::getOrSession("apres_midi", true);
$prats_selected = CValue::getOrSession("prats_selected");

$mode_urgence = CValue::get("mode_urgence", false);
$offline      = CValue::get("offline"     , false);

$hour         = CMbDT::time(null);
$board        = CValue::get("board", 1);
$boardItem    = CValue::get("boardItem", 1);
$consult      = new CConsultation();

$nb_anesth = 0;

$cabinets = CMediusers::loadFonctions(PERM_EDIT, null, "cabinet");

if ($mode_urgence) {
  $group = CGroups::loadCurrent();
  $cabinet_id = $group->service_urgences_id;
}

// Récupération de la liste des praticiens
$praticiens = array();
$cabinet = new CFunctions();

if ($mode_maternite) {
  $praticiens = $mediuser->loadListFromType(array("Sage Femme"));
}
elseif ($cabinet_id) {
  $praticiens = CConsultation::loadPraticiens(PERM_EDIT, $cabinet_id, null, true);
  $cabinet->load($cabinet_id);
}

// Praticiens disponibles ?
$all_prats = $praticiens;

if ($consult->_id) {
  $date = $consult->_ref_plageconsult->date;
  CValue::setSession("date", $date);
}

// Récupération des plages de consultation du jour et chargement des références
$listPlages = array();
$heure_limit_matin = CAppUI::conf("dPcabinet CPlageconsult hour_limit_matin");

foreach ($praticiens as $prat) {
  if ($prat->_user_type == 4) {
    $nb_anesth++;
  }
  $listPlage = new CPlageconsult();
  $where = array();
  $where["chir_id"] = "= '$prat->_id'";
  $where["date"] = "= '$date'";

  if ($matin && !$apres_midi) {
    // Que le matin
    $where["debut"] = "< '$heure_limit_matin:00:00'";
  }
  elseif ($apres_midi && !$matin) {
    // Que l'après-midi
    $where["debut"] = "> '$heure_limit_matin:00:00'";
  }
  elseif (!$matin && !$apres_midi) {
    // Ou rien
    $where["debut"] = "IS NULL";
  }

  $order = "debut";
  $listPlage = $listPlage->loadList($where, $order);
  if (!count($listPlage)) {
    unset($praticiens[$prat->_id]);
  }
  else {
    $listPlages[$prat->_id]["prat"] = $prat;
    $listPlages[$prat->_id]["plages"] = $listPlage;
    $listPlages[$prat->_id]["destinations"] = array();
  }
}

// Destinations : plages des autres praticiens
foreach ($listPlages as $key_prat => $infos_by_prat) {
  foreach ($listPlages as $key_other_prat => $infos_other_prat) {
    if ($infos_by_prat["prat"]->_id != $infos_other_prat["prat"]->_id) {
      foreach ($listPlages[$key_other_prat]["plages"] as $key_plage => $other_plage) {
        $listPlages[$key_prat]["destinations"][] = $other_plage;
      }
    }
  }
}


$nb_attente = 0;
$nb_a_venir = 0;
$patients_fetch = array();

$heure_min = null;

foreach ($listPlages as $key_prat => $infos_by_prat) {
  CMbObject::massLoadRefsNotes($infos_by_prat["plages"]);
  foreach ($infos_by_prat["plages"] as $_plage) {
    $_plage->loadRefsNotes();

    /** @var CPlageconsult $_plage */
    $_plage->_ref_chir = $infos_by_prat["prat"];
    $_plage->loadRefsConsultations($canceled, $finished);
    // Collection par référence susceptible d'être modifiée
    $consultations =& $_plage->_ref_consultations;
    
    if (!$paid || !$immediate) {
      $_consult = new CConsultation();
      foreach ($consultations as $_consult) {
        if (!$paid) {
          $_consult->loadRefsReglements();
          if ($_consult->valide == 1 && $_consult->_du_restant_patient == 0) {
            unset($consultations[$_consult->_id]);
          }
        }
        if (!$immediate && ($_consult->heure == CMbDT::time(null, $_consult->arrivee))
          && ($_consult->motif == "Consultation immédiate") && isset($consultations[$_consult->_id])) {
          unset($consultations[$_consult->_id]);
        }
      }
    }

    if (!count($consultations) && !$empty) {
      unset($listPlages[$key_prat]["plages"][$_plage->_id]);
      continue;
    }

    if (count($consultations) && $mode_vue == "horizontal") {
      $consultations = array_combine(range(0, count($consultations)-1), $consultations);
    }

    // Préchargement de masse sur les consultations
    CStoredObject::massLoadFwdRef($consultations, "patient_id");
    CStoredObject::massLoadFwdRef($consultations, "sejour_id");
    CStoredObject::massLoadFwdRef($consultations, "categorie_id");
    CMbObject::massCountDocItems($consultations);
    /** @var CConsultAnesth[] $dossiers */
    $dossiers = CStoredObject::massLoadBackRefs($consultations, "consult_anesth");
    $count = CMbObject::massCountDocItems($dossiers);

    // Chargement du détail des consultations
    foreach ($consultations as $_consultation) {
      if ($mode_urgence) {
        $_consultation->getType();
        if ($_consultation->_type == "urg") {
          unset($consultations[$_consultation->_id]);
          continue;
        }
      }

      if ($heure_min === null) {
        $heure_min = $_consultation->heure;
      }

      if ($_consultation->heure < $heure_min) {
        $heure_min = $_consultation->heure;
      }

      if ($_consultation->chrono < CConsultation::TERMINE) {
        $nb_a_venir++;
      }

      if ($_consultation->chrono == CConsultation::PATIENT_ARRIVE) {
        $nb_attente++;
      }

      $_consultation->loadRefSejour();
      $_consultation->loadRefPatient();
      $_consultation->loadRefCategorie();
      $_consultation->countDocItems();

      if ($offline && $_consultation->patient_id && !isset($patients_fetch[$_consultation->patient_id])) {
        $args = array(
          "object_guid" => $_consultation->_ref_patient->_guid,
          "ajax" => 1,
        );

        $patients_fetch[$_consultation->patient_id] = CApp::fetch("system", "httpreq_vw_complete_object", $args);
      }
    }
  }
  if (!count($listPlages[$key_prat]["plages"]) && !$empty) {
    unset($listPlages[$key_prat]);
    unset($praticiens[$key_prat]);
  }
}

$prat_available = $praticiens;
if (!$prats_selected) {
  $prats_selected = array_keys($praticiens);
}
else {
  $prats_selected = explode("-", $prats_selected);
}
$diff = array_diff(array_keys($praticiens), $prats_selected);
foreach($diff as $_key) {
  if (isset($praticiens[$_key])) {
    unset($praticiens[$_key]);
    unset($listPlages[$_key]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("offline"       , $offline);
$smarty->assign("cabinet_id"    , $cabinet_id);
$smarty->assign("cabinet"       , $cabinet);
$smarty->assign("patients_fetch", $patients_fetch);
$smarty->assign("consult"       , $consult);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("empty"         , $empty);
$smarty->assign("canceled"      , $canceled);
$smarty->assign("paid"          , $paid);
$smarty->assign("finished"      , $finished);
$smarty->assign("immediate"     , $immediate);
$smarty->assign("date"          , $date);
$smarty->assign("hour"          , $hour);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("praticiens_av" , $prat_available);
$smarty->assign("prats_selected", $prats_selected);
$smarty->assign("nb_anesth"     , $nb_anesth);
$smarty->assign("all_prats"     , $all_prats);
$smarty->assign("cabinets"      , $cabinets);
$smarty->assign("board"         , $board);
$smarty->assign("boardItem"     , $boardItem);
$smarty->assign("canCabinet"    , CModule::getCanDo("dPcabinet"));
$smarty->assign("mode_urgence"  , $mode_urgence);
$smarty->assign("mode_maternite", $mode_maternite);
$smarty->assign("nb_attente"    , $nb_attente);
$smarty->assign("nb_a_venir"    , $nb_a_venir);
$smarty->assign("mode_vue"      , $mode_vue);
$smarty->assign("heure_min"     , $heure_min);
$smarty->assign("matin"         , $matin);
$smarty->assign("apres_midi"    , $apres_midi);

$smarty->display("../../dPcabinet/templates/vw_journee.tpl");

