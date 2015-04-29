<?php 

/**
 * $Id$
 *  
 * @category PMSI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$sejour_id = CValue::get("sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

$sejour->loadExtDiagnostics();
$sejour->loadDiagnosticsAssocies();

$sejour->loadRelPatient();
$sejour->_ref_patient->loadRefDossierMedical();

$rss = null;
$cim_das = array();
$cim_das_patient = array();
if (CModule::getActive("atih") && CAppUI::conf("dPpmsi use_cim_pmsi")) {
  $rss            = new CRSS();
  $rss->sejour_id = $sejour_id;
  $rss->loadMatchingObject();
  $rss->loadRefDiagnostics();
  $cim_dp = CCIM10::get($sejour->DP);
  $cim_dr = CCIM10::get($sejour->DR);

  $code = null;
  foreach ($sejour->_diagnostics_associes as $_da) {
    $code = CCIM10::get($_da);
    if ($code->type != 3) {
      $cim_das[preg_replace("/\./", "", $_da)] = true;
    }
    else {
      $cim_das[preg_replace("/\./", "", $_da)] = false;
    }
  }

  if ($sejour->_ref_patient->_ref_dossier_medical->_id) {
    foreach ($sejour->_ref_patient->_ref_dossier_medical->_ext_codes_cim as $_da) {
      $code = CCIM10::get($_da->code);
      if ($code->type != 3) {
        $cim_das_patient[preg_replace("/\./", "", $_da->code)] = true;
      }
      else {
        $cim_das_patient[preg_replace("/\./", "", $_da->code)] = false;
      }
    }
  }

  $sejour->_DP_state = false;
  if ($cim_dp->type == 0) {
    $sejour->_DP_state = true;
  }
  if ($cim_dr->type == 0 || ($cim_dr->type == 4)) {
    $sejour->_DR_state = true;
  }
}

$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);
$smarty->assign("patient", $sejour->_ref_patient);
$smarty->assign("rss", $rss);
$smarty->assign("cim_das", $cim_das);
$smarty->assign("cim_das_patient", $cim_das_patient);

$smarty->display("inc_diags_dossier.tpl");