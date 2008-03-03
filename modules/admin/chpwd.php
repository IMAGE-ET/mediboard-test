<?php /* PUBLIC $Id$ */


global $AppUI, $can, $m;
$ds = CSQLDataSource::get("std");

$user = new CUser;
$where = array();
$where["user_id"]       = $ds->prepare("= %", $AppUI->user_id);
$user->loadObject($where);

$specs = $user->getSpecs();

$showMessage = dPgetParam($_REQUEST, "showMessage");

if ($showMessage) {
$message = 'Votre mot de passe ne correspond pas aux critères
    de sécurité de Mediboard. Nous vous serions gré de bien vouloir
    changer celui-ci afin qu\'il respecte ces critères. <br />
    La sécurité des informations de vos patients en dépendent.<br />
    Pour plus de précisions, veuillez vous conférer aux 
    <a href="http://mediboard.org/public/tiki-index.php?page=Recommandations+de+la+CNIL" target="_blank">
    recommandations de la CNIL</a>.<br />
	Merci';
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