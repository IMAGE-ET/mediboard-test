<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$delivery_id       = CValue::get('delivery_id');
$mode              = CValue::get('mode');
$display_delivered = CValue::getOrSession('display_delivered', 'false') == 'true';
$order_col         = CValue::get('order_col');
$order_way         = CValue::get('order_way');

if (!$order_col) $order_col = 'date_dispensation';
if (!$order_way) $order_way = 'DESC';

CValue::setSession('order_col', $order_col);
CValue::setSession('order_way', $order_way);

$datetime_min = CValue::getOrSession('_datetime_min');
$datetime_max = CValue::getOrSession('_datetime_max');

$list_moments = array();
$nom_moments  = array();
$color_moments  = array();
$lines = array();

for($i=1; $i<6; $i++){
  $conf_periodes[] = CAppUI::conf("pharmacie periode_$i");
}

$count_periods = 0;
foreach ($conf_periodes as $_periode){
  if($_periode["heure"] && $_periode["libelle"]){
    $count_periods++;
    $list_moments[]  = $_periode["heure"];
    $nom_moments[]   = $_periode["libelle"];
    $color_moments[] = "#".$_periode["couleur"];
  }
}

$prev_hour = 0;
foreach($list_moments as $key_period => $_hour_period){
  for($i=0; $i < 24; $i++){
    if(isset($list_moments[$key_period-1])){
      $prev_hour = $list_moments[$key_period-1];
    }  
    if($i < $_hour_period && $i >= $prev_hour){
      $moments[str_pad($i, 2, '0', STR_PAD_LEFT)] = $key_period;
    }
  }
}
// Initialisation du pilulier standard
$_date_min = mbDate($datetime_min);
$_date_max = mbDate($datetime_max);
$dates = array();
for($_date = $_date_min; $_date <= $_date_max; $_date = mbDate("+ 1 DAY", $_date)){
  foreach($nom_moments as $key_period => $_moment){
    $pilulier_init[$_date][$key_period] = "";
  }
}
  
$delivery = new CProductDelivery();
if($delivery_id){
  $delivery->load($delivery_id);
  if ($delivery->patient_id) {
    $mode = "nominatif";
  }
  
  $services = array(
    $delivery->service_id => $delivery->loadRefService()
  );
}

if (!in_array($mode, array("global", "nominatif"))) {
  $mode = "global";
}

CProductOrderItem::$_load_lite = true;

$services = CProductStockGroup::getServicesList();
$services["none"] = new CService;
$services["none"]->_view = "";
$service_keys = array_keys($services);

$deliveries_by_service = array_fill_keys($service_keys, array());
$delivered_counts = array_fill_keys($service_keys, 0);
$order_by_product = false;
$stocks_service = array();

$col = $order_col;
switch($col) {
  case "product_id":  $col = "product.name"; break;
  case "location_id": $col = "product_stock_location.position"; break;
}

$order_by = "product_delivery.service_id, product_delivery.patient_id, $col $order_way";

$where = array();
if ($mode == "global")
  $where['product_delivery.patient_id'] = "IS NULL";
else
  $where['product_delivery.patient_id'] = "IS NOT NULL";
  
//$where[] = "product_delivery.date_dispensation BETWEEN '$datetime_min' AND '$datetime_max'";
$where['product_delivery.datetime_min'] = " < '$datetime_max'";
$where['product_delivery.datetime_max'] = " > '$datetime_min'";
$where['product_delivery.quantity'] = " > 0";
$where['product_delivery.stock_class'] = "= 'CProductStockGroup'";
$where['product_delivery.stock_id'] = "IS NOT NULL";
//$where[] = "`order` != '1' OR `order` IS NULL";

if (!$display_delivered) {
  $where["date_delivery"] = "IS NULL";
}

$ljoin = array(
  "product_stock_group" => "product_stock_group.stock_id = product_delivery.stock_id",
  "product_stock_location" => "product_stock_location.stock_location_id = product_stock_group.location_id",
  "product" => "product.product_id = product_stock_group.product_id",
);

