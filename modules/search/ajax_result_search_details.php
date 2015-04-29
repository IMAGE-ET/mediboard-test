<?php 

/**
 * $Id$
 *  
 * @category Search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */
CCanDo::checkEdit();

$object_ref_id  = CValue::get("object_ref_id");
$object_ref_class  = CValue::get("object_ref_class");
$type = CValue::get("type");
$fuzzy_search = CValue::get("fuzzy_search", null);
$words = html_entity_decode(CValue::get("words"));

$client_index  = new CSearch();
$client_index->createClient();
$details = " object_ref_class:".$object_ref_class." "."object_ref_id:".$object_ref_id;

// Recherche fulltext
$time              = 0;
$nbresult          = 0;
$array_results     = array();
$array_highlights  = array();
$authors           = array();
$author_ids        = array();
$patients          = array();

try {
  $results_query = $client_index->searchQueryString($words, 0, 30, array($type), false, null, null, $details, null, $fuzzy_search);
  $results       = $results_query->getResults();
  $time          = $results_query->getTotalTime();
  $nbresult      = $results_query->getTotalHits();

  // traitement des résultats
  $patient_ids       = array();

  foreach ($results as $result) {
    $var = $result->getHit();
    $author_ids[]    = $var["_source"]["author_id"];
    $patient_ids[]   = $var["_source"]["patient_id"];
    $var["_source"]["body"] = CMbString::normalizeUtf8($var["_source"]["body"]);
    $array_results[] = $var;
    $highlights = $result->getHighlights();
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

} catch (Exception $e) {
  CAppUI::displayAjaxMsg("Problème à la récupération des données", UI_MSG_ERROR);
}

$smarty = new CSmartyDP();
$smarty->assign("start", null);
$smarty->assign("authors", $authors);
$smarty->assign("patients", $patients);
$smarty->assign("results", $array_results);
$smarty->assign("time", $time);
$smarty->assign("nbresult", $nbresult);
$smarty->assign("highlights", $array_highlights);
$smarty->assign("contexte", "classique");
$smarty->display("inc_results_search_details.tpl");