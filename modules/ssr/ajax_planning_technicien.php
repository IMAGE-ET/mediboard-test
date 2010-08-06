<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCando::checkRead();

$date         = CValue::getOrSession("date", mbDate());
$kine_id      = CValue::getOrSession("kine_id");
$surveillance = CValue::getOrSession("surveillance");
$sejour_id    = CValue::get("sejour_id");
$height       = CValue::get("height");
$selectable   = CValue::get("selectable");
$large        = CValue::get("large");
$print        = CValue::get("print");

$kine = new CMediusers();
$kine->load($kine_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

$nb_days_planning = $sejour->_id ? 
  $sejour->getNbJourPlanning($date) : 
	CEvenementSSR::getNbJoursPlanning($kine_id, $date);
$planning = new CPlanningWeek($date, null, null, $nb_days_planning, $selectable, $height, $large, !$print);
$planning->title = $surveillance ?
  "Surveillance '$kine->_view'" :
  "Rééducateur '$kine->_view'";	

$planning->guid = $kine->_guid;
$planning->guid .= $surveillance ? "-surv" : "-tech";

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where = array();
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$where["therapeute_id"] = " = '$kine->_id'";
$where["equipement_id"] = $surveillance ? " IS NOT NULL" : " IS NULL";
$evenements = $evenement_ssr->loadList($where);

// Chargement des evenements SSR de "charge"
$where["equipement_id"] = $surveillance ? " IS NULL" : " IS NOT NULL";
$evenements_charge = $evenement_ssr->loadList($where);

foreach($evenements_charge as $_evenement){
  $planning->addLoad($_evenement->debut, $_evenement->duree);
}

foreach($evenements as $_evenement){
	$_evenement->loadRefPrescriptionLineElement();
  $_evenement->loadRefSejour();
  $_evenement->loadRefEquipement();
  $_evenement->_ref_sejour->loadRefPatient();
  
	$important = $sejour_id ? ($_evenement->sejour_id == $sejour_id) : true;
  
  $patient =  $_evenement->_ref_sejour->_ref_patient;
  
	if($_evenement->sejour_id){
	  $title = $patient->nom;
  } else {
		$_evenement->loadRefsEvenementsSeance();
		$title = count($_evenement->_ref_evenements_seance)." patient(s)";
	}
	if($large){
    $title .= " ". substr($patient->prenom,0,2).".";		
	}
	if(!$sejour_id && $_evenement->remarque){
		$title .= " - ".$_evenement->remarque;
	}
  $element_prescription =& $_evenement->_ref_prescription_line_element->_ref_element_prescription;
  $color = $element_prescription->_color ? "#$element_prescription->_color" : null;
  
  
	$class= "";
	if (!$_evenement->countBackRefs("actes_cdarr")) {
    $class = "zero-actes";
  }
  
	$_sejour = $_evenement->_ref_sejour;
	if (!in_range($_evenement->debut, mbDate($_sejour->entree), mbDate("+1 DAY", $_sejour->sortie))) {
		$class = "disabled";
	}
	
  if ($_evenement->realise && $selectable){
    $class = "realise";
  }

  $css_classes = array(
	  $class,
    $element_prescription->_guid, 
    $_evenement->_ref_sejour->_guid, 
    $_evenement->_ref_equipement->_guid
	);
                       
  $planning->addEvent(new CPlanningEvent(
    $_evenement->_guid, 
    $_evenement->debut, 
    $_evenement->duree, 
    $title, 
    $color, 
    $important,
    $css_classes
  ));
}

$config = $surveillance ? CAppUI::conf("ssr occupation_surveillance") : CAppUI::conf("ssr occupation_technicien");

// Labels de charge sur la journée
$ds = CSQLDataSource::get("std");
$query = "SELECT SUM(duree) as total, DATE(debut) as date
          FROM evenement_ssr
          WHERE therapeute_id = '$kine->_id' AND debut BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59' AND ";
          
$query .= $surveillance ? "equipement_id IS NULL" : "equipement_id IS NOT NULL";
$query .= " GROUP BY DATE(debut)";
          
$duree_occupation = $ds->loadList($query);

foreach($duree_occupation as $_occupation){
	$duree_occupation = $_occupation["total"];
	
	if($duree_occupation < $config["faible"]){
	  $color = "#8f8";
	}
	if($duree_occupation > $config["eleve"]){
    $color = "#f88";
  }
	if($duree_occupation >= $config["faible"] && $duree_occupation <= $config["eleve"]){
		$color = "#ff4";
	}
  $planning->addDayLabel($_occupation["date"], $_occupation["total"]." mn", null, $color);
}

foreach ($kine->loadBackRefs("plages_conge") as $_plage) {
	$planning->addUnavailability($_plage->date_debut, $_plage->date_fin);
}

// Heure courante
$planning->showNow();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");

?>