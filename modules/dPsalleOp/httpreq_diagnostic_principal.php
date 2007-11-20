<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$sejour_id = mbGetValueFromGetOrSession("sejour_id");

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefDiagnosticPrincipal();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("inc_diagnostic_principal.tpl");

?>