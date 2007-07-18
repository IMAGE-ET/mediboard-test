<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;

$can->needsAdmin();

set_time_limit(360);
ini_set("memory_limit", "128M");

$sourcePath = "modules/dPccam/base/ccamV2.tar.gz";
$targetDir = "tmp/ccam";
$targetPath = "tmp/ccam/ccamV2.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ccamV2");
if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Erreur de requête SQL: $msg", UI_MSG_ERROR);
}

$AppUI->stepAjax("import effectué avec succès de $lineCount lignes", UI_MSG_OK);
?>
