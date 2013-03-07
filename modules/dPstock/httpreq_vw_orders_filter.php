<?php /* $Id: httpreq_vw_orders_list.php 10744 2010-11-30 09:53:36Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 10744 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
CCanDo::checkRead();

$invoiced = CValue::get('invoiced');

$date_min = CMbDT::transform("-1 MONTH", null, "%Y-%m-01");
$date_max = CMbDT::date("+1 MONTH -1 DAY", $date_min);

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("invoiced", $invoiced);

$smarty->display("inc_orders_filter.tpl");
