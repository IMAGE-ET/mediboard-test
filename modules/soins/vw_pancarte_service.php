<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::getOrSession("service_id");
$date = CValue::getOrSession("date_pancarte", mbDate());

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
$where["sejour.entree_prevue"]      = " < '$date 23:59:59'";
$where["sejour.sortie_prevue"]      = " > '$date 00:00:00'";
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
    }
  }
}

foreach($prescriptions as $_prescription){
  $_prescription->_ref_object->loadRefsAffectations();
  $lits[$_prescription->_ref_object->_ref_last_affectation->_ref_lit->_view."-".$_prescription->_id] = $_prescription->_id;
  $patients[$_prescription->_ref_patient->_id] = $_prescription->_ref_patient;
  $_prescription->loadRefPraticien();
  $_prescription->_ref_praticien->loadRefFunction();
  $_prescription->_ref_patient->loadRefPhotoIdentite();
  $_prescription->loadRefsLinesMedByCat("1","1","service"); 
  $_prescription->loadRefsPerfusions();
  $_prescription->loadRefsLinesElementByCat("1",null,"service");
  
  // Calcul du plan de soin
  foreach($tabHours as $curr_date => $curr_hours) {
    $_prescription->calculPlanSoin($curr_date);
  }

  // Creation du tableau de stockage des elements precrits pour un patient et un dateTime donné
	foreach($tabHours as $_date => $_hours_by_moment){
    foreach($_hours_by_moment as $moment_journee => $_dates){
      foreach($_dates as $date_reelle => $_hours){
        foreach($_hours as $_heure_reelle => $_hour){
          $dateTime = "$date_reelle $_heure_reelle";
          $lines_by_type = array();
          if($_prescription->_ref_lines_med_for_plan){
            $lines_by_type["produit"]["med"] = $_prescription->_ref_lines_med_for_plan;
          }
          if($_prescription->_ref_injections_for_plan){
            $lines_by_type["produit"]["inj"] = $_prescription->_ref_injections_for_plan;
          }
          if($_prescription->_ref_lines_elt_for_plan){
            $lines_by_type["elt"] = $_prescription->_ref_lines_elt_for_plan;
          }
          // Parcours des medicaments, injections, elements
          foreach($lines_by_type as $_lines_by_chap){
            foreach($_lines_by_chap as $type => $_lines){
	          	foreach($_lines as $chapitres){
						    foreach($chapitres as $_lines_by_unite){
						      foreach($_lines_by_unite as $unite_prise => $_line){
						      	if($_line->_class_name == "CPrescriptionLineMedicament"){
						      		$_line->loadRefProduitPrescription();
						      	}
						        $quantite_prevue = $quantite_adm = 0;           
						        if(isset($_line->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'])){
			                $quantite_prevue = $_line->_administrations[$unite_prise][$date_reelle][$_hour]['quantite_planifiee'];
	                  } else {
							        if(isset($_line->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'])){
							          $quantite_prevue = $_line->_quantity_by_date[$unite_prise][$date_reelle]['quantites'][$_hour]['total'];
							        }
						        }
						        if($quantite_prevue){
	                    $pancarte[$_prescription->_id][$dateTime][$type][$_line->_id]["prevue"] = $quantite_prevue;
							      }
						      	if(isset($_line->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"])){
							        $quantite_adm = $_line->_administrations[$unite_prise][$date_reelle][$_hour]["quantite"];
							        @$pancarte[$_prescription->_id][$dateTime][$type][$_line->_id]["adm"] = $quantite_adm;
								    }
								    if($quantite_prevue || $quantite_adm){
								      $list_lines[$type][$_line->_id] = $_line;
									    if($_line->_recent_modification){
							          $new[$_prescription->_id][$dateTime] = 1;
							          $pancarte[$_prescription->_id][$dateTime][$type][$_line->_id]["new"] = 1;
							        }
							        // Creation du tableau d'urgences
	                    if(is_array($_line->_dates_urgences) && array_key_exists($date_reelle, $_line->_dates_urgences) 
							           && in_array($dateTime, $_line->_dates_urgences[$date_reelle])){
							          $urgences[$_prescription->_id][$dateTime] = 1;
							          $pancarte[$_prescription->_id][$dateTime][$type][$_line->_id]["urgence"] = 1;
							        }
								    }
							      if($quantite_prevue != $quantite_adm){
							        $alertes[$_prescription->_id][$dateTime][$type] = 1;
							      }
						      }
						    }
		          }
            }
          }
          // Parcours des perfusions
          if($_prescription->_ref_perfusions_for_plan){
            foreach($_prescription->_ref_perfusions_for_plan as $_perfusion){
              $list_lines["perf"][$_perfusion->_id] = $_perfusion;
              foreach($_perfusion->_ref_lines as $_perf_line){
                $list_lines["perf_line"][$_perf_line->_id] = $_perf_line;
                $quantite_prevue = 0;
                $quantite_adm = 0;
                if(isset($_perfusion->_prises_prevues[$date_reelle][$_hour])){
                  $quantite_prevue = $_perf_line->_quantite_administration;
                  $pancarte[$_prescription->_id][$dateTime]["perf"][$_perfusion->_id][$_perf_line->_id]["prevue"] = $quantite_prevue;
                }
                if(isset($_perf_line->_administrations[$date_reelle][$_hour])){
                  $quantite_adm = $_perf_line->_administrations[$date_reelle][$_hour];
                  $pancarte[$_prescription->_id][$dateTime]["perf"][$_perfusion->_id][$_perf_line->_id]["adm"] = $quantite_adm;
                }		
                if($quantite_prevue != $quantite_adm){
							    $alertes[$_prescription->_id][$dateTime]["perf"] = 1;
							  }    
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
$smarty->display('vw_pancarte_service.tpl');

?>