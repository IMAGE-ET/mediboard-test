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
$group_by = CValue::get("group_by");

// Filtre
$filter = new CSejour;
$filter->service_id   = CValue::getOrSession("service_id");
$filter->praticien_id = CValue::getOrSession("praticien_id");
$filter->referent_id  = CValue::getOrSession("referent_id");

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
$services = array();
$praticiens = array();
$kines = array();
$sejours_by_kine = array();

// Chargement du détail des séjour
foreach ($sejours as $_sejour) {
	// Filtre sur service
	$service = $_sejour->loadFwdRef("service_id");
  $services[$service->_id] = $service;
  if ($filter->service_id && $_sejour->service_id != $filter->service_id) {
    unset($sejours[$_sejour->_id]);
    continue;
	}

  // Filtre sur prescription
  $_sejour->loadRefPrescriptionSejour();
	if ($show == "nopresc" && $_sejour->_ref_prescription_sejour->_id) {
		unset($sejours[$_sejour->_id]);
		continue;
	}

  // Filtre sur praticien
  $_sejour->loadRefPraticien(1);
  $praticien =& $_sejour->_ref_praticien;
  $praticiens[$praticien->_id] = $praticien;
  if ($filter->praticien_id && $_sejour->praticien_id != $filter->praticien_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }
	
  // Bilan SSR
  $_sejour->loadRefBilanSSR();
  $bilan =& $_sejour->_ref_bilan_ssr;

  // Kinés référent et journée
  $bilan->loadRefKineJournee($date);
  $kine_journee = $bilan->_ref_kine_journee;
  $kines[$kine_journee->_id] = $kine_journee;
  $kine_referent = $bilan->_ref_kine_referent;
  $kines[$kine_referent->_id] = $kine_referent;
  if ($filter->referent_id && $kine_referent->_id != $filter->referent_id && $kine_journee->_id != $filter->referent_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }

  // Regroupement par kine
  $sejours_by_kine[$kine_referent->_id][] = $_sejour;
  if ($kine_journee->_id && $kine_journee->_id != $kine_referent->_id) {
  	$sejours_by_kine[$kine_journee->_id ][] = $_sejour;
	}
	
  // Détail du séjour
  $_sejour->checkDaysRelative($date);
  $_sejour->loadNumDossier();
  $_sejour->loadRefsNotes();
	$_sejour->countBackRefs("evenements_ssr");
	$_sejour->countEvenementsSSR($date);
	
  // Patient
  $_sejour->loadRefPatient();
	$patient =& $_sejour->_ref_patient;
	$patient->loadIPP();

  // Modification des prescription
	$_sejour->_ref_prescription_sejour->loadRefsLinesElementByCat();
	$_sejour->_ref_prescription_sejour->countRecentModif();
}

// Ajustements services
$service = new CService;
$service->load($filter->service_id);
$services[$service->_id] = $service;
unset($services[""]);

// Ajustements kinés
$kine = new CMediusers;
$kine->load($filter->referent_id);
$kines[$kine->_id] = $kine;
unset($kines[""]);

// Tris a posteriori : détruit les clés !
array_multisort(CMbArray::pluck($kines     , "_view"), SORT_ASC, $kines);
array_multisort(CMbArray::pluck($services  , "_view"), SORT_ASC, $services);
array_multisort(CMbArray::pluck($praticiens, "_view"), SORT_ASC, $praticiens);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("date", $date);
$smarty->assign("filter", $filter);
$smarty->assign("sejours", $sejours);
$smarty->assign("sejours_by_kine", $sejours_by_kine);
$smarty->assign("kines", $kines);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services", $services);
$smarty->assign("show", $show);
$smarty->assign("group_by", $group_by);
$smarty->assign("order_way", $order_way);
$smarty->assign("order_col", $order_col);
$smarty->display("vw_sejours_ssr.tpl");

?>