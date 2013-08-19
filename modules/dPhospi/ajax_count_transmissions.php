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

$transmission = new CTransmissionMedicale;
$where = array();
$where["sejour_id"] = " = '$sejour_id'";

$nb_trans_obs = $transmission->countList($where);

$observation = new CObservationMedicale;
$nb_trans_obs += $observation->countList($where);

$consultation = new CConsultation;
$nb_trans_obs += $consultation->countList($where);

echo $nb_trans_obs;
