<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Fabien Ménager
*/

global $AppUI, $can, $m;

$can->needsRead();

$patient_id    = mbGetValueFromGet('patient_id');
$context_class = mbGetValueFromGet('context_class');
$context_id    = mbGetValueFromGet('context_id');

// Récupération des constantes du patient
list($constantes, $dates) = CConstantesMedicales::getLatestFor($patient_id);
$constantes->context_class = $context_class;
$constantes->context_id = $context_id;
$constantes->updateFormFields();

$constantes_context = new CConstantesMedicales();
if ($context_class) {
  $constantes_context->patient_id    = $patient_id;
  $constantes_context->context_class = $context_class;
  $constantes_context->context_id    = $context_id;
  $constantes_context->loadMatchingObject();
  $constantes_context->updateFormFields();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes',         $constantes);
$smarty->assign('constantes_context', $constantes_context);
$smarty->assign('dates',              $dates);

$smarty->display('inc_vw_constantes_medicales.tpl');
?>