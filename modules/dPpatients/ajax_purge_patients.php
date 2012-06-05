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
  CAppUI::setMsg($_patient->_view, UI_MSG_OK);
  if($msg = $_patient->purge()) {
    CAppUI::setMsg($msg, UI_MSG_ALERT);
    $error++;
    continue;
  }
  CAppUI::setMsg("patient supprimé", UI_MSG_OK);
  $suppr++;
}

// Nombre de patients
$nb_patients = $patient->countList();

CAppUI::callbackAjax("repeatPurge");

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("resultsMsg" , CAppUI::getMsg());
$smarty->assign("suppr"      , $suppr);
$smarty->assign("error"      , $error);
$smarty->assign("nb_patients", $nb_patients);

$smarty->display("inc_purge_patients.tpl");

 
?>