foreach($services as $_service_id => $_service) {
  // one line
  if($delivery_id){
    $deliveries[$delivery->_id] = $delivery;
  } 
  
  // all lines
  else {
    if ($_service_id == "none") {
      $where['service_id'] = "IS NULL OR service_id = ''";
    }
    else {
      $where['service_id'] = " = '$_service->_id'";
    }
    $deliveries = $delivery->loadList($where, $order_by, 200, null, $ljoin);
  }
  
  foreach($deliveries as $_id => $_delivery) {
    $_delivery->_pilulier = $pilulier_init;
    
    $_delivery->loadRefsFwd();
    $_delivery->_ref_stock->loadRefsFwd();
    $_delivery->isDelivered();
    
    $delivered = /*$_delivery->date_delivery || */$_delivery->_delivered;
    if (!$delivered || ($delivered && $display_delivered)) {
      // can't touch this !
    }
    else {
      unset($deliveries[$_id]);
    }
  }
  
  foreach($deliveries as $_id => $_delivery) {
    $_product = $_delivery->_ref_stock->_ref_product;
    $_product->getPendingOrderItems(false);
    
    $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_product->code, $_delivery->service_id);

    
    // Chargement du pilulier
    $_delivery->loadRefsPrisesDispensationMed($datetime_min, $datetime_max);
    foreach($_delivery->_ref_prises_dispensation_med as $_prise_disp){
      $lines[$_delivery->_id] = "$_prise_disp->object_class-$_prise_disp->object_id";
      
      $time = mbTransformTime($_prise_disp->datetime,null,"%H");
      @$_delivery->_pilulier[mbDate($_prise_disp->datetime)][$moments[$time]] += $_prise_disp->quantite_disp;
      
      if (!$_delivery->_code_cis || !$_delivery->_code_ucd) {
        $_prise_disp->loadTargetObject();
        $_delivery->_code_cis = $_prise_disp->_ref_object->code_cis;
        $_delivery->_code_ucd = $_prise_disp->_ref_object->code_ucd;
      }
    }
    
    $key_delivery = (($_delivery->patient_id) ? "$_delivery->datetime_min-$_delivery->datetime_max-$_delivery->_code_ucd" : "key");
    $key = "global";
    
    if ($_delivery->patient_id) {
      $patient = $_delivery->_ref_patient;
      $patient->loadRefsAffectations($_delivery->datetime_min);
      if ($patient->_ref_curr_affectation) {
        $patient->_ref_curr_affectation->loadRefLit();
      }
      $key = str_pad($patient->nom, 20, " ", STR_PAD_RIGHT).str_pad($patient->prenom, 20, " ", STR_PAD_RIGHT);
    }
    else {
      if ($_delivery->date_delivery || $_delivery->_delivered) {
        $delivered_counts[$_service_id]++;
      }
    }
    
    $deliveries_by_service[$_service_id][$key][$key_delivery][$_delivery->_id] = $_delivery;
  }
}


$deliveries_count = 0;
$deliveries_count_by_service = array();
foreach($deliveries_by_service as $_service_id => $_by_service) {
  $deliveries_count_by_service[$_service_id] = 0;
  ksort($deliveries_by_service[$_service_id]);
  
  foreach($_by_service as $_deliveries_by_patient) {
    foreach($_deliveries_by_patient as $_deliveries_by_ucd) {
      $deliveries_count += count($_deliveries_by_ucd);
      $deliveries_count_by_service[$_service_id] += count($_deliveries_by_ucd);
    }
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign('stocks_service', $stocks_service);
$smarty->assign("pilulier_init", $pilulier_init);
$smarty->assign('mode', $mode);
$smarty->assign("moments", $moments);
$smarty->assign("deliveries_count_by_service", $deliveries_count_by_service);
$smarty->assign("nom_moments", $nom_moments);
$smarty->assign("list_moments", $list_moments);
$smarty->assign("color_moments", $color_moments);
$smarty->assign("count_periods", $count_periods);
if($delivery_id){
  $smarty->assign('curr_delivery',  $delivery);
  $smarty->assign('line_refresh', true);
  $smarty->assign('service_id', $delivery->service_id);
  $smarty->assign("single_location", CProductStockGroup::getHostGroup(false)->countBackRefs("stock_locations") < 2);
  $smarty->assign("count_date", count($pilulier_init));
  $smarty->assign('show_pil',  true);
  $smarty->assign('count_pil',  1);
  $smarty->display('inc_vw_line_delivrance.tpl');
  
} else {
  $smarty->assign('order_col',     $order_col);
  $smarty->assign('order_way',     $order_way);
  $smarty->assign('deliveries',     $deliveries);
  $smarty->assign('deliveries_by_service', $deliveries_by_service);
  $smarty->assign('deliveries_count', $deliveries_count);
  $smarty->assign('services',       $services);
  $smarty->assign('delivered_counts', $delivered_counts);
  $smarty->assign('display_delivered', $display_delivered);
  $smarty->assign("single_location", CProductStockGroup::getHostGroup(false)->countBackRefs("stock_locations") < 2);
  $smarty->display('inc_deliveries_list.tpl');  
}
