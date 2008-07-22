<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Fabien Ménager
*/

global $AppUI, $can, $m;

$const_id = mbGetValueFromGet('const_id', 0);

$constantes = new CConstantesMedicales();
$constantes->load($const_id);

$latest_constantes = CConstantesMedicales::getLatestFor($constantes->patient_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes', $constantes);
$smarty->assign('latest_constantes', $latest_constantes);

$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>