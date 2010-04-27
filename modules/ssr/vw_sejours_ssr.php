<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

// Plateaux disponibles
$date = CValue::getOrSession("date", mbDate());
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "patient_id");

// Chargement des sejours SSR pour la date selectionnée
$group_id = CGroups::loadCurrent()->_id;
$where["type"] = "= 'ssr'";
$where["group_id"] = "= '$group_id'";

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

if($order_col == "patient_id"){
  $order = "patients.nom $order_way, patients.prenom, sejour.entree";
}
if($order_col == "praticien_id"){
  $order = "users.user_last_name $order_way, users.user_first_name";
}

$sejours = CSejour::loadListForDate($date, $where, $order, null, null, $ljoin);
 
foreach($sejours as $_sejour) {
	$_sejour->loadRefPraticien();
  $_sejour->checkDaysRelative($date);
  $_sejour->loadRefPatient();
  $_sejour->loadRefBilanSSR();
  $_sejour->loadRefPrescriptionSejour();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("sejours", $sejours);
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);
$smarty->display("vw_sejours_ssr.tpl");

?>