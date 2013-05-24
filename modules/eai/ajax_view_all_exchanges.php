<?php 
/**
 * View all exchanges
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$_date_min    = CValue::getOrSession('_date_min', CMbDT::dateTime("-7 day"));
$_date_max    = CValue::getOrSession('_date_max', CMbDT::dateTime("+1 day"));
$group_id     = CValue::getOrSession('group_id' , CGroups::loadCurrent()->_id);
$id_permanent = CValue::getOrSession("id_permanent");
$object_id    = CValue::getOrSession("object_id");

$total_exchanges = 0;
$exchanges       = array();

$where = array();
if ($id_permanent) {
  $where["id_permanent"] = " = '$id_permanent'";
}
if ($object_id) {
  $where["object_id"] = " = '$object_id'";
}

$where["group_id"] = " = '$group_id'";

$forceindex[] = "date_production";

foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
  foreach (CApp::getChildClasses($_exchange_class, array(), true) as $under_key => $_under_class) {    
    $exchange = new $_under_class;
    $exchange->_date_min = $_date_min;
    $exchange->_date_max = $_date_max;
    $exchange->group_id = $group_id;
    $exchange->loadRefGroups();
    
    $total_exchanges += $exchange->countList($where, null, null, $forceindex);
    
    $order = "date_production DESC";    
    $exchanges[$_under_class] = $exchange->loadList($where, $order, "0, 10", null, null, $forceindex);
    foreach ($exchanges[$_under_class] as $_exchange) {
      $_exchange->loadRefsBack();
      $_exchange->getObservations();
      $_exchange->loadRefsInteropActor();
    }
  }
}

$exchange_df               = new CExchangeDataFormat();
$exchange_df->_date_min    = $_date_min;
$exchange_df->_date_max    = $_date_max;
$exchange_df->group_id     = $group_id;
$exchange_df->id_permanent = $id_permanent;
$exchange_df->object_id    = $object_id;
    
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchanges"  , $exchanges);
$smarty->assign("exchange_df", $exchange_df);
$smarty->display("inc_vw_all_exchanges.tpl");


