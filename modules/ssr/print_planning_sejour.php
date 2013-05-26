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

CCanDo::checkRead();

$date = CValue::getOrSession("date", CMbDT::date());
$sejour_id = CValue::getOrSession("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefPatient();
$sejour->loadRefPraticien();
$bilan = $sejour->loadRefBilanSSR();
$technicien = $bilan->loadRefTechnicien();
$technicien->loadRefKine();

// Chargement des evenement SSR 
$monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
$sunday = CMbDT::date("next sunday", CMbDT::date("-1 DAY", $date));

$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$sejour_id'";
$where["debut"] = "BETWEEN '$monday 00:00:00' AND '$sunday 23:59:59'";

/** @var CEvenementSSR[] $evenements */
$evenements = $evenement_ssr->loadList($where);

$elements = array();
$intervenants = array();
foreach ($evenements as $_evenement) {
  $line = $_evenement->loadRefPrescriptionLineElement();
  $element = $line->_ref_element_prescription;
  $_evenement->loadRefTherapeute();
  $elements[$element->_id] = $element;
  $intervenants[$element->_id][$_evenement->therapeute_id] = $_evenement->_ref_therapeute;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("elements", $elements);
$smarty->assign("intervenants", $intervenants);
$smarty->assign("sejour", $sejour);
$smarty->display("print_planning_sejour.tpl");
