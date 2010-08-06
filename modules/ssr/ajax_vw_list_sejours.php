<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date = CValue::getOrSession("date", mbDate());
$type = CValue::getOrSession("type");

$monday = mbDate("last monday", mbDate("+1 day", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

// Chargement des conges
$plage_conge = new CPlageConge();
$where = array();
$where[] = "(plageconge.date_debut BETWEEN '$monday' AND '$sunday') OR 
            (plageconge.date_fin BETWEEN '$monday' AND '$sunday') OR
            (plageconge.date_debut <= '$monday' AND plageconge.date_fin >= '$sunday')";
$plages_conge = $plage_conge->loadList($where);

$group_id = CGroups::loadCurrent()->_id;
$sejours = array();
$_sejours = array();
$count_evts = array();
$sejours_count = 0;

// Pour chaque plage de conge, recherche 
foreach($plages_conge as $_plage_conge){
	$_plage_conge->loadRefUser();
	$_sejours = array();
	
	$date_debut = $_plage_conge->date_debut;
  $date_fin = mbDate("+1 DAY", $_plage_conge->date_fin);
	
	if($type == "kine"){
		$sejour = new CSejour();
	  $ljoin["bilan_ssr"] = "bilan_ssr.sejour_id = sejour.sejour_id";
	  $ljoin["technicien"] = "bilan_ssr.technicien_id = technicien.technicien_id";
	  
	  $where = array();
	  $where["type"] = "= 'ssr'";
	  $where["group_id"] = "= '$group_id'";
	  $where[] = "(sejour.entree BETWEEN '$date_debut' AND '$date_fin') OR 
	              (sejour.sortie BETWEEN '$date_debut' AND '$date_fin') OR
	              (sejour.entree <= '$date_debut' AND sejour.sortie >= '$date_fin')";
	              
	  $where["technicien.kine_id"] = " = '$_plage_conge->user_id'";
	  $_sejours = $sejour->loadList($where, null, null, null, $ljoin);
  }
	
	if($type == "reeducateur"){
		$evenement = new CEvenementSSR();
		$where = array();
		$where["debut"] = " BETWEEN '$date_debut' AND '$date_fin'";
    $where["therapeute_id"] = " = '$_plage_conge->user_id'";
		$evenements = $evenement->loadList($where);

		foreach($evenements as $_evenement){
			$_evenement->loadRefSejour();
			$sejour =& $_evenement->_ref_sejour;
			$sejour->loadRefBilanSSR();
			$bilan =& $sejour->_ref_bilan_ssr;
			$bilan->loadRefTechnicien();
      $_sejours[$_evenement->sejour_id] = $_evenement->_ref_sejour;
		}
	}
	
	if (count($_sejours)){
		foreach($_sejours as $_sejour) {
			
			// On compte le nombre d'evenements SSR à transferer
			$evenement_ssr = new CEvenementSSR();
			$where = array();
			$where["sejour_id"] = " = '$_sejour->_id'";
			$where["therapeute_id"] = " = '$_plage_conge->user_id'";
      $where["debut"] = " BETWEEN '$date_debut' AND '$date_fin'";
			$count_evts["$_plage_conge->_id-$_sejour->_id"] = $evenement_ssr->countList($where);
			
			$_sejour->checkDaysRelative($date);
			$_sejour->loadRefReplacement();
			if (!$_sejour->_ref_replacement->_id || $type == "reeducateur") {
		    $sejours_count++;
			}

		  // Bilan SSR
		  $_sejour->loadRefBilanSSR();
		  $bilan =& $_sejour->_ref_bilan_ssr;
		  $bilan->loadFwdRef("technicien_id");
		  
		  // Kine principal
		  $technicien =& $bilan->_fwd["technicien_id"];
		  $technicien->loadRefKine();
		  $technicien->_ref_kine->loadRefFunction(); 
		  
		  // Patient
		  $_sejour->loadRefPatient();
		  $patient =& $_sejour->_ref_patient;
		  $patient->loadIPP();
		}
		$sejours[$_plage_conge->_id] = $_sejours;
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("sejours_count", $sejours_count);
$smarty->assign("plages_conge", $plages_conge);
$smarty->assign("type", $type);
$smarty->assign("count_evts", $count_evts);
$smarty->display("inc_vw_list_sejours.tpl");

?>