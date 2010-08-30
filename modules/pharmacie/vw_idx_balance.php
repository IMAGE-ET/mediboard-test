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

function fillFlow(&$array, $product, $n, $start, $unit, $services) {
  foreach($services as $_service) {
    $array["out"]["total"][$_service->_id] = 0;
  }
  
  $d = &$array["out"];
  
  // Y init
  for($i = 0; $i < 12; $i++) {
    $from = mbDate("+$i $unit", $start);
    $to = mbDate("+1 $unit", $from);
    
    $d[$from] = array();
  }
  $d["total"] = array();
  
  for($i = 0; $i < $n; $i++) {
    $from = mbDate("+$i $unit", $start);
    $to = mbDate("+1 $unit", $from);
    
    // X init
    foreach($services as $_service) {
      $d[$from][$_service->_id] = 0;
    }
    $d[$from]["total"] = 0;
    
    foreach($services as $_service) {
      $count = $product->getConsumption($from, $to, $_service->_id);
      
      $d[$from][$_service->_id] = $count;
      
      $d[$from]["total"] += $count;
      @$d["total"][$_service->_id] += $count;
      @$d["total"]["total"] += $count;
    }
  }

  // Put the total at the end
  $total = $d["total"];
  unset($d["total"]);
  $d["total"] = $total;
  
  $total = $d["total"]["total"];
  unset($d["total"]["total"]);
  $d["total"]["total"] = $total;
}


// YEAR //////////
$year_flows = array(
  "in"  => array(),
  "out" => array(),
);
$start = mbDate(null, "$year-01-01");
fillFlow($year_flows, $product, 12, $start, "MONTH", $services);


// MONTH //////////
$month_flows = array(
  "in"  => array(),
  "out" => array(),
);
$start = mbDate(null, "$year-$month-01");
fillFlow($month_flows, $product, mbTransformTime("+1 MONTH -1 DAY", $start, "%d"), $start, "DAY", $services);

$flows = array(
  "year" => array($year_flows, "%b"), 
  "month" => array($month_flows, "%d"),
);


// Balance des stocks ////////////////
$balance = array(
  "in" => $flows["year"][0]["in"],
  "out" => array(),
  "diff" => array(),
);

$start = mbDate(null, "$year-01-01");
for($i = 0; $i < 12; $i++) {
  $from = mbDate("+$i MONTH", $start);
  $to = mbDate("+1 MONTH", $from);
  
  $balance["out"][$from] = $product->getConsumption($from, $to);
  $balance["in"][$from] = $product->getSupply($from, $to);
}

$cumul = 0;
foreach($balance["in"] as $_date => $_balance) {
  $diff = $balance["in"][$_date] - $balance["out"][$_date];
  $balance["diff"][$_date] = $diff+$cumul;
  $cumul += $diff;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign('product',  $product);
$smarty->assign('services', $services);
$smarty->assign('stock',    $stock);
$smarty->assign('flows',    $flows);
$smarty->assign('balance',  $balance);

$smarty->display('vw_idx_balance.tpl');

?>