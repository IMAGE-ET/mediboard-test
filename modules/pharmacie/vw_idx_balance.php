<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$product_id = CValue::getOrSession("product_id");
$year       = CValue::getOrSession("year", mbTransformTime(null, null, "%Y"));
$month      = CValue::getOrSession("month", mbTransformTime(null, null, "%m"));

$product = new CProduct;
$product->load($product_id);

$stock = new CProductStockGroup;
$stock->product_id = $product_id;

$service = new CService;
$services = $service->loadListWithPerms(PERM_READ);

// YEAR //////////
$year_flows = array(
  "in"  => array(),
  "out" => array(),
);
$start = mbDate(null, "$year-01-01");

foreach($services as $_service) {
  $year_flows["out"]["total"][$_service->_id] = 0;
}

$d = &$year_flows["out"];

// Y init
for($i = 0; $i < 12; $i++) {
  $from = mbDate("+$i MONTHS", $start);
  $to = mbDate("+1 MONTH", $from);
  
  $d[$from] = array();
}
$d["total"] = array();

for($i = 0; $i < 12; $i++) {
  $from = mbDate("+$i MONTHS", $start);
  $to = mbDate("+1 MONTH", $from);
  
  // X init
  foreach($services as $_service) {
    $d[$from][$_service->_id] = 0;
  }
  $d[$from]["total"] = 0;
  
  foreach($services as $_service) {
    $count = $product->getConsumption($from, $to, $_service->_id);
    
    $d[$from][$_service->_id] = $count; // ok
    
    $d[$from]["total"] += $count;
    @$d["total"][$_service->_id] += $count;
    @$d["total"]["total"] += $count;
  }
  
  $year_flows["in"][$from] = $product->getSupply($from, $to);
}

// Put the total at the end
$total = $d["total"];
unset($d["total"]);
$d["total"] = $total;

$total = $d["total"]["total"];
unset($d["total"]["total"]);
$d["total"]["total"] = $total;



// Création du template
$smarty = new CSmartyDP();

$smarty->assign('product',     $product);
$smarty->assign('services',    $services);
$smarty->assign("stock", $stock);

$smarty->assign('year_flows',  $year_flows);
//$smarty->assign('month_flows', $month_flows);

$smarty->display('vw_idx_balance.tpl');

?>