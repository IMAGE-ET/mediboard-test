<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can;

$can->needsAdmin();

$sourcePath = "modules/dPpatients/INSEE/insee.tar.gz";
$targetDir = "tmp/insee";

// Extract the CSV Files dump
if (null == $nbFiles = CMbPath::extract($sourcePath, $targetDir)) {
  $AppUI->stepAjax("Erreur, impossible d'extraire l'archive", UI_MSG_ERROR);
} 

$AppUI->stepAjax("Extraction de $nbFiles fichier(s)", UI_MSG_OK);

$ds = CSQLDataSource::get("INSEE");

// Create communes table
$query = "DROP TABLE IF EXISTS `communes_france`";
$ds->exec($query);
$query = "CREATE TABLE `communes_france` (
          `commune` varchar(25) NOT NULL default '',
          `code_postal` varchar(5) NOT NULL default '',
          `departement` varchar(25) NOT NULL default '',
          `INSEE` varchar(5) NOT NULL default '',
          PRIMARY KEY  (`INSEE`),
          KEY `commune` (`commune`),
          KEY `code_postal` (`code_postal`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des informations sur les communes françaises';";
$ds->exec($query);

// Load communes data from CSV
$targetPath = $AppUI->getTmpPath("insee/insee.csv");
$query = "LOAD DATA LOCAL INFILE '$targetPath'" .
    "\nINTO TABLE `communes_france`" .
    "\nFIELDS TERMINATED BY ';'" .
    "\nLINES TERMINATED BY '\r\n'";
$ds->exec($query);
if ($msg = $ds->error()) {
  $AppUI->stepAjax("Erreur d'import CSV : $msg", UI_MSG_ERROR);
}

$nbRows = $ds->affectedRows();
$AppUI->stepAjax("Import villes INSEE effectué avec succès : $nbRows enregistrements", UI_MSG_OK);

// Create pays table
$query = "DROP TABLE IF EXISTS `pays`";
$ds->exec($query);
$query = "CREATE TABLE `pays` (
          `numerique` mediumint(3) unsigned zerofill NOT NULL,
          `alpha_3` char(3) NOT NULL default '',
          `alpha_2` char(2) NOT NULL default '',
          `nom_fr` varchar(255) NOT NULL default '',
          `nom_ISO` varchar(255) NOT NULL default '',
          PRIMARY KEY (`alpha_3`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des nom de pays';";
$ds->exec($query);

// Load pays data from CSV
$targetPath = $AppUI->getTmpPath("insee/pays-iso.csv");
$query = "LOAD DATA LOCAL INFILE '$targetPath'" .
    "\nINTO TABLE `pays`" .
    "\nFIELDS TERMINATED BY '|'" .
    "\nENCLOSED BY ''" .
    "\nLINES TERMINATED BY '\r\n'";
$ds->exec($query);
if ($msg = $ds->error()) {
  $AppUI->stepAjax("Erreur d'import CSV : $msg", UI_MSG_ERROR);
}

$nbRows = $ds->affectedRows();
$AppUI->stepAjax("Import pays ISO effectué avec succès : $nbRows enregistrements", UI_MSG_OK);

?>