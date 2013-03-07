<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user = CUser::get();

$ds = CSQLDataSource::get("std");
$datetime = CMbDT::dateTime();
$date_max = $datetime;
$date_min = CMbDT::dateTime("-1 DAY", $date_max);

$praticien_id = CValue::get("praticien_id", $user->_id);

// Chargement des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

/* Chargement de la liste des sejours qui possedents des transmissions ou
   observations dans les dernieres 24 heures */

$sejour = new CSejour(); 
$sejours = array();
$where = array();
$ljoin["transmission_medicale"] = "transmission_medicale.sejour_id = sejour.sejour_id";
$ljoin["observation_medicale"] = "observation_medicale.sejour_id = sejour.sejour_id";

$where[] = "(transmission_medicale.date BETWEEN '$date_min' and '$date_max') OR
						(observation_medicale.date BETWEEN '$date_min' and '$date_max')";

$where["sejour.praticien_id"] = " = '$praticien_id'";
$sejours = $sejour->loadList($where, null, null, null, $ljoin);

foreach($sejours as $_sejour){
  $_sejour->loadRefPatient();
}

// Variables de templates
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("praticien_id", $praticien_id);
$smarty->display("vw_bilan_transmissions.tpl");


?>