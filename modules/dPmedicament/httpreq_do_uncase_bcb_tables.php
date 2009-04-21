<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision$
* @author Alexis Granger
*/

global $can;
$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$errors = 0;
$success = 0;
$ds = CBcbObject::getDataSource();
foreach ($ds->loadTables() as $table) {
  if (!$ds->renameTable($table, strtoupper($table))) {
    CAppUI::stepAjax("Failed to uppercase table '$table'", UI_MSG_WARNING);
    $errors++;
    continue;
  }
  $success++;
}

if ($errors) {
  CAppUI::stepAjax("Renaming failed on $errors tables", UI_MSG_WARNING);
}

CAppUI::stepAjax("Renaming succeded on $success tables", UI_MSG_OK);
?>