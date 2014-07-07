<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::checkRead();

$patient_id = CValue::post('patient_id');
$vitale_data = json_decode(stripslashes(CValue::post('vitale_data')));

$update_patient_fields = array(
  'administrative' => array(
    'nom',
    'prenom',
    'sexe',
    'naissance',
    'rang_naissance',
    'matricule',
    'adresse',
    'cp',
    'ville',
  ),
  'assure' => array(
    'assure_nom',
    'assure_prenom',
    'assure_naissance',
    'assure_sexe',
    'assure_matricule',
    'assure_adresse',
    'assure_cp',
    'assure_ville',
    'code_exo',
    'code_regime',
    'caisse_gest',
    'centre_gest',
    'code_gestion',
    'deb_amo',
    'fin_amo',
    'cmu',
  )
);

$patient_vitale = new CPatient();
foreach ($vitale_data as $_field => $_data) {
  $patient_vitale->$_field = $_data;
}

$patient_mb = new CPatient();
$patient_mb->load($patient_id);

$patient_fields = array();
if ($patient_mb->_id) {
  foreach ($update_patient_fields as $_type => $_fields) {
    $patient_fields[$_type] = array();
    foreach ($_fields as $_field) {
      $patient_fields[$_type][$_field] = strtolower($patient_mb->$_field) == strtolower($patient_vitale->$_field);
    }
  }
}

$smarty = new CSmartyDP('modules/dPpatients');
$smarty->assign('patient_vitale', $patient_vitale);
$smarty->assign('patient_mb', $patient_mb);
$smarty->assign('fields', $patient_fields);
$smarty->assign('patient_id', $patient_id);
$smarty->display('update_patient_from_vitale.tpl');