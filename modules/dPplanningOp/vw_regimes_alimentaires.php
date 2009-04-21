<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dialog;

$sejour = new CSejour;

$sejour->hormone_croissance = mbGetValueFromGet("hormone_croissance", 0);
$sejour->repas_sans_sel     = mbGetValueFromGet("repas_sans_sel"    , 0);
$sejour->repas_sans_porc    = mbGetValueFromGet("repas_sans_porc"   , 0);
$sejour->repas_diabete      = mbGetValueFromGet("repas_diabete"     , 0);
$sejour->repas_sans_residu  = mbGetValueFromGet("repas_sans_residu" , 0);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("vw_regimes_alimentaires.tpl");

?>