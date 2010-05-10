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
$sejour_id    = CValue::getOrSession("sejour_id");
$height       = CValue::get("height");
$selectable   = CValue::get("selectable");
$large         = CValue::get("large");

$kine = new CMediusers();
$kine->load($kine_id);

$sejour = new CSejour();
$sejour->load($sejour_id);

$nb_days_planning = $sejour->_id ? $sejour->getNbJourPlanning($date) : 7;
$planning = new CPlanningWeek($date, null, null, $nb_days_planning, $selectable, $height, $large);
$planning->title = $surveillance ?
  "Planning de surveillance du technicien '$kine->_view'" :
  "Planning du technicien '$kine->_view'";	

$planning->guid = $kine->_guid;

// Chargement des evenement SSR 
$evenement_ssr = new CEvenementSSR();
$where["debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";
$where["therapeute_id"] = " = '$kine->_id'";
$where["equipement_id"] = $surveillance ? " IS NOT NULL" : " IS NULL";
$evenements = $evenement_ssr->loadList($where);

$ds = CSQLDataSource::get("std");
$query = "SELECT SUM(duree) as total, DATE(debut) as date
					FROM evenement_ssr
					WHERE therapeute_id = '$kine->_id' AND debut BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59' AND ";
					
$query .= $surveillance ? "equipement_id IS NULL" : "equipement_id IS NOT NULL";
$query .= " GROUP BY DATE(debut)";
					
$duree_occupation = $ds->loadList($query);

foreach($evenements as $_evenement){
  $_evenement->loadRefElementPrescription();
  $_evenement->loadRefSejour();
  $_evenement->_ref_sejour->loadRefPatient();
  $_evenement->loadRefEquipement();
  
	
  $important = $sejour_id ? ($_evenement->sejour_id == $sejour_id) : true;
  
  $patient =  $_evenement->_ref_sejour->_ref_patient;
  $title = "$patient->_civilite $patient->nom - $_evenement->code";
  $element_prescription =& $_evenement->_ref_element_prescription;
  $color = $element_prescription->_color ? "#$element_prescription->_color" : null;
  
  $css_classes = array($element_prescription->_guid, 
                       $_evenement->_ref_sejour->_guid, 
                       $_evenement->_ref_equipement->_guid);
	if($_evenement->realise && $selectable){
		$css_classes[] = "realise";
	}

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

foreach($duree_occupation as $_occupation){
	$duree_occupation = $_occupation["total"];
	
	if($duree_occupation < $config["faible"]){
	  $color = "green";
	}
	if($duree_occupation > $config["eleve"]){
    $color = "red";
  }
	if($duree_occupation >= $config["faible"] && $duree_occupation <= $config["eleve"]){
		$color = "yellow";
	}
  $planning->addDayLabel($_occupation["date"], $_occupation["total"]." mn", null, $color);
}

foreach ($kine->loadBackRefs("plages_vacances") as $_plage) {
	$planning->addUnavailability($_plage->date_debut, $_plage->date_fin);
	$replacer = $_plage->loadFwdRef("replacer_id");
	if ($replacer->_id) {
		for ($day = $_plage->date_debut; $day <= $_plage->date_fin; $day = mbDate("+1 DAY", $day)) {
      $planning->addDayLabel($day, $replacer->_view);
		}
	}
}
		
// Heure courante
$planning->addEvent(new CPlanningEvent(null, mbDateTime(), null, null, "red"));

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_vw_week.tpl");

?>