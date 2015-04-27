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

$lit_id                  = CValue::post("lit_id");
$service_sortie_id       = CValue::post("service_sortie_id");
$mode_sortie             = CValue::post("mode_sortie");
$type                    = CValue::post("type");
$entree_preparee_trigger = CValue::post("_entree_preparee_trigger");
$sejour_id               = CValue::post("sejour_id");

$create_affectation = CAppUI::conf("urgences create_affectation");

$sejour = new CSejour();
$sejour->load($sejour_id);
$curr_affectation = $sejour->loadRefCurrAffectation();

$rpu = $sejour->loadRefRPU();

// Pour un séjour ayant comme mode de sortie urgence:
if ($create_affectation && $mode_sortie == "mutation" &&
    $rpu && $rpu->_id && ($curr_affectation->lit_id != $lit_id || $sejour->service_sortie_id != $service_sortie_id)
) {
  if ($rpu->mutation_sejour_id) {
    $sejour_id = $sejour->_ref_rpu->mutation_sejour_id;
  }

  $sejour_hospit = new CSejour();
  $sejour_hospit->load($sejour_id);

  // Création de l'affectation d'hospitalisation
  $affectation_hospit = new CAffectation();
  $affectation_hospit->entree     = CMbDT::dateTime();
  $affectation_hospit->lit_id     = $lit_id;
  $affectation_hospit->service_id = $service_sortie_id;

  // Mutation en provenance des urgences
  $affectation_hospit->_mutation_urg = true;

  $sejour_hospit->forceAffectation($affectation_hospit);
}

// Lancement des formulaires automatiques sur le champ entrée préparée
if ($sejour->_id && $entree_preparee_trigger && CModule::getActive("forms")) {
  $ex_class_events = CExClassEvent::getForObject($sejour, "preparation_entree_auto", "required");
  echo CExClassEvent::getJStrigger($ex_class_events);
}

$do = new CDoObjectAddEdit("CSejour");
$do->doIt();