<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::getOrSession("service_id");
$date = CValue::getOrSession("debut", mbDate());

$filter_line = new CPrescriptionLineMedicament();
$filter_line->debut = $date;

// Chargement du service
$service = new CService();
$service->load($service_id);

// Initialisations
$services = $service->loadGroupList();
$patients = array();
$alertes = array();
$perfs = array();
$new = array();
$urgences = array();
$lines = array();
$pancarte = array();
$lits = array();
$list_lines = array();

// Chargement des prescriptions qui sont dans le service selectionné
$prescription = new CPrescription();
$prescriptions = array();
$ljoin = array();
$ljoin["sejour"]      = "prescription.object_id = sejour.sejour_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"]         = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]     = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]     = "service.service_id = chambre.service_id";
$where = array();
$where["prescription.object_class"] = " = 'CSejour'";
$where["prescription.type"]         = " = 'sejour'";
$where["service.service_id"]        = " = '$service_id'";


//$where["sejour.entree_prevue"]      = " < '$date 23:59:59'";
//$where["sejour.sortie_prevue"]      = " > '$date 00:00:00'";
$where["affectation.entree"]      = " < '$date 23:59:59'";
$where["affectation.sortie"]      = " > '$date 00:00:00'";	


$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

// Chargement des configs de services
if(!$service_id){
  $service_id = "none";
}
$config_service = new CConfigService();
$configs = $config_service->getConfigForService($service_id);

$matin = range($configs["Borne matin min"], $configs["Borne matin max"]);
$soir = range($configs["Borne soir min"], $configs["Borne soir max"]);
$nuit_soir = range($configs["Borne nuit min"], 23);
$nuit_matin = range(00, $configs["Borne nuit max"]);

