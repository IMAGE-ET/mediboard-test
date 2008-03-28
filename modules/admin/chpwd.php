<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");

$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$user->loadObject($where);

$specs = $user->getSpecs();

$forceChange = dPgetParam($_REQUEST, "forceChange");

if ($forceChange) {
$message = '<strong>Votre mot de passe ne correspond pas aux critères de sécurité de Mediboard</strong>. Vous ne pourrez
            pas accéder à Mediboard tant que vous ne l\'aurez pas changé afin qu\'il respecte ces critères.
            La sécurité des informations de vos patients en dépend.<br />
            Pour plus de précisions, veuillez vous référer aux 
            <a href="http://mediboard.org/public/tiki-index.php?page=Recommandations+de+la+CNIL" target="_blank">
            recommandations de la CNIL</a>.<br />';
} else {
  $message = null;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("user"    , $user    );
$smarty->assign("specs"   , $specs   );
$smarty->assign("message" , $message );
$smarty->display("change_password.tpl");
?>