<?php /* $*/

/**
* @package Mediboard
* @subpackage soins
* @version 
* @author 
*/
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("current", CValue::get("page",0));
$smarty->assign("step", CValue::getOrSession("step"));
$smarty->assign("total", CValue::getOrSession("total"));
$smarty->assign("change_page", CValue::getOrSession("change_page"));
$smarty->display("inc_pagination.tpl");
?>