<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $can;

$const_id     = CValue::get('const_id', 0);
$context_guid = CValue::get('context_guid');
$patient_id   = CValue::get('patient_id');
$readonly     = CValue::get('readonly');
$selection    = CValue::get('selection');

if (!$selection) {
  //$selection = CConstantesMedicales::$list_constantes;
  $conf_constantes = explode("|", CAppUI::conf("dPpatients CConstantesMedicales important_constantes"));
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, array_flip($conf_constantes));
}
else {
  $selection_flip = array_flip($selection);
  $selection = array_intersect_key(CConstantesMedicales::$list_constantes, $selection_flip);
}

$constantes = new CConstantesMedicales();
$constantes->load($const_id);
$constantes->loadRefContext();
$constantes->loadRefPatient();

$patient_id = $constantes->patient_id ? $constantes->patient_id : $patient_id;
$latest_constantes = CConstantesMedicales::getLatestFor($patient_id);
                 
// Création du template
$smarty = new CSmartyDP("modules/dPhospi");

$smarty->assign('constantes', $constantes);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('context_guid', $context_guid);
$smarty->assign('readonly', $readonly);
$smarty->assign('selection', $selection);
$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>