<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage soins
 *  @version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$service_id  = CValue::getOrSession("service_id");
$nb_unites   = CValue::getOrSession("nb_unites", 1);
$show_cost   = CValue::getOrSession("show_cost", 1);

$datetime   = CValue::getOrSession("datetime", CMbDT::dateTime());
$period     = CValue::getOrSession("period", "day");
$nb_periods = CValue::getOrSession("nb_periods", "14");

$datetime = CMbDate::dirac($period, $datetime);
$datetimes = array();
for ($i = 0; $i < $nb_periods; $i++) {
  $_datetime = CMbDT::dateTime("+$i $period", $datetime);
  $datetimes[$_datetime] = $_datetime;
}

$service = new CService();
$where = array();
$where["cancelled"] = "= '0'";
$services = $service->loadGroupList($where);

// Chargement des sejours pour le service selectionné
$sejours = array();
$affectation = new CAffectation();

$ljoin = array();
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
  
$where = array();

$datetime_min = CMbDT::dateTime($datetime);
$datetime_max = CMbDT::dateTime("+$nb_periods $period", $datetime);

$where["sortie"] = ">= '$datetime_min'";
$where["entree"] = "<= '$datetime_max'";
$where["service.service_id"] = " = '$service_id'";

$affectations = $affectation->loadList($where, null, null, null, $ljoin);
$planifications = array();
$ressources = array();


CMbObject::massLoadFwdRef($affectations, "sejour_id");

foreach ($affectations as $_affectation){
  $_affectation->loadView();

  // Chargement du séjour
  $sejour = $_affectation->loadRefSejour(1);
  $sejour->_ref_current_affectation = $_affectation;
  $sejour->loadRefPatient();
  $sejour->entree = CMbDate::dirac($period, $sejour->entree);
  $sejour->sortie = CMbDate::dirac($period, $sejour->sortie);
  $sejours[$sejour->_id] = $sejour;
  
  // Chargement des planification système
  $planif = new CPlanificationSysteme();
  $ljoin = array();
  $ljoin["affectation"] = "affectation.sejour_id = planification_systeme.sejour_id";
  $ljoin["lit"        ] = "lit.lit_id = affectation.lit_id";
  $ljoin["chambre"    ] = "chambre.chambre_id = lit.chambre_id";
  
  $where = array();
  $where["planification_systeme.sejour_id"] = " = '$sejour->_id'";
  $where["dateTime"          ] = " BETWEEN '$datetime_min' AND '$datetime_max'";
  $where["chambre.service_id"] = " = '$service_id'";
  $where["object_class"      ] = " = 'CPrescriptionLineElement'";
  $planifs = $planif->loadList($where, null, null, null, $ljoin);
  
  // Classement par séjour
  if (!isset($planifications[$sejour->_id])) {
    $planifications[$sejour->_id] = array();
  } 
  
  $planifications[$sejour->_id] += $planifs;
}

$total_sejour = array();
$total_date = array();
$total = array();
$charge = array();

foreach ($datetimes as $_datetime) {
  $total_datetime[$_datetime] = array();
}
      
// Parcours des planifications et calcul de la charge
foreach ($planifications as &$_planifs){
  foreach ($_planifs as &$_planif){
    $line_element = $_planif->loadTargetObject();
    $element_prescription = $line_element->_ref_element_prescription;
    $element_prescription->loadRefsIndicesCout();
    
    if (!count($element_prescription->_ref_indices_cout)) {
      continue;
    }
    if (!isset($charge[$_planif->sejour_id])){
      foreach ($datetimes as $_datetime) {
        $charge[$_planif->sejour_id][$_datetime] = array();
      }
    }
    
    foreach ($element_prescription->_ref_indices_cout as $_indice_cout) {
      $ressource = $_indice_cout->loadRefRessourceSoin();
      
      $planif_date_time = CMbDate::dirac($period, $_planif->dateTime);
      
      $ressources[$ressource->_id] = $ressource;
      @$charge[$_planif->sejour_id][$planif_date_time][$ressource->_id] += $_indice_cout->nb;

      @$total_sejour[$_planif->sejour_id][$ressource->_id] += $_indice_cout->nb;
      @$total_datetime[$planif_date_time][$ressource->_id] += $_indice_cout->nb;
      @$total[$ressource->_id] += $_indice_cout->nb;
    }
  }
}

$bank_holidays = CMbDT::bankHolidays();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign("datetimes", $datetimes);
$smarty->assign("datetime", $datetime);
$smarty->assign("nb_periods", $nb_periods);
$smarty->assign("period", $period);
$smarty->assign("sejours", $sejours);
$smarty->assign("planifications", $planifications);
$smarty->assign("ressources", $ressources);
$smarty->assign("charge", $charge);
$smarty->assign("nb_unites", $nb_unites);
$smarty->assign("show_cost", $show_cost);
$smarty->assign("total_datetime", $total_datetime);
$smarty->assign("total_sejour", $total_sejour);
$smarty->assign("total", $total);
$smarty->display('vw_ressources_soins.tpl');
