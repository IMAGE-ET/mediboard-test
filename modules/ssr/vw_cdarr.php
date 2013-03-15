<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */


CCanDo::checkRead();

$activite = new CActiviteCdARR();
$activite->code = CValue::getOrSession("code");
$activite->type = CValue::getOrSession("type");

// Pagination
$current = CValue::getOrSession("current", 0);
$step    = 20;

$type_activite = new CTypeActiviteCdARR();
$listTypes = $type_activite->loadList(null, "code");

$where = array();
if($activite->type) {
  $where["type"] = "= '$activite->type'";
}

$limit = "$current, $step";
$order = "type, code";
$listActivites = $activite->seek($activite->code, $where, $limit, true);
$total = $activite->_totalSeek;

// D�tail du chargement
foreach ($listActivites as $_activite) {
  $_activite->countElements();
  $_activite->countActes();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("activite"      , $activite);
$smarty->assign("listTypes"     , $listTypes);
$smarty->assign("listActivites" , $listActivites);

$smarty->assign("current", $current);
$smarty->assign("step"   , $step);
$smarty->assign("total"  , $total);

$smarty->display("vw_cdarr.tpl");

?>