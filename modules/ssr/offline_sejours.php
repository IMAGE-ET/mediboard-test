<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

CApp::setMemoryLimit("256M");

$date = CValue::get("date", mbDate());

// Chargement des sejours SSR pour la date courante
$group_id = CGroups::loadCurrent()->_id;
$where["type"] = "= 'ssr'";
$where["group_id"] = "= '$group_id'";
$where["annule"] = "= '0'";

// Masquer les services inactifs
$service = new CService;
$service->group_id = $group_id;
$service->cancelled = "1";
$services = $service->loadMatchingList();
$where["service_id"] = CSQLDataSource::prepareNotIn(array_keys($services));

$sejours = CSejour::loadListForDate($date, $where);

$plannings = array();
 
// Chargement du détail des séjour
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien(1);
  
  // Bilan SSR
  $bilan = $_sejour->loadRefBilanSSR();
  $bilan->loadRefKineJournee($date);
  $bilan->loadRefPraticienDemandeur();
  
  // Détail du séjour
  $_sejour->checkDaysRelative($date);
  $_sejour->loadNDA();
  $_sejour->loadRefsNotes();
  
  // Chargement du lit
  $_sejour->loadRefCurrAffectation();
  
  // Patient
  $patient = $_sejour->loadRefPatient();
  $patient->loadIPP();
	
  // Prescription
	$prescription = $_sejour->loadRefPrescriptionSejour();
	$prescription->loadRefsLinesElementByCat();
  $prescription->countFastRecentModif();

  // Chargement du planning du sejour
  $args_planning = array();
  $args_planning["sejour_id"] = $_sejour->_id;
  $args_planning["large"] = 1;
  $args_planning["print"] = 1;
  $args_planning["height"] = 600;
  $args_planning["date"] = $date;
  
  // Chargement du planning de technicien
  $plannings[$_sejour->_id] = CApp::fetch("ssr", "ajax_planning_sejour", $args_planning);
}

// Couleurs
$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($sejours, "libelle"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("colors", $colors);
$smarty->assign("date", $date);
$smarty->assign("order_col", "");
$smarty->assign("order_way", "");
$smarty->assign("plannings", $plannings);
$smarty->display("offline_sejours.tpl");

?>