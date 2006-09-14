<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$filepath = "modules/dPpatients/INSEE/insee.tar.gz";
$filedir = "tmp/insee";

if ($nbFiles = CMbPath::extract($filepath, $filedir)) {
  echo "<div class='message'>Extraction de $nbFiles fichiers</div>";
} else {
  echo "<div class='error'>Erreur, impossible d'extraire l'archive<div>";
  exit(0);
}

$base = $AppUI->cfg["baseINSEE"];

do_connect($base);

$sql = "DROP TABLE IF EXISTS `communes_france`";
db_exec($sql, $base);
$sql = "CREATE TABLE `communes_france` (
          `commune` varchar(25) NOT NULL default '',
          `code_postal` varchar(5) NOT NULL default '',
          `departement` varchar(25) NOT NULL default '',
          `INSEE` varchar(5) NOT NULL default '',
          PRIMARY KEY  (`INSEE`),
          KEY `commune` (`commune`),
          KEY `code_postal` (`code_postal`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table des informations sur les communes françaises';";
db_exec($sql, $base);

$fileTmpPath = $AppUI->cfg["root_dir"]."/$filedir/insee.csv";

$sql = "LOAD DATA LOCAL INFILE '$fileTmpPath'" .
    "\nINTO TABLE `communes_france`" .
    "\nFIELDS TERMINATED BY ';'" .
    "\nLINES TERMINATED BY '\r\n'";
db_exec($sql, $base);
if(!($msg = db_error($base)))
  echo '<div class="message">import effectué avec succès</div>';
else
  echo '<div class="error"><strong>une erreur s\'est produite</strong> : $msg</div>';