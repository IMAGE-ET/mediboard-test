<?php 

/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
 */

CCanDo::checkAdmin();

CApp::setTimeLimit(360);

$source_path = "modules/ameli/base/aati.tar.gz";
$target_dir = "tmp/ameli/aati";

$target_tables    = "tmp/ameli/aati/tables.sql";
$target_datas = "tmp/ameli/aati/data.sql";


// Extract the SQL dump
if (null == $nb_files = CMbPath::extract($source_path, $target_dir)) {
  CAppUI::stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
}

CAppUI::stepAjax("Extraction de $nb_files fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("ameli");

// Création des tables
if (null == $line_count = $ds->queryDump($target_tables, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des tables - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Création de $line_count tables", UI_MSG_OK);

// Ajout des données de base
if (null == $line_count = $ds->queryDump($target_datas, true)) {
  $msg = $ds->error();
  CAppUI::stepAjax("Import des données de base - erreur de requête SQL: $msg", UI_MSG_ERROR);
}
CAppUI::stepAjax("Import des données de base effectué avec succès ($line_count lignes)", UI_MSG_OK);
