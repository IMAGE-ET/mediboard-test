<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
CCanDo::checkRead();

$_min_date     = CValue::get("_min_date", "*");
$_max_date     = CValue::get("_max_date", "*");
$_date         = CValue::get("_date", null);
$specific_user = CValue::get("user_id");
$start         = (int)CValue::get("start", 0);
$words = " ";
// Données nécessaires pour la recherche
new CSearch();
$client_index = new CSearchLog();
$client_index->createClient();

$words = $client_index->constructWordsWithDate($_date, $_min_date, $_max_date);
$words = $client_index->constructWordsWithUser($words, CMediusers::get()->_id);

// Recherche fulltext
$time              = 0;
$nbresult          = 0;
$array_results     = array();

try {
  $results_query = $client_index->searchQueryString('AND', $words, $start, 20, null, null);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  // traitement des résultats
  foreach ($results as $result) {
    $var = $result->getHit();
    $var["_source"]["body"] = mb_convert_encoding($var["_source"]["body"], "WINDOWS-1252",  "UTF-8");
    $author_ids[]    = $var["_source"]["user_id"];
    $array_results[] = $var;
  }
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("La requête est mal formée", UI_MSG_ERROR);
  mbLog($e->getMessage());
}

$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("results", $array_results);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("show_score", false);

$smarty->display("inc_search_result_thesaurus.tpl");