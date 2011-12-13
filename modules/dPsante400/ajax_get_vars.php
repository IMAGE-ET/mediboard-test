<?php /* $Id: view_identifiants.php 12379 2011-06-08 10:13:32Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage sante400
 * @version $Revision: 12379 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();

$object_class = CValue::get("object_class");
if (!$object_class) {
  return;
}

$object = new $object_class;

$vars = array_keys(CIncrementer::getVars($object));
$vars = array_combine($vars, $vars);
foreach ($vars as &$_var) {
  $_var = "[$_var]";
}

$vars["VALUE"] = "%06d";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("vars", $vars);
$smarty->display("inc_object_vars.tpl");

?>