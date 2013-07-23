<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if ($praticien_id = CValue::post("praticien_id")) {
  CValue::setSession("praticien_id", $praticien_id);
}
$lit_id            = CValue::post("lit_id");
$service_sortie_id = CValue::post("service_sortie_id");

// Pour un séjour ayant comme mode de sortie urgence:
if (CValue::post("mode_sortie") == "mutation" && CValue::post("type") == "urg" && ($lit_id || $service_sortie_id)) {
  $sejour_id = CValue::post("sejour_id");

  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $rpu = $sejour->loadRefRPU();

  if ($rpu->mutation_sejour_id) {
    $sejour_id = $sejour->_ref_rpu->mutation_sejour_id;
  }

  // Rercherche de l'affectation d'urgences, et création si on en a pas
  // Il s'agit de l'affectation qui a lieu entre le début du séjour et la mutation
  $affectation_urg = new CAffectation();
  $affectation_urg->entree    = $sejour->entree;
  $affectation_urg->loadMatchingObject();
  if (!$affectation_urg->_id) {
    $affectation_urg->sortie    = CMbDT::dateTime();
    $affectation_urg->sejour_id = $sejour_id;
    $affectation_urg->lit_id    = $rpu->box_id;
    if (!$rpu->box_id) {
      $services = CService::loadServicesUrgence();
      $affectation_urg->service_id = reset($services)->_id;
      $affectation_urg->store();
    }
  }

  // Création de l'affectation d'hospitalisation
  $affectation_hospit = new CAffectation();
  $affectation_hospit->entree     = $affectation_urg->sortie;
  $affectation_hospit->loadMatchingObject();
  $affectation_hospit->lit_id     = $lit_id;
  $affectation_hospit->service_id = $service_sortie_id;
  $affectation_hospit->sejour_id  = $sejour_id;
  $affectation_hospit->sortie     = $sejour->sortie_prevue;
  $affectation_hospit->store();
}

$do = new CDoObjectAddEdit("CSejour");
$do->doIt();