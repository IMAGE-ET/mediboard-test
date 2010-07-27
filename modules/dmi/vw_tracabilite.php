<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Filter
$filter = new CPrescriptionLineDMI;
$filter->_patient_id             = CValue::getOrSession("patient_id");
$filter->product_id              = CValue::getOrSession("product_id");
$filter->order_item_reception_id = CValue::getOrSession("lot");
$start = intval(CValue::getOrSession("start", 0));

// Filter query
$where = array();
$where["product_id"] = "IS NOT NULL";
$join = array();
$order = "date";

$lines =      $filter->loadList($where, $order, "$start,30", null, $join);
$line_count = $filter->countList($where, $order, null, null, $join);

// Detail loading
foreach($lines as $_line) {
	$_line->loadRefOperation();
  $_line->_ref_operation->loadRefSejour();
  $_line->loadRefProductOrderItemReception();
  $_line->loadRefProduct();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("filter"    , $filter);
$smarty->assign("start"     , $start);
$smarty->assign("lines"     , $lines);
$smarty->assign("line_count", $line_count);
$smarty->display("vw_tracabilite.tpl");
