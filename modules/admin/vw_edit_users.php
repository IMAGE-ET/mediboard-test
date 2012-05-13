<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

// Récuperation de l'utilisateur sélectionné
$user_id = CValue::getOrSession("user_id");
$user = $user_id == "0" ? new CUser() : CUser::get($user_id);

// Récuperation des utilisateurs recherchés
$user_username   = CValue::getOrSession("user_username"  );
$user_last_name  = CValue::getOrSession("user_last_name" );
$user_first_name = CValue::getOrSession("user_first_name");
$user_type       = CValue::getOrSession("user_type"      );
$template        = CValue::getOrSession("template"       );

// Where clause
$where = null;
if ($user_last_name ) $where["user_last_name"]  = "LIKE '$user_last_name%'";
if ($user_first_name) $where["user_first_name"] = "LIKE '$user_first_name%'";
if ($user_username  ) $where["user_username"]   = "LIKE '$user_username%'";
if ($user_type      ) $where["user_type"]       = "= '$user_type'";
if ($template != null)$where["template"]        = "= '$template'";

// Query
$users = null;
if ($where) {
	$order = "user_type, user_last_name, user_first_name, template";
	$limit = 100;
  $users = $user->loadList($where, $order, $limit);
  foreach ($users as $_user) {
  	$_user->countBackRefs("profiled_users");
  }
  
  // Auto sélection du user s'il est unique
  if (count($users) == 1) {
  	$user = reset($users);
  }
}

// Chargement du détail de l'utilisateur
$user->loadRefMediuser();
$user->loadRefsNotes();
$user->isLDAPLinked();

// Chargement des conexions
if ($user->dont_log_connection) {
  $user->countConnections();
}

// Chargement des utilateurs associés
if ($user->template) {
  $user->loadRefProfiledUsers();
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("template"       , $template       );
$smarty->assign("user_last_name" , $user_last_name );
$smarty->assign("user_first_name", $user_first_name);
$smarty->assign("user_username"  , $user_username  );
$smarty->assign("user_type"      , $user_type      );
$smarty->assign("utypes"         , CUser::$types   );
$smarty->assign("users"          , $users          );
$smarty->assign("user"           , $user           );
$smarty->assign("specs"          , $user->getProps());

$smarty->display("vw_edit_users.tpl");
?>