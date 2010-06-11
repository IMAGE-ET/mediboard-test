<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = CValue::getOrSession("praticien_id");
$service_id   = CValue::getOrSession("service_id");
$date_min     = CValue::getOrSession("date_min", mbDate("- 3 month"));
$date_max     = CValue::getOrSession("date_max", mbDate());

$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();

$service = new CService();
$services = $service->loadListWithPerms();

$group_id = CGroups::loadCurrent()->_id;

if($praticien_id){
	$praticien->load($praticien_id);
	$praticien->isAnesth();
}

// Chargement du nombre de sejours par semaine sur 6 mois
$ds = CSQLDataSource::get("std");
$query = "SELECT count(`sejour`.`sejour_id`) AS `nb_sejours`,  DATE_FORMAT(`sejour`.`entree`, '%U') AS `period`
          FROM `sejour`";

if($service_id){
  $query .= " LEFT JOIN `affectation` ON `sejour`.`sejour_id` = `affectation`.`sejour_id`
	            LEFT JOIN `lit` ON `affectation`.`lit_id` = `lit`.`lit_id`
              LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
              LEFT JOIN `service` ON `chambre`.`service_id` = `service`.`service_id`";
}

$query .= " WHERE DATE(`sejour`.`entree`) BETWEEN '$date_min' AND '$date_max'
            AND `sejour`.`group_id` = '$group_id'";
					
if($praticien_id && !$praticien->_is_anesth){
  $query .= " AND `sejour`.`praticien_id` = '$praticien_id'";	
}

if($service_id){
	$query .= " AND `service`.`service_id` = '$service_id'";
}

$query .= " GROUP BY `period`
            ORDER BY `sejour`.`entree`";
					 
// Initialisation du tableau pour afficher toutes les semaines
$date_min = mbDate("first sunday", $date_min);
for ($day = $date_min; $day <= $date_max; $day = mbDate("+1 week", $day)) {
	$period = mbTransformTime(null, $day, "%U");
	$results_sejours[$period]["nb_sejours"] = 0;
}

	
$_results_sejours = $ds->loadList($query);

foreach($_results_sejours as $_result_sejour){
	if(array_key_exists($_result_sejour["period"], $results_sejours)){
    $results_sejours[$_result_sejour["period"]]["nb_sejours"] = $_result_sejour["nb_sejours"];
	}
}

$query_presc = "SELECT COUNT(DISTINCT `sejour`.`sejour_id`) as `nb_sejours`, DATE_FORMAT(`sejour`.`entree`, '%U') as `period`
               FROM `sejour`
               LEFT JOIN `prescription` ON (`sejour`.`sejour_id` = `prescription`.`object_id` AND `prescription`.`type` = 'sejour')";
							
if($praticien_id){
	$query_presc .= " LEFT JOIN `prescription_line_medicament` ON (`prescription`.`prescription_id` = `prescription_line_medicament`.`prescription_id` AND `prescription_line_medicament`.`praticien_id` = '$praticien_id')
              LEFT JOIN `prescription_line_element`    ON (`prescription`.`prescription_id` = `prescription_line_element`.`prescription_id` AND `prescription_line_element`.`praticien_id` = '$praticien_id')
              LEFT JOIN `prescription_line_mix`        ON (`prescription`.`prescription_id` = `prescription_line_mix`.`prescription_id` AND `prescription_line_mix`.`praticien_id` = '$praticien_id')";
}	else {
  $query_presc .= " LEFT JOIN `prescription_line_medicament` ON `prescription`.`prescription_id` = `prescription_line_medicament`.`prescription_id`
              LEFT JOIN `prescription_line_element`    ON `prescription`.`prescription_id` = `prescription_line_element`.`prescription_id`
              LEFT JOIN `prescription_line_mix`        ON `prescription`.`prescription_id` = `prescription_line_mix`.`prescription_id`";
}

if($service_id){
  $query_presc .= " LEFT JOIN `affectation` ON `sejour`.`sejour_id` = `affectation`.`sejour_id`
              LEFT JOIN `lit` ON `affectation`.`lit_id` = `lit`.`lit_id`
              LEFT JOIN `chambre` ON `lit`.`chambre_id` = `chambre`.`chambre_id`
              LEFT JOIN `service` ON `chambre`.`service_id` = `service`.`service_id`";
}

$query_presc .= " WHERE `sejour`.`group_id` = '$group_id'
                 AND DATE(`sejour`.`entree`) BETWEEN '$date_min' AND '$date_max'
                 AND (`prescription_line_medicament`.`prescription_line_medicament_id` IS NOT NULL
                 OR `prescription_line_element`.`prescription_line_element_id` IS NOT NULL
                 OR `prescription_line_mix`.`prescription_line_mix_id` IS NOT NULL)";


if($service_id){
  $query_presc .= " AND `service`.`service_id` = '$service_id'";
}
						
if($praticien_id && !$praticien->_is_anesth){
  $query .= " AND `sejour`.`praticien_id` = '$praticien_id'"; 
}
					
$query_presc .= " GROUP BY `period`
                 ORDER BY `sejour`.`entree`;";

$_results_presc = $ds->loadList($query_presc);

foreach($_results_presc as $_result_presc){
  $results_presc[$_result_presc["period"]] = $_result_presc["nb_sejours"];
}

$ticks = array();
foreach($results_sejours as $_period => &$_result_sejour){
	$ticks[] = array(count($ticks), "$_period");
  $nb_presc = array_key_exists($_period, $results_presc) ? $results_presc[$_period] : 0;
  $_result_sejour["nb_presc"] = $nb_presc;
}

$series = array();
foreach($results_sejours as $period => $_result){
  $series[0]["data"][] = array(
	  count($series[0]["data"]), 
		intval($_result["nb_presc"]),
		$_result["nb_presc"] / ($_result["nb_sejours"])
		
  );
  $series[1]["data"][] = array(count($series[1]["data"]), $_result["nb_sejours"] - $_result["nb_presc"]);
	$series[2]["data"][] = array(count($series[2]["data"]), $_result["nb_sejours"]);
}

$series[0]["label"] = utf8_encode("Séjours avec prescriptions");
$series[0]["markers"] = array("show" => true);

$series[1]["label"] =  utf8_encode("Séjours sans prescription");

$series[2]["bars"] = array("show" => false);
$series[2]["markers"] = array("show" => true);
$series[2]["label"] = "Total";
$series[2]["color"] = "#000";

$options = CFlotrGraph::merge("bars", array(
  'xaxis' => array('ticks' => $ticks, 'labelsAngle' => 45),
  'bars' => array('stacked' => true)
));

$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("series", $series);
$smarty->assign("options", $options);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services", $services);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("service_id", $service_id);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->display("vw_stats.tpl");

?>