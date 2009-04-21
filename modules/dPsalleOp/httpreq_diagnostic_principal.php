<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$sejour_id = mbGetValueFromGetOrSession("sejour_id");
$modeDAS   = mbGetValueFromGetOrSession("modeDAS", 1);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadExtDiagnostics();
$sejour->loadRefDossierMedical();

//mbTrace($sejour->_ref_dossier_medical->_)

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("modeDAS", $modeDAS);

$smarty->display("inc_diagnostic_principal.tpl");

?>