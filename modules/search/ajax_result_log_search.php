<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$words         = CValue::get("words");
$_min_date     = CValue::get("_min_date", "*");
$_max_date     = CValue::get("_max_date", "*");
$_date         = CValue::get("_date");
$specific_user = CValue::get("user_id");
$start         = (int)CValue::get("start", 0);
$names_types   = CValue::get("names_types", array());
$contextes    = CValue::get("contextes", array());
$aggregate     = CValue::get("aggregate");

if (in_array("CPrescriptionLineMedicament", $names_types)) {
  $names_types[] = "CPrescriptionLineMix";
  $names_types[] = "CPrescriptionLineElement";
}

// Données nécessaires pour la recherche
new CSearch();
$client_index = new CSearchLog();
$client_index->createClient();

$words .= $client_index->constructWordsWithDate($_date, $_min_date, $_max_date);
$words = $client_index->constructWordsWithType($words, $names_types);
$words = $client_index->constructWordsWithUser($words, $specific_user);

// Recherche fulltext
$time              = 0;
$nbresult          = 0;
$array_results     = array();
$array_highlights  = array();
$array_aggregation = array();
$objects_refs      = array();
$authors           = array();
$author_ids        = array();

try {
  $results_query = $client_index->searchQueryString('AND', $words, $start, 30, $contextes, $aggregate);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  // traitement des résultats
  foreach ($results as $result) {
    $var = $result->getHit();
    $author_ids[]    = $var["_source"]["user_id"];
    $var["_source"]["body"] = mb_convert_encoding($var["_source"]["body"], "WINDOWS-1252",  "UTF-8");
    $array_results[] = $var;
    // Traitement des highlights
    $highlights = $result->getHighlights();
    if (count($highlights) != 0) {
      $array_highlights[] =  mb_convert_encoding(implode(" [...] ", $highlights['body']), "WINDOWS-1252",  "UTF-8");
    }
    else {
      $array_highlights[] = "";
    }
  }

  // traitement des auteurs
  foreach ($author_ids as $author) {
    $authors[$author] = CMbObject::loadFromGuid("CMediusers-$author");
    $authors[$author]->loadRefFunction();
  }

  //traitement des contextes référents si aggregation est cochée
  if ($aggregate) {
    $objects_refs = $client_index->loadAggregationLog($results_query->getAggregations("date_log"));
  }
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("La requête est mal formée", UI_MSG_ERROR);
  mbLog($e->getMessage());
}

$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("authors", $authors);
$smarty->assign("results", $array_results);
$smarty->assign("highlights", $array_highlights);
$smarty->assign("objects_refs", $objects_refs);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("words", $words);
$smarty->display("inc_results_log_search.tpl");