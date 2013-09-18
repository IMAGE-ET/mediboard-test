<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$ds = CSQLDataSource::get("ASIP");
$path = "modules/eai/resources";

if (!$ds) {
  CAppUI::stepAjax("Import impossible - Aucune source de données", UI_MSG_ERROR);
  CApp::rip();
}

$files = glob("$path/*.jv");
$lineCount = 0;
foreach ($files as $_file) {
  $name = basename($_file);
  $name = substr($name, strpos($name, "_")+1);
  $table = substr($name, 0, strrpos($name, "."));
  if ($ds && $ds->loadTable($table)) {
    CAppUI::stepAjax("Import impossible - Table déjà présente", UI_MSG_ERROR);
    continue;
  }
  $ds->query("CREATE TABLE IF NOT EXISTS `$table` (
                `table_id` INT (11) UNSIGNED NOT NULL auto_increment PRIMARY KEY,
                `code` VARCHAR (255) NOT NULL,
                `oid` VARCHAR (255) NOT NULL,
                `libelle` VARCHAR (255) NOT NULL,
                INDEX (`table_id`)
              )/*! ENGINE=MyISAM */;");

  $csv = new CCSVFile($_file);
  $csv->jumpLine(3);
  while ($line = $csv->readLine()) {
    list($oid, $code, $libelle) = $line;
    if (strpos($code, "/") === false || $oid === "1.2.250.1.213.1.1.4.6") {
      continue;
    }
    $query = "INSERT INTO `$table`(
        `code`, `oid`, `libelle`)
        VALUES (?1, ?2, ?3);";
    $query = $ds->prepare($query, $code, $oid, $libelle);
    $result = $ds->query($query);
    if (!$result) {
      $msg = $ds->error();
      CAppUI::displayAjaxMsg("Erreur de requête SQL: $msg", UI_MSG_ERROR);
      CApp::rip();
    }
    $lineCount++;
  }
}

CAppUI::stepAjax("Import effectué avec succès de $lineCount lignes", UI_MSG_OK);