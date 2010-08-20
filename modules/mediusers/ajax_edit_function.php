<?php /* $Id: vw_idx_mediusers.php 7695 2009-12-23 09:10:10Z rhum1 $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 7695 $
* @author Romain Ollivier
*/

CCanDo::checkRead();

$function_id = CValue::getOrSession("function_id");
$page_function = intval(CValue::get('page_function', 0));

// Récupération des groupes
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

// Récupération de la fonction selectionnée
$function = new CFunctions;
$function->load($function_id);

$primary_users = array();
$total_functions = null;
if($function->_id) {
  $function->loadRefsNotes();
  $function->loadRefsFwd();
  $function->loadBackRefs("users");
  $total_functions = $function->countBackRefs("users");
  $primary_users = $function->loadBackRefs("users", null, "$page_function, 20");
  foreach($primary_users as $_user) {
    $_user->loadRefProfile();
  }

  $function->loadBackRefs("secondary_functions");
  foreach($function->_back["secondary_functions"] as &$_sec_function) {
    $_sec_function->loadRefUser();
    $_sec_function->_ref_user->loadRefProfile();
  }
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("function"       , $function);
$smarty->assign("primary_users"      , $primary_users);
$smarty->assign("total_functions", $total_functions);
$smarty->assign("page_function"  , $page_function);
$smarty->assign("groups"             , $groups  );
$smarty->assign("secondary_function" , new CSecondaryFunction());
$smarty->assign("utypes"             , CUser::$types );

$smarty->display("inc_edit_function.tpl");

?>