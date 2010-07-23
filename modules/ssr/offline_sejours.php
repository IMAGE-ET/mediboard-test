<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date = mbDate();

// Chargement des sejours SSR pour la date courante
$group_id = CGroups::loadCurrent()->_id;
$where["type"] = "= 'ssr'";
$where["group_id"] = "= '$group_id'";
$where["annule"] = "= '0'";
$sejours = CSejour::loadListForDate($date, $where);
 
// Chargement du détail des séjour
foreach ($sejours as $_sejour) {
  $_sejour->loadRefPraticien(1);
  
  // Bilan SSR
  $_sejour->loadRefBilanSSR();
  $bilan =& $_sejour->_ref_bilan_ssr;
  $bilan->loadRefKineJournee($date);
  
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
	
	$_sejour->loadRefPrescriptionSejour();
	$_sejour->_ref_prescription_sejour->loadRefsLinesElementByCat();
  $_sejour->_ref_prescription_sejour->countRecentModif();
	
  // Chargement des lignes de la prescription
	$_sejour->_ref_prescription_sejour->loadRefsLinesElement();

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

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("date", $date);
$smarty->assign("order_col", "");
$smarty->assign("order_way", "");
$smarty->assign("plannings", $plannings);
$smarty->display("offline_sejours.tpl");

?>