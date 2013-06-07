<?php

/**
 * Edit function
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
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
