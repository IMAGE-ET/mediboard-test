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

// Tableau contenant le nom de tous les graphs
$graphs = array("constantes-medicales-ta","constantes-medicales-poids","constantes-medicales-taille","constantes-medicales-pouls",
                 "constantes-medicales-temperature","constantes-medicales-spo2","constantes-medicales-score_sensibilite",
                 "constantes-medicales-score_motricite","constantes-medicales-score_sedation","constantes-medicales-frequence_respiratoire",
                 "constantes-medicales-EVA");
                 
// Création du template
$smarty = new CSmartyDP();

$smarty->assign('constantes', $constantes);
$smarty->assign('latest_constantes', $latest_constantes);
$smarty->assign('graphs', $graphs);
$smarty->display('inc_form_edit_constantes_medicales.tpl');

?>