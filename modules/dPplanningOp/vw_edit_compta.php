<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI, $can, $m, $tab;

$user = new CMediusers();
$user->load($AppUI->user_id);

// Si ni praticien ni admin, redirect
if(!$user->isPraticien() && $can->needsAdmin()){
  CAppUI::redirect();
}
 
// Gestion des bouton radio des dates
$now       = mbDate();
$week_deb  = mbDate("last sunday", $now);
$week_fin  = mbDate("next sunday", $week_deb);
$week_deb  = mbDate("+1 day"     , $week_deb);
$rectif     = mbTransformTime("+0 DAY", $now, "%d")-1;
$month_deb  = mbDate("-$rectif DAYS", $now);
$month_fin  = mbDate("+1 month", $month_deb);
$month_fin  = mbDate("-1 day", $month_fin);

// Chargement du filter permettant de faire la recherche
$filter = new COperation();
$filter->_date_min = $now;
$filter->_date_max = $now;

// Chargement du user courant
$user = new CMediusers();
$user->load($AppUI->user_id);

// Chargement de la liste de tous les praticiens
$praticien = new CMediusers();
$praticiens = array();

if ($user->isFromType(array("Administrator"))){
  $praticiens = $praticien->loadPraticiens(PERM_EDIT);
}

if ($user->isPraticien()){
  $praticiens[] = $user;
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter", $filter);
$smarty->assign("now", $now);
$smarty->assign("week_deb", $week_deb);
$smarty->assign("week_fin", $week_fin);
$smarty->assign("month_deb", $month_deb);
$smarty->assign("month_fin", $month_fin);
$smarty->assign("praticiens", $praticiens);
$smarty->display("vw_edit_compta.tpl");


?>