foreach($matin as &$_hour_matin){
  $_hour_matin = str_pad($_hour_matin, 2, "0", STR_PAD_LEFT);
}
foreach($soir as &$_soir_matin){
  $_soir_matin = str_pad($_soir_matin, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_soir as &$_hour_nuit_soir){
  $nuit[] = str_pad($_hour_nuit_soir, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_matin as &$_hour_nuit_matin){
  $nuit[] = str_pad($_hour_nuit_matin, 2, "0", STR_PAD_LEFT);
}

// Recuperation de l'heure courante
$time = mbTransformTime(null,null,"%H");

// Construction de la structure de date à parcourir dans le tpl
if($date == mbDate()){
  if(in_array($time, $matin)){
    $date_min = mbDate("- 1 DAY", $date)." ".reset($soir).":00:00";
    $dates = array($date => array("matin" => $matin, "soir" => $soir));
  }
  if(in_array($time, $soir)){
    $date_min = mbDate("- 1 DAY", $date)." ".reset($nuit).":00:00";
    $dates = array($date => array("soir" => $soir, "nuit" => $nuit));
  }
  if(in_array($time, $nuit)){
    $date_min = mbDate("- 1 DAY", $date)." ".reset($matin).":00:00";
    $dates = array($date => array("nuit" => $nuit), mbDate("+ 1 DAY", $date) => array("matin" => $matin));
  }
} else {
  $date_min = "$date 00:00:00"; 
  $dates = array($date => array("matin" => $matin, "soir" => $soir, 'nuit' => $nuit));
}

$tabDates = array();

$composition_dossier = array();
foreach($dates as $curr_date => $_date){
  foreach($_date as $moment_journee => $_hours){
    $composition_dossier[] = "$curr_date-$moment_journee";
    foreach($_hours as $_hour){
      $date_reelle = $curr_date;
      if($moment_journee == "nuit" && $_hour < "12:00:00"){
        $date_reelle = mbDate("+ 1 DAY", $curr_date);
      }
      $_dates[$date_reelle] = $date_reelle;
      $tabHours[$curr_date][$moment_journee][$date_reelle]["$_hour:00:00"] = $_hour;
      if(!in_array($date_reelle, $tabDates)){
        $tabDates[] = $date_reelle;
      }
    }
  }
}
$date_max = "$date_reelle $_hour:00:00";


foreach($prescriptions as $_prescription){
  $_prescription->loadRefPatient();
  $patients[$_prescription->_ref_patient->_id] = $_prescription->_ref_patient;
  
	$_prescription->_ref_object->loadRefsAffectations();
  $lits[$_prescription->_ref_object->_ref_last_affectation->_ref_lit->_view."-".$_prescription->_id] = $_prescription->_id;
  
	$_prescription->loadRefPraticien();
  $_prescription->_ref_praticien->loadRefFunction();
  $_prescription->_ref_patient->loadRefPhotoIdentite();
	
	$_prescription->calculAllPlanifSysteme();
  
	// Chargement des prises prevues entre les dates prevues
	$planif = new CPlanificationSysteme();
	$where = array();
  $where["sejour_id"] = " = '$_prescription->object_id'";
	$where["dateTime"] = " BETWEEN '$date_min' AND '$date_max'";
  $planifs = $planif->loadList($where);
	
	
	foreach($planifs as $_planif){
		// Chargement et stockage de la ligne
		$_planif->loadTargetObject();

    if($_planif->_ref_object instanceof CPrescriptionLineMedicament || $_planif->_ref_object instanceof CPrescriptionLineElement){
	    // Chargement de la prise
	    $_planif->loadRefPrise();
			if($_planif->_ref_object instanceof CPrescriptionLineMedicament){
				$type = $_planif->_ref_object->_is_injectable ? "inj" : "med";
			}
			if($_planif->_ref_object instanceof CPrescriptionLineElement){
	      $type = $_planif->_ref_object->_ref_element_prescription->_ref_category_prescription->chapitre;
	    }
			$list_lines[$type][$_planif->_ref_object->_id] = $_planif->_ref_object;
			
			if($_planif->_ref_prise->_quantite_administrable){			
	      $time = mbTransformTime($_planif->dateTime,null,"%H").":00:00";
	      $date = mbDate($_planif->dateTime);
			  if(!isset($pancarte[$_prescription->_id][$_planif->dateTime][$type][$_planif->object_id]["prevue"])){
				  $pancarte[$_prescription->_id]["$date $time"][$type][$_planif->object_id]["prevue"] = 0;
				}
				$pancarte[$_prescription->_id]["$date $time"][$type][$_planif->object_id]["prevue"] += $_planif->_ref_prise->_quantite_administrable;
			}
		}
		
		if($_planif->_ref_object instanceof CPerfusionLine){
      $_planif->_ref_object->updateQuantiteAdministration();
			$list_lines["perf"][$_planif->_ref_object->_ref_perfusion->_id] = $_planif->_ref_object->_ref_perfusion;
      $list_lines["perf_line"][$_planif->_ref_object->_id] = $_planif->_ref_object; 
			$time = mbTransformTime($_planif->dateTime,null,"%H").":00:00";
			$date = mbDate($_planif->dateTime);
      $pancarte[$_prescription->_id]["$date $time"]["perf"][$_planif->_ref_object->perfusion_id][$_planif->object_id]["prevue"] = $_planif->_ref_object->_quantite_administration;
  	}
	}
	
	// Chargement des administrations
	$administration = new CAdministration();
	$ljoin = array();
	$ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = administration.object_id) 
	                                           AND (administration.object_class = 'CPrescriptionLineMedicament')";
																						 
	$ljoin["prescription_line_element"] = "(prescription_line_element.prescription_line_element_id = administration.object_id) 
                                             AND (administration.object_class = 'CPrescriptionLineElement')";
																						 
	$ljoin["perfusion_line"] = "(perfusion_line.perfusion_line_id = administration.object_id) 
                                             AND (administration.object_class = 'CPerfusionLine')";										
																						 
	$ljoin["perfusion"] = "perfusion_line.perfusion_id = perfusion.perfusion_id";           
																						 																					 
	$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
	                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
														(perfusion.prescription_id = prescription.prescription_id)";
	$where = array();
	$where["prescription.prescription_id"] = " = '$_prescription->_id'";
	$administrations = $administration->loadList($where, null, null, null, $ljoin);
											
	foreach($administrations as $_administration){
		$_administration->loadTargetObject();
		if($_administration->_ref_object instanceof CPrescriptionLineMedicament || $_administration->_ref_object instanceof CPrescriptionLineElement){
			if($_administration->_ref_object instanceof CPrescriptionLineMedicament){
	      $type = $_administration->_ref_object->_is_injectable ? "inj" : "med";
	    }
	    if($_administration->_ref_object instanceof CPrescriptionLineElement){
	      $type = $_administration->_ref_object->_ref_element_prescription->_ref_category_prescription->chapitre;
	    }	  
	    $_administration->_ref_object->_unite_administration = $_administration->unite_prise;
	    $list_lines[$type][$_administration->_ref_object->_id] = $_administration->_ref_object;
	    
	    if(!isset($pancarte[$_prescription->_id][$_administration->dateTime][$type][$_administration->object_id]["adm"])){
	      $pancarte[$_prescription->_id][$_administration->dateTime][$type][$_administration->object_id]["adm"] = 0;
	    }
	    $pancarte[$_prescription->_id][$_administration->dateTime][$type][$_administration->object_id]["adm"] += $_administration->quantite;
    }
		
		
		if($_administration->_ref_object instanceof CPerfusionLine){
			$perfusion_line = $_administration->_ref_object;
      $time = mbTransformTime($_administration->dateTime,null,"%H").":00:00";
      $date = mbDate($_administration->dateTime);			
			
			$list_lines["perf"][$_administration->_ref_object->_ref_perfusion->_id] = $_administration->_ref_object->_ref_perfusion;
      $list_lines["perf_line"][$_administration->_ref_object->_id] = $_administration->_ref_object; 
			
			if(!isset($pancarte[$_prescription->_id]["$date $time"]["perf"][$perfusion_line->perfusion_id][$_administration->object_id]["adm"])){
        $pancarte[$_prescription->_id]["$date $time"]["perf"][$perfusion_line->perfusion_id][$_administration->object_id]["adm"] = 0;
      }
		  $pancarte[$_prescription->_id]["$date $time"]["perf"][$perfusion_line->perfusion_id][$_administration->object_id]["adm"] += $_administration->quantite;
		}
	}																																		 											 


  foreach($pancarte as $_prescription_id => $pancarte_by_prescription){
  	foreach($pancarte_by_prescription as $_dateTime => $prescription_by_datetime){
  		foreach($prescription_by_datetime as $_type => $presc_by_type){
  			if($_type != "perf"){
	  			foreach($presc_by_type as $prescription_by_object){
	  				if(!isset($prescription_by_object["adm"])){
	  					$prescription_by_object["adm"] = 0;
	  				}
						if(!isset($prescription_by_object["prevue"])){
	            $prescription_by_object["prevue"] = 0;
	          }
						if($prescription_by_object["adm"] != $prescription_by_object["prevue"]){
	  					$alertes[$_prescription_id][$_dateTime][$_type] = 1;
	  				}
	  			}
				} else {
					foreach($presc_by_type as $prescription_by_object){
						foreach($prescription_by_object as $_prescription_by_object){
						  if(!isset($_prescription_by_object["adm"])){
                $_prescription_by_object["adm"] = 0;
	            }
	            if(!isset($_prescription_by_object["prevue"])){
	              $_prescription_by_object["prevue"] = 0;
	            }
	            if($_prescription_by_object["adm"] != $_prescription_by_object["prevue"]){
	              $alertes[$_prescription_id][$_dateTime][$_type] = 1;
	            }	
						}
					}
				}
  		}
  	}
  }
}
// Classement par lit
ksort($lits);
$_prescriptions = array();
foreach($lits as $prescription_id){
  $_prescriptions[$prescription_id] = $prescriptions[$prescription_id];
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("pancarte", $pancarte);
$smarty->assign("list_lines", $list_lines);
$smarty->assign("count_matin", count($matin));
$smarty->assign("count_soir", count($soir));
$smarty->assign("count_nuit", count($nuit));
$smarty->assign("tabHours", $tabHours);
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);
$smarty->assign("prescriptions", $_prescriptions);
$smarty->assign("date"     , $date);
$smarty->assign("date_min", $date_min);
$smarty->assign("service", $service);
$smarty->assign("patients", $patients);
$smarty->assign("alertes", $alertes);
$smarty->assign("new", $new);
$smarty->assign("urgences", $urgences);
$smarty->assign("filter_line", $filter_line);
$smarty->display('vw_pancarte_service.tpl');

?>