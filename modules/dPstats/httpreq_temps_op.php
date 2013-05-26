<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

date_default_timezone_set("UTC");

$intervalle = CValue::get("intervalle", "none");

// Vide la table contenant les données
$ds = CSQLDataSource::get("std");
$ds->exec("TRUNCATE `temps_op`");

switch ($intervalle) {
  case "month" : $deb = CMbDT::date("-1 month");
  case "6month": $deb = CMbDT::date("-6 month");
  case "year"  : $deb = CMbDT::date("-1  year");
  default      : $deb = CMbDT::date("-10 year");
}

$fin = CMbDT::date();

$sql = "SELECT operations.chir_id,
          COUNT(operations.operation_id) AS total,
          SEC_TO_TIME(AVG(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as duree_bloc,
          SEC_TO_TIME(STD(TIME_TO_SEC(operations.sortie_salle)-TIME_TO_SEC(operations.entree_salle))) as ecart_bloc,
          SEC_TO_TIME(AVG(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as duree_operation,
          SEC_TO_TIME(STD(TIME_TO_SEC(operations.fin_op)-TIME_TO_SEC(operations.debut_op))) as ecart_operation,
          SEC_TO_TIME(AVG(TIME_TO_SEC(operations.temp_operation))) AS estimation,
          operations.codes_ccam AS ccam
        FROM operations
          LEFT JOIN users    ON operations.chir_id = users.user_id
          LEFT JOIN plagesop ON operations.plageop_id = plagesop.plageop_id
        WHERE operations.annulee = '0'
          AND operations.entree_salle IS NOT NULL
          AND operations.debut_op IS NOT NULL
          AND operations.fin_op IS NOT NULL
          AND operations.sortie_salle IS NOT NULL
          AND operations.entree_salle < operations.debut_op
          AND operations.debut_op < operations.fin_op
          AND operations.fin_op < operations.sortie_salle
          AND ((plagesop.date BETWEEN '$deb' AND '$fin') OR (plagesop.date BETWEEN '$deb' AND '$fin'))
        GROUP BY operations.chir_id, ccam
        ORDER BY ccam";
       
$listOps = $ds->loadList($sql);       

// Mémorisation des données dans MySQL
foreach ($listOps as $keylistOps => $curr_listOps) {
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
  $ds->exec( $sql ); $ds->error();
}

echo "Liste des temps opératoire mise à jour (".count($listOps)." lignes trouvées)";
