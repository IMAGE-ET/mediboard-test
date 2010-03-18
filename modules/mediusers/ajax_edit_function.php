<?php /* $Id: vw_idx_mediusers.php 7695 2009-12-23 09:10:10Z rhum1 $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 7695 $
* @author Romain Ollivier
*/

global $can;

$can->needsRead();

$function_id = CValue::getOrSession("function_id");
$page_userfunction = intval(CValue::get('page_userfunction', 0));

// Récupération des groupes
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

// Récupération de la fonction selectionnée
$userfunction = new CFunctions;
$userfunction->load($function_id);

$primary_users = array();
$total_userfunctions = null;
if($userfunction->_id) {
  $userfunction->loadRefsFwd();
  $userfunction->loadBackRefs("users");
  $total_userfunctions = $userfunction->countBackRefs("users");
  $primary_users = $userfunction->loadBackRefs("users", null, "$page_userfunction, 20");
  foreach($primary_users as $_user) {
    $_user->loadRefProfile();
  }

  $userfunction->loadBackRefs("secondary_functions");
  foreach($userfunction->_back["secondary_functions"] as &$_sec_function) {
    $_sec_function->loadRefUser();
    $_sec_function->_ref_user->loadRefProfile();
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("userfunction"       , $userfunction);
$smarty->assign("primary_users"      , $primary_users);
$smarty->assign("total_userfunctions", $total_userfunctions);
$smarty->assign("page_userfunction"  , $page_userfunction);
$smarty->assign("groups"             , $groups  );
$smarty->assign("secondary_function" , new CSecondaryFunction());
$smarty->assign("utypes"             , CUser::$types );

$smarty->display("inc_edit_function.tpl");

?>