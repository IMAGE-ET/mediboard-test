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

$_date_min = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max = CValue::getOrSession('_date_max', mbDateTime("+1 day"));

$total_exchanges = 0;
$exchanges       = array();
foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
  foreach (CApp::getChildClasses($_exchange_class, array(), true) as $under_key => $_under_class) {    
    $exchange = new $_under_class;
    $exchange->_date_min = $_date_min;
    $exchange->_date_max = $_date_max;
    
    $group_id = CGroups::loadCurrent()->_id;
    $where["group_id"] = " = '$group_id'";
    $exchange->group_id = $group_id;
    $exchange->loadRefGroups();
    
    $forceindex[] = "date_production";
    $total_exchanges += $exchange->countList($where, null, null, $forceindex);
        
    $exchanges[$_under_class] = $exchange->loadList($where, null, "0, 10", null, null, $forceindex);
    foreach ($exchanges[$_under_class] as $_exchange) {
      $_exchange->loadRefsBack();
      $_exchange->getObservations();
      $_exchange->loadRefsInteropActor();
    }
  }
}

$exchange_df = new CExchangeDataFormat();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("exchanges"  , $exchanges);
$smarty->assign("exchange_df", $exchange_df);
$smarty->display("vw_all_exchanges.tpl");

?>
