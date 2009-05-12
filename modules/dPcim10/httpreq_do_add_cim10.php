<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can;

$can->needsAdmin();

set_time_limit(360);

$sourcePath = "modules/dPcim10/base/cim10.tar.gz";
$targetDir = "tmp/cim10";
$targetPath = "tmp/cim10/cim10.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("cim10");
if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  $AppUI->stepAjax("Erreur de requête SQL: $msg", UI_MSG_ERROR);
}

$AppUI->stepAjax("Import effectué avec succès de $lineCount lignes", UI_MSG_OK);

?>