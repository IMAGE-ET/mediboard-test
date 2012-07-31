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

$praticiens = new CMediusers;
$praticiens = $praticiens->loadChirurgiens(); 
CMbObject::massLoadFwdRef($praticiens, "function_id");

foreach ($praticiens as $_prat) {
  $_prat->loadRefFunction();
}

$smarty = new CSmartyDP;

$smarty->assign("date_planning", $date_planning);
$smarty->assign("praticien_id" , $praticien_id);
$smarty->assign("praticiens"   , $praticiens);

$smarty->display("vw_planning.tpl");
