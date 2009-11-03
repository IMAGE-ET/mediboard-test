<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

date_default_timezone_set("UTC");

global $can;
$can->needsAdmin();

// Vide la table finale
$ds = CSQLDataSource::get("std");
$ds->exec("TRUNCATE `temps_op`");


function buildPartialTables($tableName, $tableFields, $queryFields, $querySelect, $queryWhere) {
  $ds = CSQLDataSource::get("std");
  
  $joinedFields = join(", ", $queryFields);
  
  // Intervale de temps
	$intervalle = CValue::get("intervalle");
	
	switch ($intervalle) {
	  case "month" : $deb = mbDate("-1 month");
	  case "6month": $deb = mbDate("-6 month");
	  case "year"  : $deb = mbDate("-1  year");
	  default      : $deb = mbDate("-10 year");
	}
	
	$fin = mbDate();

	// Suppression si existe
  $drop = "DROP TABLE IF EXISTS `$tableName`";
  $ds->exec($drop);
    
	// Création de la table partielle
  $create = "CREATE TABLE `$tableName` (" .
    "\n`chir_id` int(11) unsigned NOT NULL default '0'," .
    "$tableFields" . 
    "\n`ccam` varchar(255) NOT NULL default ''," .
    "\nKEY `chir_id` (`chir_id`)," .
    "\nKEY `ccam` (`ccam`)" .
    "\n) ENGINE=MyISAM;";
  
//  mbDump($create);
  $ds->exec($create);
    
  // Remplissage de la table partielle
  $query = "INSERT INTO `$tableName` ($joinedFields, `chir_id`, `ccam`)" .
    "\nSELECT $querySelect" .
    "\noperations.chir_id, " .
    "\noperations.codes_ccam AS ccam" .
    "\nFROM operations" .
    "\nLEFT JOIN users" .
    "\nON operations.chir_id = users.user_id" .
    "\nLEFT JOIN plagesop" .
    "\nON operations.plageop_id = plagesop.plageop_id" .
    "\nWHERE operations.annulee = '0'" .
    "$queryWhere" .
    "\nAND ((plagesop.date BETWEEN '$deb' AND '$fin') OR (operations.date BETWEEN '$deb' AND '$fin'))" .
    "\nGROUP BY operations.chir_id, ccam" .
    "\nORDER BY ccam;";
    
  $ds->exec($query);
  CAppUI::stepAjax("Nombre de valeurs pour la table '$tableName': " . $ds->affectedRows(), UI_MSG_OK);
  
  // Insert dans la table principale si vide
  if (!$ds->loadResult("SELECT COUNT(*) FROM temps_op")) {
    $query = "INSERT INTO temps_op ($joinedFields, `chir_id`, `ccam`)" .
      "\nSELECT $joinedFields, `chir_id`, `ccam` " .
      "\nFROM $tableName";
    $ds->exec($query);
  } 
  // Update pour enrichir en ajoutant des colonnes sinon
  else {
    $query = "UPDATE temps_op, $tableName SET ";
    
    foreach ($queryFields as $queryField) {
      $query .= "\ntemps_op.$queryField = $tableName.$queryField, ";
    }
    
    $query.= "temps_op.chir_id = $tableName.chir_id" .
      "\nWHERE temps_op.chir_id = $tableName.chir_id" .
      "\nAND temps_op.ccam = $tableName.ccam";
    $ds->exec($query);
  }
}

// Total des opérations 
$tableName   = "op_total";
$tableFields = "\n`nb_intervention` int(11) unsigned NOT NULL default '0',";
$queryFields = array("nb_intervention");
$querySelect = "\nCOUNT(operations.operation_id) AS total,";
$queryWhere  = "";

buildPartialTables($tableName, $tableFields, $queryFields, $querySelect, $queryWhere);

// Estimations de durées 
$tableName   = "op_estimation";
$tableFields = "\n`estimation` time NOT NULL default '00:00:00',";
$queryFields = array("estimation");
$querySelect = "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.temp_operation))) AS estimation,";
$queryWhere  = "";

buildPartialTables($tableName, $tableFields, $queryFields, $querySelect, $queryWhere);

// Occupation de la salle
$tableName   = "op_occup";
$tableFields = "\n`occup_moy` time NOT NULL default '00:00:00',";
$tableFields.= "\n`occup_ecart` time NOT NULL default '00:00:00',";
$queryFields = array("occup_moy","occup_ecart");
$querySelect = "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as occup_moy,";
$querySelect.= "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as occup_ecart,";
$queryWhere  = "\nAND operations.entree_salle IS NOT NULL";
$queryWhere .= "\nAND operations.sortie_salle IS NOT NULL";
$queryWhere .= "\nAND operations.entree_salle < operations.sortie_salle";

buildPartialTables($tableName, $tableFields, $queryFields, $querySelect, $queryWhere);

// Durée de l'intervention
$tableName   = "op_duree";
$tableFields = "\n`duree_moy` time NOT NULL default '00:00:00',";
$tableFields.= "\n`duree_ecart` time NOT NULL default '00:00:00',";
$queryFields = array("duree_moy", "duree_ecart");
$querySelect = "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_moy,";
$querySelect.= "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_ecart,";
$queryWhere  = "\nAND operations.debut_op IS NOT NULL";
$queryWhere .= "\nAND operations.fin_op IS NOT NULL";
$queryWhere .= "\nAND operations.debut_op < operations.fin_op";

buildPartialTables($tableName, $tableFields, $queryFields, $querySelect, $queryWhere);

  
//echo "Liste des temps opératoire mise à jour (".count($listOps)." lignes trouvées)";

?>