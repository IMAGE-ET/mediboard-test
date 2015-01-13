<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

$date     = CValue::get("date");
$user_id  = CValue::get("user_id", null);
$words    = html_entity_decode(CValue::get("words"));
$object_ref_id  = CValue::get("object_ref_id");
$object_ref_class  = CValue::get("object_ref_class");
$fuzzy_search = CValue::get("fuzzy_search", null);
$fuzzy_search = CValue::get("fuzzy_search", null);
$types = CValue::get("types", array());

if ($date || $user_id) {
  $client_index  = new CSearchLog();
  $client_index->createClient();
  $date = CMbDT::format($date, "%Y/%m/%d");
  $words .= "date:[".$date." TO "."$date] user_id:(".$user_id.")";
  $agregation = array();
  $tabActive = "";
}
else{
  $client_index  = new CSearch();
  $client_index->createClient();
  $words .= " object_ref_class:".$object_ref_class." "."object_ref_id:".$object_ref_id;
  $results = $client_index->queryByType($words, null, $types);
  $agregation = $results->getAggregation("ref_type");
  $tabActive = $agregation["buckets"][0]["key"];
}


try {
  $results = $client_index->queryByType($words, CAppUI::conf("db std dbname"), $types);
  $agregation = $results->getAggregation("ref_type");
  $tabActive = $agregation["buckets"][0]["key"];
} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Problème à la récupération des données", UI_MSG_ERROR);
}

$smarty = new CSmartyDP();
$smarty->assign("agregation", $agregation["buckets"]);
$smarty->assign("date", $date);
$smarty->assign("user_id", $user_id);
$smarty->assign("object_ref_class", $object_ref_class);
$smarty->assign("object_ref_id", $object_ref_id);
$smarty->assign("fuzzy_search", $fuzzy_search);
$smarty->assign("tabActive", $tabActive);
$smarty->display("inc_results_list_details.tpl");