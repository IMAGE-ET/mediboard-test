<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */



global $AppUI, $can, $m, $tab;

$activite = new CActiviteCdARR();
$activite->code = CValue::getOrSession("code");
$activite->type = CValue::getOrSession("type");

// Pagination
$current = CValue::getOrSession("current", 0);
$step    = 20;

$type_activite = new CTypeActiviteCdARR();
$listTypes = $type_activite->loadList(null, "code");

$where = array();
$where["code"] = "LIKE '$activite->code%'";
if($activite->type) {
  $where["type"] = "= '$activite->type'";
}
$total = $activite->countList($where);
$limit = "$current, $step";
$listActivites = $activite->loadList($where, "type, code", $limit);
foreach($listActivites as &$_activite) {
  $_activite->loadRefTypeActivite();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("activite"      , $activite);
$smarty->assign("activite"      , $activite);
$smarty->assign("listTypes"     , $listTypes);
$smarty->assign("listActivites" , $listActivites);

$smarty->assign("current", $current);
$smarty->assign("step"   , $step);
$smarty->assign("total"  , $total);

$smarty->display("vw_cdar.tpl");

?>