<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Sébastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
    $AppUI->redirect("m=system&a=access_denied");
}

$list_5 = mbArrayCreateRange(1,5, true);
$list_10 = mbArrayCreateRange(1,10, true);
$list_20 = mbArrayCreateRange(1,20, true);
$list_14 = mbArrayCreateRange(1,14, true);
$list_50 = mbArrayCreateRange(1,50, true);

$smarty = new CSmartyDP(1);

$smarty->assign("list_5", $list_5);
$smarty->assign("list_10", $list_10);
$smarty->assign("list_20", $list_20);
$smarty->assign("list_14", $list_14);
$smarty->assign("list_50", $list_50);

$smarty->display("echantillonnage.tpl");
?>