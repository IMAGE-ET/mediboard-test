<?php /* $Id: httpreq_do_element_autocomplete.php 8169 2010-03-02 15:31:33Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: 8169 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$needle = CValue::post("_executant", "aaa");

$intervenant = new CMediusers();

$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$where = array();
$where["code_intervenant_cdarr"] = "IS NOT NULL";
$where["users_mediboard.actif"] = "= '1'";
$where["functions_mediboard.group_id"] = "= '" . CGroups::loadCurrent()->_id . "'";

$order = "users.user_last_name ASC, users.user_first_name ASC";

$intervenants = $intervenant->seek($needle, $where, 100, false, $ljoin, $order);

foreach($intervenants as &$_intervenant) {
  $_intervenant->loadRefFunction();
  $_intervenant->loadRefCodeIntervenantCdARR();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("intervenants", $intervenants);
$smarty->assign("needle"      , $needle);
$smarty->assign("nodebug"     , true);

$smarty->display("inc_do_intervenant_autocomplete.tpl");

?>