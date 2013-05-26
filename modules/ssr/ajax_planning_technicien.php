<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCando::checkRead();

$date         = CValue::getOrSession("date", CMbDT::date());
$kine_id      = CValue::getOrSession("kine_id");
$surveillance = CValue::getOrSession("surveillance");
$sejour_id    = CValue::get("sejour_id");
$height       = CValue::get("height");
$selectable   = CValue::get("selectable");
$large        = CValue::get("large");
$print        = CValue::get("print");

$kine = new CMediusers();
$kine->load($kine_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

$nb_days_planning = $sejour->_id ? 
  $sejour->getNbJourPlanning($date) : 
  CEvenementSSR::getNbJoursPlanning($kine_id, $date);
$planning = new CPlanningWeek($date, null, null, $nb_days_planning, $selectable, $height, $large, !$print);
$planning->title = $surveillance ?
  "Surveillance '$kine->_view'" :
  "Rééducateur '$kine->_view'";  

$planning->guid = $kine->_guid;
$planning->guid .= $surveillance ? "-surv" : "-tech";

// Chargement des evenement SSR 
$evenement = new CEvenementSSR();
$where = array();
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$where["therapeute_id"] = " = '$kine->_id'";
$where["equipement_id"] = $surveillance ? " IS NOT NULL" : " IS NULL";

/** @var CEvenementSSR[] $evenements */
$evenements = $evenement->loadList($where);

// Chargement des evenements SSR de "charge"
$where["equipement_id"] = $surveillance ? " IS NULL" : " IS NOT NULL";

/** @var CEvenementSSR[] $evenements_charge */
$evenements_charge = $evenement->loadList($where);
foreach ($evenements_charge as $_evenement) {
  $planning->addLoad($_evenement->debut, $_evenement->duree);
}

foreach ($evenements as $_evenement) {
  $important = $sejour_id ? ($_evenement->sejour_id == $sejour_id) : true;

  $sejour = $_evenement->loadRefSejour();
  $patient = $sejour->loadRefPatient();
  $equipement = $_evenement->loadRefEquipement();
  
  // Title  
  if ($_evenement->sejour_id) {
    $title = $patient->nom;
  }
  else {
    $_evenement->loadRefsEvenementsSeance();
    $title = count($_evenement->_ref_evenements_seance)." patient(s)";
  }
  if ($large) {
    $title .= " " . substr($patient->prenom, 0, 2) . ".";
  }
  if (!$sejour_id && $_evenement->remarque) {
    $title .= " - ".$_evenement->remarque;
  }
  
  // Color
  $therapeute = $_evenement->loadRefTherapeute();
  $function = $therapeute->loadRefFunction();
  $color = "#$function->color";
  
  // Classes
  $class= "";
  if (!$_evenement->countBackRefs("actes_cdarr") && !$_evenement->countBackRefs("actes_csarr")) {
    $class = "zero-actes";
  }
  
  $_sejour = $_evenement->_ref_sejour;
  if (!CMbRange::in($_evenement->debut, CMbDT::date($_sejour->entree), CMbDT::date("+1 DAY", $_sejour->sortie))) {
    $class = "disabled";
  }
  
  if ($_evenement->realise && $selectable) {
    $class = "realise";
  }

  if ($_evenement->annule && $selectable) {
    $class = "annule";
  }

  $css_classes = array();
  $css_classes[] = $class;
  $css_classes[] = $sejour->_guid;
  $css_classes[] = $equipement->_guid;

  // Title and color in prescription case
  if ($line = $_evenement->loadRefPrescriptionLineElement()) {
    $element = $line->_ref_element_prescription;
    $category = $element->loadRefCategory();
    $title .= $category->_view;
  
    // Color
    $color = $element->_color ? "#$element->_color" : null;
    
    // CSS Class
    $css_classes[] = $element->_guid; 
    $css_classes[] = $category->_guid;
  }

  $event = new CPlanningEvent(
    $_evenement->_guid,
    $_evenement->debut,
    $_evenement->duree,
    $title,
    $color,
    $important,
    $css_classes
  );
  $planning->addEvent($event);
}

$config = $surveillance ? CAppUI::conf("ssr occupation_surveillance") : CAppUI::conf("ssr occupation_technicien");

// Labels de charge sur la journée
$ds = CSQLDataSource::get("std");
$query = "SELECT SUM(duree) as total, DATE(debut) as date
  FROM evenement_ssr
  WHERE therapeute_id = '$kine->_id'
  AND debut BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
          
$query .= $surveillance ? "AND equipement_id IS NULL" : "AND equipement_id IS NOT NULL";
$query .= " GROUP BY DATE(debut)";
          
$duree_occupation = $ds->loadList($query);

foreach ($duree_occupation as $_occupation) {
  $duree_occupation = $_occupation["total"];
  $color = "#fff";
  if ($duree_occupation < $config["faible"]) {
    $color = "#8f8";
  }
  if ($duree_occupation > $config["eleve"]) {
    $color = "#f88";
  }
  if ($duree_occupation >= $config["faible"] && $duree_occupation <= $config["eleve"]) {
    $color = "#ff4";
  }
  $planning->addDayLabel($_occupation["date"], $_occupation["total"]." mn", null, $color);
}

// Congés du personnel
/** @var CPlageConge $_plage */
foreach ($kine->loadBackRefs("plages_conge") as $_plage) {
  $planning->addUnavailability($_plage->date_debut, $_plage->date_fin);
}

// Activité du compte
if ($kine->deb_activite) {
  $deb = CMbDT::date("-1 DAY", $kine->deb_activite);
  $planning->addUnavailability(CMbDT::date("-1 WEEK", $deb), $deb);
}

if ($kine->fin_activite) {
  $fin = CMbDT::date("+1 DAY", $kine->fin_activite);
  $planning->addUnavailability($fin, CMbDT::date("+1 WEEK", $fin));
}


// Heure courante
$planning->showNow();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->assign("date"    , CMbDT::dateTime());

$smarty->display("inc_vw_week.tpl");
