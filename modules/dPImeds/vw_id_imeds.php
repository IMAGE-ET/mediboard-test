<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPImeds
* @version $Revision$
* @author Alexis Granger
* @author Fabien Menager
*/


$tag_login = "Imeds_login";
$tag_password = "Imeds_password";

$today = mbDateTime();

// Chargement des mediusers
$order = 'users.user_last_name ASC';
$ljoin["users"] = 'users.user_id = users_mediboard.user_id';
$mediuser = new CMediusers();
$mediusers = $mediuser->loadList(null, $order, null, null, $ljoin);

$tab = array();

// Parcours des utilisateurs et stockage des id externes
foreach ($mediusers as &$_mediuser) {
  $_mediuser->loadLastId400($tag_login);
  $tab[$_mediuser->_id] = array();
  $tab[$_mediuser->_id]["login"] = $_mediuser->_ref_last_id400;
  $_mediuser->loadLastId400($tag_password);
  $tab[$_mediuser->_id]["password"] = $_mediuser->_ref_last_id400; 
}

//mbTrace($tab[1]);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("tab"       , $tab);
$smarty->assign("mediusers" , $mediusers);
$smarty->assign("tag_login" , $tag_login);
$smarty->assign("tag_password" , $tag_password);
$smarty->assign("today" , $today);

$smarty->display("vw_id_imeds.tpl");

?>