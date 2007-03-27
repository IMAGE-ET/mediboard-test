<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;

$user = new CUser;

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user", $user);
$smarty->display("change_password.tpl");
?>