<?php 
/**
 * View tools EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkAdmin();

$count          = CValue::get("count", 20);
$continue       = CValue::get("continue"); 
$error_code     = CValue::get("error_code"); 
$exchange_class = CValue::get("exchange_class"); 
$group_id       = CValue::get("group_id", CGroups::loadCurrent()->_id);
$tool           = CValue::get("tool"); 
$date_min       = CValue::getOrSession('date_min', CMbDT::dateTime("-7 day"));
$date_max       = CValue::getOrSession('date_max', CMbDT::dateTime("+1 day"));

$exchange = new $exchange_class;
$exchange->group_id = $group_id;
$exchange->_date_min = $date_min;
$exchange->_date_max = $date_max;

if (!$error_code) {
  CAppUI::stepAjax("CEAI-tools-exchanges-no_error_code", UI_MSG_ERROR);
}

$ljoin = null;
$content_exchange = $exchange->loadFwdRef("acquittement_content_id");
$table            = $content_exchange->_spec->table;
$ljoin[$table]    = $exchange->_spec->table.".acquittement_content_id = $table.content_id";
    
$where = array();
$where["$table.content"]  = " LIKE '%$error_code%'";

$where["date_production"] = " BETWEEN '$date_min' AND '$date_max'";
$where["group_id"]        = " = '$group_id'";
$where["reprocess"]       = " < '".CAppUI::conf("eai max_reprocess_retries")."'";

$forceindex[] = "date_production";

$total_exchanges = $exchange->countList($where, null, $ljoin, $forceindex);
if ($total_exchanges == 0) {
  CAppUI::stepAjax("CEAI-tools-exchanges-no_corresponding_exchange", UI_MSG_ERROR);
}

CAppUI::stepAjax("CEAI-tools-exchanges-corresponding_exchanges", UI_MSG_OK, $total_exchanges);

$order = "date_echange ASC, date_production ASC";
$exchanges = $exchange->loadList($where, $order, "0, $count", null, $ljoin, $forceindex);

// Création du template
$smarty = new CSmartyDP();
    
switch ($tool) {
  case "reprocessing":
    foreach ($exchanges as $_exchange) {
      try {
        $_exchange->reprocessing();
      }
      catch (CMbException $e) {
        $e->stepAjax(UI_MSG_WARNING);
      }
      
      if (!$_exchange->_id) {
        CAppUI::stepAjax("CExchangeAny-msg-delete", UI_MSG_ALERT);
      }
      
      CAppUI::stepAjax("CExchangeDataFormat-reprocessed");
    }
    
    break;
    
  case "detect_collision":
    $collisions = array();
    
    foreach ($exchanges as $_exchange) {
      if ($_exchange instanceof CExchangeHL7v2) {
        $hl7_message = new CHL7v2Message;
        $hl7_message->parse($_exchange->_message);
        
        $xml = $hl7_message->toXML(null, false);
        
        $PV1 = $xml->queryNode("PV1");
        $PV2 = $xml->queryNode("PV2");
        
        $sejour = new CSejour();
        $sejour->load($_exchange->object_id);
        
        $sejour_hl7 = new CSejour;
        $sejour_hl7->entree_prevue = $xml->queryTextNode("PV2.8/TS.1",  $PV2);
        $sejour_hl7->entree_reelle = $xml->queryTextNode("PV1.44/TS.1", $PV1);
        $sejour_hl7->sortie_prevue = $xml->queryTextNode("PV2.9/TS.1",  $PV2);
        $sejour_hl7->sortie_reelle = $xml->queryTextNode("PV1.45/TS.1", $PV1);
        
        $collisions[] = array(
          "hl7" => $sejour_hl7,
          "mb"  => $sejour,
        );
      }
    }  

    $smarty->assign("collisions", $collisions);
    $smarty->display("inc_detect_collisions.tpl");
      
    break;

  default:
}

CAppUI::js("next$tool()");

