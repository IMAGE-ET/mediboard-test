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

$date = CValue::getOrSession("date", CMbDT::date());
$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$equipement = new CEquipement;
$equipement->load(CValue::get("equipement_id"));

if (!$equipement->visualisable) {
  echo "<div class='small-info'>L'équipement <strong>$equipement->_view</strong> n'est pas visualisable</div>";
  CApp::rip();
}

$nb_days_planning = $sejour->getNbJourPlanning($date);
$planning = new CPlanningWeek($date, null, null, $nb_days_planning, false, "auto", false, true);
$planning->title = "Equipement '$equipement->_view'";
$planning->guid = $equipement->_guid;

// Chargement des evenement SSR
$evenement = new CEvenementSSR();
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$where["equipement_id"] = " = '$equipement->_id'";

/** @var CEvenementSSR[] $evenements */
$evenements = $evenement->loadList($where);
foreach ($evenements as $_evenement) {
  $important = !$sejour_id || $_evenement->sejour_id == $sejour_id;

  $sejour = $_evenement->loadRefSejour();
  $patient = $sejour->loadRefPatient();

  // Title
  $therapeute = $_evenement->loadRefTherapeute();
  $title = ucfirst(strtolower($patient->nom))."  $therapeute->_shortview";

  // Color
  $function = $therapeute->loadRefFunction();
  $color = "#$function->color";

  // Classes
  $css_classes = array();

  // Prescription case
  if ($line = $_evenement->loadRefPrescriptionLineElement()) {
    $element = $line->_ref_element_prescription;
    $category = $element->loadRefCategory();
    $title = $category->_view;

    // Color
    $color = $element->_color ? "#$element->_color" : null;

    // CSS Class
    $css_classes[] = $element->_guid;
  }

  $event = new CPlanningEvent($_evenement->_guid, $_evenement->debut, $_evenement->duree, $title, $color, $important, $css_classes);
  $planning->addEvent($event);
}

$planning->showNow();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");
