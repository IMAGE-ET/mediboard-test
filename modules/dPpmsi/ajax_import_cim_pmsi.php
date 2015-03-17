<?php 

/**
 * $Id$
 *  
 * @category pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkAdmin();

$ds = CSQLDataSource::get("cim10");

$sourcePath = "modules/dPpmsi/base/cimPMSI.tar.gz";
$targetDir = "tmp/pmsi";
$targetPath = "tmp/pmsi/CIM10.sql";

// Extract the SQL dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
}

CAppUI::stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

if (null == $lineCount = $ds->queryDump($targetPath)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Erreur de requête SQL: $msg", UI_MSG_ERROR);
}

CAppUI::stepAjax("Import effectué avec succès de $lineCount lignes", UI_MSG_OK);
