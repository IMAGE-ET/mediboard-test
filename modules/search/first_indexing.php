<?php

/**
 * $Id$
 *
 * @category search
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */
CCanDo::checkAdmin();
$table       = CValue::get("table");
$mapping     = CValue::get("mapping");
$log         = CValue::get("log", "");
$names_types = CValue::get("names_types");
$error       = "";

if ($table) {
  $ds    = CSQLDataSource::get("std");
  $search_indexing = new CSearchIndexing();
  foreach ($names_types as $name_type) {
    $queries = $search_indexing->firstIndexingStore($name_type);
    foreach ($queries as $query) {
      $ds->exec($query);
    }
  }
  CAppUI::displayAjaxMsg("l'opération en base s'est déroulée avec succès ", UI_MSG_OK);
}

try {
  if ($mapping) {
    $client_index = new CSearch();
    //create a client
    $client_index->createClient();
    $index = $client_index->getIndex(CAppUI::conf("db std dbname"))->exists();
    $client_index->firstIndexingMapping($names_types, $index);
    CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " s'est correctement créé", UI_MSG_OK);
  }
}
catch (Exception $e) {
  mbLog($e);
  CAppUI::displayAjaxMsg("L'index " . CAppUI::conf("db std dbname") . " existe déjà", UI_MSG_ERROR);
  $error = "mapping";
}

if ($log) {
  $client_index = new CSearchLog();
  //create a client
  $client_index->createClient();
  $index = $client_index->getIndex(CAppUI::conf("db std dbname")."_log")->exists();
  $client_index->createLogMapping($index);
}

$smarty = new CSmartyDP();
$smarty->assign("error", $error);
$smarty->display("inc_configure_es.tpl");

