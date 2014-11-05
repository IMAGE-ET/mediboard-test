<?php
/**
 * View import
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

// Nombre de patients
$patient = new CPatient();
$nb_patients = $patient->countList();

// import
$patient = new CPatient();
$patient_specs = CModelObjectFieldDescription::getSpecList($patient);
CModelObjectFieldDescription::addBefore($patient->_specs["_IPP"], $patient_specs);

// import temp file
$start_pat = 0;
$count_pat = 20;
if ($data = @file_get_contents(CAppUI::conf("root_dir")."/tmp/import_patient.txt", "r")) {
  $nb = explode(";", $data);
  $start_pat = $nb[0];
  $count_pat = $nb[1];
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("count_pat"    , $count_pat);
$smarty->assign("start_pat"    , $start_pat);
$smarty->assign("patient_specs", $patient_specs);
$smarty->assign("nb_patients"  , $nb_patients);

$smarty->display("inc_vw_import.tpl");
