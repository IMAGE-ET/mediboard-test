<?php

/**
 * $Id$
 *
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
CCanDo::checkRead();
// Récupération des valeurs nécessaires
$words         = CValue::get("words");
$_min_date     = CValue::get("_min_date", "*");
$_max_date     = CValue::get("_max_date", "*");
$_date         = CValue::get("_date");
$specific_user = CValue::get("user_id");
$start         = (int)CValue::get("start", 0);
$names_types   = CValue::get("names_types", array());
$aggregate     = CValue::get("aggregate");
$fuzzy_search  = CValue::get("fuzzy", null);
$sejour_id     = CValue::get("sejour_id", null);
$contexte      = CValue::get("contexte");
$user          = CMediusers::get();

if (in_array("CPrescriptionLineMedicament", $names_types)) {
  $names_types[] = "CPrescriptionLineMix";
  $names_types[] = "CPrescriptionLineElement";
}

// Données nécessaires pour la recherche
$client_index = new CSearch();
$client_log = new CSearchLog();
$client_index->createClient();
$client_log->createClient();

// Journalisation de la recherche
$group = CGroups::loadCurrent();
if ($words && CAppUI::conf("search indexing active_indexing_log", $group)) {
  try {
    $client_log->log($names_types, $contexte, $user->_id, $words, $aggregate);
  }
  catch (Exception $e) {
    CAppUI::displayAjaxMsg("La requête ne peut pas être journalisée", UI_MSG_WARNING);
    mbLog($e->getMessage());
  }
}

// Recherche fulltext
$time              = 0;
$nbresult          = 0;
$array_results     = array();
$array_highlights  = array();
$array_aggregation = array();
$objects_refs      = array();
$authors           = array();
$author_ids        = array();
$patients          = array();

try {
  $date = $client_index->constructWordsWithDate($_date, $_min_date, $_max_date);
  $results_query = $client_index->searchQueryString($words, $start, 30, $names_types, $aggregate, $sejour_id, $specific_user, null, $date, $fuzzy_search);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  // traitement des résultats
  $patient_ids       = array();
  foreach ($results as $result) {
    $var             = $result->getHit();
    $author_ids[]    = $var["_source"]["author_id"];
    $patient_ids[]   = $var["_source"]["patient_id"];
    $var["_source"]["body"] = CMbString::normalizeUtf8($var["_source"]["body"]);
    $array_results[] = $var;

    // Traitement des highlights
    $highlights =$result->getHighlights();
    if (count($highlights) != 0) {
      $array_highlights[] = mb_convert_encoding(implode(" [...] ", $highlights['body']), "WINDOWS-1252",  "UTF-8");
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

  // traitement des patients
  foreach ($patient_ids as $_patient) {
    $patients[$_patient] = CMbObject::loadFromGuid("CPatient-$_patient");
  }

  //traitement des contextes référents si aggregation est cochée
  if ($aggregate) {
    $objects_refs = $client_index->loadAggregationObject($results_query->getAggregations("ref_class"));
  }
}
catch (Exception $e) {
  CAppUI::displayAjaxMsg("La requête est mal formée", UI_MSG_ERROR);
  mbLog($e->getMessage());
}

$rss_items = array();
$items = array();
if ($contexte == "pmsi" && CModule::getActive("atih")) {
  $rss = new CRSS();
  $rss->sejour_id = $sejour_id;
  $rss->loadMatchingObject();
  $rss_items = $rss->loadRefsSearchItems();

  foreach ($rss_items as $_items) {
    $items[] = $_items->search_class."-".$_items->search_id;
  }
}

$smarty = new CSmartyDP();
$smarty->assign("start", $start);
$smarty->assign("authors", $authors);
$smarty->assign("patients", $patients);
$smarty->assign("results", $array_results);
$smarty->assign("highlights", $array_highlights);
$smarty->assign("objects_refs", $objects_refs);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("words", $words);
$smarty->assign("contexte", $contexte);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("fuzzy_search", $fuzzy_search);
$smarty->assign("rss_items", $rss_items);
$smarty->assign("items", $items);

$smarty->display("inc_results_search.tpl");
