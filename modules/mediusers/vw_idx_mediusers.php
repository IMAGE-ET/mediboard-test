<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$filter    = CValue::getOrSession("filter", "");
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "function_id");

// Récupération des fonctions
$group = CGroups::loadCurrent();
$group->loadFunctions();

// Liste des utilisateurs
$mediuser = new CMediusers();
$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";
$where = array();
$where["functions_mediboard.group_id"] = "= $group->_id";
if($filter) {
  $where[] = "functions_mediboard.text LIKE '%$filter%' OR users.user_last_name LIKE '$filter%' OR users.user_first_name LIKE '$filter%'";
}
$order = "";
if($order_col == "function_id") {
  $order = "functions_mediboard.text $order_way, users.user_last_name ASC, users.user_first_name ASC";
} elseif($order_col == "user_username") {
  $order = "users.user_username $order_way, users.user_last_name ASC, users.user_first_name ASC";
} elseif($order_col == "user_last_name") {
  $order = "users.user_last_name $order_way, users.user_first_name ASC";
} elseif($order_col == "user_first_name") {
  $order = "users.user_first_name $order_way, users.user_last_name ASC";
} elseif($order_col == "user_type") {
  $order = "users.user_type $order_way, users.user_last_name ASC, users.user_first_name ASC";
} elseif($order_col == "user_last_login") {
  $order = "users.user_last_login ";
  $order .= $order_way == "ASC" ? "DESC" : "ASC";
  $order .= ", users.user_last_name ASC, users.user_first_name ASC";
}
$mediusers = $mediuser->loadListWithPerms(PERM_EDIT, $where, $order, null, null, $ljoin);
foreach($mediusers as &$_mediuser) {
  $_mediuser->loadRefFunction();
  $_mediuser->loadRefProfile();
}

// Chargement des banques
$banques = array();
if (class_exists("CBanque")) {
	$order = "nom ASC";
	$banque = new CBanque();
	$banques = $banque->loadList(null, $order);
}

// Récupération des disciplines
$disciplines = new CDiscipline;
$disciplines = $disciplines->loadList();

// Récupération des spécialités CPAM
$spec_cpam = new CSpecCPAM();
$spec_cpam = $spec_cpam->loadList();
  
// Récupération des profils
$where = array (
    "template" => "= '1'"
);
$profiles = new CUser();
$profiles = $profiles->loadList($where);

// Creation du tableau de profil en fonction du type
foreach($profiles as $key => $profil){
  $tabProfil[$profil->user_type][] = $profil->_id;
}

// Récupération du user à ajouter/editer 
// (mis en dernier car interferences avec le chargement 
// des autres users car utilisation d'une spec commune)
$object = new CMediusers;
$object->load(CValue::getOrSession("user_id"));

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"       , $filter       );
$smarty->assign("mediusers"    , $mediusers    );
$smarty->assign("tabProfil"    , $tabProfil    );
$smarty->assign("utypes"       , CUser::$types );
$smarty->assign("banques"      , $banques      );
$smarty->assign("object"       , $object       );
$smarty->assign("profiles"     , $profiles     );
$smarty->assign("group"        , $group        );
$smarty->assign("disciplines"  , $disciplines  );
$smarty->assign("spec_cpam"    , $spec_cpam    );
$smarty->assign("order_way"    , $order_way);
$smarty->assign("order_col"    , $order_col);

$smarty->display("vw_idx_mediusers.tpl");

?>