<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

$consult_id = CValue::get("consult_id");
$sejour_id  = CValue::get("sejour_id");

$consult = new CConsultation;
$consult->load($consult_id);
$consult->canEdit();

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

$user = CMediusers::get();
$user->isAnesth();
$user->isPraticien();

$smarty = new CSmartyDP;

$smarty->assign("consult"        , $consult);
$smarty->assign("consult_anesth" , $consult_anesth);
$smarty->assign("patient"        , $patient);
$smarty->assign("_is_anesth"     , $user->isAnesth());
$smarty->assign("antecedent"     , new CAntecedent);
$smarty->assign("traitement"     , new CTraitement);
if (CModule::getActive("dPprescription")) {
  $smarty->assign("line"           , new CPrescriptionLineMedicament);
}
$smarty->assign("userSel"        , $user);
$smarty->assign("sejour_id"      , $sejour_id);
$smarty->assign("today"          , CMbDT::date());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));

if ($consult_anesth->_id) {
  $consult_anesth->loadRefOperation();
  $consult_anesth->loadRefsTechniques();
  $anesth = new CTypeAnesth;
  $order = "name";
  $anesth = $anesth->loadList(null, $order);
  
  $smarty->assign("list_etat_dents", $list_etat_dents);
  $smarty->assign("mins"           , range(0, 15-1, 1));
  $smarty->assign("secs"           , range(0, 60-1, 1));
  $smarty->assign("examComp"       , new CExamComp);
  $smarty->assign("techniquesComp" , new CTechniqueComp);
  $smarty->assign("anesth"         , $anesth);
  $smarty->assign("view_prescription", 0);
  
  if (CAppUI::conf("dPcabinet CConsultAnesth show_facteurs_risque")) {
    $sejour = new CSejour;
    $sejour->load($sejour_id);
    $sejour->loadRefDossierMedical();
    $smarty->assign("sejour"       , $sejour);
  }
  
  if ($consult_anesth->operation_id) {
    $listAnesths = new CMediusers;
    $listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);
    $smarty->assign("listAnesths", $listAnesths);
  }
}

$smarty->display("inc_short_consult.tpl");
