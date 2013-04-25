<?php /* $Id: vw_idx_mediusers.php 7695 2009-12-23 09:10:10Z rhum1 $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: 7695 $
* @author Romain Ollivier
*/

CCanDo::checkRead();

$function_id = CValue::getOrSession("function_id");

// Récupération des groupes
$group = new CGroups;
$order = "text";
$groups = $group->loadListWithPerms(PERM_EDIT, null, $order);

// Récupération de la fonction selectionnée
$function = new CFunctions;
$function->load($function_id);

if ($function->_id) {
  $function->loadRefsNotes();
  $function->loadRefsFwd();
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("function"           , $function);
$smarty->assign("groups"             , $groups);

$smarty->display("inc_edit_function.tpl");
