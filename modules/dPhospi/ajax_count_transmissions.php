<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$sejour_id = CValue::get("sejour_id");

$transmission = new CTransmissionMedicale();
$where = array(
  "sejour_id" => "= '$sejour_id'"
);

$nb_trans_obs = $transmission->countList($where);

$observation = new CObservationMedicale();
$nb_trans_obs += $observation->countList($where);

$consultation = new CConsultation();
$where["annule"] = "= '0'";
$nb_trans_obs += $consultation->countList($where);
unset($where["annule"]);

// Compter les consultations d'anesthésie hors séjour
$sejour = new CSejour();
$sejour->load($sejour_id);
$patient = $sejour->loadRefPatient();
$consultations = $patient->loadRefsConsultations(array("annule" => "= '0'"));
CStoredObject::massCountBackRefs($consultations, "consult_anesth");
foreach ($consultations as $_consult) {
  if ($_consult->_count["consult_anesth"]) {
    $nb_trans_obs++;
  }
}

$constantes = new CConstantesMedicales();
$where = array(
  "context_class" => "= 'CSejour'",
  "context_id" => "= '$sejour_id'"
);

$nb_trans_obs += $constantes->countList($where);

echo $nb_trans_obs;
