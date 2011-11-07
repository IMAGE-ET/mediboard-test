<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id  = CValue::getOrSession("service_id");
$date        = CValue::getOrSession("date", mbDate());
$date_max    = CValue::getOrSession("date_max", mbDate("+ 7 DAY", $date));
$nb_unites   = CValue::getOrSession("nb_unites", 1);
$cout_euro   = CValue::getOrSession("cout_euro", 1);

$nb_days = mbDaysRelative($date, $date_max);
$dates = array();
for($i = 0; $i < $nb_days; $i++) {
  $_tmp_date = mbDate("+$i day", $date);
  $dates[$_tmp_date] = $_tmp_date;
}

$service = new CService();
$services = $service->loadGroupList();

// Chargement des sejours pour le service selectionné
$sejours = array();
$affectation = new CAffectation();

$ljoin = array();
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
  
$where = array();

$where[] = "'$date' <= affectation.sortie && '$date_max' >= affectation.entree";
$where["service.service_id"] = " = '$service_id'";

$affectations = $affectation->loadList($where, null, null, null, $ljoin);
$planifications = array();
$ressources = array();


CMbObject::massLoadFwdRef($affectations, "sejour_id");

foreach($affectations as $_affectation){
  $_affectation->loadView();
  
  $sejour = $_affectation->loadRefSejour(1);
  $sejour->_ref_current_affectation = $_affectation;
	$sejour->loadRefPatient();

	$planifs = array();
	$planif = new CPlanificationSysteme();
	$ljoin = array();
	$ljoin["affectation"] = "affectation.sejour_id = planification_systeme.sejour_id";
	$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
	$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";

  $where = array();
	$where["planification_systeme.sejour_id"] = " = '$sejour->_id'";
	$where["dateTime"] = " BETWEEN '$date' AND '$date_max'";
  $where["chambre.service_id"] = " = '$service_id'";
	$where["object_class"] = " = 'CPrescriptionLineElement'";
	$planifs = $planif->loadList($where, null, null, null, $ljoin);
  
	if(isset($sejours[$sejour->_id])){
		$planifications[$sejour->_id] += $planifs;
	} else {
		$planifications[$sejour->_id] = $planifs;
	}
	$sejours[$sejour->_id] = $sejour;
}

$total_sejour = array();
$total_date = array();
$total = array();
$charge = array();

foreach($dates as $_date){
  $total_date[$_date] = array();
}
			
// Parcours des planifications et calcul de la charge
foreach($planifications as &$_planifs){
	foreach($_planifs as &$_planif){
		if(!isset($charge[$_planif->sejour_id])){
			foreach($dates as $_date){
				$charge[$_planif->sejour_id][$_date] = array();
			}
		}
		
		$line_element = $_planif->loadTargetObject();
	  $element_prescription = $line_element->_ref_element_prescription;
		$element_prescription->loadRefsIndicesCout();
		foreach($element_prescription->_ref_indices_cout as $_indice_cout){
			$_indice_cout->loadRefRessourceSoin();
			$ressources[$_indice_cout->_ref_ressource_soin->_id] = $_indice_cout->_ref_ressource_soin;
      @$charge[$_planif->sejour_id][mbDate($_planif->dateTime)][$_indice_cout->_ref_ressource_soin->_id] += $_indice_cout->nb;
      
      @$total_sejour[$_planif->sejour_id][$_indice_cout->_ref_ressource_soin->_id] += $_indice_cout->nb;
      @$total_date[mbDate($_planif->dateTime)][$_indice_cout->_ref_ressource_soin->_id] += $_indice_cout->nb;
      @$total[$_indice_cout->_ref_ressource_soin->_id] += $_indice_cout->nb;
		}
	}
}


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("date", $date);
$smarty->assign("date_max", $date_max);
$smarty->assign("sejours", $sejours);
$smarty->assign("planifications", $planifications);
$smarty->assign("ressources", $ressources);
$smarty->assign("charge", $charge);
$smarty->assign("dates", $dates);
$smarty->assign("nb_unites", $nb_unites);
$smarty->assign("cout_euro", $cout_euro);
$smarty->assign("total_date", $total_date);
$smarty->assign("total_sejour", $total_sejour);
$smarty->assign("total", $total);
$smarty->display('vw_ressources_soins.tpl');
