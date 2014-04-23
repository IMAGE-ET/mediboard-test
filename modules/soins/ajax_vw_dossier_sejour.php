<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id    = CValue::get("sejour_id");
$date         = CValue::get("date");
$default_tab  = CValue::get("default_tab", "dossier_traitement");
$popup        = CValue::get("popup", 0);
$modal        = CValue::get("modal", 0);
$operation_id = CValue::get("operation_id");
$mode_pharma  = CValue::get("mode_pharma", 0);

$sejour = new CSejour();
$sejour->load($sejour_id);
CPrescription::$_load_lite = true;
$sejour->loadRefPraticien();
$sejour->loadRefPrescriptionSejour();
$sejour->loadJourOp($date);
$sejour->_ref_prescription_sejour->loadJourOp($date);
$sejour->_ref_prescription_sejour->loadRefCurrentPraticien();
$patient = $sejour->loadRefPatient();
$sejour->loadRefsOperations();
$sejour->loadRefCurrAffectation();
$patient->loadRefPhotoIdentite();
$patient->loadRefDossierMedical();
$patient->loadRefConstantesMedicales(null, array("poids", "taille"));
if ($patient->_ref_dossier_medical->_id) {
  $patient->_ref_dossier_medical->loadRefsAllergies();
  $patient->_ref_dossier_medical->loadRefsAntecedents();
  $patient->_ref_dossier_medical->countAntecedents();
  $patient->_ref_dossier_medical->countAllergies();
}
$operation = new COperation();
if ($operation->load($operation_id)) {
  $operation->loadRefPlageOp();
  $operation->_ref_anesth->loadRefFunction();
}
$is_praticien = CAppUI::$user->isPraticien();

CPrescription::$_load_lite = false;

$smarty = new CSmartyDP();

$smarty->assign("sejour"          , $sejour);
$smarty->assign("patient"         , $patient);
$smarty->assign("date"            , $date);
$smarty->assign("default_tab"     , $default_tab);
$smarty->assign("popup"           , $popup);
$smarty->assign("modal"           , $modal);
$smarty->assign("operation_id"    , $operation_id);
$smarty->assign("mode_pharma"     , $mode_pharma);
$smarty->assign("is_praticien"    , $is_praticien);
$smarty->assign("mode_protocole"  , CValue::getOrSession("mode_protocole", 0));
$smarty->assign("operation", $operation);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("isPrescriptionInstalled" , CModule::getActive("dPprescription"));

$smarty->display("inc_dossier_sejour.tpl");
