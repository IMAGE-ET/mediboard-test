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
$group_id       = CValue::get("group_id");
$tool           = CValue::get("tool"); 

$exchange = new $exchange_class;
$exchange->group_id = $group_id;

if (!$error_code) {
  CAppUI::stepAjax("CEAI-tools-exchanges-no_error_code", UI_MSG_ERROR);
}

$ljoin = null;
$content_exchange = $exchange->loadFwdRef("acquittement_content_id");
$table            = $content_exchange->_spec->table;
$ljoin[$table]    = $exchange->_spec->table.".acquittement_content_id = $table.content_id";
    
$where = array();
$where["$table.content"] = " LIKE '%$error_code%'";

$forceindex[] = "date_production";

$total_exchanges = $exchange->countList($where, null, $ljoin, $forceindex);
if ($total_exchanges == 0) {
  CAppUI::stepAjax("CEAI-tools-exchanges-no_corresponding_exchange", UI_MSG_ERROR);
}

CAppUI::stepAjax("CEAI-tools-exchanges-corresponding_exchanges", UI_MSG_OK, $total_exchanges);

$order = "date_production ASC";
$exchanges = $exchange->loadList($where, $order, "0, $count", null, $ljoin, $forceindex);

// Création du template
$smarty = new CSmartyDP();
    
switch ($tool) {
  case "reprocessing" :
    foreach ($exchanges as $_exchange) {
      try {
        $_exchange->reprocessing();
      }
      catch (CMbException $e) {
        $e->stepAjax(UI_MSG_ERROR);
      }
      
      if (!$exchange->_id) {
        CAppUI::stepAjax("CExchangeAny-msg-delete", UI_MSG_ALERT);
      }
      
      CAppUI::stepAjax("CExchangeDataFormat-reprocessed");
    }
    
    break;
    
  case "detect_collision":
    $collisions = array();
    
    foreach ($exchanges as $_exchange) {
      if ($_exchange instanceof CExchangeIHE) {
        $hl7_message = new CHL7v2Message;
        $hl7_message->parse($_exchange->_message);
        
        $xml = $hl7_message->toXML(null ,false);
        
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
/*
        echo "Date d'entrée prévue (réelle) du fichier HL7 : <strong>$entree_prevue ($entree_reelle)</strong>, ".
             "date de sortie prévue (réelle) du fichier HL7 : <strong>$sortie_prevue ($sortie_reelle)</strong> <br />";

        $entree_prevue = mbDateToLocale($sejour->entree_prevue);
        $entree_reelle = mbDateToLocale($sejour->entree_reelle);
        $sortie_prevue = mbDateToLocale($sejour->sortie_prevue);
        $sortie_reelle = mbDateToLocale($sejour->sortie_reelle);
        
        echo "Date d'entrée prévue (réelle) du séjour Mediboard : <strong>$entree_prevue ($entree_reelle)</strong>, ".
             "date de sortie prévue (réelle) du séjour Mediboard : <strong>$sortie_prevue ($sortie_reelle)</strong> <br />";
             
        echo "<a href=\"index.php?m=dPplanningOp&tab=vw_edit_sejour&sejour_id=$sejour->_id\" target=\"_blank\">Visualiser le séjour dans Mediboard</a><br />";*/
      }
        
    }  

    $smarty->assign("collisions", $collisions);
    $smarty->display("inc_detect_collisions.tpl");
      
    break;
}


CAppUI::js("next$tool()");

?>