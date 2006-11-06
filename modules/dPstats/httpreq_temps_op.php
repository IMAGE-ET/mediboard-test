<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision: $
* @author Sébastien Fillonneau
*/

if(function_exists("date_default_timezone_set")) {
  date_default_timezone_set("UTC");
}

global $AppUI, $canRead, $canEdit, $m;

$intervalle = mbGetValueFromGet("intervalle", "none");

// Vide la table contenant les données
db_exec("TRUNCATE `temps_op`"); db_error();

$sql = "SELECT operations.chir_id, " .
       "\nCOUNT(operations.operation_id) AS total," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as duree_bloc," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as ecart_bloc," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_operation," .
       "\nSEC_TO_TIME(STD(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as ecart_operation," .
       "\nSEC_TO_TIME(AVG(TIME_TO_SEC(operations.temp_operation))) AS estimation,";

$sql .= "\noperations.codes_ccam AS ccam";
$sql .="\nFROM operations" .
       "\nLEFT JOIN users" .
       "\nON operations.chir_id = users.user_id" .
       "\nLEFT JOIN plagesop" .
       "\nON operations.plageop_id = plagesop.plageop_id" .
       "\nWHERE operations.annulee = '0'" .
       "\nAND operations.entree_salle IS NOT NULL" .
       "\nAND operations.debut_op IS NOT NULL" .
       "\nAND operations.fin_op IS NOT NULL" .
       "\nAND operations.sortie_salle IS NOT NULL" .
       "\nAND operations.entree_salle < operations.debut_op";
       "\nAND operations.debut_op < operations.fin_op";
       "\nAND operations.fin_op < operations.sortie_salle";

switch($intervalle) {
  case "month":
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 month")."' AND '".mbDate()."'";
    break;
  case "6month":
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-6 month")."' AND '".mbDate()."'";
    break;
  case "year":
    $sql .= "\nAND plagesop.date BETWEEN '".mbDate("-1 year")."' AND '".mbDate()."'";
    break;
}

$sql .= "\nGROUP BY operations.chir_id, ccam" .
        "\nORDER BY ccam";
       
$listOps = db_loadList($sql);       

// Mémorisation des données dans MySQL
foreach($listOps as $keylistOps => $curr_listOps){
  // Mémorisation des données dans MySQL
  $sql = "INSERT INTO `temps_op` (`temps_op_id`, `chir_id`, `ccam`, `nb_intervention`, `estimation`, `occup_moy`, `occup_ecart`, `duree_moy`, `duree_ecart`)
          VALUES (NULL, 
                  '".$curr_listOps["chir_id"]."',
            	  '".$curr_listOps["ccam"]."',
            	  '".$curr_listOps["total"]."',
            	  '".$curr_listOps["estimation"]."',
            	  '".$curr_listOps["duree_bloc"]."',
            	  '".$curr_listOps["ecart_bloc"]."',
            	  '".$curr_listOps["duree_operation"]."',
            	  '".$curr_listOps["ecart_operation"]."');";
  db_exec( $sql ); db_error();
}

echo "Liste des temps opératoire mise à jour (".count($listOps)." lignes trouvées)";
?>
