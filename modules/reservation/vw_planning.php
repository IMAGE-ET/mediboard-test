<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage reservation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date_planning = CValue::getOrSession("date_planning", mbDate());
$praticien_id  = CValue::getOrSession("praticien_id", "");
$bloc_id       = CValue::getOrSession("bloc_id", "");

$praticiens = new CMediusers;
$praticiens = $praticiens->loadChirurgiens(); 
CMbObject::massLoadFwdRef($praticiens, "function_id");

foreach ($praticiens as $_prat) {
  $_prat->loadRefFunction();
}

$bloc = new CBlocOperatoire;
$blocs = $bloc->loadGroupList();

$smarty = new CSmartyDP;

$smarty->assign("date_planning", $date_planning);
$smarty->assign("praticien_id" , $praticien_id);
$smarty->assign("praticiens"   , $praticiens);
$smarty->assign("blocs"        , $blocs);
$smarty->assign("bloc_id"      , $bloc_id);

$smarty->display("vw_planning.tpl");
