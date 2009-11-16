<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

$sejour = new CSejour;

$sejour->hormone_croissance = CValue::get("hormone_croissance", 0);
$sejour->repas_sans_sel     = CValue::get("repas_sans_sel"    , 0);
$sejour->repas_sans_porc    = CValue::get("repas_sans_porc"   , 0);
$sejour->repas_diabete      = CValue::get("repas_diabete"     , 0);
$sejour->repas_sans_residu  = CValue::get("repas_sans_residu" , 0);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("vw_regimes_alimentaires.tpl");

?>