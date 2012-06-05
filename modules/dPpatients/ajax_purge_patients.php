<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision: 7138 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::Admin();

$patient = new CPatient();

// Supression de patients
$suppr = 0;
$error = 0;
$qte = CValue::get("qte", 1);
$listPatients = $patient->loadList(null, null, $qte);

foreach($listPatients as $_patient) {
  if($msg = $_patient->purge()) {
    //CAppUI::displayAjaxMsg($msg, UI_MSG_ERROR);
    $error++;
  } else {
    $suppr++;
  }
}

// Nombre de patients
$nb_patients = $patient->countList();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("suppr"      , $suppr);
$smarty->assign("error"      , $error);
$smarty->assign("nb_patients", $nb_patients);

$smarty->display("inc_purge_patients.tpl");

 
?>
