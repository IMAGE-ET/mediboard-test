<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$sejour_id = CValue::getOrSession("sejour_id");
$modeDAS   = CValue::getOrSession("modeDAS", 1);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefGHM();
$sejour->countExchanges();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour" , $sejour);
$smarty->display("inc_vw_GHM.tpl");

?>