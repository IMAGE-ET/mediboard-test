<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Plateaux disponibles
$date = CValue::getOrSession("date", mbDate());
$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "patient_id");
$show = CValue::getOrSession("show", "all");

// Filtre
$filter = new CSejour;
$filter->service_id = CValue::getOrSession("service_id");

// Chargement des sejours SSR pour la date selectionnée
$group_id = CGroups::loadCurrent()->_id;
$where["type"] = "= 'ssr'";
$where["group_id"] = "= '$group_id'";
$where["annule"] = "= '0'";
$order = null;

if ($order_col == "entree") {
  $order = "sejour.entree $order_way, patients.nom, patients.prenom";
}

if ($order_col == "sortie") {
  $order = "sejour.sortie $order_way, patients.nom, patients.prenom";
}

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom, sejour.entree";
}

$sejours = CSejour::loadListForDate($date, $where, $order, null, null, $ljoin);
 
// Filtre sur les services
$services = CMbObject::massLoadFwdRef($sejours, "service_id");
if ($filter->service_id) {
  $filter->loadFwdRef("service_id");
  $services[$filter->service_id] = $filter->_fwd["service_id"];
}

// Chargement du détail des séjour
foreach ($sejours as $_sejour) {
  if ($filter->service_id && $_sejour->service_id != $filter->service_id) {
    unset($sejours[$_sejour->_id]);
    continue;
	}

  $_sejour->loadRefPrescriptionSejour();
	if ($show == "nopresc" && $_sejour->_ref_prescription_sejour->_id) {
		unset($sejours[$_sejour->_id]);
		continue;
	}
	
  $_sejour->checkDaysRelative($date);
  $_sejour->loadNumDossier();
  $_sejour->loadRefsNotes();
	$_sejour->countBackRefs("evenements_ssr");
	$_sejour->countEvenementsSSR($date);
	$_sejour->loadFwdRef("sejour_id");

  // Bilan SSR
  $_sejour->loadRefBilanSSR();
  $bilan =& $_sejour->_ref_bilan_ssr;
	
	// Kinés référent et journée
  $bilan->loadRefKineJournee($date);
	
  // Patient
  $_sejour->loadRefPatient();
	$patient =& $_sejour->_ref_patient;
	$patient->loadIPP();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("filter", $filter);
$smarty->assign("sejours", $sejours);
$smarty->assign("services", $services);
$smarty->assign("show", $show);
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);
$smarty->display("vw_sejours_ssr.tpl");

?>