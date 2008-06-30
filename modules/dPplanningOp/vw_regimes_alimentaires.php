<?php /* $Id: vw_protocoles.php 3033 2007-12-06 10:40:17Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 3033 $
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