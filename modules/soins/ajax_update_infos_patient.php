<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

$patient_id = CValue::get('patient_id', 0);
$context_guid = CValue::get('context_guid', 0);

$patient = new CPatient();
$patient->load($patient_id);
$context = CMbObject::loadFromGuid($context_guid);

if ($patient->_id && $context->_id) {
  $constantes = $patient->loadRefConstantesMedicales(null, array('poids', 'taille'), $context);
  echo json_encode(array('poids' => $constantes[0]->poids, 'taille' => $constantes[0]->taille, 'imc' => $constantes[0]->_imc));
}
CApp::rip();