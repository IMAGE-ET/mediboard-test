<?php /* $Id: vw_idx_patients.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);

// Récuperation de l'utilisateur sélectionné
$user = new CUser;
$user->load($user_id);
$user->loadRefMediuser();

// Récuperation des utilisateurs recherchés
$user_last_name  = mbGetValueFromGetOrSession("user_last_name" , "");
$user_first_name = mbGetValueFromGetOrSession("user_first_name", "");
$user_username   = mbGetValueFromGetOrSession("user_username"  , "");
$user_type       = mbGetValueFromGetOrSession("user_type"      , 0);
$template        = mbGetValueFromGetOrSession("template"       , "");

$where = null;
if ($user_last_name ) $where["user_last_name"]  = "LIKE '".addslashes($user_last_name )."%'";
if ($user_first_name) $where["user_first_name"] = "LIKE '".addslashes($user_first_name)."%'";
if ($user_username  ) $where["user_username"]   = "LIKE '".addslashes($user_username)."%'";
if ($user_type      ) $where["user_type"]       = "= '".addslashes($user_type)."'";
if ($template != null)$where["template"]        = "= '".addslashes($template)."'";

$users = null;
if ($where) {
  $users = $user->loadList($where, "user_type, user_last_name, user_first_name, template", "0, 100");
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
$smarty->assign("specs"          , $user->getSpecs());

$smarty->display("vw_edit_users.tpl");
?>