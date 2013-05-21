<?php

/**
 * dPcim10
 *
 * @category Cim10
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

global $can;

$can->needsAdmin();

CApp::setTimeLimit(360);

$sourcePath = "modules/dPcim10/base/cim10.tar.gz";
$targetDir = "tmp/cim10";
$targetPath = "tmp/cim10/cim10.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("cim10");
if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Erreur de requête SQL: $msg", UI_MSG_ERROR);
}

CAppUI::stepAjax("Import effectué avec succès de $lineCount lignes", UI_MSG_OK);
