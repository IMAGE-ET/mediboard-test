<?php /* $Id */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision:
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
//$can->needsRead();


// Get filter
$filter = new CPlageVacances;
$year = date("Y");
$filter->user_id    = CValue::get("user_id", CAppUI::$user->_id);
$filter->_id = CValue::get("plage_id","");
$filter->date_debut = CValue::get("date_debut", "$year-01-01");
$filter->date_fin   = CValue::get("date_fin"  , "$year-12-31");



$page      = intval(CValue::get('page', 0));



// load available users order by name
//$orderby = "user_last_name ASC";
//$ljoin = array();
//$ljoin["users"]="users_mediboard.user_id = users.user_id";
$mediuser  = new CMediusers();
$mediusers = $mediuser->loadListFromType();
//$mediusers = $mediusers->loadList(null,$orderby,null,null, $ljoin); // Load with perms...

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
$plages = $filter->loadList($where);

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

$nbusers = count($found_users);
$found_users = array_slice($found_users, $page, 20, true);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("mediusers",       $mediusers);
$smarty->assign("found_users",     $found_users);
$smarty->assign("filter",          $filter);
$smarty->assign("plages_per_user", $plages_per_user);
$smarty->assign("nbusers",         $nbusers);
$smarty->assign("page",            $page);
$smarty->display("vw_idx_plages_vac.tpl");