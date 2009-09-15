<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $can;

$const_id = mbGetValueFromGet('const_id', 0);
$context_guid = mbGetValueFromGet('context_guid');
$readonly = mbGetValueFromGet('readonly');

$constantes = new CConstantesMedicales();
$constantes->load($const_id);
$constantes->loadRefContext();
$constantes->loadRefPatient();

$latest_constantes = CConstantesMedicales::getLatestFor($constantes->patient_id);

// Tableau contenant le nom de tous les graphs
$graphs = array();
foreach(CConstantesMedicales::$list_constantes as $cst) {
  $graphs[] = "constantes-medicales-$cst";
}
                 
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes', $constantes);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('context_guid', $context_guid);
$smarty->assign('graphs', $graphs);
$smarty->assign('readonly', $readonly);
$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>