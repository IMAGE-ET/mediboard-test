<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsRead();

// Get filter
$filter = new CPlageVacances;
$filter->user_id    = CValue::getOrSession("user_id", CAppUI::$user->_id);

$year = date("Y");
$filter->date_debut = CValue::getOrSession("date_debut", "$year-01-01");
$filter->date_fin   = CValue::getOrSession("date_fin"  , "$year-12-31");

$_plage_id = CValue::get("plage_id","");
$_user_id = CValue::get("user_id","");

// load available users order by name
$orderby = "user_last_name ASC";
$ljoin = array();
$ljoin["users"]="users_mediboard.user_id = users.user_id";
$mediuser  = new CMediusers();
$mediusers = $mediuser->loadList(null,$orderby,null,null, $ljoin); // Load with perms...

// load ref function
foreach($mediusers as $_medius) {
	$_medius->loadRefFunction();
}

// Query
$where = array();
$where["user_id"] = CSQLDataSource::prepareIn(array_keys($mediusers), $filter->user_id);

$debut = CValue::first($filter->date_debut, $filter->date_fin);
$fin   = CValue::first($filter->date_fin, $filter->date_debut);

if ($fin || $debut) {
  $where["date_debut"] = "<= '$fin'";
  $where["date_fin"] = ">= '$debut'";
}

// Penser à rajouter une limite et demander à Fabien un exemple d'affichage paginé
// $count_plages = $filter->countList($where);
$plages = $filter->loadList($where, null, "0, 30");

// Regrouper par utilisateur
$found_users = array();
$plages_per_user = array();
foreach ($plages as $_plage) {
	$found_users[$_plage->user_id] = $mediusers[$_plage->user_id];
	
	if (!isset($plages_per_user[$_plage->user_id])){
	  $plages_per_user[$_plage->user_id] = 0;
	}
	$plages_per_user[$_plage->user_id]++;
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mediusers",       $mediusers);
$smarty->assign("found_users",     $found_users);
$smarty->assign("filter",          $filter);
$smarty->assign("plages_per_user", $plages_per_user);
$smarty->assign("_plage_id",       $_plage_id);
$smarty->assign("_user_id",        $_user_id);
$smarty->display("vw_idx_plages_vac.tpl");