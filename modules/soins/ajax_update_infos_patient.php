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

$patient = new CPatient();
$patient->load($patient_id);

if ($patient->_id) {
  $constantes = $patient->loadRefConstantesMedicales(null, array('poids', 'taille'));
  echo json_encode(
    array(
      'poids' => $constantes[0]->poids . ' ' . CConstantesMedicales::$list_constantes['poids']['unit'],
      'taille' => $constantes[0]->taille . ' ' . CConstantesMedicales::$list_constantes['taille']['unit'],
      'imc' => $constantes[0]->_imc
    )
  );
}
CApp::rip();