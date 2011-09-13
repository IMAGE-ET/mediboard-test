<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$consult_id = CValue::get("consult_id");

$consult = new CConsultation;
$consult->load($consult_id);

if (!$consult->_id) {
  CAppUI::stepAjax(CAppUI::tr("CConsultation.none"));
  CApp::rip();
}

$patient = $consult->loadRefPatient();
$dossier_medical = $patient->loadRefDossierMedical();
$consult_anesth = $consult->loadRefConsultAnesth();

$list_etat_dents = array();

if ($dossier_medical->_id) {
  $etat_dents = $dossier_medical->loadRefsEtatsDents();
  foreach ($etat_dents as $etat) {
    $list_etat_dents[$etat->dent] = $etat->etat;
  }
}

$smarty = new CSmartyDP;

$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult_anesth);
$smarty->assign("patient"        , $patient);

if ($consult_anesth->_id) {
  $consult_anesth->loadRefOperation();
  $anesth = new CTypeAnesth;
  $orderanesth = "name";
  $anesth = $anesth->loadList(null,$orderanesth);
  
  $smarty->assign("list_etat_dents", $list_etat_dents);
  $smarty->assign("mins"           , range(0, 15-1, 1));
  $smarty->assign("secs"           , range(0, 60-1, 1));
  $smarty->assign("examComp"       , new CExamComp);
  $smarty->assign("techniquesComp" , new CTechniqueComp);
  $smarty->assign("anesth"         , $anesth);
  $smarty->assign("view_prescription", 0);
  $smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
}

$smarty->display("inc_short_consult.tpl");

?>