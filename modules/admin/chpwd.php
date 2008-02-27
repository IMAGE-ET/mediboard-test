<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");

$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$user->loadObject($where);

$specs = $user->getSpecs();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"    , $user    );
$smarty->assign("specs"   , $specs   );
$smarty->display("change_password.tpl");
?>